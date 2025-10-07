document.getElementById('info').addEventListener('submit', function(event) {
    event.preventDefault();

    const nombre = document.getElementById('name').value;
    const correo = document.getElementById('email').value;
    const edad = document.getElementById('edad').value;
    const nacimiento = document.getElementById('nacimiento').value;
    const color = document.getElementById('color').value;
    // Verificamos la opcion del radio
    const generoSeleccionado = document.querySelector('input[name="genero"]:checked');
    const genero = generoSeleccionado ? generoSeleccionado.value : 'No especificado';

    const resultado = document.getElementById('resultadoInfo');

    resultado.innerHTML = `Mi nombre es <strong>${nombre}</strong> y tengo <strong>${edad}</strong> años, ya que nací en la fecha <strong>${nacimiento}</strong> y soy del sexo <strong>${genero}</strong>. <br>
                         Si quieren contactarme lo pueden hacer al correo: <strong>${correo}</strong>. <br>
                         Un dato extra es que mi color favorito es este: <input type="color" value="${color}" disabled>`;
});