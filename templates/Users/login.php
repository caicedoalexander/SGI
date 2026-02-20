<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Iniciar Sesión');
?>
<div class="card shadow-lg border-0 rounded-3">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center mb-2 gap-2">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:54px;height:54px;background-color:#469D61;">
                    <i class="bi bi-building text-white fs-2"></i>
                </div>
                <h1 class="fw-bold mb-1">SGI</h1>
            </div>
            <p class="text-muted fw-semibold">Sistema de Gestión Interna</p>
        </div>

        <?= $this->Form->create(null, ['url' => ['action' => 'login']]) ?>

        <div class="mb-3">
            <label for="username" class="form-label fw-semibold">Usuario</label>
            <div class="input-group border rounded">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-person"></i>
                </span>
                <?= $this->Form->control('username', [
                    'label' => false,
                    'class' => 'form-control border-0 ps-2 shadow-none',
                    'id' => 'username',
                    'placeholder' => 'Ingrese su usuario',
                    'autofocus' => true,
                    'templates' => ['inputContainer' => '{{content}}'],
                ]) ?>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label fw-semibold">Contraseña</label>
            <div class="input-group border rounded">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-lock"></i>
                </span>
                <?= $this->Form->control('password', [
                    'label' => false,
                    'type' => 'password',
                    'class' => 'form-control border-0 ps-2 shadow-none bg-success bg-opacity-10',
                    'id' => 'password',
                    'placeholder' => '••••••••',
                    'templates' => ['inputContainer' => '{{content}}'],
                ]) ?>
            </div>
        </div>

        <?= $this->Form->button('Ingresar', [
            'class' => 'btn btn-success w-100 py-2 fw-semibold',
        ]) ?>

        <?= $this->Form->end() ?>
    </div>
</div>
<p class="text-center text-white-50 fw-light mt-3">
    Compañía Operadora Portuaria Cafetera S.A.
</p>
