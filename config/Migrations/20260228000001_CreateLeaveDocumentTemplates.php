<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateLeaveDocumentTemplates extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('leave_document_templates');

        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false]);
        $table->addColumn('description', 'text', ['null' => true]);
        $table->addColumn('file_path', 'string', ['limit' => 500, 'null' => false]);
        $table->addColumn('mime_type', 'string', ['limit' => 100, 'null' => false]);
        $table->addColumn('page_width', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 215.9]);
        $table->addColumn('page_height', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 279.4]);
        $table->addColumn('is_active', 'boolean', ['default' => true, 'null' => false]);
        $table->addColumn('created_by', 'integer', ['null' => true, 'signed' => true]);

        $table->addColumn('created', 'datetime', ['null' => true, 'default' => null]);
        $table->addColumn('modified', 'datetime', ['null' => true, 'default' => null]);

        $table->addForeignKey('created_by', 'users', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);

        $table->create();
    }
}
