<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddContractFieldsToEmployees extends BaseMigration
{
    public function up(): void
    {
        $this->table('employees')
            ->addColumn('tipo_contrato', 'string', [
                'limit' => 20,
                'null' => true,
                'default' => null,
                'after' => 'salary',
            ])
            ->addColumn('organizacion_temporal_id', 'integer', [
                'null' => true,
                'default' => null,
                'after' => 'tipo_contrato',
            ])
            ->addColumn('chaleco', 'string', [
                'limit' => 20,
                'null' => true,
                'default' => null,
                'after' => 'organizacion_temporal_id',
            ])
            ->addForeignKey('organizacion_temporal_id', 'organizaciones_temporales', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('employees')
            ->dropForeignKey('organizacion_temporal_id')
            ->update();

        $this->table('employees')
            ->removeColumn('tipo_contrato')
            ->removeColumn('organizacion_temporal_id')
            ->removeColumn('chaleco')
            ->update();
    }
}
