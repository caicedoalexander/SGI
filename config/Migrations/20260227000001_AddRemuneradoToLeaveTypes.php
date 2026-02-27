<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddRemuneradoToLeaveTypes extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('leave_types');
        $table->addColumn('remunerado', 'boolean', [
            'default' => false,
            'null' => false,
            'after' => 'name',
        ]);
        $table->update();
    }
}
