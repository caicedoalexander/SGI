<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Exception;

class DashboardController extends AppController
{
    public function index()
    {
        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $counters = [];

        try {
            $invoicesTable = TableRegistry::getTableLocator()->get('Invoices');
            $counters['invoices'] = $invoicesTable->find()->count();
        } catch (Exception $e) {
            $counters['invoices'] = 0;
        }

        try {
            $employeesTable = TableRegistry::getTableLocator()->get('Employees');
            $counters['employees'] = $employeesTable->find()->where(['active' => true])->count();
        } catch (Exception $e) {
            $counters['employees'] = 0;
        }

        try {
            $providersTable = TableRegistry::getTableLocator()->get('Providers');
            $counters['providers'] = $providersTable->find()->where(['active' => true])->count();
        } catch (Exception $e) {
            $counters['providers'] = 0;
        }

        try {
            $usersTable = TableRegistry::getTableLocator()->get('Users');
            $counters['users'] = $usersTable->find()->where(['active' => true])->count();
        } catch (Exception $e) {
            $counters['users'] = 0;
        }

        $this->set(compact('counters'));
    }
}
