// Definición de unidades por categoría
const units = {
    temperature: {
        name: "Temperatura",
        info: "Convierte entre diferentes escalas de temperatura: Celsius, Fahrenheit y Kelvin.",
        units: {
            celsius: { name: "Celsius (°C)", factor: 1 },
            fahrenheit: { name: "Fahrenheit (°F)", factor: 1 },
            kelvin: { name: "Kelvin (K)", factor: 1 }
        }
    },
    distance: {
        name: "Distancia",
        info: "Convierte entre unidades de longitud desde milímetros hasta kilómetros.",
        units: {
            mm: { name: "Milímetros (mm)", factor: 0.001 },
            cm: { name: "Centímetros (cm)", factor: 0.01 },
            m: { name: "Metros (m)", factor: 1 },
            km: { name: "Kilómetros (km)", factor: 1000 },
            inch: { name: "Pulgadas (in)", factor: 0.0254 },
            ft: { name: "Pies (ft)", factor: 0.3048 },
            yard: { name: "Yardas (yd)", factor: 0.9144 },
            mile: { name: "Millas (mi)", factor: 1609.34 }
        }
    },
    weight: {
        name: "Peso",
        info: "Convierte entre unidades de masa desde gramos hasta toneladas.",
        units: {
            mg: { name: "Miligramos (mg)", factor: 0.000001 },
            g: { name: "Gramos (g)", factor: 0.001 },
            kg: { name: "Kilogramos (kg)", factor: 1 },
            ton: { name: "Toneladas (t)", factor: 1000 },
            oz: { name: "Onzas (oz)", factor: 0.0283495 },
            lb: { name: "Libras (lb)", factor: 0.453592 }
        }
    },
    time: {
        name: "Tiempo",
        info: "Convierte entre unidades de tiempo desde segundos hasta años.",
        units: {
            ms: { name: "Milisegundos (ms)", factor: 0.001 },
            s: { name: "Segundos (s)", factor: 1 },
            min: { name: "Minutos (min)", factor: 60 },
            h: { name: "Horas (h)", factor: 3600 },
            day: { name: "Días", factor: 86400 },
            week: { name: "Semanas", factor: 604800 },
            month: { name: "Meses", factor: 2629746 },
            year: { name: "Años", factor: 31556952 }
        }
    },
    volume: {
        name: "Volumen",
        info: "Convierte entre unidades de volumen y capacidad.",
        units: {
            ml: { name: "Mililitros (ml)", factor: 0.001 },
            l: { name: "Litros (l)", factor: 1 },
            gal: { name: "Galones (gal)", factor: 3.78541 },
            qt: { name: "Cuartos (qt)", factor: 0.946353 },
            pt: { name: "Pintas (pt)", factor: 0.473176 },
            cup: { name: "Tazas", factor: 0.236588 },
            floz: { name: "Onzas fluidas (fl oz)", factor: 0.0295735 }
        }
    },
    area: {
        name: "Área",
        info: "Convierte entre unidades de superficie y área.",
        units: {
            mm2: { name: "Milímetros² (mm²)", factor: 0.000001 },
            cm2: { name: "Centímetros² (cm²)", factor: 0.0001 },
            m2: { name: "Metros² (m²)", factor: 1 },
            km2: { name: "Kilómetros² (km²)", factor: 1000000 },
            inch2: { name: "Pulgadas² (in²)", factor: 0.00064516 },
            ft2: { name: "Pies² (ft²)", factor: 0.092903 },
            acre: { name: "Acres", factor: 4046.86 },
            hectare: { name: "Hectáreas (ha)", factor: 10000 }
        }
    },
    speed: {
        name: "Velocidad",
        info: "Convierte entre diferentes unidades de velocidad.",
        units: {
            mps: { name: "Metros/segundo (m/s)", factor: 1 },
            kph: { name: "Kilómetros/hora (km/h)", factor: 0.277778 },
            mph: { name: "Millas/hora (mph)", factor: 0.44704 },
            fps: { name: "Pies/segundo (ft/s)", factor: 0.3048 },
            knot: { name: "Nudos (kn)", factor: 0.514444 }
        }
    },
    energy: {
        name: "Energía",
        info: "Convierte entre unidades de energía y trabajo.",
        units: {
            j: { name: "Julios (J)", factor: 1 },
            kj: { name: "Kilojulios (kJ)", factor: 1000 },
            cal: { name: "Calorías (cal)", factor: 4.184 },
            kcal: { name: "Kilocalorías (kcal)", factor: 4184 },
            wh: { name: "Vatios-hora (Wh)", factor: 3600 },
            kwh: { name: "Kilovatios-hora (kWh)", factor: 3600000 },
            btu: { name: "BTU", factor: 1055.06 }
        }
    }
};

// Variables globales
let currentCategory = '';

// Elementos del DOM
const categorySelect = document.getElementById('category');
const converterMain = document.getElementById('converter-main');
const fromValueInput = document.getElementById('fromValue');
const toValueInput = document.getElementById('toValue');
const fromUnitSelect = document.getElementById('fromUnit');
const toUnitSelect = document.getElementById('toUnit');
const conversionResult = document.getElementById('conversionResult');
const categoryInfo = document.getElementById('categoryInfo');
const swapButton = document.getElementById('swapButton');
const clearButton = document.getElementById('clearButton');
const copyButton = document.getElementById('copyButton');

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    initializeConverter();
});

function initializeConverter() {
    categorySelect.addEventListener('change', handleCategoryChange);
    fromValueInput.addEventListener('input', performConversion);
    fromUnitSelect.addEventListener('change', performConversion);
    toUnitSelect.addEventListener('change', performConversion);
    swapButton.addEventListener('click', swapUnits);
    clearButton.addEventListener('click', clearInputs);
    copyButton.addEventListener('click', copyResult);
}

function handleCategoryChange() {
    const selectedCategory = categorySelect.value;
    
    if (selectedCategory === '') {
        converterMain.classList.add('converter-hidden');
        categoryInfo.innerHTML = '<p>Selecciona una categoría para comenzar a convertir unidades.</p>';
        return;
    }
    
    currentCategory = selectedCategory;
    populateUnitSelects();
    converterMain.classList.remove('converter-hidden');
    updateCategoryInfo();
    clearInputs();
}

function populateUnitSelects() {
    const categoryUnits = units[currentCategory].units;
    
    // Limpiar selects
    fromUnitSelect.innerHTML = '';
    toUnitSelect.innerHTML = '';
    
    // Poblar con las unidades de la categoría
    Object.keys(categoryUnits).forEach(unitKey => {
        const option1 = new Option(categoryUnits[unitKey].name, unitKey);
        const option2 = new Option(categoryUnits[unitKey].name, unitKey);
        
        fromUnitSelect.appendChild(option1);
        toUnitSelect.appendChild(option2);
    });
    
    // Seleccionar diferentes unidades por defecto
    const unitKeys = Object.keys(categoryUnits);
    if (unitKeys.length > 1) {
        fromUnitSelect.value = unitKeys[0];
        toUnitSelect.value = unitKeys[1];
    }
}

function updateCategoryInfo() {
    const categoryData = units[currentCategory];
    categoryInfo.innerHTML = `
        <h4>${categoryData.name}</h4>
        <p>${categoryData.info}</p>
        <p><strong>Unidades disponibles:</strong> ${Object.keys(categoryData.units).length}</p>
    `;
}

function performConversion() {
    const fromValue = parseFloat(fromValueInput.value);
    const fromUnit = fromUnitSelect.value;
    const toUnit = toUnitSelect.value;
    
    if (isNaN(fromValue) || fromValue === '' || !fromUnit || !toUnit) {
        toValueInput.value = '';
        conversionResult.innerHTML = '';
        return;
    }
    
    let result;
    
    // Conversión especial para temperatura
    if (currentCategory === 'temperature') {
        result = convertTemperature(fromValue, fromUnit, toUnit);
    } else {
        // Conversión estándar usando factores
        const fromFactor = units[currentCategory].units[fromUnit].factor;
        const toFactor = units[currentCategory].units[toUnit].factor;
        result = (fromValue * fromFactor) / toFactor;
    }
    
    // Redondear resultado a 6 decimales
    result = Math.round(result * 1000000) / 1000000;
    
    toValueInput.value = result;
    updateResultDisplay(fromValue, fromUnit, result, toUnit);
}

function convertTemperature(value, fromUnit, toUnit) {
    let celsius;
    
    // Convertir a Celsius primero
    switch (fromUnit) {
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
    switch (toUnit) {
        case 'celsius':
            return celsius;
        case 'fahrenheit':
            return (celsius * 9/5) + 32;
        case 'kelvin':
            return celsius + 273.15;
    }
}

function updateResultDisplay(fromValue, fromUnit, toValue, toUnit) {
    const fromUnitName = units[currentCategory].units[fromUnit].name;
    const toUnitName = units[currentCategory].units[toUnit].name;
    
    conversionResult.innerHTML = `
        <div class="conversion-equation">
            <span class="from-value">${fromValue}</span>
            <span class="unit-name">${fromUnitName}</span>
            <span class="equals">=</span>
            <span class="to-value">${toValue}</span>
            <span class="unit-name">${toUnitName}</span>
        </div>
    `;
}

function swapUnits() {
    const fromUnit = fromUnitSelect.value;
    const toUnit = toUnitSelect.value;
    const fromValue = fromValueInput.value;
    const toValue = toValueInput.value;
    
    // Intercambiar unidades
    fromUnitSelect.value = toUnit;
    toUnitSelect.value = fromUnit;
    
    // Intercambiar valores
    fromValueInput.value = toValue;
    
    // Realizar nueva conversión
    performConversion();
    
    // Animación del botón
    swapButton.style.transform = 'rotate(180deg)';
    setTimeout(() => {
        swapButton.style.transform = 'rotate(0deg)';
    }, 300);
}

function clearInputs() {
    fromValueInput.value = '';
    toValueInput.value = '';
    conversionResult.innerHTML = '';
}

function copyResult() {
    const result = toValueInput.value;
    
    if (result) {
        navigator.clipboard.writeText(result).then(() => {
            // Feedback visual
            const originalText = copyButton.innerHTML;
            copyButton.innerHTML = '<i class="fas fa-check"></i> Copiado';
            copyButton.classList.add('copied');
            
            setTimeout(() => {
                copyButton.innerHTML = originalText;
                copyButton.classList.remove('copied');
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar:', err);
        });
    }
}

// Funciones de utilidad para formato de números
function formatNumber(number) {
    return new Intl.NumberFormat('es-ES', {
        maximumFractionDigits: 6,
        minimumFractionDigits: 0
    }).format(number);
}

// Validación de entrada
fromValueInput.addEventListener('keypress', function(e) {
    // Permitir números, punto decimal, signo negativo y teclas de control
    const allowedChars = /[0-9.\-]/;
    const controlKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'Home', 'End', 'ArrowLeft', 'ArrowRight'];
    
    if (!allowedChars.test(e.key) && !controlKeys.includes(e.key)) {
        e.preventDefault();
    }
});