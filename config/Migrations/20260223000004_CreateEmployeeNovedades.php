<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEmployeeNovedades extends BaseMigration
{
    public function up(): void
    {
        $this->table('employee_novedades')
            ->addColumn('employee_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('novedad_type', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('start_date', 'date', [
                'null' => false,
            ])
            ->addColumn('end_date', 'date', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('active', 'boolean', [
                'default' => true,
                'null' => false,
            ])
            ->addColumn('observations', 'text', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('created_by', 'integer', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('created', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            ->addIndex(['employee_id', 'active'], ['name' => 'idx_novedades_employee_active'])
            ->addForeignKey('employee_id', 'employees', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('created_by', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->create();
    }

    public function down(): void
    {
        $this->table('employee_novedades')->drop()->save();
    }
}
