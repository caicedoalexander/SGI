<?php
declare(strict_types=1);

namespace App\Service;

use Cake\ORM\Query\SelectQuery;

class InvoiceFilterService
{
    /**
     * Apply search and filter parameters to an invoices query.
     *
     * @param SelectQuery $query Base query (already contains associations).
     * @param array<string,mixed> $params Query-string parameters.
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, array $params): SelectQuery
    {
        $this->applySearch($query, $params['search'] ?? null);
        $this->applyExact($query, 'Invoices.provider_id', $params['provider_id'] ?? null);
        $this->applyExact($query, 'Invoices.operation_center_id', $params['operation_center_id'] ?? null);
        $this->applyExact($query, 'Invoices.expense_type_id', $params['expense_type_id'] ?? null);
        $this->applyExact($query, 'Invoices.pipeline_status', $params['pipeline_status'] ?? null);
        $this->applyDateRange($query, $params['date_from'] ?? null, $params['date_to'] ?? null);

        return $query;
    }

    private function applySearch(SelectQuery $query, mixed $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        $like = '%' . $search . '%';
        $query->where([
            'OR' => [
                'Invoices.invoice_number LIKE' => $like,
                'Invoices.purchase_order LIKE' => $like,
                'Invoices.detail LIKE' => $like,
                'Providers.name LIKE' => $like,
            ],
        ]);
    }

    private function applyExact(SelectQuery $query, string $field, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $query->where([$field => $value]);
    }

    private function applyDateRange(SelectQuery $query, mixed $from, mixed $to): void
    {
        if ($from !== null && $from !== '') {
            $query->where(['Invoices.issue_date >=' => $from]);
        }

        if ($to !== null && $to !== '') {
            $query->where(['Invoices.issue_date <=' => $to]);
        }
    }
}
