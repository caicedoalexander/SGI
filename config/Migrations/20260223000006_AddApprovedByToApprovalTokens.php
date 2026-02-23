<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddApprovedByToApprovalTokens extends BaseMigration
{
    public function up(): void
    {
        $this->table('approval_tokens')
            ->addColumn('approved_by_user_id', 'integer', [
                'null' => true,
                'default' => null,
                'after' => 'user_agent',
            ])
            ->addForeignKey('approved_by_user_id', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('approval_tokens')
            ->dropForeignKey('approved_by_user_id')
            ->update();

        $this->table('approval_tokens')
            ->removeColumn('approved_by_user_id')
            ->update();
    }
}
