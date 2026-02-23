<?php
declare(strict_types=1);

namespace App\Service;

use Cake\ORM\TableRegistry;
use Laminas\Diactoros\UploadedFile;

class InvoiceDocumentService
{
    private const MAX_DOC_SIZE = 10 * 1024 * 1024; // 10 MB

    private const ALLOWED_DOC_MIMES = [
        'application/pdf',
        'image/jpeg', 'image/png', 'image/gif',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    public function uploadDocument(
        int $invoiceId,
        string $pipelineStatus,
        UploadedFile $file,
        ?int $uploadedBy,
        ?string $documentType = null,
    ): object|string {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return 'No se recibió ningún archivo válido.';
        }

        if ($file->getSize() > self::MAX_DOC_SIZE) {
            return 'El archivo excede el tamaño máximo de 10MB.';
        }

        $mimeType = $file->getClientMediaType();
        if (!in_array($mimeType, self::ALLOWED_DOC_MIMES)) {
            return 'Tipo de archivo no permitido. Use PDF, imágenes, Word o Excel.';
        }

        $uploadDir = WWW_ROOT . 'uploads' . DS . 'invoices' . DS . $invoiceId;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = $file->getClientFilename();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('inv_') . '.' . $extension;
        $filePath = $uploadDir . DS . $uniqueName;

        $file->moveTo($filePath);

        $documentsTable = TableRegistry::getTableLocator()->get('InvoiceDocuments');
        $document = $documentsTable->newEntity([
            'invoice_id' => $invoiceId,
            'pipeline_status' => $pipelineStatus,
            'document_type' => $documentType,
            'file_path' => 'uploads/invoices/' . $invoiceId . '/' . $uniqueName,
            'file_name' => $originalName,
            'file_size' => $file->getSize(),
            'mime_type' => $mimeType,
            'uploaded_by' => $uploadedBy,
        ]);

        if (!$documentsTable->save($document)) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            return 'No se pudo guardar el documento.';
        }

        return $document;
    }

    public function deleteDocument(int $documentId): bool
    {
        $documentsTable = TableRegistry::getTableLocator()->get('InvoiceDocuments');
        $document = $documentsTable->get($documentId);

        $filePath = WWW_ROOT . $document->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $documentsTable->delete($document);
    }

    public function canDeleteDocument(object $document, string $currentPipelineStatus): bool
    {
        // Can only delete documents that were uploaded in the current status
        return $document->pipeline_status === $currentPipelineStatus;
    }

    public function getDocumentsByStatus(int $invoiceId): array
    {
        $documentsTable = TableRegistry::getTableLocator()->get('InvoiceDocuments');
        $documents = $documentsTable->find()
            ->where(['invoice_id' => $invoiceId])
            ->contain(['UploadedByUsers'])
            ->order(['InvoiceDocuments.created' => 'DESC'])
            ->all();

        $grouped = [];
        foreach ($documents as $doc) {
            $grouped[$doc->pipeline_status][] = $doc;
        }

        return $grouped;
    }
}
