<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class LeaveDocumentTemplate extends Entity
{
    protected array $_accessible = [
        'name' => true,
        'description' => true,
        'file_path' => true,
        'mime_type' => true,
        'page_width' => true,
        'page_height' => true,
        'orientation' => true,
        'is_active' => true,
        'created_by' => true,
    ];
}
