<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddOrientationToLeaveDocumentTemplates extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('leave_document_templates');
        $table->addColumn('orientation', 'string', [
            'limit' => 1,
            'default' => 'P',
            'null' => false,
            'after' => 'page_height',
        ]);
        $table->update();
    }
}
