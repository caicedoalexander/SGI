<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class LeaveType extends Entity
{
    protected array $_accessible = [
        'code' => true,
        'name' => true,
        'remunerado' => true,
        'leave_document_template_id' => true,
    ];
}
