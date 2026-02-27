<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class DianCrosscheck extends Entity
{
    protected array $_accessible = [
        'uploaded_by' => true,
        'file_name' => true,
        'file_path' => true,
        'status' => true,
        'n8n_response' => true,
        'error_message' => true,
    ];
}
