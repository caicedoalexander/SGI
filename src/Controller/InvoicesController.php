<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\InvoiceFilterService;
use App\Service\InvoicePipelineService;

class InvoicesController extends AppController
{
    private InvoicePipelineService $pipeline;
    private InvoiceFilterService $filterService;

    public function initialize(): void
    {
        parent::initialize();
        $this->pipeline = new InvoicePipelineService();
        $this->filterService = new InvoiceFilterService();
    }

    private function _getCurrentUser(): object
    {
        return $this->Authentication->getIdentity()->getOriginalData();
    }

    private function _getRoleName(): string
    {
        return $this->_getUserRoleName($this->_getCurrentUser());
    }

    public function index()
    {
        $roleName = $this->_getRoleName();
        $visibleStatuses = $this->pipeline->getVisibleStatuses($roleName);

        $query = $this->Invoices->find()
            ->contain(['Providers', 'OperationCenters', 'ExpenseTypes', 'CostCenters', 'RegisteredByUsers']);

        if (!empty($visibleStatuses)) {
            $query->where(['Invoices.pipeline_status IN' => $visibleStatuses]);
        }

        $this->filterService->apply($query, $this->request->getQueryParams());

        $this->paginate = ['limit' => 15, 'maxLimit' => 15];
        $invoices = $this->paginate($query);

        $this->set(compact('invoices', 'visibleStatuses', 'roleName'));
        $this->set($this->_getFilterDropdowns());
    }

    public function all()
    {
        $roleName = $this->_getRoleName();

        $query = $this->Invoices->find()
            ->contain(['Providers', 'OperationCenters', 'ExpenseTypes', 'CostCenters', 'RegisteredByUsers']);

        $this->filterService->apply($query, $this->request->getQueryParams());

        $this->paginate = ['limit' => 15, 'maxLimit' => 15];
        $invoices = $this->paginate($query);
        $visibleStatuses = [];

        $this->set(compact('invoices', 'visibleStatuses', 'roleName'));
        $this->set($this->_getFilterDropdowns());
        $this->render('index');
    }

    public function view($id = null)
    {
        $invoice = $this->Invoices->get($id, contain: [
            'Providers',
            'OperationCenters',
            'ExpenseTypes',
            'CostCenters',
            'ConfirmedByUsers',
            'RegisteredByUsers',
            'ApproverUsers',
            'InvoiceHistories' => ['Users'],
            'InvoiceObservations' => [
                'Users',
                'sort' => ['InvoiceObservations.created' => 'ASC'],
            ],
        ]);

        $roleName = $this->_getRoleName();
        $isRejected = $this->pipeline->isRejected($invoice);
        $pipelineStatuses = InvoicePipelineService::STATUSES;
        $pipelineLabels = InvoicePipelineService::STATUS_LABELS;

        $this->set(compact('invoice', 'roleName', 'isRejected', 'pipelineStatuses', 'pipelineLabels'));
    }

    public function add()
    {
        $invoice = $this->Invoices->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->_getCurrentUser();
            $data = $this->request->getData();
            $data['registered_by'] = $user->id;
            $data['pipeline_status'] = $data['pipeline_status'] ?? 'registro';

            $invoice = $this->Invoices->patchEntity($invoice, $data);
            if ($this->Invoices->save($invoice)) {
                $this->Flash->success(__('La factura ha sido guardada.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la factura. Intente de nuevo.'));
        }

        $this->set(compact('invoice'));
        $this->set($this->_getFormDropdowns());
    }

    public function edit($id = null)
    {
        $invoice = $this->Invoices->get($id, contain: [
            'Providers',
            'OperationCenters',
            'InvoiceObservations' => [
                'Users',
                'sort' => ['InvoiceObservations.created' => 'ASC'],
            ],
        ]);
        $roleName = $this->_getRoleName();
        $currentStatus = $invoice->pipeline_status;

        $editableFields = $this->pipeline->getEditableFields($roleName, $currentStatus);
        $canAdvance = $this->pipeline->canAdvance($roleName, $currentStatus);
        $visibleSections = $this->pipeline->getVisibleSections($roleName, $currentStatus);
        $isRejected = $this->pipeline->isRejected($invoice);

        // Pre-compute advance errors for GET
        $advanceErrors = [];
        $nextStatus = null;
        if ($canAdvance && !$isRejected) {
            $advanceErrors = $this->pipeline->validateTransitionRequirements($invoice, $currentStatus);
            if (empty($advanceErrors)) {
                $nextStatus = $this->pipeline->getNextStatus($currentStatus);
            }
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->_getCurrentUser();
            $result = $this->pipeline->saveAndAdvance(
                $invoice,
                $this->request->getData(),
                $roleName,
                $user->id,
            );

            if ($result['saved']) {
                if ($result['advanced']) {
                    $nextLabel = InvoicePipelineService::STATUS_LABELS[$result['nextStatus']] ?? $result['nextStatus'];
                    $this->Flash->success(sprintf('Factura guardada y avanzada a: %s', $nextLabel));
                } else {
                    $this->Flash->success('La factura ha sido actualizada.');
                    foreach ($result['advanceErrors'] as $err) {
                        $this->Flash->warning($err);
                    }
                }

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('No se pudo guardar la factura. Verifique los datos e intente de nuevo.');
        }

        $pipelineStatuses = InvoicePipelineService::STATUSES;
        $pipelineLabels = InvoicePipelineService::STATUS_LABELS;

        $this->set(compact(
            'invoice', 'editableFields', 'canAdvance', 'roleName',
            'pipelineStatuses', 'pipelineLabels', 'currentStatus',
            'visibleSections', 'isRejected', 'advanceErrors', 'nextStatus',
        ));
        $this->set($this->_getFormDropdowns());
    }

    public function advanceStatus($id = null)
    {
        $this->request->allowMethod(['post']);
        $invoice = $this->Invoices->get($id);
        $user = $this->_getCurrentUser();

        $result = $this->pipeline->advance($invoice, $this->_getRoleName(), $user->id);

        if ($result['success']) {
            $nextLabel = InvoicePipelineService::STATUS_LABELS[$result['nextStatus']] ?? $result['nextStatus'];
            $this->Flash->success(sprintf('Factura avanzada a: %s', $nextLabel));

            return $this->redirect(['action' => 'index']);
        }

        $this->Flash->error($result['error']);

        return $this->redirect(['action' => 'edit', $id]);
    }

    public function generateApprovalLink($id = null)
    {
        $this->request->allowMethod(['post']);
        $invoice = $this->Invoices->get($id);
        $user = $this->_getCurrentUser();

        $scheme = $this->request->getHeaderLine('X-Forwarded-Proto') ?: $this->request->scheme();
        $baseUrl = $scheme . '://' . $this->request->host();
        $url = $this->pipeline->generateApprovalLink($invoice->id, $user->id, $baseUrl);

        $this->Flash->success('Enlace de aprobación generado (válido por 48h): ' . $url);

        return $this->redirect(['action' => 'view', $id]);
    }

    public function addObservation($id = null)
    {
        $this->request->allowMethod(['post']);
        $user = $this->_getCurrentUser();

        $observationsTable = $this->fetchTable('InvoiceObservations');
        $observation = $observationsTable->newEntity([
            'invoice_id' => $id,
            'user_id' => $user->id,
            'message' => $this->request->getData('message'),
        ]);

        if ($observationsTable->save($observation)) {
            $this->Flash->success('Observación agregada.');
        } else {
            $this->Flash->error('No se pudo agregar la observación.');
        }

        return $this->redirect(['action' => 'edit', $id]);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $invoice = $this->Invoices->get($id);
        if ($this->Invoices->delete($invoice)) {
            $this->Flash->success(__('La factura ha sido eliminada.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar la factura. Intente de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function _getFilterDropdowns(): array
    {
        return [
            'providers' => $this->Invoices->Providers->find('list', limit: 200)->all(),
            'operationCenters' => $this->Invoices->OperationCenters->find('codeList')->all(),
            'expenseTypes' => $this->Invoices->ExpenseTypes->find('list', limit: 200)->all(),
        ];
    }

    private function _getFormDropdowns(): array
    {
        $activeApproverIds = $this->fetchTable('Approvers')
            ->find()
            ->select(['user_id'])
            ->where(['active' => true]);

        return [
            'providers' => $this->Invoices->Providers->find('list', limit: 200)->all(),
            'operationCenters' => $this->Invoices->OperationCenters->find('codeList')->all(),
            'expenseTypes' => $this->Invoices->ExpenseTypes->find('list', limit: 200)->all(),
            'costCenters' => $this->Invoices->CostCenters->find('codeList')->all(),
            'approvers' => $this->Invoices->ApproverUsers
                ->find('list', limit: 200)
                ->where(['ApproverUsers.id IN' => $activeApproverIds])
                ->all(),
        ];
    }
}
