// Definición de unidades y factores de conversión
const units = {
    temperature: {
        name: "Temperatura",
        units: {
            celsius: { name: "Celsius", symbol: "°C" },
            fahrenheit: { name: "Fahrenheit", symbol: "°F" },
            kelvin: { name: "Kelvin", symbol: "K" }
        }
    },
    distance: {
        name: "Distancia",
        units: {
            meters: { name: "Metros", symbol: "m", factor: 1 },
            kilometers: { name: "Kilómetros", symbol: "km", factor: 1000 },
            miles: { name: "Millas", symbol: "mi", factor: 1609.344 },
            feet: { name: "Pies", symbol: "ft", factor: 0.3048 },
            inches: { name: "Pulgadas", symbol: "in", factor: 0.0254 }
        }
    },
    time: {
        name: "Tiempo",
        units: {
            seconds: { name: "Segundos", symbol: "s", factor: 1 },
            hours: { name: "Horas", symbol: "h", factor: 3600 },
            days: { name: "Días", symbol: "días", factor: 86400 },
            years: { name: "Años", symbol: "años", factor: 31536000 }
        }
    },
    currency: {
        name: "Moneda",
        units: {
            mxn: { name: "Peso Mexicano", symbol: "MXN", rate: 1 },
            usd: { name: "Dólar Estadounidense", symbol: "USD", rate: 0.059 }, // 1 MXN = 0.059 USD (aproximado)
            eur: { name: "Euro", symbol: "EUR", rate: 0.054 } // 1 MXN = 0.054 EUR (aproximado)
        }
    }
};

// Variables globales
let currentCategory = '';

// Elementos del DOM
const categorySelect = document.getElementById('category');
const conversionPanel = document.getElementById('conversionPanel');
const fromValueInput = document.getElementById('fromValue');
const toValueInput = document.getElementById('toValue');
const fromUnitSelect = document.getElementById('fromUnit');
const toUnitSelect = document.getElementById('toUnit');
const convertBtn = document.getElementById('convertBtn');
const clearBtn = document.getElementById('clearBtn');
const swapBtn = document.getElementById('swapUnits');
const conversionResult = document.getElementById('conversionResult');
const categoryCards = document.querySelectorAll('.category-card');

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    categorySelect.addEventListener('change', handleCategoryChange);
    convertBtn.addEventListener('click', performConversion);
    clearBtn.addEventListener('click', clearForm);
    swapBtn.addEventListener('click', swapUnits);
    fromValueInput.addEventListener('input', performConversion);
    fromUnitSelect.addEventListener('change', performConversion);
    toUnitSelect.addEventListener('change', performConversion);

    // Event listeners para las tarjetas de categorías
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            categorySelect.value = category;
            handleCategoryChange();
        });
    });
});

// Manejo del cambio de categoría
function handleCategoryChange() {
    const selectedCategory = categorySelect.value;
    
    if (selectedCategory) {
        currentCategory = selectedCategory;
        populateUnitSelects(selectedCategory);
        conversionPanel.style.display = 'block';
        highlightSelectedCategory(selectedCategory);
        clearForm();
    } else {
        conversionPanel.style.display = 'none';
        removeHighlights();
    }
}

// Poblar los selects de unidades
function populateUnitSelects(category) {
    const categoryUnits = units[category].units;
    

    fromUnitSelect.innerHTML = '';
    toUnitSelect.innerHTML = '';
    

    Object.keys(categoryUnits).forEach(unitKey => {
        const unit = categoryUnits[unitKey];
        
        const fromOption = new Option(`${unit.name} (${unit.symbol})`, unitKey);
        const toOption = new Option(`${unit.name} (${unit.symbol})`, unitKey);
        
        fromUnitSelect.appendChild(fromOption);
        toUnitSelect.appendChild(toOption);
    });
    

    const unitKeys = Object.keys(categoryUnits);
    if (unitKeys.length > 1) {
        fromUnitSelect.value = unitKeys[0];
        toUnitSelect.value = unitKeys[1];
    }
}


function performConversion() {
    const value = parseFloat(fromValueInput.value);
    const fromUnit = fromUnitSelect.value;
    const toUnit = toUnitSelect.value;
    
    if (isNaN(value) || !fromUnit || !toUnit) {
        toValueInput.value = '';
        conversionResult.innerHTML = '';
        return;
    }
    
    let result;
    let explanation;
    
    switch (currentCategory) {
        case 'temperature':
            result = convertTemperature(value, fromUnit, toUnit);
            break;
        case 'distance':
            result = convertDistance(value, fromUnit, toUnit);
            break;
        case 'time':
            result = convertTime(value, fromUnit, toUnit);
            break;
        case 'currency':
            result = convertCurrency(value, fromUnit, toUnit);
            break;
        default:
            return;
    }
    
    toValueInput.value = result.toFixed(6).replace(/\.?0+$/, ''); // Eliminar ceros innecesarios
    
    // Mostrar resultado formateado
    showConversionResult(value, fromUnit, result, toUnit);
}

// Conversión de temperatura
function convertTemperature(value, from, to) {
    if (from === to) return value;
    
    // Convertir todo a Celsius primero
    let celsius;
    switch (from) {
        case 'celsius':
            celsius = value;
            break;
        case 'fahrenheit':
            celsius = (value - 32) * 5/9;
            break;
        case 'kelvin':
            celsius = value - 273.15;
            break;
    }
    
    // Convertir de Celsius a la unidad destino
    switch (to) {
        case 'celsius':
            return celsius;
        case 'fahrenheit':
            return celsius * 9/5 + 32;
        case 'kelvin':
            return celsius + 273.15;
    }
}

// Conversión de distancia
function convertDistance(value, from, to) {
    if (from === to) return value;
    
    const fromFactor = units.distance.units[from].factor;
    const toFactor = units.distance.units[to].factor;
    
    // Convertir a metros, luego a la unidad destino
    const meters = value * fromFactor;
    return meters / toFactor;
}

// Conversión de tiempo
function convertTime(value, from, to) {
    if (from === to) return value;
    
    const fromFactor = units.time.units[from].factor;
    const toFactor = units.time.units[to].factor;
    
    // Convertir a segundos, luego a la unidad destino
    const seconds = value * fromFactor;
    return seconds / toFactor;
}

// Conversión de moneda
function convertCurrency(value, from, to) {
    if (from === to) return value;
    
    const fromRate = units.currency.units[from].rate;
    const toRate = units.currency.units[to].rate;
    
    // Convertir a MXN, luego a la moneda destino
    const mxn = value / fromRate;
    return mxn * toRate;
}

// Mostrar resultado de conversión
function showConversionResult(fromValue, fromUnit, toValue, toUnit) {
    const fromUnitData = units[currentCategory].units[fromUnit];
    const toUnitData = units[currentCategory].units[toUnit];
    
    const formattedResult = formatNumber(toValue);
    
    conversionResult.innerHTML = `
        <div class="result-display">
            <div class="result-main">
                <span class="from-value">${formatNumber(fromValue)} ${fromUnitData.symbol}</span>
                <span class="equals">=</span>
                <span class="to-value">${formattedResult} ${toUnitData.symbol}</span>
            </div>
            <div class="result-details">
                ${fromUnitData.name} → ${toUnitData.name}
            </div>
        </div>
    `;
}

// Formatear números para mostrar
function formatNumber(num) {
    if (Math.abs(num) >= 1000000) {
        return num.toExponential(3);
    } else if (Math.abs(num) < 0.001 && num !== 0) {
        return num.toExponential(3);
    } else {
        return num.toFixed(6).replace(/\.?0+$/, '');
    }
}

// Intercambiar unidades
function swapUnits() {
    const fromValue = fromUnitSelect.value;
    const toValue = toUnitSelect.value;
    
    fromUnitSelect.value = toValue;
    toUnitSelect.value = fromValue;
    
    // Intercambiar también los valores
    const inputValue = fromValueInput.value;
    const outputValue = toValueInput.value;
    
    fromValueInput.value = outputValue;
    if (inputValue) {
        performConversion();
    }
}

// Limpiar formulario
function clearForm() {
    fromValueInput.value = '';
    toValueInput.value = '';
    conversionResult.innerHTML = '';
}

// Resaltar categoría seleccionada


// Remover resaltados
function removeHighlights() {
    categoryCards.forEach(card => {
        card.classList.remove('selected');
    });
}

// Funciones de utilidad para mejorar la UX
document.addEventListener('keydown', function(e) {
    // Permitir conversión con Enter
    if (e.key === 'Enter' && conversionPanel.style.display !== 'none') {
        performConversion();
    }
    
   
    if (e.key === 'Escape') {
        clearForm();
    }
});

// Validación de entrada
fromValueInput.addEventListener('keypress', function(e) {
    // Permitir solo números, punto decimal y signo negativo
    const allowedChars = /[0-9.\-]/;
    if (!allowedChars.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
        e.preventDefault();
    }
});