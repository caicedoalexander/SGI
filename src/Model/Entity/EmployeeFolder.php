<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class EmployeeFolder extends Entity
{
    protected array $_accessible = [
        'employee_id' => true,
        'name' => true,
        'parent_id' => true,
    ];
}
