<?php
declare(strict_types=1);

namespace App\Service;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Http\Message\UploadedFileInterface;

class ExcelService
{
    /**
     * Export a catalog query to an XLSX file.
     * Returns the file path of the generated file.
     */
    public function exportCatalog(string $tableName, SelectQuery $query): string
    {
        $results = $query->all()->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($tableName);

        if (empty($results)) {
            $sheet->setCellValue('A1', 'Sin datos');
        } else {
            // Headers from first row keys
            $first = $results[0];
            $firstRow = method_exists($first, 'toArray') ? $first->toArray() : (array)$first;
            $headers = array_keys($firstRow);
            foreach ($headers as $col => $header) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
                $sheet->setCellValue($cell, $header);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            // Data rows
            foreach ($results as $rowNum => $entity) {
                $row = method_exists($entity, 'toArray') ? $entity->toArray() : (array)$entity;
                foreach ($headers as $col => $header) {
                    $value = $row[$header] ?? '';
                    if ($value instanceof \DateTimeInterface) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                    $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . ($rowNum + 2);
                    $sheet->setCellValue($cell, $value);
                }
            }

            // Auto-size columns
            foreach ($headers as $col => $header) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'sgi_export_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return $tempFile;
    }

    /**
     * Import a catalog from an XLSX file.
     * Uses $keyField as unique identifier for upsert (default: 'code').
     *
     * @param string $tableName ORM table alias
     * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded XLSX
     * @param string $keyField Column used as unique key for upsert
     * @param array<string> $skipFields Extra fields to skip on import
     */
    public function importCatalog(
        string $tableName,
        UploadedFileInterface $file,
        string $keyField = 'code',
        array $skipFields = [],
    ): ImportResult {
        $result = new ImportResult();

        // Save uploaded file temporarily
        $tempFile = tempnam(sys_get_temp_dir(), 'sgi_import_');
        $file->moveTo($tempFile);

        try {
            $spreadsheet = IOFactory::load($tempFile);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Exception $e) {
            $result->errors[] = 'No se pudo leer el archivo: ' . $e->getMessage();
            return $result;
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }

        if (count($rows) < 2) {
            $result->errors[] = 'El archivo está vacío o solo tiene encabezados.';
            return $result;
        }

        $headers = array_map('trim', $rows[0]);
        $keyIndex = array_search($keyField, $headers);

        if ($keyIndex === false) {
            $result->errors[] = "El archivo debe contener una columna \"{$keyField}\".";
            return $result;
        }

        $table = TableRegistry::getTableLocator()->get($tableName);

        // Skip system fields + any extra fields
        $defaultSkip = ['id', 'created', 'modified'];
        $allSkip = array_merge($defaultSkip, $skipFields);

        for ($i = 1; $i < count($rows); $i++) {
            $rowData = [];
            foreach ($headers as $col => $header) {
                if (in_array($header, $allSkip)) {
                    continue;
                }
                $rowData[$header] = $rows[$i][$col] ?? null;
            }

            $keyValue = trim((string)($rowData[$keyField] ?? ''));
            if (empty($keyValue)) {
                $result->skipped++;
                continue;
            }

            // Check if exists
            $existing = $table->find()
                ->where([$keyField => $keyValue])
                ->first();

            if ($existing) {
                $entity = $table->patchEntity($existing, $rowData);
                if ($table->save($entity)) {
                    $result->updated++;
                } else {
                    $errors = $entity->getErrors();
                    $errorMsg = "Fila {$i}: ";
                    foreach ($errors as $field => $fieldErrors) {
                        $errorMsg .= "$field: " . implode(', ', $fieldErrors) . '. ';
                    }
                    $result->errors[] = $errorMsg;
                }
            } else {
                $entity = $table->newEntity($rowData);
                if ($table->save($entity)) {
                    $result->created++;
                } else {
                    $errors = $entity->getErrors();
                    $errorMsg = "Fila {$i}: ";
                    foreach ($errors as $field => $fieldErrors) {
                        $errorMsg .= "$field: " . implode(', ', $fieldErrors) . '. ';
                    }
                    $result->errors[] = $errorMsg;
                }
            }
        }

        return $result;
    }
}
