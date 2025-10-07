document.getElementById('calificacionForm').addEventListener('submit', function(event) {
    event.preventDefault(); //Que no se vuelva a cargar la pagina

    const calificacionInput = document.getElementById('calificacion');
    const calificacion = parseFloat(calificacionInput.value);
    const resultadoP = document.getElementById('resultadoCalificacion');

    function evaluarCalificacion(calificacion) {
        if (calificacion < 0 || calificacion > 10) {
            return "Error: La calificación debe estar entre 0 y 10.";
        } else if (calificacion >= 0 && calificacion < 6) {
            return "Equivalencia: NA";
        } else if (calificacion >= 6 && calificacion < 7.5) {
            return "Equivalencia: S";
        } else if (calificacion >= 7.5 && calificacion < 9) {
            return "Equivalencia: B";
        } else if (calificacion >= 9 && calificacion < 10) {
            return "Equivalencia: MB";
        } else if (calificacion === 10) {
            return "Equivalencia: LAP";
        }
    }

    resultadoP.textContent = evaluarCalificacion(calificacion);
});