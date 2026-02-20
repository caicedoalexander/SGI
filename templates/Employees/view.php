<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Employee $employee
 * @var iterable $folders
 */
$this->assign('title', 'Empleado: ' . $employee->full_name);
?>
<div class="mb-4 d-flex justify-content-between">
    <?= $this->Html->link('<i class="bi bi-arrow-left me-1"></i>Volver', ['action' => 'index'], ['class' => 'btn btn-outline-secondary btn-sm', 'escape' => false]) ?>
    <div>
        <?= $this->Html->link('<i class="bi bi-pencil me-1"></i>Editar', ['action' => 'edit', $employee->id], ['class' => 'btn btn-warning btn-sm', 'escape' => false]) ?>
        <?= $this->Form->postLink('<i class="bi bi-trash me-1"></i>Eliminar', ['action' => 'delete', $employee->id], ['confirm' => '¿Está seguro de eliminar este empleado y todos sus documentos?', 'class' => 'btn btn-danger btn-sm', 'escape' => false]) ?>
    </div>
</div>

<!-- Datos Personales -->
<div class="card shadow-sm mb-4">
    <div class="card-header"><h5 class="mb-0"><i class="bi bi-person me-2"></i>Datos Personales</h5></div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Documento</dt>
            <dd class="col-sm-3"><code><?= h($employee->document_type . ' ' . $employee->document_number) ?></code></dd>
            <dt class="col-sm-3">Nombre Completo</dt>
            <dd class="col-sm-3"><?= h($employee->full_name) ?></dd>

            <dt class="col-sm-3">Fecha Nacimiento</dt>
            <dd class="col-sm-3"><?= $employee->birth_date ? $employee->birth_date->format('d/m/Y') : '-' ?></dd>
            <dt class="col-sm-3">Género</dt>
            <dd class="col-sm-3"><?= h($employee->gender ?: '-') ?></dd>

            <dt class="col-sm-3">Estado Civil</dt>
            <dd class="col-sm-3"><?= $employee->has('marital_status') ? h($employee->marital_status->name) : '-' ?></dd>
            <dt class="col-sm-3">Nivel Educativo</dt>
            <dd class="col-sm-3"><?= $employee->has('education_level') ? h($employee->education_level->name) : '-' ?></dd>
        </dl>
    </div>
</div>

<!-- Contacto -->
<div class="card shadow-sm mb-4">
    <div class="card-header"><h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contacto</h5></div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Correo Electrónico</dt>
            <dd class="col-sm-3"><?= h($employee->email ?: '-') ?></dd>
            <dt class="col-sm-3">Teléfono</dt>
            <dd class="col-sm-3"><?= h($employee->phone ?: '-') ?></dd>

            <dt class="col-sm-3">Ciudad</dt>
            <dd class="col-sm-3"><?= h($employee->city ?: '-') ?></dd>
            <dt class="col-sm-3">Dirección</dt>
            <dd class="col-sm-3"><?= h($employee->address ?: '-') ?></dd>
        </dl>
    </div>
</div>

<!-- Datos Laborales -->
<div class="card shadow-sm mb-4">
    <div class="card-header"><h5 class="mb-0"><i class="bi bi-briefcase me-2"></i>Datos Laborales</h5></div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Estado</dt>
            <dd class="col-sm-3"><?= $employee->has('employee_status') ? '<span class="badge bg-info">' . h($employee->employee_status->name) . '</span>' : '-' ?></dd>
            <dt class="col-sm-3">Activo</dt>
            <dd class="col-sm-3"><?= $employee->active ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></dd>

            <dt class="col-sm-3">Cargo</dt>
            <dd class="col-sm-3"><?= $employee->has('position') ? h($employee->position->name) : '-' ?></dd>
            <dt class="col-sm-3">Cargo Jefe Inmediato</dt>
            <dd class="col-sm-3"><?= $employee->has('supervisor_position') ? h($employee->supervisor_position->name) : '-' ?></dd>

            <dt class="col-sm-3">Centro de Operación</dt>
            <dd class="col-sm-3"><?= $employee->has('operation_center') ? h($employee->operation_center->name) : '-' ?></dd>
            <dt class="col-sm-3">Centro de Costos</dt>
            <dd class="col-sm-3"><?= $employee->has('cost_center') ? h($employee->cost_center->name) : '-' ?></dd>

            <dt class="col-sm-3">Fecha Ingreso</dt>
            <dd class="col-sm-3"><?= $employee->hire_date ? $employee->hire_date->format('d/m/Y') : '-' ?></dd>
            <dt class="col-sm-3">Fecha Retiro</dt>
            <dd class="col-sm-3"><?= $employee->termination_date ? $employee->termination_date->format('d/m/Y') : '-' ?></dd>

            <dt class="col-sm-3">Salario</dt>
            <dd class="col-sm-3"><?= $employee->salary ? '$ ' . $this->Number->format($employee->salary, ['places' => 0]) : '-' ?></dd>
        </dl>
    </div>
</div>

<!-- Seguridad Social -->
<div class="card shadow-sm mb-4">
    <div class="card-header"><h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Seguridad Social</h5></div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">EPS</dt>
            <dd class="col-sm-3"><?= h($employee->eps ?: '-') ?></dd>
            <dt class="col-sm-3">Fondo de Pensiones</dt>
            <dd class="col-sm-3"><?= h($employee->pension_fund ?: '-') ?></dd>

            <dt class="col-sm-3">ARL</dt>
            <dd class="col-sm-3"><?= h($employee->arl ?: '-') ?></dd>
            <dt class="col-sm-3">Fondo de Cesantías</dt>
            <dd class="col-sm-3"><?= h($employee->severance_fund ?: '-') ?></dd>
        </dl>
    </div>
</div>

<?php if ($employee->notes): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header"><h5 class="mb-0">Observaciones</h5></div>
    <div class="card-body">
        <p class="mb-0"><?= nl2br(h($employee->notes)) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Gestión Documental -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-folder me-2"></i>Gestión Documental</h5>
        <div>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newFolderModal">
                <i class="bi bi-folder-plus me-1"></i>Nueva Carpeta
            </button>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                <i class="bi bi-upload me-1"></i>Subir Documento
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if ($folders->isEmpty()): ?>
            <p class="text-muted mb-0">No hay carpetas ni documentos.</p>
        <?php else: ?>
            <div class="accordion" id="foldersAccordion">
                <?php foreach ($folders as $i => $folder): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#folder-<?= $folder->id ?>">
                            <i class="bi bi-folder-fill text-warning me-2"></i>
                            <?= h($folder->name) ?>
                            <span class="badge bg-secondary ms-2"><?= count($folder->employee_documents) ?></span>
                        </button>
                    </h2>
                    <div id="folder-<?= $folder->id ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#foldersAccordion">
                        <div class="accordion-body p-0">
                            <?php if (!empty($folder->employee_documents)): ?>
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Tamaño</th>
                                        <th>Subido por</th>
                                        <th>Fecha</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($folder->employee_documents as $doc): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-file-earmark me-1"></i>
                                            <?= $this->Html->link(h($doc->name), '/' . $doc->file_path, ['target' => '_blank']) ?>
                                        </td>
                                        <td><small class="text-muted"><?= h($doc->mime_type) ?></small></td>
                                        <td><small><?= $doc->file_size ? $this->Number->toReadableSize($doc->file_size) : '-' ?></small></td>
                                        <td><small><?= $doc->has('uploaded_by_user') ? h($doc->uploaded_by_user->full_name) : '-' ?></small></td>
                                        <td><small><?= $doc->created?->format('d/m/Y H:i') ?></small></td>
                                        <td class="text-end">
                                            <?= $this->Html->link('<i class="bi bi-download"></i>', '/' . $doc->file_path, ['class' => 'btn btn-sm btn-outline-info', 'escape' => false, 'target' => '_blank', 'title' => 'Descargar']) ?>
                                            <?= $this->Form->postLink('<i class="bi bi-trash"></i>', ['action' => 'deleteDocument', $employee->id, $doc->id], ['confirm' => '¿Eliminar este documento?', 'class' => 'btn btn-sm btn-outline-danger', 'escape' => false, 'title' => 'Eliminar']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <p class="text-muted p-3 mb-0">Carpeta vacía.</p>
                            <?php endif; ?>

                            <?php if (!empty($folder->child_folders)): ?>
                                <?php foreach ($folder->child_folders as $subfolder): ?>
                                <div class="border-top p-3">
                                    <h6 class="mb-2"><i class="bi bi-folder text-warning me-1"></i><?= h($subfolder->name) ?></h6>
                                    <?php if (!empty($subfolder->employee_documents)): ?>
                                    <table class="table table-sm table-hover mb-0">
                                        <tbody>
                                            <?php foreach ($subfolder->employee_documents as $doc): ?>
                                            <tr>
                                                <td>
                                                    <i class="bi bi-file-earmark me-1"></i>
                                                    <?= $this->Html->link(h($doc->name), '/' . $doc->file_path, ['target' => '_blank']) ?>
                                                </td>
                                                <td><small class="text-muted"><?= h($doc->mime_type) ?></small></td>
                                                <td><small><?= $doc->file_size ? $this->Number->toReadableSize($doc->file_size) : '-' ?></small></td>
                                                <td><small><?= $doc->has('uploaded_by_user') ? h($doc->uploaded_by_user->full_name) : '-' ?></small></td>
                                                <td><small><?= $doc->created?->format('d/m/Y H:i') ?></small></td>
                                                <td class="text-end">
                                                    <?= $this->Html->link('<i class="bi bi-download"></i>', '/' . $doc->file_path, ['class' => 'btn btn-sm btn-outline-info', 'escape' => false, 'target' => '_blank', 'title' => 'Descargar']) ?>
                                                    <?= $this->Form->postLink('<i class="bi bi-trash"></i>', ['action' => 'deleteDocument', $employee->id, $doc->id], ['confirm' => '¿Eliminar este documento?', 'class' => 'btn btn-sm btn-outline-danger', 'escape' => false, 'title' => 'Eliminar']) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php else: ?>
                                    <p class="text-muted small mb-0">Subcarpeta vacía.</p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Nueva Carpeta -->
<div class="modal fade" id="newFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= $this->Form->create(null, ['url' => ['action' => 'addFolder', $employee->id]]) ?>
            <div class="modal-header">
                <h5 class="modal-title">Nueva Carpeta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <?= $this->Form->control('name', ['class' => 'form-control', 'label' => ['text' => 'Nombre de la Carpeta', 'class' => 'form-label'], 'required' => true]) ?>
                </div>
                <div class="mb-3">
                    <?php
                    $folderOptions = [];
                    foreach ($folders as $f) {
                        $folderOptions[$f->id] = $f->name;
                    }
                    ?>
                    <?= $this->Form->control('parent_id', [
                        'class' => 'form-select',
                        'label' => ['text' => 'Carpeta Padre (opcional)', 'class' => 'form-label'],
                        'options' => $folderOptions,
                        'empty' => '-- Raíz --',
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-folder-plus me-1"></i>Crear Carpeta</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<!-- Modal: Subir Documento -->
<div class="modal fade" id="uploadDocModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= $this->Form->create(null, ['url' => ['action' => 'uploadDocument', $employee->id], 'type' => 'file']) ?>
            <div class="modal-header">
                <h5 class="modal-title">Subir Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <?php
                    $allFolderOptions = [];
                    foreach ($folders as $f) {
                        $allFolderOptions[$f->id] = $f->name;
                        if (!empty($f->child_folders)) {
                            foreach ($f->child_folders as $cf) {
                                $allFolderOptions[$cf->id] = '— ' . $cf->name;
                            }
                        }
                    }
                    ?>
                    <?= $this->Form->control('employee_folder_id', [
                        'class' => 'form-select',
                        'label' => ['text' => 'Carpeta de Destino', 'class' => 'form-label'],
                        'options' => $allFolderOptions,
                        'required' => true,
                        'id' => 'upload-folder-select',
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $this->Form->control('file', [
                        'type' => 'file',
                        'class' => 'form-control',
                        'label' => ['text' => 'Archivo', 'class' => 'form-label'],
                        'required' => true,
                        'accept' => '.pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.txt',
                    ]) ?>
                    <div class="form-text">Máximo 10MB. PDF, imágenes, Word, Excel o texto.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Subir</button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
