<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class RemoveRegistroFromPipeline extends BaseMigration
{
    public function up(): void
    {
        // Move any invoices still in 'registro' to 'aprobacion'
        $this->execute("UPDATE invoices SET pipeline_status = 'aprobacion' WHERE pipeline_status = 'registro'");
    }

    public function down(): void
    {
        // No reverse needed — 'registro' status is removed from the application
    }
}
