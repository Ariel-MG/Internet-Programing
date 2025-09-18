document.addEventListener('DOMContentLoaded', function () {

    const figuraSelect = document.getElementById('figura');
    const calcularBtn = document.getElementById('calcularBtn');
    const resultadoDiv = document.getElementById('resultado');

    const inputCuadrado = document.getElementById('inputCuadrado');
    const inputTriangulo = document.getElementById('inputTriangulo');
    const inputRectangulo = document.getElementById('inputRectangulo');

    
    figuraSelect.addEventListener('change', function () {
        // Ocultar todos los grupos de inputs
        inputCuadrado.classList.add('hidden');
        inputTriangulo.classList.add('hidden');
        inputRectangulo.classList.add('hidden');
        resultadoDiv.textContent = ''; 

        // Los deocultamos pues jajaja 
        const figuraSeleccionada = this.value;
        if (figuraSeleccionada === 'cuadrado') {
            inputCuadrado.classList.remove('hidden');
        } else if (figuraSeleccionada === 'triangulo') {
            inputTriangulo.classList.remove('hidden');
        } else if (figuraSeleccionada === 'rectangulo') {
            inputRectangulo.classList.remove('hidden');
        }
    });

    // Evento calcular 
    calcularBtn.addEventListener('click', function () {
        const figura = figuraSelect.value;
        let area = 0;
        let esValido = true;

        if (figura === 'cuadrado') {
            const lado = parseFloat(document.getElementById('lado').value);
            //Validamos datos CUADRADO
            if (isNaN(lado) || lado <= 0) {
                esValido = false;
            } else {
                area = lado * lado;
            }
        } else if (figura === 'triangulo') {
            const base = parseFloat(document.getElementById('baseTriangulo').value);
            const altura = parseFloat(document.getElementById('alturaTriangulo').value);
            //Validamos datos TRIANGULO
            if (isNaN(base) || isNaN(altura) || base <= 0 || altura <= 0) {
                esValido = false;
            } else {
                area = (base * altura) / 2;
            }
        } else if (figura === 'rectangulo') {
            const base = parseFloat(document.getElementById('baseRectangulo').value);
            const altura = parseFloat(document.getElementById('alturaRectangulo').value);
            //Validamos datos RECTANGULO (hay que validar que no midan lo mismo el alto y la base, si no seria un cuadrado)
            if (isNaN(base) || isNaN(altura) || base <= 0 || altura <= 0 ||base == altura) {
                esValido = false;
            } else {
                area = base * altura;
            }
        } else {
            resultadoDiv.textContent = 'Selecciona una figura.';
            return;
        }

        // Validamos y mostramos resultado si si
        if (!esValido) {
            resultadoDiv.textContent = 'Ingresa valores numéricos y positivos o revisa tus datos';
        } else {
            resultadoDiv.textContent = `El área es ${area.toFixed(2)} cm cuadrados`;
        }
    });
});