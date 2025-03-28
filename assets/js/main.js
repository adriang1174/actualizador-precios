/**
 * Funciones JavaScript principales para el Actualizador de Precios Factusol
 * 
 * Este archivo contiene funciones comunes utilizadas en múltiples vistas
 * de la aplicación.
 */

/**
 * Inicializa los componentes de la aplicación cuando el DOM está completamente cargado
 */
document.addEventListener('DOMContentLoaded', function() {
    // Activar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Activar los elementos de navegación según la página actual
    activarNavActual();
    
    // Inicializar validación para todos los formularios
    inicializarValidacion();
});

/**
 * Activa el elemento de navegación correspondiente a la página actual
 */
function activarNavActual() {
    // Obtener la URL actual
    const currentUrl = window.location.href;
    
    // Obtener todos los enlaces de navegación
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    // Quitar la clase active de todos los enlaces
    navLinks.forEach(link => {
        link.classList.remove('active');
        
        // Si el enlace está en la URL actual, marcar como activo
        if (currentUrl.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
    
    // Si estamos en la página principal, activar el primer enlace
    if (currentUrl.endsWith('index.php') || currentUrl.endsWith('/')) {
        document.querySelector('.navbar-brand').classList.add('active');
    }
}

/**
 * Inicializa la validación de formularios
 */
function inicializarValidacion() {
    // Obtener todos los formularios con la clase 'needs-validation'
    const forms = document.querySelectorAll('.needs-validation');
    
    // Iterar sobre cada formulario y prevenir el envío si no es válido
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Muestra un mensaje de alerta al usuario
 * 
 * @param {string} mensaje - El mensaje a mostrar
 * @param {string} tipo - El tipo de alerta (success, danger, warning, info)
 * @param {string} contenedor - El selector del contenedor donde mostrar la alerta
 */
function mostrarAlerta(mensaje, tipo = 'info', contenedor = '#alertContainer') {
    const alertContainer = document.querySelector(contenedor);
    
    if (alertContainer) {
        // Crear el elemento de alerta
        const alertElement = document.createElement('div');
        alertElement.className = `alert alert-${tipo} alert-dismissible fade show`;
        alertElement.role = 'alert';
        
        // Añadir el mensaje y el botón de cierre
        alertElement.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        `;
        
        // Añadir la alerta al contenedor
        alertContainer.appendChild(alertElement);
        
        // Eliminar la alerta después de 5 segundos
        setTimeout(() => {
            const alert = bootstrap.Alert.getOrCreateInstance(alertElement);
            alert.close();
        }, 5000);
    } else {
        console.error('No se encontró el contenedor de alertas:', contenedor);
    }
}

/**
 * Realiza una petición AJAX
 * 
 * @param {string} url - La URL a la que se realizará la petición
 * @param {Object} data - Los datos a enviar en la petición
 * @param {string} method - El método HTTP a utilizar (GET, POST, etc.)
 * @param {Function} successCallback - Función a ejecutar en caso de éxito
 * @param {Function} errorCallback - Función a ejecutar en caso de error
 */
function ajaxRequest(url, data = {}, method = 'GET', successCallback, errorCallback) {
    // Crear el objeto XMLHttpRequest
    const xhr = new XMLHttpRequest();
    
    // Configurar la petición
    xhr.open(method, url, true);
    
    // Configurar las cabeceras para peticiones POST
    if (method.toUpperCase() === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/json');
    }
    
    // Manejar eventos de carga
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            // Si la petición fue exitosa
            let response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                response = xhr.responseText;
            }
            
            if (typeof successCallback === 'function') {
                successCallback(response);
            }
        } else {
            // Si la petición falló
            if (typeof errorCallback === 'function') {
                errorCallback(xhr.statusText);
            } else {
                console.error('Error en la petición AJAX:', xhr.statusText);
            }
        }
    };
    
    // Manejar errores de red
    xhr.onerror = function() {
        if (typeof errorCallback === 'function') {
            errorCallback('Error de red');
        } else {
            console.error('Error de red al realizar la petición AJAX');
        }
    };
    
    // Enviar la petición
    if (method.toUpperCase() === 'POST') {
        xhr.send(JSON.stringify(data));
    } else {
        xhr.send();
    }
}

/**
 * Formatea un número como moneda
 * 
 * @param {number} valor - El valor a formatear
 * @param {string} moneda - El código de moneda (por defecto, ARS)
 * @return {string} - El valor formateado
 */
function formatearMoneda(valor, moneda = 'ARS') {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: moneda
    }).format(valor);
}

/**
 * Formatea una fecha en formato local
 * 
 * @param {string|Date} fecha - La fecha a formatear
 * @return {string} - La fecha formateada
 */
function formatearFecha(fecha) {
    const date = fecha instanceof Date ? fecha : new Date(fecha);
    
    return date.toLocaleDateString('es-AR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}
