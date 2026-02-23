<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CleanUpEmployeeStatuses extends BaseMigration
{
    public function up(): void
    {
        // Ensure only "Activo" and "Retirado" statuses exist
        // First, check if they exist and create if not
        $builder = $this->getQueryBuilder('insert');

        // Use raw SQL for idempotent upserts
        $this->execute("INSERT INTO employee_statuses (id, name, code, created, modified) VALUES (1, 'Activo', 'ACT', NOW(), NOW()) ON DUPLICATE KEY UPDATE name='Activo', code='ACT'");
        $this->execute("INSERT INTO employee_statuses (id, name, code, created, modified) VALUES (2, 'Retirado', 'RET', NOW(), NOW()) ON DUPLICATE KEY UPDATE name='Retirado', code='RET'");

        // Update employees pointing to other statuses to "Activo" (1)
        $this->execute("UPDATE employees SET employee_status_id = 1 WHERE employee_status_id IS NOT NULL AND employee_status_id NOT IN (1, 2)");

        // Remove other statuses
        $this->execute("DELETE FROM employee_statuses WHERE id NOT IN (1, 2)");
    }

    public function down(): void
    {
        // Cannot reliably reverse this
    }
}
