<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Employee> $employees
 */
$this->assign('title', 'Empleados');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Empleados</h1>
    <?= $this->Html->link('<i class="bi bi-plus-lg me-1"></i>Nuevo Empleado', ['action' => 'add'], ['class' => 'btn btn-primary', 'escape' => false]) ?>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th><?= $this->Paginator->sort('document_number', 'Documento') ?></th>
                    <th><?= $this->Paginator->sort('last_name', 'Nombre') ?></th>
                    <th><?= $this->Paginator->sort('position_id', 'Cargo') ?></th>
                    <th><?= $this->Paginator->sort('operation_center_id', 'Centro de Operación') ?></th>
                    <th><?= $this->Paginator->sort('employee_status_id', 'Estado') ?></th>
                    <th><?= $this->Paginator->sort('active', 'Activo') ?></th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                <tr class="clickable-row" data-href="<?= $this->Url->build(['action' => 'view', $employee->id]) ?>">
                    <td><code><?= h($employee->document_type . ' ' . $employee->document_number) ?></code></td>
                    <td><?= h($employee->full_name) ?></td>
                    <td><?= $employee->has('position') ? h($employee->position->name) : '' ?></td>
                    <td><?= $employee->has('operation_center') ? h($employee->operation_center->name) : '' ?></td>
                    <td><?= $employee->has('employee_status') ? '<span class="badge bg-info">' . h($employee->employee_status->name) . '</span>' : '' ?></td>
                    <td><?= $employee->active ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                    <td class="text-end">
                        <?= $this->Html->link('<i class="bi bi-eye"></i>', ['action' => 'view', $employee->id], ['class' => 'btn btn-sm btn-outline-info', 'escape' => false, 'title' => 'Ver']) ?>
                        <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $employee->id], ['class' => 'btn btn-sm btn-outline-warning', 'escape' => false, 'title' => 'Editar']) ?>
                        <?= $this->Form->postLink('<i class="bi bi-trash"></i>', ['action' => 'delete', $employee->id], ['confirm' => '¿Está seguro de eliminar este empleado?', 'class' => 'btn btn-sm btn-outline-danger', 'escape' => false, 'title' => 'Eliminar']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted"><?= $this->Paginator->counter('Mostrando {{start}}-{{end}} de {{count}}') ?></small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?= $this->Paginator->first('«', ['class' => 'page-item', 'link' => ['class' => 'page-link']]) ?>
                <?= $this->Paginator->prev('‹', ['class' => 'page-item', 'link' => ['class' => 'page-link']]) ?>
                <?= $this->Paginator->numbers(['class' => 'page-item', 'link' => ['class' => 'page-link']]) ?>
                <?= $this->Paginator->next('›', ['class' => 'page-item', 'link' => ['class' => 'page-link']]) ?>
                <?= $this->Paginator->last('»', ['class' => 'page-item', 'link' => ['class' => 'page-link']]) ?>
            </ul>
        </nav>
    </div>
</div>
