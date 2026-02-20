<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class EmployeeDocument extends Entity
{
    protected array $_accessible = [
        'employee_folder_id' => true,
        'name' => true,
        'file_path' => true,
        'file_size' => true,
        'mime_type' => true,
        'uploaded_by' => true,
    ];
}
