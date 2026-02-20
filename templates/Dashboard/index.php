<?php
/**
 * @var \App\View\AppView $this
 * @var array $counters
 * @var array $userPermissions
 * @var object|null $currentUser
 */
$this->assign('title', 'Inicio');
$userPermissions = $userPermissions ?? [];
$counters = $counters ?? [];

$canView = function (string $module) use ($userPermissions): bool {
    return !empty($userPermissions[$module]['can_view']);
};
?>

<div class="mb-4">
    <h1 class="h3">Bienvenido<?= $currentUser ? ', ' . h($currentUser->full_name) : '' ?></h1>
    <p class="text-muted">Sistema de Gestión Interna — Compañía Operadora Portuaria Cafetera S.A.</p>
</div>

<div class="row g-4">
    <?php if ($canView('invoices')): ?>
    <div class="col-md-6 col-lg-3">
        <a href="<?= $this->Url->build(['controller' => 'Invoices', 'action' => 'index']) ?>" class="text-decoration-none">
            <div class="card shadow-sm border-start border-primary border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:#e8f0fe;flex-shrink:0;">
                        <i class="bi bi-receipt text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Facturas</div>
                        <div class="h4 mb-0"><?= $this->Number->format($counters['invoices'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($canView('employees')): ?>
    <div class="col-md-6 col-lg-3">
        <a href="<?= $this->Url->build(['controller' => 'Employees', 'action' => 'index']) ?>" class="text-decoration-none">
            <div class="card shadow-sm border-start border-success border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:#e6f4ea;flex-shrink:0;">
                        <i class="bi bi-people-fill text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Empleados Activos</div>
                        <div class="h4 mb-0"><?= $this->Number->format($counters['employees'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($canView('providers')): ?>
    <div class="col-md-6 col-lg-3">
        <a href="<?= $this->Url->build(['controller' => 'Providers', 'action' => 'index']) ?>" class="text-decoration-none">
            <div class="card shadow-sm border-start border-warning border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:#fef7e0;flex-shrink:0;">
                        <i class="bi bi-truck text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Proveedores</div>
                        <div class="h4 mb-0"><?= $this->Number->format($counters['providers'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($canView('users')): ?>
    <div class="col-md-6 col-lg-3">
        <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'index']) ?>" class="text-decoration-none">
            <div class="card shadow-sm border-start border-info border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:#e0f7fa;flex-shrink:0;">
                        <i class="bi bi-person-gear text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Usuarios</div>
                        <div class="h4 mb-0"><?= $this->Number->format($counters['users'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Accesos rápidos -->
<div class="mt-5">
    <h5 class="text-muted mb-3">Accesos Rápidos</h5>
    <div class="row g-3">
        <?php
        $quickLinks = [
            ['module' => 'invoices', 'label' => 'Facturas', 'icon' => 'bi-receipt', 'controller' => 'Invoices', 'color' => 'primary'],
            ['module' => 'employees', 'label' => 'Empleados', 'icon' => 'bi-people-fill', 'controller' => 'Employees', 'color' => 'success'],
            ['module' => 'providers', 'label' => 'Proveedores', 'icon' => 'bi-truck', 'controller' => 'Providers', 'color' => 'warning'],
            ['module' => 'approvers', 'label' => 'Aprobadores', 'icon' => 'bi-person-check', 'controller' => 'Approvers', 'color' => 'secondary'],
            ['module' => 'operation_centers', 'label' => 'Centros de Operación', 'icon' => 'bi-geo-alt', 'controller' => 'OperationCenters', 'color' => 'dark'],
            ['module' => 'expense_types', 'label' => 'Tipos de Gasto', 'icon' => 'bi-tags', 'controller' => 'ExpenseTypes', 'color' => 'dark'],
            ['module' => 'cost_centers', 'label' => 'Centros de Costos', 'icon' => 'bi-diagram-3', 'controller' => 'CostCenters', 'color' => 'dark'],
            ['module' => 'positions', 'label' => 'Cargos', 'icon' => 'bi-briefcase', 'controller' => 'Positions', 'color' => 'dark'],
            ['module' => 'users', 'label' => 'Usuarios', 'icon' => 'bi-people', 'controller' => 'Users', 'color' => 'info'],
            ['module' => 'roles', 'label' => 'Roles', 'icon' => 'bi-shield-lock', 'controller' => 'Roles', 'color' => 'info'],
        ];
        foreach ($quickLinks as $link):
            if (!$canView($link['module'])) continue;
        ?>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="<?= $this->Url->build(['controller' => $link['controller'], 'action' => 'index']) ?>" class="text-decoration-none">
                <div class="card text-center py-3 h-100 shadow-sm">
                    <i class="bi <?= $link['icon'] ?> fs-3 text-<?= $link['color'] ?>"></i>
                    <div class="small mt-1"><?= $link['label'] ?></div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
