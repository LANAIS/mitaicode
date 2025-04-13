/**
 * MitaiCode - JavaScript para Hackathones
 * Este archivo contiene las funciones JavaScript para la sección de hackathones
 */

document.addEventListener('DOMContentLoaded', function() {
    // Configurar botones de acción para los hackathones
    setupHackathonButtons();
    
    // Configurar modales y formularios
    setupHackathonModals();
});

/**
 * Configura los botones de acción para los hackathones
 */
function setupHackathonButtons() {
    // Botón para unirse o crear equipo
    const joinHackathonBtns = document.querySelectorAll('[id^="joinEducationHackathon"], [id^="joinHackathon"]');
    joinHackathonBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                const teamJoinModal = new bootstrap.Modal(document.getElementById('teamJoinModal'));
                teamJoinModal.show();
            });
        }
    });
    
    // Botón para ver detalles de equipos
    const viewTeamBtns = document.querySelectorAll('[id^="viewClimateHackathon"], [id^="viewHackathon"], [id^="viewTeam"]');
    viewTeamBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                // Cambiar a la pestaña "Mis Equipos"
                document.getElementById('my-teams-tab').click();
            });
        }
    });
    
    // Configuración de botones en hackathones pasados
    const projectBtns = document.querySelectorAll('.tab-pane#past-hackathons .btn-outline-primary');
    projectBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                showProjectDetails();
            });
        }
    });
    
    const certificateBtns = document.querySelectorAll('.tab-pane#past-hackathons .btn-outline-info');
    certificateBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                showHackathonCertificate();
            });
        }
    });
    
    const codeBtns = document.querySelectorAll('.tab-pane#past-hackathons .btn-outline-success');
    codeBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                // Redirigir a un repositorio ficticio
                window.open('https://github.com/mitaicode-examples/healthtech-heroes', '_blank');
            });
        }
    });
}

/**
 * Configura los modales y formularios relacionados con hackathones
 */
function setupHackathonModals() {
    // Configurar botón de confirmación en el modal de unión a equipos
    const confirmTeamActionBtn = document.getElementById('confirmTeamAction');
    if (confirmTeamActionBtn) {
        confirmTeamActionBtn.addEventListener('click', function() {
            // Verificar qué pestaña está activa en el modal
            const activeTab = document.querySelector('#teamModalTabs .nav-link.active');
            
            if (activeTab && activeTab.id === 'join-team-tab') {
                // Lógica para unirse a un equipo
                joinSelectedTeam();
            } else if (activeTab && activeTab.id === 'create-team-tab') {
                // Lógica para crear un equipo
                createNewTeam();
            }
            
            // Cerrar el modal después de la acción
            const teamJoinModal = bootstrap.Modal.getInstance(document.getElementById('teamJoinModal'));
            if (teamJoinModal) {
                teamJoinModal.hide();
            }
        });
    }
    
    // Botones para unirse a equipos en la lista
    document.querySelectorAll('.list-group-item .btn-primary').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Obtener información del equipo
            const listItem = this.closest('.list-group-item');
            const teamName = listItem.querySelector('h6').textContent;
            const teamId = listItem.dataset.teamId || 1; // Valor por defecto para demo
            
            joinTeam(teamId, teamName);
        });
    });
    
    // Configurar botón para añadir compañeros al equipo
    const addTeammateBtn = document.querySelector('#create-team .btn-outline-primary');
    if (addTeammateBtn) {
        addTeammateBtn.addEventListener('click', function() {
            const selectEl = document.getElementById('teamMemberSelect');
            
            if (selectEl && selectEl.selectedIndex > 0) {
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                addInvitedTeammate(selectedOption.text, selectedOption.value);
                
                // Deshabilitar la opción seleccionada
                selectedOption.disabled = true;
                selectEl.selectedIndex = 0;
            }
        });
    }
    
    // Preparar el espacio para invitados
    const invitedTeammatesDiv = document.getElementById('invitedTeammates');
    if (invitedTeammatesDiv && !invitedTeammatesDiv.querySelector('#invitedList')) {
        const listDiv = document.createElement('div');
        listDiv.id = 'invitedList';
        invitedTeammatesDiv.appendChild(listDiv);
    }
}

/**
 * Unirse a un equipo seleccionado
 */
function joinSelectedTeam() {
    // Obtener el equipo seleccionado
    const selectedTeam = document.querySelector('.list-group-item:focus') || 
                         document.querySelector('.list-group-item .btn-primary:focus')?.closest('.list-group-item') ||
                         document.querySelector('.list-group-item .btn-primary:hover')?.closest('.list-group-item');
    
    if (!selectedTeam) {
        // Mostrar mensaje de error
        Swal.fire({
            icon: 'error',
            title: 'No se ha seleccionado ningún equipo',
            text: 'Por favor, selecciona un equipo para unirte.'
        });
        return;
    }
    
    const teamName = selectedTeam.querySelector('h6').textContent;
    const teamId = selectedTeam.dataset.teamId || 1; // Valor por defecto para demo
    
    joinTeam(teamId, teamName);
}

/**
 * Unirse a un equipo específico
 */
function joinTeam(teamId, teamName) {
    Swal.fire({
        icon: 'success',
        title: 'Te has unido al equipo',
        text: `Te has unido exitosamente al equipo "${teamName}". ¡Ya puedes comenzar a trabajar en el hackathon!`
    });
    
    // Actualizar la interfaz para reflejar que se ha unido al equipo
    updateHackathonParticipationStatus('joinEducationHackathon', teamName);
}

/**
 * Crear un nuevo equipo
 */
function createNewTeam() {
    const teamNameInput = document.getElementById('teamName');
    const teamDescriptionInput = document.getElementById('teamDescription');
    
    if (!teamNameInput || !teamNameInput.value.trim()) {
        Swal.fire({
            icon: 'error',
            title: 'Nombre de equipo requerido',
            text: 'Por favor, ingresa un nombre para tu equipo.'
        });
        return;
    }
    
    const teamName = teamNameInput.value.trim();
    
    Swal.fire({
        icon: 'success',
        title: 'Equipo creado',
        text: `Tu equipo "${teamName}" ha sido creado exitosamente. ¡Ya puedes comenzar a trabajar en el hackathon!`
    });
    
    // Actualizar la interfaz para reflejar que se ha creado el equipo
    updateHackathonParticipationStatus('joinEducationHackathon', teamName);
}

/**
 * Añade un compañero a la lista de invitados
 */
function addInvitedTeammate(name, userId) {
    const invitedList = document.getElementById('invitedList');
    if (!invitedList) return;
    
    const invitedCount = invitedList.querySelectorAll('.invited-teammate').length;
    
    // Limitar a 3 invitados (4 con el estudiante actual)
    if (invitedCount >= 3) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de miembros alcanzado',
            text: 'Un equipo puede tener un máximo de 4 miembros.'
        });
        return;
    }
    
    const invitedItem = document.createElement('div');
    invitedItem.className = 'invited-teammate d-flex justify-content-between align-items-center border rounded p-2 mb-2';
    invitedItem.dataset.userId = userId;
    invitedItem.innerHTML = `
        <div class="d-flex align-items-center">
            <img src="${asset_url}img/avatars/default.png" class="rounded-circle me-2" width="24" height="24" alt="Avatar">
            <span>${name}</span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger remove-teammate">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    invitedList.appendChild(invitedItem);
    
    // Añadir evento para eliminar
    invitedItem.querySelector('.remove-teammate').addEventListener('click', function() {
        // Habilitar de nuevo la opción en el select
        const selectEl = document.getElementById('teamMemberSelect');
        if (selectEl) {
            Array.from(selectEl.options).forEach(option => {
                if (option.text === name) {
                    option.disabled = false;
                }
            });
        }
        
        // Eliminar de la lista
        invitedItem.remove();
    });
}

/**
 * Actualiza la interfaz para reflejar que el estudiante está participando en un hackathon
 */
function updateHackathonParticipationStatus(buttonId, teamName) {
    const joinButton = document.getElementById(buttonId);
    if (joinButton) {
        const card = joinButton.closest('.hackathon-card');
        const statusDiv = card.querySelector('.d-flex.justify-content-between.align-items-center div:first-child');
        
        // Cambiar el texto de estado
        statusDiv.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i> Ya estás participando</span>`;
        
        // Cambiar el botón para ver equipo
        joinButton.innerHTML = `<i class="fas fa-eye me-1"></i> Ver mi equipo`;
        joinButton.classList.remove('btn-success');
        joinButton.classList.add('btn-primary');
        joinButton.id = 'viewNewTeam'; // Cambiar ID para evitar conflictos
        
        // Añadir evento al nuevo botón
        joinButton.addEventListener('click', function() {
            // Cambiar a la pestaña "Mis Equipos"
            document.getElementById('my-teams-tab').click();
        });
        
        // Actualizar contador de la pestaña de equipos
        const badgeTeams = document.querySelector('#my-teams-tab .badge');
        if (badgeTeams) {
            // Extraer el número actual y sumar 1
            const currentCount = parseInt(badgeTeams.textContent.trim()) || 0;
            badgeTeams.textContent = currentCount + 1;
        }
    }
}

/**
 * Muestra detalles del proyecto
 */
function showProjectDetails() {
    Swal.fire({
        title: 'MediAssist - Asistente de diagnóstico con IA',
        html: `
            <div class="text-start">
                <h5 class="mb-3">Descripción del proyecto</h5>
                <p>MediAssist es un asistente de diagnóstico basado en IA que ayuda a los médicos a identificar patrones en imágenes médicas y datos de pacientes para sugerir posibles diagnósticos. El sistema utiliza algoritmos de machine learning entrenados con miles de casos clínicos previos.</p>
                
                <h5 class="mb-3 mt-4">Características principales</h5>
                <ul class="text-start">
                    <li>Análisis de imágenes médicas (radiografías, resonancias, etc.)</li>
                    <li>Procesamiento de datos clínicos del paciente</li>
                    <li>Sugerencia de diagnósticos posibles con porcentaje de probabilidad</li>
                    <li>Interfaz intuitiva para profesionales médicos</li>
                </ul>
                
                <h5 class="mb-3 mt-4">Tecnologías utilizadas</h5>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="badge bg-primary">Python</span>
                    <span class="badge bg-success">TensorFlow</span>
                    <span class="badge bg-info">OpenCV</span>
                    <span class="badge bg-warning">React</span>
                    <span class="badge bg-danger">Flask API</span>
                </div>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Cerrar',
        showClass: {
            popup: 'animate__animated animate__fadeIn'
        }
    });
}

/**
 * Muestra el certificado de participación
 */
function showHackathonCertificate() {
    Swal.fire({
        title: 'Certificado de Participación',
        html: `
            <div class="certificate">
                <div class="certificate-header">
                    <img src="${asset_url}img/mitai-logo.png" alt="MitaiCode" width="50">
                    <h2>MitaiCode Hackathon</h2>
                </div>
                <div class="certificate-body">
                    <p>Este certificado se otorga a</p>
                    <h3>Ana García</h3>
                    <p>Por su destacada participación en el hackathon</p>
                    <h4>Inteligencia Artificial para la Salud</h4>
                    <p>Obteniendo el 2º lugar con el proyecto "MediAssist"</p>
                    <p>Fecha: Junio 2023</p>
                </div>
            </div>
            <div class="mt-3">
                <button id="downloadCertificate" class="btn btn-primary">Descargar Certificado</button>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Cerrar',
        width: '600px',
        showClass: {
            popup: 'animate__animated animate__fadeIn'
        }
    });
    
    // Configurar botón de descarga del certificado
    setTimeout(() => {
        const downloadBtn = document.getElementById('downloadCertificate');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Certificado Descargado',
                    text: 'El certificado se ha guardado en tu dispositivo.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    }, 500);
}

// Variable para la URL base de assets
const asset_url = '/';

// Función para cargar estilos adicionales si es necesario
function loadHackathonStyles() {
    if (!document.getElementById('hackathon-styles')) {
        const styleLink = document.createElement('link');
        styleLink.id = 'hackathon-styles';
        styleLink.rel = 'stylesheet';
        styleLink.href = `${asset_url}css/hackathons.css`;
        document.head.appendChild(styleLink);
    }
}

// Cargar estilos al inicializar
loadHackathonStyles(); 