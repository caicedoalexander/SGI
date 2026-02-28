<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\ExcelCatalogTrait;
use Cake\ORM\TableRegistry;

class LeaveTypesController extends AppController
{
    use ExcelCatalogTrait;

    public function index()
    {
        $query = $this->LeaveTypes->find()
            ->contain(['LeaveDocumentTemplates']);
        $leaveTypes = $this->paginate($query);
        $this->set(compact('leaveTypes'));
    }

    public function add()
    {
        $leaveType = $this->LeaveTypes->newEmptyEntity();
        if ($this->request->is('post')) {
            $leaveType = $this->LeaveTypes->patchEntity($leaveType, $this->request->getData());
            if ($this->LeaveTypes->save($leaveType)) {
                $this->Flash->success(__('El tipo de permiso ha sido guardado.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar. Intente de nuevo.'));
        }
        $documentTemplates = $this->_getDocumentTemplatesList();
        $this->set(compact('leaveType', 'documentTemplates'));
    }

    public function edit($id = null)
    {
        $leaveType = $this->LeaveTypes->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $leaveType = $this->LeaveTypes->patchEntity($leaveType, $this->request->getData());
            if ($this->LeaveTypes->save($leaveType)) {
                $this->Flash->success(__('El tipo de permiso ha sido actualizado.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo actualizar. Intente de nuevo.'));
        }
        $documentTemplates = $this->_getDocumentTemplatesList();
        $this->set(compact('leaveType', 'documentTemplates'));
    }

    private function _getDocumentTemplatesList(): array
    {
        $templatesTable = TableRegistry::getTableLocator()->get('LeaveDocumentTemplates');

        return $templatesTable->find('list', valueField: 'name')
            ->where(['is_active' => true])
            ->order(['name' => 'ASC'])
            ->toArray();
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $leaveType = $this->LeaveTypes->get($id);
        if ($this->LeaveTypes->delete($leaveType)) {
            $this->Flash->success(__('El tipo de permiso ha sido eliminado.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar. Intente de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
