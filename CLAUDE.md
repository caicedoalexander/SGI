# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Desarrollo
bin/cake migrations migrate          # Ejecutar migraciones pendientes
bin/cake migrations create NombreMigracion  # Crear nueva migración
bin/cake bake ...                    # Generar código scaffolding

# Tests
vendor/bin/phpunit --colors=always   # Correr todos los tests
composer test                        # Alias del anterior
composer check                       # Tests + code style check

# Code style
composer cs-check                    # PHP_CodeSniffer (estándar CakePHP)
composer cs-fix                      # Auto-fix de code style (phpcbf)
```

## Arquitectura

### Stack
- **CakePHP 5.3** + PHP 8.2+
- **MariaDB remota** en easypanel (credenciales en `.env`)
- **cakephp/authentication ^3.0** para sesiones/formulario
- **phpoffice/phpspreadsheet** para exportación Excel
- Sin Tailwind, sin SCSS — Bootstrap 5 + CSS plano (`webroot/css/styles.css`)

### Estructura clave

```
src/
  Controller/       # AppController base + 29 controllers
  Model/Entity/     # Entidades tipadas
  Model/Table/      # Tablas ORM con asociaciones y custom finders
  Service/          # Lógica de negocio (16 servicios, instanciados manualmente)
  View/AppView.php  # formatDateEs() para fechas en español
templates/
  layout/           # default.php (sidebar+topbar), login.php, ajax.php
  element/          # Componentes reutilizables (pipeline_progress.php)
config/
  routes.php        # Rutas custom además de RESTful
  Migrations/       # Migraciones con prefijo de fecha
webroot/
  css/styles.css    # Sistema de diseño completo
  js/sgi-common.js  # Clickable rows, inicialización de formularios
  fonts/            # Inter Variable (local)
```

### RBAC y permisos
- `AppController::_enforcePermission()` verifica `role + module + action` contra la tabla `permissions`
- El rol Admin bypassa todos los permisos
- `AuthorizationService` en `src/Service/` centraliza la lógica
- Constantes de rol en `src/Constants/RoleConstants.php`

### Pipeline de facturas (InvoicePipelineService)
4 estados: `aprobacion` → `contabilidad` → `tesoreria` → `pagada`
- Clase central: `src/Service/InvoicePipelineService.php`
- Cada rol edita solo campos de su estado (ver `EDITABLE_FIELDS`)
- Secciones visibles por rol: `getVisibleSections(roleName, status)`
- Avance: POST `/invoices/advance-status/{id}`
- Aprobación externa vía token (bypasea login): `/approve/{token}`
- Cuando `area_approval = 'Rechazada'` → factura está rechazada (`isRejected()`)

### Migraciones
- Base: `Migrations\BaseMigration` (NO `AbstractMigration`)
- Si una migración falla a mitad, usar `$this->hasTable()` antes de añadir FKs
- FKs requieren tipos de columna idénticos (signed/unsigned consistente)

### Custom Finders en CakePHP 5
- NO sobreescribir `findList()` (firma incompatible)
- Usar `findCodeList()` → `find('codeList')` para el patrón `"code - name"`

## Sistema de diseño

**Leer `STYLES.md` antes de modificar cualquier vista.**

Reglas fundamentales:
- Bordes en lugar de sombras (sin `box-shadow` salvo sidebar activo con `inset 2px 0 0`)
- Fuente: Inter Variable local (NO Google Fonts)
- **Orden CSS obligatorio:** Bootstrap → Bootstrap Icons → Flatpickr → `styles.css`
- Clases custom: `.sgi-stat-card`, `.sgi-quick-tile`, `.sgi-btn-primary`, `.sgi-input-group`, `.sgi-topbar`
- Fechas con clase `flatpickr-date` (Flatpickr CDN en layout default)
- Montos COP con clase `currency-input` (AutoNumeric CDN)
- Filas clickeables: `<tr class="clickable-row" data-href="url">`

## Patrones importantes

### Servicios
Todos en `src/Service/`, se instancian manualmente en los controllers. Los más críticos:
- `InvoicePipelineService` — transiciones de estado, campos editables por rol
- `InvoiceHistoryService` — audit trail campo a campo en `invoice_histories`
- `ApprovalTokenService` — tokens SHA256 para aprobación externa por email
- `InvoiceDocumentService` / `EmployeeDocumentService` — gestión de archivos

### Historial de cambios
`InvoiceHistoryService` registra cada cambio en `invoice_histories` con `field_changed`, `old_value`, `new_value`. Llamar en `InvoicesController` al guardar.

### Paginación
`public $paginate = ['limit' => 15, 'maxLimit' => 15]` en los controllers de índice.

### Formateo de fechas
`AppView::formatDateEs($date)` → "Lunes, 17 Febrero 2026" (sin extensión intl, implementación manual).
