<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateDianCrosschecks extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('dian_crosschecks');
        $table
            ->addColumn('uploaded_by', 'integer', ['null' => false])
            ->addColumn('file_name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('file_path', 'string', ['limit' => 500, 'null' => false])
            ->addColumn('status', 'string', ['limit' => 30, 'default' => 'enviado', 'null' => false])
            ->addColumn('n8n_response', 'text', ['null' => true, 'default' => null])
            ->addColumn('error_message', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addForeignKey('uploaded_by', 'users', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}
