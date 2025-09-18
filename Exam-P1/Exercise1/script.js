//Generamos el numero random
let randomNumber = Math.floor(Math.random() * 100) + 1;
let attemptsLeft = 6;


const messageEl = document.getElementById('message');
const attemptsEl = document.getElementById('attempts');
const guessField = document.getElementById('guessField');
const guessButton = document.querySelector('button'); // Selecciona el botón

// Agrega un event listener al botón para ejecutar checkGuess al hacer clic
guessButton.addEventListener('click', checkGuess);

function checkGuess() {
    // Convierte la entrada del usuario a un número entero
    const userGuess = parseInt(guessField.value);

    // Validamos que la entrada del numero sea uno enre el 1 y el 100 (y que no este vacio)
    if (isNaN(userGuess) || userGuess < 1 || userGuess > 100) {
        messageEl.textContent = 'Introduce un número válido entre 1 y 100.';
        return;
    }

    // Disminuye los intentos restantes
    attemptsLeft--;

    // Compara la suposición del usuario con el número aleatorio
    if (userGuess === randomNumber) {
        messageEl.textContent = `Correcto! El número era ${randomNumber}.`;
        endGame();
    } else if (userGuess > randomNumber) {
        messageEl.textContent = 'Intenta con un número más pequeño!';
    } else {
        messageEl.textContent = '¡Intenta con un número más grande!';
    }
    //Comprobamos su aun quedan intentos
    if (attemptsLeft > 0 && userGuess !== randomNumber) {
        attemptsEl.textContent = `Te quedan ${attemptsLeft} intentos.`;
    } else if (attemptsLeft === 0 && userGuess !== randomNumber) {
        messageEl.textContent = `Se acabaron los intentos! El número era ${randomNumber}.`;
        attemptsEl.textContent = '';
        endGame();
    }
}

function endGame() {
    //Ya no puedes jugar y desabilitamos el boton
    guessField.disabled = true;
    guessButton.disabled = true;

    // Crear y añadir el botón de reinicio
    const resetButton = document.createElement('button');
    resetButton.textContent = 'Reiniciar Juego';
    resetButton.id = 'resetButton'; // Añadir un ID al botón
    resetButton.addEventListener('click', resetGame);
    document.getElementById('main').appendChild(resetButton); // Agregar al div main
}

function resetGame() {
    // Reinicia las variables
    randomNumber = Math.floor(Math.random() * 100) + 1;
    attemptsLeft = 6;

    // Restablece la interfaz de usuario
    messageEl.textContent = '';
    attemptsEl.textContent = `Te quedan ${attemptsLeft} intentos.`;
    guessField.disabled = false;
    guessButton.disabled = false;
    guessField.value = '';

    // Elimina el botón de reinicio
    const resetButton = document.getElementById('resetButton'); // Seleccionar por ID
    resetButton.parentNode.removeChild(resetButton);
}