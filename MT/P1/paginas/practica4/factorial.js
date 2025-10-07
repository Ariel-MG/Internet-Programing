function factorial(n) {
    if (n < 0) {
        return "Error: Número negativo no permitido.";
    }
    if (n === 0) {
        return 1;
    }
    return n * factorial(n - 1);
}

document.getElementById('factorialForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const inputElement = document.getElementById('factorial');
    const resultadoElement = document.getElementById('resultadoFactorial');
    
    const num = parseInt(inputElement.value);

    // Calculamos el factorial y lo mostramos
    resultadoElement.textContent = `El factorial de ${num} es: ${factorial(num)}`;
});