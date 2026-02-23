<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\ExcelCatalogTrait;

class OrganizacionesTemporalesController extends AppController
{
    use ExcelCatalogTrait;

    public function index()
    {
        $organizacionesTemporales = $this->paginate($this->OrganizacionesTemporales);

        $this->set(compact('organizacionesTemporales'));
    }

    public function add()
    {
        $organizacionTemporal = $this->OrganizacionesTemporales->newEmptyEntity();
        if ($this->request->is('post')) {
            $organizacionTemporal = $this->OrganizacionesTemporales->patchEntity($organizacionTemporal, $this->request->getData());
            if ($this->OrganizacionesTemporales->save($organizacionTemporal)) {
                $this->Flash->success(__('La organización temporal ha sido guardada.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la organización temporal. Intente de nuevo.'));
        }

        $this->set(compact('organizacionTemporal'));
    }

    public function edit($id = null)
    {
        $organizacionTemporal = $this->OrganizacionesTemporales->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $organizacionTemporal = $this->OrganizacionesTemporales->patchEntity($organizacionTemporal, $this->request->getData());
            if ($this->OrganizacionesTemporales->save($organizacionTemporal)) {
                $this->Flash->success(__('La organización temporal ha sido actualizada.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo actualizar la organización temporal. Intente de nuevo.'));
        }

        $this->set(compact('organizacionTemporal'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $organizacionTemporal = $this->OrganizacionesTemporales->get($id);
        if ($this->OrganizacionesTemporales->delete($organizacionTemporal)) {
            $this->Flash->success(__('La organización temporal ha sido eliminada.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar la organización temporal. Intente de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
