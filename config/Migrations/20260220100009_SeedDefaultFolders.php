<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class SeedDefaultFolders extends BaseMigration
{
    public function up(): void
    {
        $now = date('Y-m-d H:i:s');
        $rows = [
            ['code' => 'HV', 'name' => 'Hoja de Vida', 'sort_order' => 1, 'created' => $now, 'modified' => $now],
            ['code' => 'DOC_ID', 'name' => 'Documentos de Identidad', 'sort_order' => 2, 'created' => $now, 'modified' => $now],
            ['code' => 'CERT', 'name' => 'Certificados', 'sort_order' => 3, 'created' => $now, 'modified' => $now],
            ['code' => 'CONTR', 'name' => 'Contratos', 'sort_order' => 4, 'created' => $now, 'modified' => $now],
            ['code' => 'SS', 'name' => 'Seguridad Social', 'sort_order' => 5, 'created' => $now, 'modified' => $now],
            ['code' => 'OTROS', 'name' => 'Otros', 'sort_order' => 6, 'created' => $now, 'modified' => $now],
        ];

        $this->table('default_folders')->insert($rows)->saveData();
    }

    public function down(): void
    {
        $this->execute("DELETE FROM default_folders WHERE code IN ('HV','DOC_ID','CERT','CONTR','SS','OTROS')");
    }
}
