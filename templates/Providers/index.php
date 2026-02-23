<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Provider> $providers
 */
$this->assign('title', 'Proveedores');
?>
<div class="sgi-page-header d-flex justify-content-between align-items-center">
    <span class="sgi-page-title">Proveedores</span>
    <div class="d-flex gap-2">
        <?= $this->Html->link(
            '<i class="bi bi-download me-1"></i>Exportar',
            ['action' => 'export'],
            ['class' => 'btn btn-outline-success btn-sm', 'escape' => false]
        ) ?>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importExcelModal">
            <i class="bi bi-upload me-1"></i>Importar
        </button>
        <?= $this->Html->link('<i class="bi bi-plus-lg me-1"></i>Nuevo Proveedor', ['action' => 'add'], ['class' => 'btn btn-primary', 'escape' => false]) ?>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= $this->Form->create(null, [
                'url' => ['controller' => 'Providers', 'action' => 'import'],
                'type' => 'file',
            ]) ?>
            <div class="modal-header">
                <h5 class="modal-title">Importar Proveedores desde Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:.85rem;color:#666;">
                    El archivo debe ser .xlsx con una columna <code>nit</code> como identificador.
                    Los registros existentes se actualizarán, los nuevos se crearán.
                </p>
                <p style="font-size:.8rem;color:#999;">
                    <i class="bi bi-info-circle me-1"></i>Tip: Exporte primero para obtener la plantilla con las columnas correctas.
                </p>
                <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Importar</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th><?= $this->Paginator->sort('id', '#') ?></th>
                    <th><?= $this->Paginator->sort('nit', 'NIT') ?></th>
                    <th><?= $this->Paginator->sort('name', 'Nombre') ?></th>
                    <th><?= $this->Paginator->sort('active', 'Estado') ?></th>
                    <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($providers as $provider): ?>
                <tr>
                    <td><?= $this->Number->format($provider->id) ?></td>
                    <td><code><?= h($provider->nit) ?></code></td>
                    <td><?= h($provider->name) ?></td>
                    <td><?= $provider->active ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                    <td><?= $provider->created?->format('d/m/Y H:i') ?></td>
                    <td class="text-end">
                        <?= $this->Html->link('<i class="bi bi-eye"></i>', ['action' => 'view', $provider->id], ['class' => 'btn btn-sm btn-outline-info', 'escape' => false, 'title' => 'Ver']) ?>
                        <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $provider->id], ['class' => 'btn btn-sm btn-outline-warning', 'escape' => false, 'title' => 'Editar']) ?>
                        <?= $this->Form->postLink('<i class="bi bi-trash"></i>', ['action' => 'delete', $provider->id], ['confirm' => '¿Está seguro de eliminar este proveedor?', 'class' => 'btn btn-sm btn-outline-danger', 'escape' => false, 'title' => 'Eliminar']) ?>
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
