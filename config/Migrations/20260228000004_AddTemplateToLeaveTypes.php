<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddTemplateToLeaveTypes extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('leave_types');
        $table->addColumn('leave_document_template_id', 'integer', [
            'null' => true,
            'signed' => true,
            'default' => null,
            'after' => 'remunerado',
        ]);
        $table->addForeignKey('leave_document_template_id', 'leave_document_templates', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'CASCADE',
        ]);
        $table->update();
    }
}
