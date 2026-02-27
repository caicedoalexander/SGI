<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\EmployeeNovedadesTable;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;

class EmployeeNovedadesController extends AppController
{
    /**
     * List all active novedades.
     *
     * @return void
     */
    public function index(): void
    {
        $query = $this->EmployeeNovedades->find()
            ->contain(['Employees' => ['EmployeeStatuses'], 'CreatedByUsers'])
            ->where(['EmployeeNovedades.active' => true])
            ->order(['EmployeeNovedades.created' => 'DESC']);

        // Filter by novedad type
        $typeFilter = $this->request->getQuery('type');
        if ($typeFilter) {
            $query->where(['EmployeeNovedades.novedad_type' => $typeFilter]);
        }

        // Filter by employee status
        $statusFilter = $this->request->getQuery('employee_status');
        if ($statusFilter) {
            $query->where(['Employees.employee_status_id' => $statusFilter]);
        }

        $this->paginate = ['limit' => 15, 'maxLimit' => 15];
        $novedades = $this->paginate($query);

        $novedadTypes = array_combine(
            EmployeeNovedadesTable::NOVEDAD_TYPES,
            EmployeeNovedadesTable::NOVEDAD_TYPES,
        );

        $employeeStatuses = TableRegistry::getTableLocator()->get('EmployeeStatuses')
            ->find('list')
            ->all();

        $this->set(compact('novedades', 'novedadTypes', 'employeeStatuses', 'typeFilter', 'statusFilter'));
    }

    public function add($employeeId = null)
    {
        $employeesTable = TableRegistry::getTableLocator()->get('Employees');
        $employee = $employeesTable->get($employeeId, contain: ['EmployeeStatuses']);

        // Don't allow novedades for retired employees
        if ($employee->employee_status_id === 2) {
            $this->Flash->error(__('No se pueden agregar novedades a un empleado retirado.'));

            return $this->redirect(['controller' => 'Employees', 'action' => 'view', $employeeId]);
        }

        $novedad = $this->EmployeeNovedades->newEmptyEntity();

        if ($this->request->is('post')) {
            // Deactivate current active novedad if exists
            $this->EmployeeNovedades->updateAll(
                ['active' => false, 'end_date' => Date::now()],
                ['employee_id' => $employeeId, 'active' => true],
            );

            $data = $this->request->getData();
            $data['employee_id'] = $employeeId;
            $data['active'] = true;

            $identity = $this->Authentication->getIdentity();
            $data['created_by'] = $identity ? (int)$identity->getIdentifier() : null;

            $novedad = $this->EmployeeNovedades->patchEntity($novedad, $data);
            if ($this->EmployeeNovedades->save($novedad)) {
                $this->Flash->success(__('La novedad ha sido registrada.'));

                return $this->redirect(['controller' => 'Employees', 'action' => 'view', $employeeId]);
            }
            $this->Flash->error(__('No se pudo registrar la novedad. Intente de nuevo.'));
        }

        $novedadTypes = array_combine(
            EmployeeNovedadesTable::NOVEDAD_TYPES,
            EmployeeNovedadesTable::NOVEDAD_TYPES,
        );

        $this->set(compact('novedad', 'employee', 'novedadTypes'));
    }

    public function deactivate($id = null)
    {
        $this->request->allowMethod(['post']);
        $novedad = $this->EmployeeNovedades->get($id);

        $novedad->active = false;
        $novedad->end_date = Date::now();

        if ($this->EmployeeNovedades->save($novedad)) {
            $this->Flash->success(__('La novedad ha sido desactivada.'));
        } else {
            $this->Flash->error(__('No se pudo desactivar la novedad.'));
        }

        return $this->redirect(['controller' => 'Employees', 'action' => 'view', $novedad->employee_id]);
    }
}
