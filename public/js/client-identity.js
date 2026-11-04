(function () {
    function initialize() {
        const type = document.getElementById('tipo_documento');
        if (!type) return;
        const documentInput = document.getElementById('dniCliente');
        function update() {
            const company = type.value === 'RUC';
            document.getElementById('nombreClienteLabel').textContent = company ? 'Razón social *' : 'Nombres *';
            document.getElementById('documentoClienteLabel').textContent = company ? 'RUC (11 dígitos) *' : 'DNI (8 dígitos) *';
            documentInput.maxLength = company ? 11 : 8;
            documentInput.pattern = company ? '[0-9]{11}' : '[0-9]{8}';
            document.getElementById('direccionCliente').required = company;
            document.querySelectorAll('[data-person-field]').forEach(group => {
                group.hidden = company;
                group.querySelectorAll('input,select').forEach(field => { field.disabled = company; field.required = !company; });
            });
        }
        type.addEventListener('change', update);
        documentInput.addEventListener('input', () => { documentInput.value = documentInput.value.replace(/[^0-9]/g, ''); });
        update();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize);
    else initialize();
})();
