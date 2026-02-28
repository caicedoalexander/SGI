<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class LeaveTemplateField extends Entity
{
    protected array $_accessible = [
        'leave_document_template_id' => true,
        'field_key' => true,
        'label' => true,
        'x' => true,
        'y' => true,
        'width' => true,
        'height' => true,
        'font_size' => true,
        'font_style' => true,
        'alignment' => true,
        'field_type' => true,
        'format' => true,
        'sort_order' => true,
    ];
}
