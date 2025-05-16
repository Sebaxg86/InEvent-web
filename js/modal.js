
// modal.js

// Función para mostrar el error con el mensaje recibido
function showError(message) {
    document.getElementById('error-message').innerText = message;
    document.getElementById('error-popup').style.display = 'block';
}

// Cerrar el popup cuando se hace clic en la "X"
document.querySelector('.error-popup-close').addEventListener('click', function() {
    document.getElementById('error-popup').style.display = 'none';
});

// Cerrar el popup si se hace clic fuera del contenido
window.addEventListener('click', function(event) {
    const popup = document.getElementById('error-popup');
    if (event.target === popup) {
        popup.style.display = 'none';
    }
});