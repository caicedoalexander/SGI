<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddFirmaToEmployeeLeaves extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('employee_leaves');
        $table->addColumn('firma_path', 'string', [
            'limit' => 500,
            'null' => true,
            'default' => null,
            'after' => 'remunerado',
        ]);
        $table->update();
    }
}
