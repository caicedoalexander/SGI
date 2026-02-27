<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\EmployeeNovedad> $novedades
 * @var array $novedadTypes
 * @var array $employeeStatuses
 * @var string|null $typeFilter
 * @var string|null $statusFilter
 */
$this->assign('title', 'Novedades');
?>

<div class="sgi-page-header d-flex justify-content-between align-items-center">
    <span class="sgi-page-title">Novedades</span>
</div>

<div class="card card-primary mb-3">
    <div class="card-body p-3">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tipo de Novedad</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($novedadTypes as $key => $label): ?>
                    <option value="<?= h($key) ?>" <?= ($typeFilter ?? '') === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado del Empleado</label>
                <select name="employee_status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($employeeStatuses as $id => $name): ?>
                    <option value="<?= h($id) ?>" <?= ($statusFilter ?? '') == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card card-primary">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Tipo Novedad</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado Empleado</th>
                    <th>Registrado por</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($novedades as $novedad): ?>
                <tr>
                    <td>
                        <?php if ($novedad->employee): ?>
                            <?= $this->Html->link(
                                h($novedad->employee->full_name ?? '—'),
                                ['controller' => 'Employees', 'action' => 'view', $novedad->employee->id]
                            ) ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?= h($novedad->novedad_type) ?></td>
                    <td><?= $novedad->start_date?->format('d/m/Y') ?: '—' ?></td>
                    <td><?= $novedad->end_date?->format('d/m/Y') ?: '—' ?></td>
                    <td>
                        <?= h($novedad->employee->employee_status->name ?? '—') ?>
                    </td>
                    <td><?= h($novedad->created_by_user->full_name ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty(iterator_to_array($novedades))): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No hay novedades activas.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?= $this->element('pagination') ?>
</div>
