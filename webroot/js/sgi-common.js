/**
 * SGI Common JS - Flatpickr + AutoNumeric + Row click
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Flatpickr para inputs de fecha ──────────────────────────────────────
    if (typeof flatpickr !== 'undefined') {
        flatpickr.localize(flatpickr.l10ns.es);

        document.querySelectorAll('input.flatpickr-date').forEach(function (el) {
            flatpickr(el, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'l, d F Y',
                locale: 'es',
                animate: true,
                allowInput: true,
            });
        });
    }

    // ── AutoNumeric para campo de monto COP ─────────────────────────────────
    if (typeof AutoNumeric !== 'undefined') {
        document.querySelectorAll('input.currency-input').forEach(function (el) {
            new AutoNumeric(el, {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                currencySymbol: '$ ',
                currencySymbolPlacement: 'p',
                decimalPlaces: 0,
                unformatOnSubmit: true,
                modifyValueOnUpDownArrow: false,
            });
        });
    }

    // ── Auto-dismiss flash notifications ────────────────────────────────────
    document.querySelectorAll('#sgi-flash-container .alert').forEach(function (el) {
        setTimeout(function () {
            bootstrap.Alert.getOrCreateInstance(el).close();
        }, 4000);
    });

    // ── Select2 para todos los selects del sistema ──────────────────────────
    if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
        $('select.form-select').select2({
            width: '100%',
            language: 'es',
            minimumResultsForSearch: 7,
        });
    }

    // ── Click en fila de tabla para editar ──────────────────────────────────
    document.querySelectorAll('tr.clickable-row').forEach(function (row) {
        row.addEventListener('click', function () {
            var href = this.dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
    });

});
