<?php
declare(strict_types=1);

namespace App\Service;

use App\Constants\RoleConstants;
use App\Model\Entity\Invoice;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Exception;

class InvoicePipelineService
{
    // Pipeline statuses in order
    public const STATUSES = ['aprobacion', 'contabilidad', 'tesoreria', 'pagada'];

    public const STATUS_LABELS = [
        'aprobacion' => 'Aprobación',
        'contabilidad' => 'Contabilidad',
        'tesoreria' => 'Tesorería',
        'pagada' => 'Pagada',
    ];

    public const STATUS_ICONS = [
        'aprobacion' => 'bi-check-circle',
        'contabilidad' => 'bi-calculator',
        'tesoreria' => 'bi-bank',
        'pagada' => 'bi-cash-coin',
    ];

    // Which statuses each role can see/work with
    private const ROLE_VISIBLE_STATUSES = [
        RoleConstants::REGISTRO_REVISION => ['aprobacion'],
        RoleConstants::CONTABILIDAD      => ['aprobacion', 'contabilidad'],
        RoleConstants::TESORERIA         => ['tesoreria'],
        RoleConstants::ADMIN             => ['aprobacion', 'contabilidad', 'tesoreria', 'pagada'],
    ];

    // All fields available for Admin in any status
    private const ALL_FIELDS = [
        'invoice_number', 'registration_date', 'issue_date', 'due_date',
        'document_type', 'purchase_order', 'provider_id', 'operation_center_id',
        'detail', 'amount', 'expense_type_id', 'cost_center_id',
        'confirmed_by', 'approver_id', 'area_approval',
        'dian_validation', 'accrued', 'accrual_date', 'ready_for_payment',
        'payment_status', 'payment_date', 'pipeline_status',
    ];

    // Fields editable by role in each status
    private const EDITABLE_FIELDS = [
        RoleConstants::REGISTRO_REVISION => [
            'aprobacion' => [
                'invoice_number', 'registration_date', 'issue_date', 'due_date',
                'document_type', 'purchase_order', 'provider_id', 'operation_center_id',
                'detail', 'amount', 'expense_type_id', 'cost_center_id',
                'confirmed_by', 'approver_id',
                'dian_validation',
            ],
        ],
        RoleConstants::CONTABILIDAD => [
            'aprobacion' => [
                'accrued', 'accrual_date', 'ready_for_payment',
            ],
            'contabilidad' => [
                'accrued', 'accrual_date', 'ready_for_payment',
            ],
        ],
        RoleConstants::TESORERIA => [
            'tesoreria' => [
                'payment_status', 'payment_date',
            ],
        ],
    ];

    // Sections visible per role (non-Admin roles have fixed sections)
    private const VISIBLE_SECTIONS_BY_ROLE = [
        RoleConstants::REGISTRO_REVISION => ['general', 'dates', 'classification', 'revision'],
        RoleConstants::CONTABILIDAD      => ['general', 'dates', 'classification', 'accounting'],
        RoleConstants::TESORERIA         => ['general', 'treasury'],
    ];

    // Fields required before advancing from each status
    private const TRANSITION_REQUIREMENTS = [
        'aprobacion' => [
            [
                'field' => 'approver_id',
                'not_empty' => true,
                'label' => 'Debe seleccionar un Aprobador',
            ],
            [
                'field' => 'dian_validation',
                'value' => 'Aprobada',
                'label' => 'Validación DIAN debe ser "Aprobada"',
            ],
            [
                'field' => 'accrued',
                'value' => true,
                'label' => 'La factura debe estar marcada como Causada',
            ],
            [
                'field' => 'accrual_date',
                'not_empty' => true,
                'label' => 'Fecha de Causación es requerida',
            ],
        ],
        'contabilidad' => [
            [
                'field' => 'ready_for_payment',
                'not_empty' => true,
                'label' => 'Campo "Lista para Pago" es requerido',
            ],
        ],
        'tesoreria' => [
            [
                'field' => 'payment_status',
                'value' => 'Pago total',
                'label' => 'Estado de Pago debe ser "Pago total" para marcar como Pagada',
            ],
            [
                'field' => 'payment_date',
                'not_empty' => true,
                'label' => 'Fecha de Pago es requerida',
            ],
        ],
    ];

    // Next status transitions
    public const TRANSITIONS = [
        'aprobacion'    => 'contabilidad',
        'contabilidad'  => 'tesoreria',
        'tesoreria'     => 'pagada',
        'pagada'        => null,
    ];

    public function getVisibleStatuses(string $roleName): array
    {
        return self::ROLE_VISIBLE_STATUSES[$roleName] ?? [];
    }

    public function getEditableFields(string $roleName, string $status): array
    {
        if ($roleName === RoleConstants::ADMIN) {
            return self::ALL_FIELDS;
        }

        return self::EDITABLE_FIELDS[$roleName][$status] ?? [];
    }

    /**
     * Returns sections visible in the edit form for the given role and current status.
     * For non-Admin roles: fixed sections regardless of status.
     * For Admin: sections depend on how far the invoice has progressed.
     */
    public function getVisibleSections(string $roleName, string $status): array
    {
        if ($roleName !== RoleConstants::ADMIN) {
            return self::VISIBLE_SECTIONS_BY_ROLE[$roleName] ?? ['general'];
        }

        // Admin: show sections up to the current state
        // STATUSES: aprobacion(0), contabilidad(1), tesoreria(2), pagada(3)
        $statusIndex = $this->getStatusIndex($status);
        $sections = ['general', 'dates', 'classification', 'revision'];
        if ($statusIndex >= 1) {
            $sections[] = 'accounting';
        }
        if ($statusIndex >= 2) {
            $sections[] = 'treasury';
        }

        return $sections;
    }

    /**
     * Returns true if the invoice has been rejected in the revision step.
     */
    public function isRejected(object $invoice): bool
    {
        return ($invoice->area_approval ?? '') === 'Rechazada';
    }

    /**
     * Validates whether all requirements are met to advance from $fromStatus.
     * Returns an array of error messages (empty = can advance).
     */
    public function validateTransitionRequirements(object $invoice, string $fromStatus): array
    {
        // Rejection blocks all advancement
        if ($this->isRejected($invoice)) {
            return ['La factura fue rechazada. El flujo ha terminado.'];
        }

        $errors = [];
        foreach (self::TRANSITION_REQUIREMENTS[$fromStatus] ?? [] as $rule) {
            $field = $rule['field'];
            $value = $invoice->$field ?? null;

            if (isset($rule['value'])) {
                $expected = $rule['value'];
                if (is_bool($expected)) {
                    $actual = (bool)$value;
                } else {
                    $actual = $value;
                }
                if ($actual !== $expected) {
                    $errors[] = $rule['label'];
                }
            } elseif (!empty($rule['not_empty'])) {
                if ($value === null || $value === '' || $value === false) {
                    $errors[] = $rule['label'];
                }
            }
        }

        return $errors;
    }

    public function canAdvance(string $roleName, string $currentStatus): bool
    {
        if ($roleName === RoleConstants::ADMIN) {
            return self::TRANSITIONS[$currentStatus] !== null;
        }

        $visibleStatuses = $this->getVisibleStatuses($roleName);
        if (!in_array($currentStatus, $visibleStatuses)) {
            return false;
        }

        return self::TRANSITIONS[$currentStatus] !== null;
    }

    public function getNextStatus(string $currentStatus): ?string
    {
        return self::TRANSITIONS[$currentStatus] ?? null;
    }

    public function filterEntityData(array $data, string $roleName, string $status): array
    {
        if ($roleName === RoleConstants::ADMIN) {
            return $data;
        }

        $allowed = $this->getEditableFields($roleName, $status);

        return array_intersect_key($data, array_flip($allowed));
    }

    public function getStatusIndex(string $status): int
    {
        $index = array_search($status, self::STATUSES);

        return $index !== false ? $index : 0;
    }

    /**
     * Save invoice fields, optionally advance the pipeline, record history, and send notifications.
     *
     * Returns an associative array:
     *   - 'saved'          => bool
     *   - 'advanced'       => bool
     *   - 'nextStatus'     => ?string
     *   - 'advanceErrors'  => string[]   (warnings when save succeeded but advance did not)
     *   - 'notificationErrors' => string[]  (notification failures, non-blocking)
     *   - 'approvalLinkSent'   => bool
     */
    public function saveAndAdvance(
        Invoice $invoice,
        array $data,
        string $roleName,
        int $userId,
        ?string $baseUrl = null,
    ): array {
        $invoicesTable = TableRegistry::getTableLocator()->get('Invoices');
        $historyService = new InvoiceHistoryService();

        $currentStatus = $invoice->pipeline_status;
        $filteredData = $this->filterEntityData($data, $roleName, $currentStatus);
        $canAdvance = $this->canAdvance($roleName, $currentStatus);
        $isRejected = $this->isRejected($invoice);

        $originalApproverId = $invoice->approver_id;

        // Determine if we can advance with submitted data
        $advanceNextStatus = null;
        $postAdvanceErrors = [];
        if ($canAdvance && !$isRejected) {
            $testEntity = $invoicesTable->patchEntity(clone $invoice, $filteredData);
            $postAdvanceErrors = $this->validateTransitionRequirements($testEntity, $currentStatus);
            if (empty($postAdvanceErrors)) {
                $advanceNextStatus = $this->getNextStatus($currentStatus);
            }
        }

        $original = clone $invoice;

        $saved = $invoicesTable->getConnection()->transactional(
            function () use ($invoicesTable, $historyService, &$invoice, $filteredData, $advanceNextStatus, $currentStatus, $userId, $original) {
                $invoice = $invoicesTable->patchEntity($invoice, $filteredData);

                if (!$invoicesTable->save($invoice)) {
                    return false;
                }

                $historyService->recordChanges($original, $invoice, $userId);

                if ($advanceNextStatus) {
                    $invoice->pipeline_status = $advanceNextStatus;
                    if (!$invoicesTable->save($invoice)) {
                        return false;
                    }
                    $historyService->recordStatusChange(
                        $invoice->id,
                        $currentStatus,
                        $advanceNextStatus,
                        $userId,
                    );
                }

                return true;
            },
        );

        $notificationErrors = [];
        $approvalLinkSent = false;

        if ($saved) {
            // Send approval link when approver_id is newly assigned in 'aprobacion' state
            if (
                $currentStatus === 'aprobacion'
                && !empty($invoice->approver_id)
                && $invoice->approver_id !== $originalApproverId
                && $baseUrl
            ) {
                $approvalResult = $this->trySendApprovalLink($invoice, $userId, $baseUrl);
                if ($approvalResult['success']) {
                    $approvalLinkSent = true;
                } else {
                    $notificationErrors[] = $approvalResult['error'];
                }
            }

            // Send status change notification if pipeline advanced
            if ($advanceNextStatus) {
                $notifResult = $this->trySendNotification($invoice, $currentStatus, $advanceNextStatus);
                if (!$notifResult['success']) {
                    $notificationErrors[] = $notifResult['error'];
                }
            }
        }

        return [
            'saved' => (bool)$saved,
            'advanced' => (bool)$advanceNextStatus && (bool)$saved,
            'nextStatus' => $advanceNextStatus,
            'advanceErrors' => $postAdvanceErrors,
            'notificationErrors' => $notificationErrors,
            'approvalLinkSent' => $approvalLinkSent,
        ];
    }

    /**
     * Standalone advance (without field edits). Used by the legacy advanceStatus route.
     *
     * Returns an associative array:
     *   - 'success' => bool
     *   - 'error'   => ?string
     *   - 'nextStatus' => ?string
     */
    public function advance(Invoice $invoice, string $roleName, int $userId): array
    {
        $currentStatus = $invoice->pipeline_status;

        if (!$this->canAdvance($roleName, $currentStatus)) {
            return ['success' => false, 'error' => 'No tiene permisos para avanzar esta factura.', 'nextStatus' => null];
        }

        if ($this->isRejected($invoice)) {
            return ['success' => false, 'error' => 'La factura fue rechazada. El flujo ha terminado.', 'nextStatus' => null];
        }

        $errors = $this->validateTransitionRequirements($invoice, $currentStatus);
        if (!empty($errors)) {
            return ['success' => false, 'error' => implode(' ', $errors), 'nextStatus' => null];
        }

        $nextStatus = $this->getNextStatus($currentStatus);
        if (!$nextStatus) {
            return ['success' => false, 'error' => 'Esta factura ya está en el estado final.', 'nextStatus' => null];
        }

        $invoicesTable = TableRegistry::getTableLocator()->get('Invoices');
        $invoice->pipeline_status = $nextStatus;

        if (!$invoicesTable->save($invoice)) {
            return ['success' => false, 'error' => 'No se pudo avanzar el estado.', 'nextStatus' => null];
        }

        $historyService = new InvoiceHistoryService();
        $historyService->recordStatusChange($invoice->id, $currentStatus, $nextStatus, $userId);

        $notifResult = $this->trySendNotification($invoice, $currentStatus, $nextStatus);

        return [
            'success' => true,
            'error' => null,
            'nextStatus' => $nextStatus,
            'notificationError' => $notifResult['error'],
        ];
    }

    /**
     * Build the approval link URL for an invoice.
     */
    public function generateApprovalLink(int $invoiceId, int $userId, string $baseUrl): string
    {
        $tokenService = new ApprovalTokenService();
        $token = $tokenService->generateToken('invoices', $invoiceId, $userId);

        return $baseUrl . '/approve/' . $token;
    }

    /**
     * Try to generate token and send approval link email.
     * Returns ['success' => bool, 'error' => ?string, 'url' => ?string].
     */
    public function trySendApprovalLink(Invoice $invoice, int $userId, string $baseUrl): array
    {
        try {
            if (empty($invoice->approver_id)) {
                return ['success' => false, 'error' => 'No hay aprobador asignado.', 'url' => null];
            }

            $tokenService = new ApprovalTokenService();
            $token = $tokenService->generateToken('invoices', $invoice->id, $userId);
            $approvalUrl = $baseUrl . '/approve/' . $token;

            // Ensure invoice has provider loaded for the email template
            if (!$invoice->has('provider') || empty($invoice->provider)) {
                $invoicesTable = TableRegistry::getTableLocator()->get('Invoices');
                $invoice = $invoicesTable->get($invoice->id, contain: ['Providers']);
            }

            $notificationService = new NotificationService();
            $notificationService->sendApprovalLinkNotification($invoice, $approvalUrl);

            return ['success' => true, 'error' => null, 'url' => $approvalUrl];
        } catch (Exception $e) {
            Log::error('Error enviando link de aprobación para factura #' . $invoice->id . ': ' . $e->getMessage());

            return ['success' => false, 'error' => 'No se pudo enviar el correo de aprobación: ' . $e->getMessage(), 'url' => null];
        }
    }

    /**
     * Try to send status change notification.
     * Returns ['success' => bool, 'error' => ?string].
     */
    private function trySendNotification(Invoice $invoice, string $fromStatus, string $toStatus): array
    {
        try {
            $notificationService = new NotificationService();
            $notificationService->sendStatusChangeNotification($invoice, $fromStatus, $toStatus);

            return ['success' => true, 'error' => null];
        } catch (Exception $e) {
            Log::error('Error enviando notificación de cambio de estado para factura #' . $invoice->id . ': ' . $e->getMessage());

            return ['success' => false, 'error' => 'No se pudo enviar la notificación de cambio de estado: ' . $e->getMessage()];
        }
    }
}
