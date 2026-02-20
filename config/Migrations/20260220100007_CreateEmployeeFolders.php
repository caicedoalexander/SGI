<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEmployeeFolders extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('employee_folders');
        $table->addColumn('employee_id', 'integer', ['null' => false, 'signed' => true]);
        $table->addColumn('name', 'string', ['limit' => 150, 'null' => false]);
        $table->addColumn('parent_id', 'integer', ['null' => true, 'signed' => true]);
        $table->addColumn('created', 'datetime', ['null' => true, 'default' => null]);
        $table->addColumn('modified', 'datetime', ['null' => true, 'default' => null]);

        $table->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        $table->addForeignKey('parent_id', 'employee_folders', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);

        $table->create();
    }
}
