<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class InvoiceDocument extends Entity
{
    protected array $_accessible = [
        'invoice_id' => true,
        'pipeline_status' => true,
        'document_type' => true,
        'file_path' => true,
        'file_name' => true,
        'file_size' => true,
        'mime_type' => true,
        'uploaded_by' => true,
    ];
}
