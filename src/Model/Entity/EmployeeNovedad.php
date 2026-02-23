<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class EmployeeNovedad extends Entity
{
    protected array $_accessible = [
        'employee_id' => true,
        'novedad_type' => true,
        'start_date' => true,
        'end_date' => true,
        'active' => true,
        'observations' => true,
        'created_by' => true,
    ];
}
