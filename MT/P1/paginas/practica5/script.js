document.getElementById('agregarBtn').addEventListener('click', function() {
    // REFERENCIAS A ELEMENTOS
    const form = document.getElementById('dataForm');
    const textoInput = document.getElementById('texto');
    const emailInput = document.getElementById('email');
    const hobbiesCheckboxes = document.querySelectorAll('input[name="hobbies"]:checked');
    const estadoCivilRadios = document.querySelector('input[name="estadoCivil"]:checked');
    const fechaInput = document.getElementById('fecha');
    const colorInput = document.getElementById('color');
    const rangoInput = document.getElementById('rango');
    const tableBody = document.querySelector("#dataTable tbody");

    // VALIDACIÓN DE CAMPOS
    if (textoInput.value.trim() === '') {
        alert('Por favor, ingresa tu nombre.');
        return;
    }
    if (emailInput.value.trim() === '') {
        alert('Por favor, ingresa tu correo electrónico.');
        return;
    }
    if (hobbiesCheckboxes.length === 0) {
        alert('Por favor, selecciona al menos un hobby.');
        return;
    }
    if (!estadoCivilRadios) {
        alert('Por favor, selecciona tu estado civil.');
        return;
    }
    if (fechaInput.value === '') {
        alert('Por favor, selecciona una fecha y hora.');
        return;
    }

    // CAPTURAR VALORES
    const texto = textoInput.value;
    const email = emailInput.value;
    const fecha = fechaInput.value;
    const color = colorInput.value;
    const rango = rangoInput.value;

    // Obtener valores de checkboxes seleccionados
    const hobbies = [];
    hobbiesCheckboxes.forEach(checkbox => {
        hobbies.push(checkbox.value);
    });

    const hobbiesStr = hobbies.join(', ');

    // Obtener valor del radio seleccionado
    const estadoCivil = estadoCivilRadios.value;

    //AGREGAR DATOS A LA TABLA
    const newRow = tableBody.insertRow(); // Crea una nueva fila (<tr>)

    //Inserta las celdas (<td>) en la nueva fila
    newRow.innerHTML = `
        <td>${texto}</td>
        <td>${email}</td>
        <td>${hobbiesStr}</td>
        <td>${estadoCivil}</td>
        <td>${fecha}</td>
        <td style="background-color:${color};"></td>
        <td>${rango}</td>
    `;

    //LIMPIAR EL FORMULARIO
    form.reset();
});