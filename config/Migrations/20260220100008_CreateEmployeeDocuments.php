<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateEmployeeDocuments extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('employee_documents');
        $table->addColumn('employee_folder_id', 'integer', ['null' => false, 'signed' => true]);
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false]);
        $table->addColumn('file_path', 'string', ['limit' => 500, 'null' => false]);
        $table->addColumn('file_size', 'integer', ['null' => true]);
        $table->addColumn('mime_type', 'string', ['limit' => 100, 'null' => true]);
        $table->addColumn('uploaded_by', 'integer', ['null' => true, 'signed' => true]);
        $table->addColumn('created', 'datetime', ['null' => true, 'default' => null]);
        $table->addColumn('modified', 'datetime', ['null' => true, 'default' => null]);

        $table->addForeignKey('employee_folder_id', 'employee_folders', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        $table->addForeignKey('uploaded_by', 'users', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);

        $table->create();
    }
}
