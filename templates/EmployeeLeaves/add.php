<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EmployeeLeave $employeeLeave
 * @var array $employees
 * @var array $leaveTypes
 * @var array $leaveTypesData
 */
$this->assign('title', 'Nueva Solicitud de Permiso');
?>

<div class="sgi-page-header d-flex justify-content-between align-items-center">
    <span class="sgi-page-title">Nueva Solicitud de Permiso</span>
    <?= $this->Html->link(
        '<i class="bi bi-arrow-left me-1"></i>Volver',
        ['action' => 'index'],
        ['class' => 'btn btn-outline-dark btn-sm', 'escape' => false]
    ) ?>
</div>

<div class="card card-primary">
    <div class="card-body p-4">
        <?= $this->Form->create($employeeLeave, ['type' => 'file']) ?>
        <input type="hidden" name="fecha_diligenciamiento" value="<?= date('Y-m-d') ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Empleado</label>
                <?= $this->Form->control('employee_id', [
                    'label' => false,
                    'options' => $employees,
                    'empty' => '-- Seleccione --',
                    'class' => 'form-select select2-enable',
                ]) ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tipo de Permiso</label>
                <?= $this->Form->control('leave_type_id', [
                    'label' => false,
                    'options' => $leaveTypes,
                    'empty' => '-- Seleccione --',
                    'class' => 'form-select',
                    'id' => 'leave-type-select',
                ]) ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha del Permiso</label>
                <input type="text" name="fecha_permiso" class="form-control flatpickr-date"
                       value="<?= h($employeeLeave->fecha_permiso?->format('Y-m-d') ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha Inicio</label>
                <input type="text" name="start_date" class="form-control flatpickr-date"
                       value="<?= h($employeeLeave->start_date?->format('Y-m-d') ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha Fin</label>
                <input type="text" name="end_date" class="form-control flatpickr-date"
                       value="<?= h($employeeLeave->end_date?->format('Y-m-d') ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Horario</label>
                <select name="horario" id="horario-select" class="form-select">
                    <option value="">-- Seleccione --</option>
                    <option value="Por horas" <?= ($employeeLeave->horario ?? '') === 'Por horas' ? 'selected' : '' ?>>Por horas</option>
                    <option value="Por días" <?= ($employeeLeave->horario ?? '') === 'Por días' ? 'selected' : '' ?>>Por días</option>
                </select>
            </div>

            <div class="col-md-4" id="hora-salida-group" style="display:none;">
                <label class="form-label">Hora Salida</label>
                <input type="time" name="hora_salida" class="form-control"
                       value="<?= h($employeeLeave->hora_salida ?? '') ?>">
            </div>
            <div class="col-md-4" id="hora-entrada-group" style="display:none;">
                <label class="form-label">Hora Entrada</label>
                <input type="time" name="hora_entrada" class="form-control"
                       value="<?= h($employeeLeave->hora_entrada ?? '') ?>">
            </div>

            <div class="col-md-4" id="cantidad-dias-group" style="display:none;">
                <label class="form-label">Cantidad de Días</label>
                <input type="number" name="cantidad_dias" class="form-control" min="1"
                       value="<?= h($employeeLeave->cantidad_dias ?? '') ?>">
            </div>

            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input type="hidden" name="remunerado" value="0">
                    <input type="checkbox" name="remunerado" value="1" class="form-check-input"
                           id="remunerado-check" <?= !empty($employeeLeave->remunerado) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="remunerado-check">Remunerado</label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Observaciones</label>
                <?= $this->Form->control('observations', [
                    'label' => false,
                    'type' => 'textarea',
                    'rows' => 3,
                    'class' => 'form-control',
                ]) ?>
            </div>

            <!-- Firma section -->
            <div class="col-12">
                <label class="form-label">Firma del Solicitante</label>
                <div class="d-flex gap-3 align-items-start">
                    <div>
                        <input type="file" name="firma_file" id="firma-file" class="form-control form-control-sm"
                               accept="image/png,image/jpeg" style="max-width:300px;">
                        <div class="form-text">O dibuje su firma abajo</div>
                    </div>
                </div>
                <div class="mt-2" style="border:1px solid var(--border-color);display:inline-block;">
                    <canvas id="firma-canvas" width="400" height="150" style="cursor:crosshair;display:block;"></canvas>
                </div>
                <input type="hidden" name="firma_base64" id="firma-base64">
                <div class="mt-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="firma-clear">
                        <i class="bi bi-eraser me-1"></i>Limpiar Firma
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 pt-3 mt-3" style="border-top:1px solid var(--border-color);">
            <button type="submit" class="btn btn-primary" id="btn-submit">
                <i class="bi bi-save me-1"></i>Crear Solicitud
            </button>
            <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>

<script>
(function() {
    // Leave types remunerado data
    var leaveTypesData = <?= json_encode($leaveTypesData) ?>;

    // Toggle fields based on horario
    var horarioSelect = document.getElementById('horario-select');
    var horaSalidaGroup = document.getElementById('hora-salida-group');
    var horaEntradaGroup = document.getElementById('hora-entrada-group');
    var cantidadDiasGroup = document.getElementById('cantidad-dias-group');

    function toggleHorarioFields() {
        var val = horarioSelect.value;
        horaSalidaGroup.style.display = val === 'Por horas' ? '' : 'none';
        horaEntradaGroup.style.display = val === 'Por horas' ? '' : 'none';
        cantidadDiasGroup.style.display = val === 'Por días' ? '' : 'none';
    }
    horarioSelect.addEventListener('change', toggleHorarioFields);
    toggleHorarioFields();

    // Control remunerado based on leave type
    var leaveTypeSelect = document.getElementById('leave-type-select');
    var remuneradoCheck = document.getElementById('remunerado-check');

    function updateRemunerado() {
        var typeId = leaveTypeSelect.value;
        if (typeId && leaveTypesData[typeId]) {
            remuneradoCheck.checked = true;
            remuneradoCheck.disabled = true;
        } else {
            remuneradoCheck.disabled = false;
        }
    }
    leaveTypeSelect.addEventListener('change', updateRemunerado);
    updateRemunerado();

    // Signature canvas
    var canvas = document.getElementById('firma-canvas');
    var ctx = canvas.getContext('2d');
    var drawing = false;
    var hasDrawn = false;

    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var clientX, clientY;
        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }
        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    }

    canvas.addEventListener('mousedown', function(e) {
        drawing = true;
        var pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    });
    canvas.addEventListener('mousemove', function(e) {
        if (!drawing) return;
        hasDrawn = true;
        var pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    });
    canvas.addEventListener('mouseup', function() { drawing = false; });
    canvas.addEventListener('mouseleave', function() { drawing = false; });

    // Touch events
    canvas.addEventListener('touchstart', function(e) {
        e.preventDefault();
        drawing = true;
        var pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    });
    canvas.addEventListener('touchmove', function(e) {
        e.preventDefault();
        if (!drawing) return;
        hasDrawn = true;
        var pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    });
    canvas.addEventListener('touchend', function() { drawing = false; });

    // Clear button
    document.getElementById('firma-clear').addEventListener('click', function() {
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        hasDrawn = false;
        document.getElementById('firma-base64').value = '';
    });

    // On form submit, serialize canvas to base64
    document.getElementById('btn-submit').closest('form').addEventListener('submit', function() {
        // Re-enable remunerado if disabled so it gets submitted
        remuneradoCheck.disabled = false;

        if (hasDrawn) {
            document.getElementById('firma-base64').value = canvas.toDataURL('image/png');
        }
    });
})();
</script>
