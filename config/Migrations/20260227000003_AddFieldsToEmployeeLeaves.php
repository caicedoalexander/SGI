<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddFieldsToEmployeeLeaves extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('employee_leaves');
        $table
            ->addColumn('fecha_permiso', 'date', ['null' => true, 'default' => null, 'after' => 'end_date'])
            ->addColumn('fecha_diligenciamiento', 'date', ['null' => true, 'default' => null, 'after' => 'fecha_permiso'])
            ->addColumn('horario', 'string', ['limit' => 20, 'null' => true, 'default' => null, 'after' => 'fecha_diligenciamiento'])
            ->addColumn('hora_salida', 'time', ['null' => true, 'default' => null, 'after' => 'horario'])
            ->addColumn('hora_entrada', 'time', ['null' => true, 'default' => null, 'after' => 'hora_salida'])
            ->addColumn('cantidad_dias', 'integer', ['null' => true, 'default' => null, 'after' => 'hora_entrada'])
            ->addColumn('remunerado', 'boolean', ['null' => true, 'default' => null, 'after' => 'cantidad_dias'])
            ->update();
    }
}
