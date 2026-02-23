<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateInvoiceDocuments extends BaseMigration
{
    public function up(): void
    {
        $this->table('invoice_documents')
            ->addColumn('invoice_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('pipeline_status', 'string', [
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('document_type', 'string', [
                'limit' => 50,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('file_path', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('file_name', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('file_size', 'integer', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('mime_type', 'string', [
                'limit' => 100,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('uploaded_by', 'integer', [
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
            ->addForeignKey('invoice_id', 'invoices', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('uploaded_by', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->create();
    }

    public function down(): void
    {
        $this->table('invoice_documents')->drop()->save();
    }
}
