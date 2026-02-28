<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateLeaveTemplateFields extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('leave_template_fields');

        $table->addColumn('leave_document_template_id', 'integer', ['null' => false, 'signed' => true]);
        $table->addColumn('field_key', 'string', ['limit' => 100, 'null' => false]);
        $table->addColumn('label', 'string', ['limit' => 255, 'null' => true]);
        $table->addColumn('x', 'decimal', ['precision' => 8, 'scale' => 2, 'null' => false]);
        $table->addColumn('y', 'decimal', ['precision' => 8, 'scale' => 2, 'null' => false]);
        $table->addColumn('width', 'decimal', ['precision' => 8, 'scale' => 2, 'null' => true]);
        $table->addColumn('height', 'decimal', ['precision' => 8, 'scale' => 2, 'null' => true]);
        $table->addColumn('font_size', 'integer', ['default' => 10, 'null' => false]);
        $table->addColumn('font_style', 'string', ['limit' => 10, 'default' => '', 'null' => false]);
        $table->addColumn('alignment', 'string', ['limit' => 1, 'default' => 'L', 'null' => false]);
        $table->addColumn('field_type', 'string', ['limit' => 20, 'default' => 'text', 'null' => false]);
        $table->addColumn('format', 'string', ['limit' => 50, 'null' => true]);
        $table->addColumn('sort_order', 'integer', ['default' => 0, 'null' => false]);

        $table->addColumn('created', 'datetime', ['null' => true, 'default' => null]);
        $table->addColumn('modified', 'datetime', ['null' => true, 'default' => null]);

        $table->addForeignKey('leave_document_template_id', 'leave_document_templates', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ]);

        $table->create();
    }
}
