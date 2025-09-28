import { GoogleGenerativeAI } from "@google/generative-ai";
// Importa y configura dotenv (solo para desarrollo local)
import * as dotenv from 'dotenv'
dotenv.config()

// API Key
let apiKey = process.env.API_KEY; // Intenta obtener la API key de la variable de entorno

if (!apiKey) {
    console.warn('API Key no encontrada en las variables de entorno.  Asumiendo que Canvas la proveerá.');
    apiKey = ""; // Deja la API key vacía para que Canvas la proporcione
}

const genAI = new GoogleGenerativeAI({ apiKey: apiKey });
const model = genAI.getModel({ model: "gemini-2.0-flash" });

const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendButton = document.getElementById('sendButton');
let chatHistory = []; // Para mantener el historial de la conversación para la API

// Función para agregar un mensaje al chat
function addMessageToChat(text, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', sender === 'user' ? 'user-message' : 'bot-message');
    messageDiv.textContent = text;
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight; // Auto-scroll al último mensaje
    return messageDiv; // Devuelve el elemento del mensaje para poder modificarlo (ej. 'pensando')
}

// Función para manejar el envío de mensajes
async function handleSendMessage() {
    const messageText = chatInput.value.trim();
    if (messageText === '') return;

    addMessageToChat(messageText, 'user');
    chatInput.value = ''; // Limpiar input
    sendButton.disabled = true; // Deshabilitar botón mientras se procesa

    // Agregar mensaje del usuario al historial para la API
    chatHistory.push({ role: "user", parts: [{ text: messageText }] });

    const thinkingMessage = addMessageToChat('', 'bot');
    thinkingMessage.classList.add('thinking');


    // --- INICIO: LLAMADA A LA API DE GEMINI ---
    try {
        const payload = {
            contents: chatHistory,
            // Opcional: Configuración de generación
            // generationConfig: {
            //     temperature: 0.7,
            //     topK: 1,
            //     topP: 1,
            //     maxOutputTokens: 2048,
            // }
        };

        // IMPORTANTE: Deja apiKey vacío para que Canvas lo proporcione en tiempo de ejecución.
        // const apiKey = "";
        const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`;

        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error("Error de la API:", errorData);
            throw new Error(`Error de la API: ${response.status} ${response.statusText}. Detalles: ${JSON.stringify(errorData.error?.message || errorData)}`);
        }

        const result = await response.json();
        let botResponseText = "No se pudo obtener una respuesta.";

        if (result.candidates && result.candidates.length > 0 &&
            result.candidates[0].content && result.candidates[0].content.parts &&
            result.candidates[0].content.parts.length > 0) {
            botResponseText = result.candidates[0].content.parts[0].text;
        } else if (result.promptFeedback && result.promptFeedback.blockReason) {
            botResponseText = `No se pudo generar una respuesta debido a: ${result.promptFeedback.blockReason}.`;
            if (result.promptFeedback.blockReason === "SAFETY" && result.promptFeedback.safetyRatings) {
                const safetyIssues = result.promptFeedback.safetyRatings.filter(r => r.blocked).map(r => r.category).join(', ');
                botResponseText += ` Categorías bloqueadas: ${safetyIssues}.`;
            }
        }


        thinkingMessage.classList.remove('thinking');
        thinkingMessage.textContent = botResponseText;

        // Agregar respuesta del bot al historial
        chatHistory.push({ role: "model", parts: [{ text: botResponseText }] });

    } catch (error) {
        console.error('Error al obtener respuesta del bot:', error);
        thinkingMessage.classList.remove('thinking');
        thinkingMessage.textContent = `Lo siento, ocurrió un error al procesar tu solicitud: ${error.message}. Por favor, intenta de nuevo.`;
    } finally {
        sendButton.disabled = false; // Habilitar botón
        chatInput.focus();
    }
    // --- FIN: LLAMADA A LA API DE GEMINI ---
}

// Event listeners
sendButton.addEventListener('click', handleSendMessage);
chatInput.addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
        handleSendMessage();
    }
});

// Mensaje inicial (opcional, ya está en el HTML)
// addMessageToChat("¡Hola! Soy el asistente virtual de la Universidad Anáhuac México. ¿En qué puedo ayudarte hoy?", 'bot');
