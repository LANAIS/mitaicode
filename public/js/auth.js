// Funciones de autenticación para la página de bienvenida
document.addEventListener('DOMContentLoaded', function() {
    // Comprobar si hay un usuario autenticado actualmente
    checkAuthStatus();

    // Gestionar formulario de registro
    const registrationForm = document.getElementById('registration-form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            // La validación se realiza en el servidor
            // Este es un lugar para agregar validación adicional si es necesario
            console.log('Enviando formulario de registro...');
        });
    }
});

// Comprobar si hay un usuario autenticado
function checkAuthStatus() {
    const userInfo = document.getElementById('user-info');
    
    // Esta función podría expandirse para hacer una solicitud AJAX
    // para verificar el estado de la sesión del usuario
    
    // Por ahora, solo mostramos los botones de login/registro o el nombre de usuario
    // según si está o no autenticado, basándonos en lo que ya renderiza Laravel
    
    console.log('Comprobando estado de autenticación...');
}

// Mostrar mensajes de notificación
function showNotification(message, type = 'info') {
    // Esta función podría usarse para mostrar mensajes de éxito o error
    // después de acciones de autenticación
    console.log(`Notificación (${type}): ${message}`);
    
    // Aquí se podría implementar un toast o notificación visual
} 