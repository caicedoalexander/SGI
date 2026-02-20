<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEmployees extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('employees');

        // Datos personales
        $table->addColumn('document_type', 'string', ['limit' => 20, 'null' => false, 'default' => 'CC']);
        $table->addColumn('document_number', 'string', ['limit' => 30, 'null' => false]);
        $table->addColumn('first_name', 'string', ['limit' => 100, 'null' => false]);
        $table->addColumn('last_name', 'string', ['limit' => 100, 'null' => false]);
        $table->addColumn('birth_date', 'date', ['null' => true]);
        $table->addColumn('gender', 'string', ['limit' => 20, 'null' => true]);
        $table->addColumn('marital_status_id', 'integer', ['null' => true, 'signed' => true]);
        $table->addColumn('education_level_id', 'integer', ['null' => true, 'signed' => true]);

        // Contacto
        $table->addColumn('email', 'string', ['limit' => 150, 'null' => true]);
        $table->addColumn('phone', 'string', ['limit' => 30, 'null' => true]);
        $table->addColumn('address', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('city', 'string', ['limit' => 100, 'null' => true]);

        // Datos laborales
        $table->addColumn('employee_status_id', 'integer', ['null' => true, 'signed' => true]);
        $table->addColumn('position_id', 'integer', ['null' => true, 'signed' => true]);
        $table->addColumn('supervisor_position_id', 'integer', ['null' => true, 'signed' => true]);
        $table->addColumn('operation_center_id', 'integer', ['null' => true, 'signed' => true]);
        $table->addColumn('cost_center_id', 'integer', ['null' => true, 'signed' => true]);
        $table->addColumn('hire_date', 'date', ['null' => true]);
        $table->addColumn('termination_date', 'date', ['null' => true]);
        $table->addColumn('salary', 'decimal', ['precision' => 15, 'scale' => 2, 'null' => true]);

        // Seguridad social
        $table->addColumn('eps', 'string', ['limit' => 100, 'null' => true]);
        $table->addColumn('pension_fund', 'string', ['limit' => 100, 'null' => true]);
        $table->addColumn('arl', 'string', ['limit' => 100, 'null' => true]);
        $table->addColumn('severance_fund', 'string', ['limit' => 100, 'null' => true]);

        $table->addColumn('notes', 'text', ['null' => true]);
        $table->addColumn('active', 'boolean', ['default' => true, 'null' => false]);

        $table->addColumn('created', 'datetime', ['null' => true, 'default' => null]);
        $table->addColumn('modified', 'datetime', ['null' => true, 'default' => null]);

        $table->addIndex(['document_number'], ['unique' => true]);

        $table->addForeignKey('marital_status_id', 'marital_statuses', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
        $table->addForeignKey('education_level_id', 'education_levels', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
        $table->addForeignKey('employee_status_id', 'employee_statuses', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
        $table->addForeignKey('position_id', 'positions', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
        $table->addForeignKey('supervisor_position_id', 'positions', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
        $table->addForeignKey('operation_center_id', 'operation_centers', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
        $table->addForeignKey('cost_center_id', 'cost_centers', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);

        $table->create();
    }
}
