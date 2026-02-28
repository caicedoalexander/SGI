<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\LeaveType> $leaveTypes
 */
$this->assign('title', 'Tipos de Permiso');
?>

<div class="sgi-page-header d-flex justify-content-between align-items-center">
    <span class="sgi-page-title">Tipos de Permiso</span>
    <div class="d-flex gap-2">
        <?= $this->element('catalog_excel_buttons') ?>
        <?php if (!empty($userPermissions['leave_types']['can_create'])): ?>
        <?= $this->Html->link(
            '<i class="bi bi-plus-lg me-1"></i>Nuevo Tipo',
            ['action' => 'add'],
            ['class' => 'btn btn-primary', 'escape' => false]
        ) ?>
        <?php endif; ?>
    </div>
</div>

<div class="card card-primary">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:120px;">Código</th>
                    <th>Nombre</th>
                    <th style="width:120px;">Remunerado</th>
                    <th>Plantilla</th>
                    <th style="width:160px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaveTypes as $leaveType): ?>
                <tr>
                    <td><code><?= h($leaveType->code) ?></code></td>
                    <td><?= h($leaveType->name) ?></td>
                    <td>
                        <?php if ($leaveType->remunerado): ?>
                            <span class="badge bg-success">Sí</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">No</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($leaveType->leave_document_template)): ?>
                            <span class="text-muted"><i class="bi bi-file-earmark-pdf me-1"></i><?= h($leaveType->leave_document_template->name) ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <?php if (!empty($userPermissions['leave_types']['can_edit'])): ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $leaveType->id],
                                ['class' => 'btn btn-sm btn-outline-dark', 'escape' => false]
                            ) ?>
                            <?php endif; ?>
                            <?php if (!empty($userPermissions['leave_types']['can_delete'])): ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $leaveType->id],
                                ['class' => 'btn btn-sm btn-outline-danger', 'escape' => false,
                                 'confirm' => '¿Eliminar este tipo de permiso?']
                            ) ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= $this->element('pagination') ?>
</div>
