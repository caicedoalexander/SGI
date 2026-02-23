<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateOrganizacionesTemporales extends BaseMigration
{
    public function up(): void
    {
        $this->table('organizaciones_temporales')
            ->addColumn('name', 'string', [
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('nit', 'string', [
                'limit' => 30,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('active', 'boolean', [
                'default' => true,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            ->create();
    }

    public function down(): void
    {
        $this->table('organizaciones_temporales')->drop()->save();
    }
}
