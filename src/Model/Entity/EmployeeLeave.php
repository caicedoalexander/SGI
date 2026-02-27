<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class EmployeeLeave extends Entity
{
    protected array $_accessible = [
        'employee_id' => true,
        'leave_type_id' => true,
        'start_date' => true,
        'end_date' => true,
        'fecha_permiso' => true,
        'fecha_diligenciamiento' => true,
        'horario' => true,
        'hora_salida' => true,
        'hora_entrada' => true,
        'cantidad_dias' => true,
        'remunerado' => true,
        'firma_path' => true,
        'status' => true,
        'observations' => true,
        'approved_by' => true,
        'approved_at' => true,
        'requested_by' => true,
    ];
}
