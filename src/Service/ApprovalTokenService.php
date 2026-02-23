<?php
declare(strict_types=1);

namespace App\Service;

use Cake\ORM\TableRegistry;
use DateTime;
use Exception;

class ApprovalTokenService
{
    public function generateToken(string $entityType, int $entityId, int $createdBy, int $hoursValid = 48): string
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = new DateTime("+{$hoursValid} hours");

        $table = TableRegistry::getTableLocator()->get('ApprovalTokens');
        $entity = $table->newEntity([
            'token' => $token,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'created_by' => $createdBy,
            'expires_at' => $expiresAt,
        ]);

        $table->save($entity);

        return $token;
    }

    public function validateToken(string $token): ?object
    {
        $table = TableRegistry::getTableLocator()->get('ApprovalTokens');
        $record = $table->find()
            ->where(['token' => $token])
            ->first();

        if (!$record) {
            return null;
        }

        // Check if already used
        if ($record->used_at !== null) {
            return null;
        }

        // Check expiration
        if ($record->expires_at < new DateTime()) {
            return null;
        }

        return $record;
    }

    public function consumeToken(
        string $token,
        string $action,
        ?string $observations,
        ?string $ip,
        ?string $userAgent,
        ?string $approvalDate = null,
        ?int $approvedByUserId = null,
    ): bool {
        $table = TableRegistry::getTableLocator()->get('ApprovalTokens');
        $record = $table->find()
            ->where(['token' => $token])
            ->first();

        if (!$record || $record->used_at !== null) {
            return false;
        }

        $record->used_at = new DateTime();
        $record->action_taken = $action;
        $record->observations = $observations;
        $record->ip_address = $ip;
        $record->user_agent = $userAgent;
        $record->approved_by_user_id = $approvedByUserId;

        if (!$table->save($record)) {
            return false;
        }

        // Apply action to the entity
        return $this->applyAction($record->entity_type, $record->entity_id, $action, $observations, $record->created_by, $approvalDate);
    }

    private function applyAction(string $entityType, int $entityId, string $action, ?string $observations, ?int $createdBy, ?string $approvalDate): bool
    {
        switch ($entityType) {
            case 'invoices':
                return $this->applyInvoiceAction($entityId, $action, $observations, $createdBy, $approvalDate);
            case 'employee_leaves':
                return $this->applyLeaveAction($entityId, $action);
            default:
                return false;
        }
    }

    private function applyInvoiceAction(int $invoiceId, string $action, ?string $observations, ?int $createdBy, ?string $approvalDate): bool
    {
        $table = TableRegistry::getTableLocator()->get('Invoices');
        $invoice = $table->get($invoiceId, contain: ['Providers']);

        $historyService = new InvoiceHistoryService();
        $userId = $createdBy ?? 0;
        $parsedDate = !empty($approvalDate) ? new DateTime($approvalDate) : new DateTime();

        if ($action === 'approve') {
            $originalStatus = $invoice->pipeline_status;
            $invoice->area_approval = 'Aprobada';
            $invoice->area_approval_date = $parsedDate;

            $pipeline = new InvoicePipelineService();
            $nextStatus = $pipeline->getNextStatus($invoice->pipeline_status);
            if ($nextStatus) {
                $invoice->pipeline_status = $nextStatus;
            }

            if (!$table->save($invoice)) {
                return false;
            }

            // Record history
            $historyService->recordFieldChange($invoiceId, 'area_approval', 'Pendiente', 'Aprobada', $userId);
            if ($nextStatus) {
                $historyService->recordStatusChange($invoiceId, $originalStatus, $nextStatus, $userId);
            }

            // Save observations as invoice_observation
            if (!empty($observations)) {
                $this->saveInvoiceObservation($invoiceId, $observations, $userId);
            }

            // Send notification to Contabilidad
            if ($nextStatus) {
                try {
                    $notificationService = new NotificationService();
                    $notificationService->sendStatusChangeNotification($invoice, $originalStatus, $nextStatus);
                } catch (Exception $e) {
                    // Don't block on email failures
                }
            }

            return true;
        }

        if ($action === 'reject') {
            $invoice->area_approval = 'Rechazada';
            $invoice->area_approval_date = $parsedDate;

            if (!$table->save($invoice)) {
                return false;
            }

            $historyService->recordFieldChange($invoiceId, 'area_approval', 'Pendiente', 'Rechazada', $userId);

            if (!empty($observations)) {
                $this->saveInvoiceObservation($invoiceId, $observations, $userId);
            }

            return true;
        }

        return true;
    }

    private function saveInvoiceObservation(int $invoiceId, string $message, int $userId): void
    {
        $observationsTable = TableRegistry::getTableLocator()->get('InvoiceObservations');
        $observation = $observationsTable->newEntity([
            'invoice_id' => $invoiceId,
            'user_id' => $userId,
            'message' => $message,
        ]);
        $observationsTable->save($observation);
    }

    private function applyLeaveAction(int $leaveId, string $action): bool
    {
        $table = TableRegistry::getTableLocator()->get('EmployeeLeaves');
        $leave = $table->get($leaveId);

        if ($action === 'approve') {
            $leave->status = 'aprobado';
            $leave->approved_at = new DateTime();
        } elseif ($action === 'reject') {
            $leave->status = 'rechazado';
            $leave->approved_at = new DateTime();
        }

        return (bool)$table->save($leave);
    }

    public function getEntity(string $entityType, int $entityId): ?object
    {
        $tableMap = [
            'invoices' => 'Invoices',
            'employee_leaves' => 'EmployeeLeaves',
        ];

        $tableName = $tableMap[$entityType] ?? null;
        if (!$tableName) {
            return null;
        }

        $table = TableRegistry::getTableLocator()->get($tableName);

        try {
            $contain = [];
            if ($entityType === 'invoices') {
                $contain = ['Providers', 'InvoiceDocuments'];
            } elseif ($entityType === 'employee_leaves') {
                $contain = ['Employees', 'LeaveTypes'];
            }

            return $table->get($entityId, contain: $contain);
        } catch (Exception $e) {
            return null;
        }
    }
}
