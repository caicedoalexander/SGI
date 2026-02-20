<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEmployeeStatuses extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('employee_statuses');
        $table->addColumn('code', 'string', [
            'limit' => 20,
            'null' => true,
        ]);
        $table->addColumn('name', 'string', [
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'null' => true,
            'default' => null,
        ]);
        $table->addColumn('modified', 'datetime', [
            'null' => true,
            'default' => null,
        ]);
        $table->addIndex(['code'], ['unique' => true]);
        $table->create();
    }
}
