/**
 * MitaiCode - JavaScript para los Desafíos de Python
 */

document.addEventListener('DOMContentLoaded', function() {
    // Variables globales para el seguimiento del progreso
    const TOTAL_CHALLENGES = 10;
    let currentChallenge = 1;
    let completedChallenges = [];
    let currentLevel = 'principiante';
    
    // Inicializar el editor de código Ace
    let editor = null;
    if (document.getElementById('python-editor')) {
        editor = ace.edit('python-editor');
        editor.setTheme('ace/theme/monokai');
        editor.session.setMode('ace/mode/python');
        editor.setValue(`# Escribe tu código aquí\n\n`);
        editor.clearSelection();
        editor.setShowPrintMargin(false);
        editor.setOptions({
            enableBasicAutocompletion: true,
            enableSnippets: true,
            enableLiveAutocompletion: true
        });
    }
    
    // Botones para ejecutar/enviar código
    const runCodeBtn = document.getElementById('runCode');
    const submitCodeBtn = document.getElementById('submitCode');
    const pythonConsole = document.getElementById('python-console');
    
    if (runCodeBtn) {
        runCodeBtn.addEventListener('click', function() {
            if (!editor) return;
            
            const code = editor.getValue();
            executePythonCode(code);
        });
    }
    
    if (submitCodeBtn) {
        submitCodeBtn.addEventListener('click', function() {
            if (!editor) return;
            
            const code = editor.getValue();
            submitPythonCode(code);
        });
    }
    
    // Función para ejecutar código Python (simulado)
    function executePythonCode(code) {
        if (!pythonConsole) return;
        
        // En un entorno real, esto llamaría a un backend para ejecutar el código
        // Por ahora, simularemos la salida
        
        pythonConsole.innerHTML = '';
        pythonConsole.innerHTML += '> Ejecutando código...\n\n';
        
        // Simulamos la ejecución del código
        setTimeout(() => {
            try {
                // Lógica mejorada para detectar diferentes patrones según el desafío actual
                let output = '';
                switch(currentChallenge) {
                    case 1:
                        if (code.includes('print') && code.toLowerCase().includes('hola mundo')) {
                            output = '¡Hola Mundo desde Python!\n';
                        } else {
                            output = simulateOutput(code);
                        }
                        break;
                    case 2:
                        if (code.includes('=') && code.includes('print')) {
                            output = simulateVariableOutput(code);
                        } else {
                            output = simulateOutput(code);
                        }
                        break;
                    case 3:
                        if (code.includes('+') || code.includes('-') || code.includes('*') || code.includes('/')) {
                            output = simulateOperatorsOutput(code);
                        } else {
                            output = simulateOutput(code);
                        }
                        break;
                    default:
                        output = simulateOutput(code);
                }
                
                pythonConsole.innerHTML += output;
            } catch (err) {
                pythonConsole.innerHTML += `Error: ${err.message}\n`;
            }
            
            pythonConsole.innerHTML += '\n> Ejecución completada.\n';
            pythonConsole.scrollTop = pythonConsole.scrollHeight;
        }, 500);
    }
    
    // Funciones auxiliares para simular salidas según el tipo de código
    function simulateOutput(code) {
        if (code.includes('print')) {
            // Extraer el contenido entre paréntesis de un print
            const match = code.match(/print\s*\(\s*["'](.*)["']\s*\)/);
            return match ? `${match[1]}\n` : 'Output simulado\n';
        }
        return 'Output simulado. En un entorno real, este código sería ejecutado en el servidor.\n';
    }
    
    function simulateVariableOutput(code) {
        // Intentar detectar una asignación de variable y un print
        const variableMatch = code.match(/(\w+)\s*=\s*["']?([\w\s]+)["']?/);
        const printMatch = code.match(/print\s*\(\s*(\w+)\s*\)/);
        
        if (variableMatch && printMatch && variableMatch[1] === printMatch[1]) {
            return `${variableMatch[2]}\n`;
        }
        return simulateOutput(code);
    }
    
    function simulateOperatorsOutput(code) {
        // Intentar detectar operaciones aritméticas simples
        if (code.includes('print') && (code.includes('+') || code.includes('-') || code.includes('*') || code.includes('/'))) {
            const match = code.match(/print\s*\(\s*([\d\+\-\*\/\s]+)\s*\)/);
            if (match) {
                try {
                    // Evaluar la expresión aritmética (cuidado con eval en producción real)
                    return `${eval(match[1])}\n`;
                } catch (e) {
                    return 'Error en la expresión\n';
                }
            }
        }
        return simulateOutput(code);
    }
    
    // Función para enviar código para evaluación (simulado)
    function submitPythonCode(code) {
        if (!pythonConsole) return;
        
        pythonConsole.innerHTML = '';
        pythonConsole.innerHTML += '> Evaluando código...\n\n';
        
        // Simulamos la evaluación
        setTimeout(() => {
            let isCorrect = false;
            let feedback = '';
            
            // Validar según el desafío actual
            switch(currentChallenge) {
                case 1:
                    isCorrect = code.includes('print') && code.includes('¡Hola Mundo desde Python!');
                    feedback = isCorrect ? 
                        '✅ ¡Excelente! Has mostrado tu primer mensaje en Python.\n' : 
                        '❌ Casi lo tienes. Asegúrate de imprimir exactamente "¡Hola Mundo desde Python!".\n';
                    break;
                case 2:
                    isCorrect = code.includes('=') && code.includes('print') && code.match(/\w+\s*=\s*["'].*["']/);
                    feedback = isCorrect ? 
                        '✅ ¡Muy bien! Has creado tu primera variable en Python.\n' : 
                        '❌ Revisa tu código. Debes crear una variable con texto y mostrarla.\n';
                    break;
                case 3:
                    isCorrect = code.includes('print') && (code.includes('+') || code.includes('-') || code.includes('*') || code.includes('/'));
                    feedback = isCorrect ? 
                        '✅ ¡Excelente trabajo! Has aplicado operadores correctamente.\n' : 
                        '❌ Revisa tu código. Debes utilizar al menos un operador matemático.\n';
                    break;
                default:
                    isCorrect = code.length > 10; // Criterio genérico
                    feedback = isCorrect ? 
                        '✅ ¡Bien hecho! Tu solución ha sido aceptada.\n' : 
                        '❌ Tu solución no cumple con los requisitos esperados.\n';
            }
            
            pythonConsole.innerHTML += feedback;
            
            if (isCorrect) {
                // Marcar el desafío actual como completado
                completeChallenge(currentChallenge);
            } else {
                pythonConsole.innerHTML += 'Intenta de nuevo. Puedes pedir pistas si necesitas ayuda.\n';
            }
            
            pythonConsole.innerHTML += '\n> Evaluación completada.\n';
            pythonConsole.scrollTop = pythonConsole.scrollHeight;
        }, 800);
    }
    
    // Función para completar un desafío
    function completeChallenge(challengeNumber) {
        if (completedChallenges.includes(challengeNumber)) return;
        
        completedChallenges.push(challengeNumber);
        
        // Actualizar UI para mostrar el desafío como completado
        const challengeItem = document.querySelector('#python-challenges .challenge-item:nth-child(' + challengeNumber + ')');
        if (challengeItem) {
            challengeItem.classList.add('completed');
            challengeItem.classList.remove('active');
            const challengeNumberElem = challengeItem.querySelector('.challenge-number');
            if (challengeNumberElem) {
                challengeNumberElem.innerHTML = '✓';
            }
        }
        
        // Calcular progreso
        const progress = Math.round((completedChallenges.length / TOTAL_CHALLENGES) * 100);
        const progressBar = document.querySelector('#python-challenges .progress .progress-bar');
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
            progressBar.innerHTML = `${progress}%`;
        }
        
        // Mostrar la celebración y desbloquear el siguiente desafío
        if (challengeNumber < TOTAL_CHALLENGES) {
            // Desbloquear el siguiente desafío
            currentChallenge = challengeNumber + 1;
            unlockChallenge(currentChallenge);
            showCelebration('¡Desafío Completado!', `Has desbloqueado el desafío ${currentChallenge}`, 'success');
            
            // Actualizar estado de los botones de navegación
            updateNavigationButtons(false);
        } else {
            // Completó todos los desafíos del nivel actual
            completeLevel(currentLevel);
        }
    }
    
    // Función para desbloquear un desafío
    function unlockChallenge(challengeNumber) {
        const nextChallenge = document.querySelector('#python-challenges .challenge-item:nth-child(' + challengeNumber + ')');
        if (nextChallenge) {
            if (nextChallenge.classList.contains('locked')) {
                nextChallenge.classList.remove('locked');
                const lockIcon = nextChallenge.querySelector('i.fa-lock');
                if (lockIcon) {
                    lockIcon.remove();
                }
            }
            
            // Marcar como activo
            document.querySelectorAll('#python-challenges .challenge-item').forEach(item => {
                item.classList.remove('active');
            });
            nextChallenge.classList.add('active');
            
            // Actualizar las instrucciones para el nuevo desafío
            updateChallengeContent(challengeNumber);
            
            // Actualizar estado de los botones de navegación
            updateNavigationButtons(false);
        }
    }
    
    // Función para actualizar el contenido del desafío según el número
    function updateChallengeContent(challengeNumber) {
        const titleElem = document.getElementById('challenge-title');
        const descriptionElem = document.getElementById('challenge-description');
        const objectiveElem = document.querySelector('.alert-primary strong');
        
        if (titleElem && descriptionElem && objectiveElem) {
            switch(challengeNumber) {
                case 1:
                    titleElem.textContent = 'Introducción a Python';
                    descriptionElem.textContent = 'Bienvenido a tu primer desafío de Python. Vamos a empezar creando un programa simple.';
                    objectiveElem.nextSibling.textContent = ' Escribe un programa que imprima "¡Hola Mundo desde Python!" en la consola.';
                    if (editor) editor.setValue(`# Escribe tu código aquí\n\n`);
                    break;
                case 2:
                    titleElem.textContent = 'Variables y tipos de datos';
                    descriptionElem.textContent = 'Aprende a usar variables para almacenar información en tu programa.';
                    objectiveElem.nextSibling.textContent = ' Crea una variable con tu nombre y muéstrala en la consola.';
                    if (editor) editor.setValue(`# Crea una variable con tu nombre\n\n# Muestra el valor de la variable\n`);
                    break;
                case 3:
                    titleElem.textContent = 'Operadores y expresiones';
                    descriptionElem.textContent = 'Utiliza operadores matemáticos para realizar cálculos en Python.';
                    objectiveElem.nextSibling.textContent = ' Escribe un programa que realice una operación matemática y muestre el resultado.';
                    if (editor) editor.setValue(`# Realiza una operación matemática\n\n# Muestra el resultado\n`);
                    break;
                case 4:
                    titleElem.textContent = 'Estructuras condicionales';
                    descriptionElem.textContent = 'Aprende a tomar decisiones en tu código con estructuras if-else.';
                    objectiveElem.nextSibling.textContent = ' Escribe un programa que verifique si un número es positivo o negativo.';
                    if (editor) editor.setValue(`# Declara una variable con un número\n\n# Verifica si es positivo o negativo\n`);
                    break;
                default:
                    titleElem.textContent = `Desafío ${challengeNumber}`;
                    descriptionElem.textContent = 'Completa este desafío para seguir avanzando.';
                    objectiveElem.nextSibling.textContent = ' Escribe un programa que cumpla con los requisitos indicados.';
            }
            
            // También actualizar el indicador de desafío actual
            const currentBadge = document.querySelector('.badge.rounded-pill.bg-primary');
            if (currentBadge) {
                currentBadge.textContent = `${challengeNumber}/10`;
            }
            
            // Y el nombre del desafío actual
            const currentChallengeName = document.querySelector('h6.mb-0 + small.text-muted');
            if (currentChallengeName && titleElem) {
                currentChallengeName.textContent = titleElem.textContent;
            }
            
            // Limpiar la consola
            if (pythonConsole) {
                pythonConsole.innerHTML = '> Nuevo desafío cargado. ¡Buena suerte!\n';
            }
        }
    }
    
    // Función para manejar la celebración cuando se completa un desafío
    function showCelebration(title, message, icon) {
        // Reproducir sonido de celebración si existe
        const successSound = document.getElementById('success-sound');
        if (successSound) {
            successSound.currentTime = 0;
            successSound.play().catch(e => console.log('No se pudo reproducir el sonido', e));
        }
        
        // Animación de confeti
        if (typeof confetti !== 'undefined') {
            const end = Date.now() + 3000;
            
            const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'];
            
            (function frame() {
                confetti({
                    particleCount: 4,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: colors
                });
                confetti({
                    particleCount: 4,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: colors
                });
                
                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        }
        
        // Usar SweetAlert2 para mostrar una animación de celebración
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            showConfirmButton: true,
            confirmButtonText: '¡Seguir adelante!',
            allowOutsideClick: false,
            backdrop: `
                rgba(0,123,255,0.4)
                url("/assets/images/confetti.gif")
                center top
                no-repeat
            `,
            didOpen: () => {
                // Animación de partículas si existe la biblioteca
                if (typeof particlesJS !== 'undefined') {
                    particlesJS('confetti-container', {
                        particles: {
                            number: { value: 80 },
                            color: { value: colors },
                            shape: {
                                type: "circle",
                            },
                            opacity: {
                                value: 0.5,
                                random: false
                            },
                            size: {
                                value: 5,
                                random: true
                            },
                            move: {
                                enable: true,
                                speed: 6
                            }
                        }
                    });
                    
                    // Mostrar el contenedor de confeti
                    const confettiContainer = document.getElementById('confetti-container');
                    if (confettiContainer) {
                        confettiContainer.style.display = 'block';
                    }
                }
            },
            willClose: () => {
                // Ocultar el contenedor de confeti al cerrar
                const confettiContainer = document.getElementById('confetti-container');
                if (confettiContainer) {
                    confettiContainer.style.display = 'none';
                }
            }
        });
    }
    
    // Función para completar un nivel y mostrar certificado
    function completeLevel(level) {
        // Mostrar una celebración más grande
        Swal.fire({
            title: '¡Felicidades!',
            html: `Has completado todos los desafíos de nivel <strong>${level}</strong>.<br>¡Has ganado un certificado!`,
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Ver certificado',
            cancelButtonText: 'Continuar',
            allowOutsideClick: false,
            backdrop: `
                rgba(0,123,255,0.4)
                url("/assets/images/confetti.gif")
                center top
                no-repeat
            `
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar el certificado
                showCertificate(level);
            } else {
                // Avanzar al siguiente nivel si existe
                if (level === 'principiante') {
                    switchLevel('intermedio');
                } else if (level === 'intermedio') {
                    switchLevel('avanzado');
                }
            }
        });
    }
    
    // Función para mostrar el certificado
    function showCertificate(level) {
        // En un entorno real, esto podría generarse en el backend
        // Aquí simulamos la visualización de un certificado
        Swal.fire({
            title: 'Certificado de Logro',
            html: `
                <div class="certificate">
                    <div class="certificate-header">
                        <img src="/assets/images/mitai-logo-128x128.svg" alt="MitaiCode" width="50">
                        <h2>MitaiCode Academy</h2>
                    </div>
                    <div class="certificate-body">
                        <p>Este certificado se otorga a</p>
                        <h3>${document.querySelector('.dropdown-toggle')?.textContent?.trim() || 'Estudiante'}</h3>
                        <p>Por completar exitosamente todos los desafíos de</p>
                        <h4>Python - Nivel ${level.charAt(0).toUpperCase() + level.slice(1)}</h4>
                        <p>Fecha: ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <button id="downloadCertificate" class="btn btn-primary">Descargar Certificado</button>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Cerrar',
            width: '600px',
            allowOutsideClick: true
        });
        
        // En un entorno real, aquí se implementaría la descarga real del certificado
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
    
    // Función para cambiar de nivel
    function switchLevel(newLevel) {
        currentLevel = newLevel;
        
        // Actualizar los botones de nivel
        const levelButtons = document.querySelectorAll('.btn-group [data-level]');
        levelButtons.forEach(btn => {
            btn.classList.remove('active', 'btn-primary');
            btn.classList.add('btn-outline-primary');
            
            if (btn.getAttribute('data-level') === newLevel) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('active', 'btn-primary');
            }
        });
        
        // Reiniciar el progreso para el nuevo nivel
        currentChallenge = 1;
        completedChallenges = [];
        
        // Actualizar la UI para el nuevo nivel
        // Aquí se podrían cargar nuevos desafíos del servidor
        Swal.fire({
            title: `Nivel ${newLevel}`,
            text: `¡Has avanzado al nivel ${newLevel}! Nuevos desafíos te esperan.`,
            icon: 'info',
            confirmButtonText: 'Comenzar'
        }).then(() => {
            // Resetear todos los desafíos
            document.querySelectorAll('.challenge-item').forEach((item, index) => {
                // Primer desafío activo, el resto bloqueados
                item.classList.remove('active', 'completed');
                if (index === 0) {
                    item.classList.add('active');
                } else {
                    item.classList.add('locked');
                    
                    // Añadir icono de candado si no lo tiene
                    if (!item.querySelector('i.fa-lock')) {
                        const lockIcon = document.createElement('i');
                        lockIcon.className = 'fas fa-lock';
                        item.appendChild(lockIcon);
                    }
                }
            });
            
            // Resetear la barra de progreso
            const progressBar = document.querySelector('.progress .progress-bar');
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
                progressBar.innerHTML = '0%';
            }
            
            // Actualizar contenido para el primer desafío del nuevo nivel
            updateChallengeContent(1);
        });
    }
    
    // Manejo del modal del tutor IA
    const askAIBtn = document.getElementById('askAI');
    const aiTutorModal = document.getElementById('aiTutorModal');
    const sendToAIBtn = document.getElementById('sendToAI');
    const aiTutorInput = document.getElementById('aiTutorInput');
    const chatContainer = document.querySelector('.chat-container');
    
    if (askAIBtn && aiTutorModal) {
        askAIBtn.addEventListener('click', function() {
            // Usar Bootstrap para mostrar el modal
            const modal = new bootstrap.Modal(aiTutorModal);
            modal.show();
        });
    }
    
    if (sendToAIBtn && aiTutorInput && chatContainer) {
        sendToAIBtn.addEventListener('click', sendQuestionToAI);
        aiTutorInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendQuestionToAI();
            }
        });
    }
    
    function sendQuestionToAI() {
        const question = aiTutorInput.value.trim();
        if (!question) return;
        
        // Agregar mensaje del usuario
        addChatMessage(question, 'user');
        aiTutorInput.value = '';
        
        // Simular respuesta del AI
        setTimeout(() => {
            // Lógica simple para simular respuestas
            let answer = '';
            
            if (question.toLowerCase().includes('print')) {
                answer = 'La función `print()` se utiliza para mostrar texto en la consola. Por ejemplo:\n\n```python\nprint("¡Hola Mundo desde Python!")\n```\n\nEsto mostrará el texto entre comillas en la consola.';
            } else if (question.toLowerCase().includes('hola')) {
                answer = '¡Hola! Estoy aquí para ayudarte con tu código de Python. ¿Tienes alguna pregunta específica sobre el desafío actual?';
            } else if (question.toLowerCase().includes('error')) {
                answer = 'Para resolver errores comunes en Python:\n\n1. Verifica la indentación\n2. Asegúrate de que los paréntesis estén cerrados correctamente\n3. Revisa que las comillas abran y cierren correctamente\n4. Comprueba la ortografía de los nombres de funciones';
            } else {
                answer = 'Gracias por tu pregunta. Para el desafío actual, recuerda que necesitas utilizar la función `print()` para mostrar texto en la consola. El objetivo es imprimir "¡Hola Mundo desde Python!".';
            }
            
            addChatMessage(answer, 'ai');
        }, 1000);
    }
    
    function addChatMessage(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}-message`;
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'chat-bubble';
        
        // Convertir markdown simple a HTML
        let formattedMessage = message.replace(/```([^`]+)```/g, '<pre>$1</pre>');
        formattedMessage = formattedMessage.split('\n').map(line => `<p>${line}</p>`).join('');
        
        bubbleDiv.innerHTML = formattedMessage;
        messageDiv.appendChild(bubbleDiv);
        
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    // Obtener pista
    const getHintBtn = document.getElementById('getHint');
    if (getHintBtn) {
        getHintBtn.addEventListener('click', function() {
            let hintText = '';
            switch(currentChallenge) {
                case 1:
                    hintText = 'Para este desafío, puedes usar:<br><br><code>print("¡Hola Mundo desde Python!")</code>';
                    break;
                case 2:
                    hintText = 'Para crear una variable con tu nombre:<br><br><code>nombre = "Tu nombre"<br>print(nombre)</code>';
                    break;
                case 3:
                    hintText = 'Para realizar una operación matemática:<br><br><code>resultado = 5 + 3<br>print(resultado)</code>';
                    break;
                case 4:
                    hintText = 'Para verificar si un número es positivo o negativo:<br><br><code>numero = 10<br>if numero > 0:<br>&nbsp;&nbsp;print("Es positivo")<br>else:<br>&nbsp;&nbsp;print("Es negativo")</code>';
                    break;
                default:
                    hintText = 'Piensa en lo que has aprendido hasta ahora y aplícalo a este nuevo desafío.';
            }
            
            Swal.fire({
                title: 'Pista',
                html: hintText,
                icon: 'info',
                confirmButtonText: '¡Entendido!'
            });
        });
    }
    
    // Selector de nivel en desafíos
    const levelButtons = document.querySelectorAll('.btn-group [data-level]');
    
    levelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const newLevel = this.getAttribute('data-level');
            
            // Si ya estamos en ese nivel, no hacemos nada
            if (newLevel === currentLevel) return;
            
            // Confirmar antes de cambiar de nivel si hay progreso
            if (completedChallenges.length > 0) {
                Swal.fire({
                    title: '¿Cambiar de nivel?',
                    text: `Tienes progreso en el nivel actual. Cambiar te hará perder ese progreso. ¿Estás seguro?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'No, quedarme'
                }).then((result) => {
                    if (result.isConfirmed) {
                        switchLevel(newLevel);
                    }
                });
            } else {
                switchLevel(newLevel);
            }
        });
    });
    
    // Añadir navegación entre desafíos para Python
    const pythonChallengeItems = document.querySelectorAll('#python-challenges .challenge-item');
    if (pythonChallengeItems.length > 0) {
        pythonChallengeItems.forEach((item, index) => {
            item.addEventListener('click', function() {
                // Ignorar si está bloqueado
                if (this.classList.contains('locked')) {
                    Swal.fire({
                        title: 'Desafío bloqueado',
                        text: 'Completa los desafíos anteriores para desbloquear este.',
                        icon: 'info'
                    });
                    return;
                }
                
                // Actualizar desafío actual
                currentChallenge = index + 1;
                
                // Actualizar UI
                updateChallengeUI(currentChallenge, false);
            });
        });
    }
    
    // Añadir navegación entre desafíos para Prompts
    const promptChallengeItems = document.querySelectorAll('#ai-challenges .challenge-item');
    if (promptChallengeItems.length > 0) {
        promptChallengeItems.forEach((item, index) => {
            item.addEventListener('click', function() {
                // Ignorar si está bloqueado
                if (this.classList.contains('locked')) {
                    Swal.fire({
                        title: 'Desafío bloqueado',
                        text: 'Completa los desafíos anteriores para desbloquear este.',
                        icon: 'info'
                    });
                    return;
                }
                
                // Actualizar desafío actual
                currentPromptChallenge = index + 1;
                
                // Actualizar UI
                updateChallengeUI(currentPromptChallenge, true);
            });
        });
    }
    
    // Añadir botones de navegación para ambos tipos de desafíos
    const addNavigationButtons = () => {
        // Para Python Challenges
        const pythonInstructions = document.querySelector('#python-challenges #challenge-instructions');
        if (pythonInstructions) {
            const navButtons = document.createElement('div');
            navButtons.className = 'mt-3 d-flex justify-content-between challenge-navigation';
            navButtons.innerHTML = `
                <button class="btn btn-sm btn-outline-primary" id="prevChallenge" ${currentChallenge <= 1 ? 'disabled' : ''}>
                    <i class="fas fa-arrow-left me-1"></i> Anterior
                </button>
                <button class="btn btn-sm btn-outline-primary" id="nextChallenge" ${currentChallenge >= TOTAL_CHALLENGES || document.querySelector('#python-challenges .challenge-item:nth-child(' + (currentChallenge + 1) + ')').classList.contains('locked') ? 'disabled' : ''}>
                    Siguiente <i class="fas fa-arrow-right ms-1"></i>
                </button>
            `;
            pythonInstructions.appendChild(navButtons);
            
            // Configurar eventos para los botones
            document.getElementById('prevChallenge')?.addEventListener('click', function() {
                if (currentChallenge > 1) {
                    currentChallenge--;
                    updateChallengeUI(currentChallenge, false);
                    this.disabled = currentChallenge <= 1;
                    document.getElementById('nextChallenge').disabled = false;
                }
            });
            
            document.getElementById('nextChallenge')?.addEventListener('click', function() {
                const nextChallengeElem = document.querySelector('#python-challenges .challenge-item:nth-child(' + (currentChallenge + 1) + ')');
                if (nextChallengeElem && !nextChallengeElem.classList.contains('locked')) {
                    currentChallenge++;
                    updateChallengeUI(currentChallenge, false);
                    this.disabled = currentChallenge >= TOTAL_CHALLENGES || 
                        document.querySelector('#python-challenges .challenge-item:nth-child(' + (currentChallenge + 1) + ')').classList.contains('locked');
                    document.getElementById('prevChallenge').disabled = false;
                }
            });
        }
        
        // Para AI Prompt Challenges
        const promptInstructions = document.querySelector('#ai-challenges #prompt-challenge-instructions');
        if (promptInstructions) {
            const navButtons = document.createElement('div');
            navButtons.className = 'mt-3 d-flex justify-content-between challenge-navigation';
            navButtons.innerHTML = `
                <button class="btn btn-sm btn-outline-primary" id="prevPromptChallenge" ${currentPromptChallenge <= 1 ? 'disabled' : ''}>
                    <i class="fas fa-arrow-left me-1"></i> Anterior
                </button>
                <button class="btn btn-sm btn-outline-primary" id="nextPromptChallenge" ${currentPromptChallenge >= TOTAL_PROMPT_CHALLENGES || document.querySelector('#ai-challenges .challenge-item:nth-child(' + (currentPromptChallenge + 1) + ')').classList.contains('locked') ? 'disabled' : ''}>
                    Siguiente <i class="fas fa-arrow-right ms-1"></i>
                </button>
            `;
            promptInstructions.appendChild(navButtons);
            
            // Configurar eventos para los botones
            document.getElementById('prevPromptChallenge')?.addEventListener('click', function() {
                if (currentPromptChallenge > 1) {
                    currentPromptChallenge--;
                    updateChallengeUI(currentPromptChallenge, true);
                    this.disabled = currentPromptChallenge <= 1;
                    document.getElementById('nextPromptChallenge').disabled = false;
                }
            });
            
            document.getElementById('nextPromptChallenge')?.addEventListener('click', function() {
                const nextChallengeElem = document.querySelector('#ai-challenges .challenge-item:nth-child(' + (currentPromptChallenge + 1) + ')');
                if (nextChallengeElem && !nextChallengeElem.classList.contains('locked')) {
                    currentPromptChallenge++;
                    updateChallengeUI(currentPromptChallenge, true);
                    this.disabled = currentPromptChallenge >= TOTAL_PROMPT_CHALLENGES || 
                        document.querySelector('#ai-challenges .challenge-item:nth-child(' + (currentPromptChallenge + 1) + ')').classList.contains('locked');
                    document.getElementById('prevPromptChallenge').disabled = false;
                }
            });
        }
    };
    
    // Llamar a la función para añadir botones de navegación
    addNavigationButtons();
});

/**
 * MitaiCode - JavaScript para los Desafíos de Prompts de IA
 */
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales para el seguimiento del progreso
    const TOTAL_PROMPT_CHALLENGES = 10;
    let currentPromptChallenge = 1;
    let completedPromptChallenges = [2]; // Comenzamos con el desafío 2 completado para ejemplo
    let currentPromptLevel = 'principiante';
    
    // Botones para probar/enviar prompts
    const testPromptBtn = document.getElementById('testPrompt');
    const submitPromptBtn = document.getElementById('submitPrompt');
    const promptEditor = document.getElementById('prompt-editor');
    const aiResponse = document.getElementById('ai-response');
    const seeExamplesBtn = document.getElementById('seeExamples');
    const getPromptHintBtn = document.getElementById('getPromptHint');
    
    if (testPromptBtn && promptEditor && aiResponse) {
        testPromptBtn.addEventListener('click', function() {
            if (!promptEditor.value.trim()) {
                Swal.fire({
                    title: 'Prompt vacío',
                    text: 'Por favor, escribe un prompt antes de probarlo.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            testPrompt(promptEditor.value);
        });
    }
    
    if (submitPromptBtn && promptEditor) {
        submitPromptBtn.addEventListener('click', function() {
            if (!promptEditor.value.trim()) {
                Swal.fire({
                    title: 'Prompt vacío',
                    text: 'Por favor, escribe un prompt antes de enviarlo.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            submitPrompt(promptEditor.value);
        });
    }
    
    if (seeExamplesBtn) {
        seeExamplesBtn.addEventListener('click', function() {
            // Mostrar modal con ejemplos
            const modal = new bootstrap.Modal(document.getElementById('promptExamplesModal'));
            modal.show();
        });
    }
    
    if (getPromptHintBtn) {
        getPromptHintBtn.addEventListener('click', function() {
            // Mostrar pista según el desafío actual
            let hintText = '';
            switch(currentPromptChallenge) {
                case 1:
                    hintText = 'Para este desafío, intenta estructurar tu prompt así:<br><br>' +
                               '<code>Actúa como un [rol específico] para [audiencia]. ' +
                               'Usa un tono [tipo de tono] y explica [concepto] usando [tipo de ejemplo].</code>';
                    break;
                case 3:
                    hintText = 'Las instrucciones claras incluyen:<br>' +
                               '- Pasos numerados<br>' +
                               '- Límites claros (longitud, formato)<br>' +
                               '- Criterios específicos de lo que quieres';
                    break;
                case 4:
                    hintText = 'Para especificar formatos de salida puedes usar:<br>' +
                               '- Tablas<br>' +
                               '- Listas numeradas o con viñetas<br>' +
                               '- Formato JSON<br>' +
                               '- Markdown específico';
                    break;
                default:
                    hintText = 'Piensa en los elementos clave de un buen prompt: rol, audiencia, tono, contenido específico y formato deseado.';
            }
            
            Swal.fire({
                title: 'Pista',
                html: hintText,
                icon: 'info',
                confirmButtonText: '¡Entendido!'
            });
        });
    }
    
    // Función para probar el prompt (simulado)
    function testPrompt(promptText) {
        if (!aiResponse) return;
        
        // Mostrar estado de carga
        aiResponse.innerHTML = '<p class="text-muted"><i class="fas fa-spinner fa-spin me-2"></i>La IA está procesando tu prompt...</p>';
        
        // Simular delay en la respuesta
        setTimeout(() => {
            let responseText = '';
            
            // Generar respuesta simulada según el contenido del prompt
            if (promptText.toLowerCase().includes('tutor') && 
                promptText.toLowerCase().includes('programación') && 
                promptText.toLowerCase().includes('niñ')) {
                
                responseText = '<p>¡Hola chicos y chicas! 👋 Soy Codi, ¡tu amigable tutor de programación! 🤖</p>' +
                               '<p>Estoy aquí para ayudarte a descubrir el mágico mundo de la programación. ¡Es como darle instrucciones a un robot para que haga cosas increíbles!</p>' +
                               '<p>¿Te gustaría aprender a hacer que una computadora dibuje formas, cuente historias o incluso cree juegos? ¡Yo te puedo enseñar!</p>' +
                               '<p>Aquí hay un ejemplo súper sencillo:</p>' +
                               '<pre>print("¡Hola amigo!") 😊</pre>' +
                               '<p>¡Esta línea hace que la computadora te salude! ¿No es genial?</p>';
                
            } else if (promptText.toLowerCase().includes('ejemplo') || 
                       promptText.toLowerCase().includes('muestra')) {
                
                responseText = '<p>Aquí tienes un ejemplo según lo solicitado:</p>' +
                               '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pulvinar risus non risus hendrerit venenatis. Pellentesque sit amet hendrerit risus, sed porttitor quam.</p>' +
                               '<p>Espero que este ejemplo sea útil. ¿Necesitas alguna modificación?</p>';
                
            } else {
                responseText = '<p>He recibido tu prompt, pero necesito más detalles específicos para darte la mejor respuesta posible.</p>' +
                               '<p>Intenta especificar:</p>' +
                               '<ul>' +
                               '<li>Qué rol debo adoptar</li>' +
                               '<li>A quién va dirigido el contenido</li>' +
                               '<li>Qué tono o estilo prefieres</li>' +
                               '<li>Qué información específica necesitas</li>' +
                               '</ul>' +
                               '<p>Con estas pautas, podré ayudarte mucho mejor.</p>';
            }
            
            aiResponse.innerHTML = responseText;
        }, 1500);
    }
    
    // Función para enviar el prompt para evaluación
    function submitPrompt(promptText) {
        if (!aiResponse) return;
        
        // Mostrar estado de carga
        aiResponse.innerHTML = '<p class="text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Evaluando tu prompt...</p>';
        
        // Simular evaluación
        setTimeout(() => {
            let isCorrect = false;
            let feedback = '';
            let score = 0; 
            let detailedFeedback = [];
            
            // Evaluar según el desafío actual
            switch(currentPromptChallenge) {
                case 1:
                    // Verificar si el prompt incluye elementos clave para un tutor de programación para niños
                    const hasRol = promptText.toLowerCase().includes('tutor') && promptText.toLowerCase().includes('programación');
                    const hasAudience = promptText.toLowerCase().includes('niñ');
                    const hasTone = promptText.toLowerCase().includes('amigable') || 
                                    promptText.toLowerCase().includes('simple') || 
                                    promptText.toLowerCase().includes('entusiasta') ||
                                    promptText.toLowerCase().includes('divertido');
                    const hasExamples = promptText.toLowerCase().includes('ejemplo') || 
                                        promptText.toLowerCase().includes('muestra');
                    
                    // Agregar cada punto de evaluación
                    detailedFeedback.push(hasRol ? 
                        '✅ <strong>Rol definido</strong>: Has especificado correctamente el rol de tutor de programación.' : 
                        '❌ <strong>Rol indefinido</strong>: No has especificado claramente que debe actuar como un tutor de programación.');
                    
                    detailedFeedback.push(hasAudience ? 
                        '✅ <strong>Audiencia clara</strong>: Has definido que el contenido es para niños.' : 
                        '❌ <strong>Audiencia indefinida</strong>: No has especificado que el contenido es para niños.');
                    
                    detailedFeedback.push(hasTone ? 
                        '✅ <strong>Tono adecuado</strong>: Has indicado que use un tono amigable/simple/entusiasta.' : 
                        '❌ <strong>Tono no especificado</strong>: No has definido el tono que debe usar la IA.');
                    
                    detailedFeedback.push(hasExamples ? 
                        '✅ <strong>Solicitud de ejemplos</strong>: Has pedido que incluya ejemplos o muestras.' : 
                        '❌ <strong>Sin ejemplos</strong>: No has pedido que incluya ejemplos concretos.');
                    
                    // Calcular puntuación
                    score = (hasRol ? 25 : 0) + (hasAudience ? 25 : 0) + (hasTone ? 25 : 0) + (hasExamples ? 25 : 0);
                    
                    // Criterio de aprobación
                    isCorrect = score >= 75; // Al menos 3 de 4 criterios
                    
                    feedback = isCorrect ? 
                        `<div class="alert alert-success">
                            <h5>¡Excelente prompt! (${score}% de efectividad)</h5>
                            <p>Has incluido la mayoría de los elementos clave para un prompt efectivo.</p>
                        </div>` : 
                        `<div class="alert alert-danger">
                            <h5>Tu prompt necesita mejorar (${score}% de efectividad)</h5>
                            <p>Asegúrate de especificar los elementos clave para un prompt efectivo.</p>
                        </div>`;
                    break;
                
                case 2:
                    // Verificar elementos para el rol de historiador
                    const hasHistorianRole = promptText.toLowerCase().includes('historiador') || 
                                             promptText.toLowerCase().includes('profesor de historia');
                    const hasEventMention = promptText.toLowerCase().includes('evento') || 
                                            promptText.toLowerCase().includes('suceso') || 
                                            promptText.toLowerCase().includes('acontecimiento');
                    const hasHistoricalDetail = promptText.toLowerCase().includes('detalle') || 
                                               promptText.toLowerCase().includes('explicar');
                    const hasToneOrPerspective = promptText.toLowerCase().includes('objetivo') || 
                                                promptText.toLowerCase().includes('imparcial') || 
                                                promptText.toLowerCase().includes('perspectiva');
                    
                    detailedFeedback.push(hasHistorianRole ? 
                        '✅ <strong>Rol de historiador</strong>: Has definido correctamente el rol.' : 
                        '❌ <strong>Rol indefinido</strong>: No has especificado que debe actuar como historiador.');
                    
                    detailedFeedback.push(hasEventMention ? 
                        '✅ <strong>Evento histórico</strong>: Has solicitado la explicación de un evento/suceso.' : 
                        '❌ <strong>Sin evento específico</strong>: No has mencionado que debe explicar un evento histórico.');
                    
                    detailedFeedback.push(hasHistoricalDetail ? 
                        '✅ <strong>Nivel de detalle</strong>: Has pedido detalles o una explicación clara.' : 
                        '❌ <strong>Sin especificar detalle</strong>: No has indicado el nivel de detalle deseado.');
                    
                    detailedFeedback.push(hasToneOrPerspective ? 
                        '✅ <strong>Tono/perspectiva</strong>: Has indicado cómo debe abordar el tema.' : 
                        '❌ <strong>Sin perspectiva</strong>: No has especificado el tono o perspectiva para abordar el tema.');
                    
                    // Calcular puntuación
                    score = (hasHistorianRole ? 25 : 0) + (hasEventMention ? 25 : 0) + 
                            (hasHistoricalDetail ? 25 : 0) + (hasToneOrPerspective ? 25 : 0);
                    
                    // Criterio de aprobación
                    isCorrect = score >= 75; // Al menos 3 de 4 criterios
                    
                    feedback = isCorrect ? 
                        `<div class="alert alert-success">
                            <h5>¡Excelente prompt! (${score}% de efectividad)</h5>
                            <p>Has definido adecuadamente el rol y contexto para el historiador.</p>
                        </div>` : 
                        `<div class="alert alert-danger">
                            <h5>Tu prompt necesita mejorar (${score}% de efectividad)</h5>
                            <p>Asegúrate de especificar claramente el rol, el evento histórico y cómo debe abordarlo.</p>
                        </div>`;
                    break;
                
                case 3:
                    // Verificar instrucciones claras con pasos
                    const hasSteps = promptText.toLowerCase().includes('paso') || 
                                   (promptText.includes('1') && promptText.includes('2'));
                    const hasNumberedSteps = (promptText.includes('1.') || promptText.includes('1)')) && 
                                            (promptText.includes('2.') || promptText.includes('2)'));
                    const hasDetailedInstructions = promptText.toLowerCase().includes('detalle') || 
                                                  promptText.toLowerCase().includes('específico');
                    const hasStudyPlanMention = promptText.toLowerCase().includes('plan') && 
                                               promptText.toLowerCase().includes('estudio');
                    
                    detailedFeedback.push(hasSteps ? 
                        '✅ <strong>Pasos incluidos</strong>: Has mencionado que requieres pasos secuenciales.' : 
                        '❌ <strong>Sin pasos</strong>: No has indicado que necesitas instrucciones paso a paso.');
                    
                    detailedFeedback.push(hasNumberedSteps ? 
                        '✅ <strong>Pasos numerados</strong>: Has usado numeración explícita para los pasos.' : 
                        '❌ <strong>Sin numeración</strong>: No has usado numeración clara (1., 2., etc.).');
                    
                    detailedFeedback.push(hasDetailedInstructions ? 
                        '✅ <strong>Nivel de detalle</strong>: Has pedido detalles específicos.' : 
                        '❌ <strong>Sin especificar detalle</strong>: No has indicado el nivel de especificidad deseado.');
                    
                    detailedFeedback.push(hasStudyPlanMention ? 
                        '✅ <strong>Propósito claro</strong>: Has especificado que es para un plan de estudio.' : 
                        '❌ <strong>Propósito indefinido</strong>: No has mencionado que es para crear un plan de estudio.');
                    
                    // Calcular puntuación
                    score = (hasSteps ? 25 : 0) + (hasNumberedSteps ? 25 : 0) + 
                            (hasDetailedInstructions ? 25 : 0) + (hasStudyPlanMention ? 25 : 0);
                    
                    // Criterio de aprobación
                    isCorrect = score >= 75; // Al menos 3 de 4 criterios
                    
                    feedback = isCorrect ? 
                        `<div class="alert alert-success">
                            <h5>¡Excelente prompt! (${score}% de efectividad)</h5>
                            <p>Has definido instrucciones claras y estructuradas.</p>
                        </div>` : 
                        `<div class="alert alert-danger">
                            <h5>Tu prompt necesita mejorar (${score}% de efectividad)</h5>
                            <p>Tus instrucciones no son suficientemente claras o estructuradas.</p>
                        </div>`;
                    break;
                
                case 4:
                    // Verificar formato de salida específico
                    const hasFormatMention = promptText.toLowerCase().includes('formato') || 
                                           promptText.toLowerCase().includes('estructura');
                    const hasSpecificFormat = promptText.toLowerCase().includes('tabla') || 
                                           promptText.toLowerCase().includes('json') || 
                                           promptText.toLowerCase().includes('lista') ||
                                           promptText.toLowerCase().includes('markdown');
                    const hasDataRequest = promptText.toLowerCase().includes('información') || 
                                         promptText.toLowerCase().includes('datos');
                    const hasFormatDetails = promptText.toLowerCase().includes('columna') || 
                                           promptText.toLowerCase().includes('campo') || 
                                           promptText.toLowerCase().includes('elemento') ||
                                           promptText.toLowerCase().includes('viñeta');
                    
                    detailedFeedback.push(hasFormatMention ? 
                        '✅ <strong>Mención de formato</strong>: Has indicado que requieres un formato específico.' : 
                        '❌ <strong>Sin mención de formato</strong>: No has indicado que necesitas un formato particular.');
                    
                    detailedFeedback.push(hasSpecificFormat ? 
                        '✅ <strong>Formato específico</strong>: Has especificado el tipo de formato deseado.' : 
                        '❌ <strong>Formato no especificado</strong>: No has detallado qué formato concreto deseas.');
                    
                    detailedFeedback.push(hasDataRequest ? 
                        '✅ <strong>Solicitud de datos</strong>: Has especificado qué información necesitas.' : 
                        '❌ <strong>Datos indefinidos</strong>: No has indicado qué información debe contener.');
                    
                    detailedFeedback.push(hasFormatDetails ? 
                        '✅ <strong>Detalles del formato</strong>: Has especificado elementos estructurales del formato.' : 
                        '❌ <strong>Sin detalles estructurales</strong>: No has mencionado detalles sobre la estructura deseada.');
                    
                    // Calcular puntuación
                    score = (hasFormatMention ? 25 : 0) + (hasSpecificFormat ? 25 : 0) + 
                            (hasDataRequest ? 25 : 0) + (hasFormatDetails ? 25 : 0);
                    
                    // Criterio de aprobación
                    isCorrect = score >= 75; // Al menos 3 de 4 criterios
                    
                    feedback = isCorrect ? 
                        `<div class="alert alert-success">
                            <h5>¡Excelente prompt! (${score}% de efectividad)</h5>
                            <p>Has especificado claramente el formato de salida deseado.</p>
                        </div>` : 
                        `<div class="alert alert-danger">
                            <h5>Tu prompt necesita mejorar (${score}% de efectividad)</h5>
                            <p>No has especificado suficientemente el formato de salida deseado.</p>
                        </div>`;
                    break;
                
                case 5:
                    // Verificar construcción de contexto
                    const hasBackgroundInfo = promptText.toLowerCase().includes('contexto') || 
                                              promptText.toLowerCase().includes('antecedentes');
                    const hasDetailedContext = promptText.length > 100;  // Un prompt con suficiente detalle
                    const hasSpecificScenario = promptText.toLowerCase().includes('escenario') || 
                                               promptText.toLowerCase().includes('situación');
                    const hasRoleOrPersona = promptText.toLowerCase().includes('actúa como') || 
                                             promptText.toLowerCase().includes('asume el papel');
                    
                    detailedFeedback.push(hasBackgroundInfo ? 
                        '✅ <strong>Información de fondo</strong>: Has proporcionado contexto o antecedentes.' : 
                        '❌ <strong>Sin contexto</strong>: No has proporcionado suficiente información de fondo.');
                    
                    detailedFeedback.push(hasDetailedContext ? 
                        '✅ <strong>Contexto detallado</strong>: Has incluido suficientes detalles en tu prompt.' : 
                        '❌ <strong>Contexto insuficiente</strong>: Tu prompt es demasiado breve para establecer un contexto completo.');
                    
                    detailedFeedback.push(hasSpecificScenario ? 
                        '✅ <strong>Escenario específico</strong>: Has definido un escenario o situación concreta.' : 
                        '❌ <strong>Sin escenario</strong>: No has especificado un escenario concreto.');
                    
                    detailedFeedback.push(hasRoleOrPersona ? 
                        '✅ <strong>Rol o persona</strong>: Has especificado un rol para la IA.' : 
                        '❌ <strong>Sin rol definido</strong>: No has definido un rol específico para la IA.');
                    
                    // Calcular puntuación
                    score = (hasBackgroundInfo ? 25 : 0) + (hasDetailedContext ? 25 : 0) + 
                            (hasSpecificScenario ? 25 : 0) + (hasRoleOrPersona ? 25 : 0);
                    
                    // Criterio de aprobación
                    isCorrect = score >= 75; // Al menos 3 de 4 criterios
                    
                    feedback = isCorrect ? 
                        `<div class="alert alert-success">
                            <h5>¡Excelente prompt! (${score}% de efectividad)</h5>
                            <p>Has construido un contexto efectivo para guiar a la IA.</p>
                        </div>` : 
                        `<div class="alert alert-danger">
                            <h5>Tu prompt necesita mejorar (${score}% de efectividad)</h5>
                            <p>Tu prompt no proporciona suficiente contexto para guiar adecuadamente a la IA.</p>
                        </div>`;
                    break;
                
                default:
                    // Criterio genérico para otros desafíos
                    const hasLength = promptText.length > 50;
                    const hasPoliteness = promptText.toLowerCase().includes('por favor');
                    const hasSpecificity = promptText.toLowerCase().includes('específico') || 
                                         promptText.toLowerCase().includes('detalle');
                    const hasStructure = promptText.includes('\n') || 
                                       (promptText.includes('.') && promptText.split('.').length > 3);
                    
                    detailedFeedback.push(hasLength ? 
                        '✅ <strong>Longitud adecuada</strong>: Tu prompt tiene un buen nivel de detalle.' : 
                        '❌ <strong>Prompt demasiado corto</strong>: Tu prompt necesita más detalle.');
                    
                    detailedFeedback.push(hasPoliteness ? 
                        '✅ <strong>Cortesía</strong>: Has incluido fórmulas de cortesía.' : 
                        '❌ <strong>Sin cortesía</strong>: Considera incluir fórmulas de cortesía.');
                    
                    detailedFeedback.push(hasSpecificity ? 
                        '✅ <strong>Especificidad</strong>: Has pedido detalles específicos.' : 
                        '❌ <strong>Falta especificidad</strong>: Tu prompt es demasiado vago.');
                    
                    detailedFeedback.push(hasStructure ? 
                        '✅ <strong>Buena estructura</strong>: Tu prompt está bien estructurado.' : 
                        '❌ <strong>Estructura pobre</strong>: Tu prompt necesita mejor estructura o formato.');
                    
                    // Calcular puntuación
                    score = (hasLength ? 25 : 0) + (hasPoliteness ? 25 : 0) + 
                            (hasSpecificity ? 25 : 0) + (hasStructure ? 25 : 0);
                    
                    // Criterio de aprobación
                    isCorrect = score >= 50; // Al menos 2 de 4 criterios para desafíos genéricos
                    
                    feedback = isCorrect ? 
                        `<div class="alert alert-success">
                            <h5>¡Buen trabajo! (${score}% de efectividad)</h5>
                            <p>Tu prompt cumple con los criterios básicos de calidad.</p>
                        </div>` : 
                        `<div class="alert alert-danger">
                            <h5>Tu prompt necesita mejorar (${score}% de efectividad)</h5>
                            <p>Intenta hacer tu prompt más específico, detallado y bien estructurado.</p>
                        </div>`;
            }
            
            // Mostrar resultados con animación de contador de puntuación
            aiResponse.innerHTML = '';
            aiResponse.innerHTML += feedback;
            
            // Agregar detalles de la evaluación
            if (detailedFeedback.length > 0) {
                let detailsHtml = '<div class="mt-3 mb-3"><h6>Evaluación detallada:</h6><ul class="list-group">';
                detailedFeedback.forEach(item => {
                    detailsHtml += `<li class="list-group-item">${item}</li>`;
                });
                detailsHtml += '</ul></div>';
                aiResponse.innerHTML += detailsHtml;
            }
            
            // Mostrar ejemplo de prompt
            if (!isCorrect) {
                let examplePrompt = '';
                switch(currentPromptChallenge) {
                    case 1:
                        examplePrompt = "Actúa como un tutor de programación amigable para niños de 8-12 años. Usa un tono entusiasta y explicaciones simples. Incluye ejemplos visuales y analogías con juegos o actividades cotidianas.";
                        break;
                    case 2:
                        examplePrompt = "Actúa como un historiador especializado en el Renacimiento. Explica de manera objetiva pero interesante el impacto de la imprenta de Gutenberg, incluyendo detalles sobre cómo cambió la difusión del conocimiento en Europa.";
                        break;
                    case 3:
                        examplePrompt = "Por favor, ayúdame a crear un plan de estudio para aprender programación en Python. Proporciona instrucciones paso a paso con:\n1. Temas a cubrir en orden secuencial\n2. Tiempo estimado para cada tema\n3. Recursos específicos recomendados\n4. Ejercicios prácticos para cada sección";
                        break;
                    case 4:
                        examplePrompt = "Proporciona información sobre los 5 lenguajes de programación más populares en 2023 en formato de tabla. La tabla debe incluir columnas para: nombre del lenguaje, áreas de aplicación principales, nivel de dificultad (de 1 a 5), y salario promedio para desarrolladores.";
                        break;
                    default:
                        examplePrompt = "Por favor, proporciona un prompt claro, específico y bien estructurado para obtener mejores resultados.";
                }
                
                aiResponse.innerHTML += `
                    <div class="mt-3">
                        <h6>Ejemplo de un buen prompt:</h6>
                        <div class="border p-3 bg-light">
                            <code>${examplePrompt}</code>
                        </div>
                    </div>
                `;
                
                aiResponse.innerHTML += '<p class="mt-3">Intenta de nuevo aplicando los principios de un buen prompt engineering.</p>';
            }
            
            // Mostrar botón de "Intentar de nuevo" o "Continuar" según corresponda
            aiResponse.innerHTML += `
                <div class="mt-3 text-center">
                    <button class="btn ${isCorrect ? 'btn-success' : 'btn-primary'}" id="promptResultAction">
                        ${isCorrect ? 'Continuar al siguiente desafío' : 'Intentar de nuevo'}
                    </button>
                </div>
            `;
            
            // Configurar el botón de acción
            setTimeout(() => {
                const actionButton = document.getElementById('promptResultAction');
                if (actionButton) {
                    // Eliminar manejadores de eventos previos si existen
                    const newButton = actionButton.cloneNode(true);
                    actionButton.parentNode.replaceChild(newButton, actionButton);
                    
                    newButton.addEventListener('click', function() {
                        if (isCorrect) {
                            // Guardar el número de desafío actual en una variable
                            const challengeToComplete = currentPromptChallenge;
                            
                            // Evitar que el botón se pueda presionar varias veces
                            this.disabled = true;
                            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Avanzando...';
                            
                            // Usar setTimeout para dar tiempo a las animaciones
                            setTimeout(() => {
                                // Marcar el desafío actual como completado
                                completePromptChallenge(challengeToComplete);
                            }, 500);
                        } else {
                            // Limpiar el prompt para intentar de nuevo
                            if (promptEditor) {
                                promptEditor.focus();
                            }
                        }
                    });
                }
            }, 100);
            
            // Si es correcto, mostrar animación de puntuación pero NO completar automáticamente
            if (isCorrect) {
                // Agregar contador de XP
                const xpGained = 50 + Math.floor(score / 2);
                aiResponse.innerHTML += `
                    <div class="xp-counter mt-3 text-center">
                        <div class="badge bg-warning text-dark p-2" style="font-size: 1.1rem;">
                            <i class="fas fa-star me-1"></i> +<span id="xp-count">0</span> XP
                        </div>
                    </div>
                `;
                
                // Animar contador de XP
                const xpCounter = document.getElementById('xp-count');
                if (xpCounter) {
                    let count = 0;
                    const interval = setInterval(() => {
                        count += 1;
                        xpCounter.textContent = count;
                        if (count >= xpGained) {
                            clearInterval(interval);
                        }
                    }, 50);
                    
                    // Ya NO completamos automáticamente, esperamos al usuario
                }
            }
            
        }, 1500);
    }
    
    // Función para completar un desafío de prompt
    function completePromptChallenge(challengeNumber) {
        if (completedPromptChallenges.includes(challengeNumber)) return;
        
        completedPromptChallenges.push(challengeNumber);
        
        // Actualizar UI para mostrar el desafío como completado
        const challengeItem = document.querySelector('#ai-challenges .challenge-item:nth-child(' + challengeNumber + ')');
        if (challengeItem) {
            challengeItem.classList.add('completed');
            challengeItem.classList.remove('active');
            const challengeNumberElem = challengeItem.querySelector('.challenge-number');
            if (challengeNumberElem) {
                challengeNumberElem.innerHTML = '✓';
            }
        }
        
        // Calcular progreso
        const progress = Math.round((completedPromptChallenges.length / TOTAL_PROMPT_CHALLENGES) * 100);
        const progressBar = document.querySelector('#ai-challenges .progress .progress-bar');
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
            progressBar.innerHTML = `${progress}%`;
        }
        
        // Mostrar la celebración y desbloquear el siguiente desafío
        if (challengeNumber < TOTAL_PROMPT_CHALLENGES) {
            // Desbloquear el siguiente desafío
            currentPromptChallenge = challengeNumber + 1;
            unlockPromptChallenge(currentPromptChallenge);
            showCelebration('¡Desafío de Prompt Completado!', `Has desbloqueado el desafío ${currentPromptChallenge}`, 'success');
            
            // Actualizar estado de los botones de navegación
            updateNavigationButtons(true);
        } else {
            // Completó todos los desafíos del nivel actual
            completePromptLevel(currentPromptLevel);
        }
    }
    
    // Función para desbloquear un desafío de prompt
    function unlockPromptChallenge(challengeNumber) {
        const nextChallenge = document.querySelector('#ai-challenges .challenge-item:nth-child(' + challengeNumber + ')');
        if (nextChallenge) {
            if (nextChallenge.classList.contains('locked')) {
                nextChallenge.classList.remove('locked');
                const lockIcon = nextChallenge.querySelector('i.fa-lock');
                if (lockIcon) {
                    lockIcon.remove();
                }
            }
            
            // Marcar como activo
            document.querySelectorAll('#ai-challenges .challenge-item').forEach(item => {
                item.classList.remove('active');
            });
            nextChallenge.classList.add('active');
            
            // Actualizar las instrucciones para el nuevo desafío
            updatePromptChallengeContent(challengeNumber);
            
            // Actualizar estado de los botones de navegación
            updateNavigationButtons(true);
        }
    }
    
    // Función para actualizar el contenido del desafío de prompt según el número
    function updatePromptChallengeContent(challengeNumber) {
        const titleElem = document.getElementById('prompt-challenge-title');
        const descriptionElem = document.getElementById('prompt-challenge-description');
        const objectiveElem = document.querySelector('#prompt-challenge-instructions .alert-primary strong');
        
        if (titleElem && descriptionElem && objectiveElem) {
            switch(challengeNumber) {
                case 1:
                    titleElem.textContent = 'Fundamentos de Prompts';
                    descriptionElem.textContent = 'Aprende a escribir prompts claros y específicos para obtener mejores resultados de la IA.';
                    objectiveElem.nextSibling.textContent = ' Escribe un prompt que pida a la IA que se presente como un tutor de programación para niños.';
                    if (promptEditor) promptEditor.value = '';
                    break;
                case 2:
                    titleElem.textContent = 'Roles y Personalidades';
                    descriptionElem.textContent = 'Aprende a asignar roles específicos a la IA para obtener respuestas más enfocadas.';
                    objectiveElem.nextSibling.textContent = ' Escribe un prompt donde la IA adopte el rol de un historiador explicando un evento histórico.';
                    if (promptEditor) promptEditor.value = '';
                    break;
                case 3:
                    titleElem.textContent = 'Instrucciones Claras';
                    descriptionElem.textContent = 'Domina el arte de dar instrucciones precisas y estructuradas a la IA.';
                    objectiveElem.nextSibling.textContent = ' Escribe un prompt con instrucciones paso a paso para que la IA te ayude a crear un plan de estudio.';
                    if (promptEditor) promptEditor.value = '';
                    break;
                case 4:
                    titleElem.textContent = 'Formato de Salida';
                    descriptionElem.textContent = 'Aprende a especificar exactamente cómo quieres que se presente la información.';
                    objectiveElem.nextSibling.textContent = ' Escribe un prompt que pida información en un formato específico (tabla, lista, JSON, etc).';
                    if (promptEditor) promptEditor.value = '';
                    break;
                default:
                    titleElem.textContent = `Desafío ${challengeNumber}`;
                    descriptionElem.textContent = 'Completa este desafío para seguir mejorando tus habilidades de prompt engineering.';
                    objectiveElem.nextSibling.textContent = ' Escribe un prompt que cumpla con los requisitos indicados.';
            }
            
            // Actualizar el indicador de desafío actual
            const currentBadge = document.querySelector('#ai-challenges .badge.rounded-pill.bg-primary');
            if (currentBadge) {
                currentBadge.textContent = `${challengeNumber}/10`;
            }
            
            // Y el nombre del desafío actual
            const currentChallengeName = document.querySelector('#ai-challenges h6.mb-0 + small.text-muted');
            if (currentChallengeName && titleElem) {
                currentChallengeName.textContent = titleElem.textContent;
            }
            
            // Limpiar la respuesta
            if (aiResponse) {
                aiResponse.innerHTML = '<p class="text-muted">La respuesta de la IA aparecerá aquí...</p>';
            }
        }
    }
    
    // Función para completar un nivel de prompt
    function completePromptLevel(level) {
        // Mostrar una celebración grande
        Swal.fire({
            title: '¡Felicidades!',
            html: `Has completado todos los desafíos de prompting de nivel <strong>${level}</strong>.<br>¡Has ganado un certificado!`,
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Ver certificado',
            cancelButtonText: 'Continuar',
            allowOutsideClick: false,
            backdrop: `
                rgba(0,123,255,0.4)
                url("/assets/images/confetti.gif")
                center top
                no-repeat
            `
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar el certificado
                showPromptCertificate(level);
            } else {
                // Avanzar al siguiente nivel si existe
                if (level === 'principiante') {
                    switchPromptLevel('intermedio');
                } else if (level === 'intermedio') {
                    switchPromptLevel('avanzado');
                }
            }
        });
    }
    
    // Función para mostrar el certificado de prompt
    function showPromptCertificate(level) {
        Swal.fire({
            title: 'Certificado de Logro',
            html: `
                <div class="certificate">
                    <div class="certificate-header">
                        <img src="/assets/images/mitai-logo-128x128.svg" alt="MitaiCode" width="50">
                        <h2>MitaiCode Academy</h2>
                    </div>
                    <div class="certificate-body">
                        <p>Este certificado se otorga a</p>
                        <h3>${document.querySelector('.dropdown-toggle')?.textContent?.trim() || 'Estudiante'}</h3>
                        <p>Por completar exitosamente todos los desafíos de</p>
                        <h4>Prompt Engineering - Nivel ${level.charAt(0).toUpperCase() + level.slice(1)}</h4>
                        <p>Fecha: ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <button id="downloadPromptCertificate" class="btn btn-primary">Descargar Certificado</button>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Cerrar',
            width: '600px',
            allowOutsideClick: true
        });
        
        setTimeout(() => {
            const downloadBtn = document.getElementById('downloadPromptCertificate');
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
    
    // Función para cambiar de nivel de prompt
    function switchPromptLevel(newLevel) {
        currentPromptLevel = newLevel;
        
        // Actualizar los botones de nivel
        const levelButtons = document.querySelectorAll('#ai-challenges .btn-group [data-level]');
        levelButtons.forEach(btn => {
            btn.classList.remove('active', 'btn-primary');
            btn.classList.add('btn-outline-primary');
            
            if (btn.getAttribute('data-level') === newLevel) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('active', 'btn-primary');
            }
        });
        
        // Reiniciar el progreso para el nuevo nivel
        currentPromptChallenge = 1;
        completedPromptChallenges = [];
        
        // Actualizar la UI para el nuevo nivel
        Swal.fire({
            title: `Nivel ${newLevel}`,
            text: `¡Has avanzado al nivel ${newLevel} de prompt engineering! Nuevos desafíos te esperan.`,
            icon: 'info',
            confirmButtonText: 'Comenzar'
        }).then(() => {
            // Resetear todos los desafíos
            document.querySelectorAll('#ai-challenges .challenge-item').forEach((item, index) => {
                item.classList.remove('active', 'completed');
                if (index === 0) {
                    item.classList.add('active');
                } else {
                    item.classList.add('locked');
                    
                    if (!item.querySelector('i.fa-lock')) {
                        const lockIcon = document.createElement('i');
                        lockIcon.className = 'fas fa-lock';
                        item.appendChild(lockIcon);
                    }
                }
            });
            
            // Resetear la barra de progreso
            const progressBar = document.querySelector('#ai-challenges .progress .progress-bar');
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
                progressBar.innerHTML = '0%';
            }
            
            // Actualizar contenido para el primer desafío del nuevo nivel
            updatePromptChallengeContent(1);
        });
    }
    
    // Inicializar el selector de nivel en desafíos de prompt
    const promptLevelButtons = document.querySelectorAll('#ai-challenges .btn-group [data-level]');
    
    promptLevelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const newLevel = this.getAttribute('data-level');
            
            if (newLevel === currentPromptLevel) return;
            
            if (completedPromptChallenges.length > 0) {
                Swal.fire({
                    title: '¿Cambiar de nivel?',
                    text: `Tienes progreso en el nivel actual. Cambiar te hará perder ese progreso. ¿Estás seguro?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'No, quedarme'
                }).then((result) => {
                    if (result.isConfirmed) {
                        switchPromptLevel(newLevel);
                    }
                });
            } else {
                switchPromptLevel(newLevel);
            }
        });
    });
    
    // Manejo de clic en los desafíos de prompts para seleccionarlos
    const promptChallengeItems = document.querySelectorAll('#ai-challenges .challenge-item');
    if (promptChallengeItems.length > 0) {
        promptChallengeItems.forEach((item, index) => {
            item.addEventListener('click', function() {
                // Ignorar los bloqueados
                if (this.classList.contains('locked')) return;
                
                // Actualizar desafío actual
                currentPromptChallenge = index + 1;
                
                // Actualizar UI
                promptChallengeItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                // Actualizar contenido
                updatePromptChallengeContent(currentPromptChallenge);
                
                // Limpiar el editor y la respuesta
                if (promptEditor) {
                    promptEditor.value = '';
                }
                if (aiResponse) {
                    aiResponse.innerHTML = '<p class="text-muted">La respuesta de la IA aparecerá aquí...</p>';
                }
            });
        });
    }
    
    // Función para actualizar la UI al cambiar de desafío
    function updateChallengeUI(challengeNumber, isPromptChallenge = false) {
        const challengeList = isPromptChallenge ? '#ai-challenges .challenge-item' : '#python-challenges .challenge-item';
        const challengeItems = document.querySelectorAll(challengeList);
        
        challengeItems.forEach((item, index) => {
            item.classList.remove('active');
            if (index === challengeNumber - 1) {
                item.classList.add('active');
            }
        });
        
        // Actualizar el contenido del desafío
        if (isPromptChallenge) {
            updatePromptChallengeContent(challengeNumber);
        } else {
            updateChallengeContent(challengeNumber);
        }
        
        // Actualizar el indicador de progreso y número de desafío
        const progressContainer = isPromptChallenge ? 
            document.querySelector('#ai-challenges .badge.rounded-pill') : 
            document.querySelector('#python-challenges .badge.rounded-pill');
        
        if (progressContainer) {
            const totalChallenges = isPromptChallenge ? TOTAL_PROMPT_CHALLENGES : TOTAL_CHALLENGES;
            progressContainer.textContent = `${challengeNumber}/${totalChallenges}`;
        }
        
        // Restablecer editores y resultados
        if (isPromptChallenge) {
            const promptEditor = document.getElementById('prompt-editor');
            const aiResponse = document.getElementById('ai-response');
            
            if (promptEditor) {
                promptEditor.value = '';
            }
            if (aiResponse) {
                aiResponse.innerHTML = '<p class="text-muted">La respuesta de la IA aparecerá aquí...</p>';
            }
        } else {
            // Limpiar editor de Python si es necesario
            if (editor) {
                editor.setValue('# Escribe tu código aquí\n');
                editor.clearSelection();
            }
            
            // Limpiar consola de Python
            const pythonConsole = document.getElementById('python-console');
            if (pythonConsole) {
                pythonConsole.innerHTML = '> Programa listo para ejecutar';
            }
        }
        
        // Actualizar los botones de navegación
        updateNavigationButtons(isPromptChallenge);
    }
    
    // Función para actualizar el estado de los botones de navegación
    function updateNavigationButtons(isPromptChallenge = false) {
        if (isPromptChallenge) {
            const prevButton = document.getElementById('prevPromptChallenge');
            const nextButton = document.getElementById('nextPromptChallenge');
            
            if (prevButton) {
                prevButton.disabled = currentPromptChallenge <= 1;
            }
            
            if (nextButton) {
                const nextChallengeElem = document.querySelector('#ai-challenges .challenge-item:nth-child(' + (currentPromptChallenge + 1) + ')');
                nextButton.disabled = currentPromptChallenge >= TOTAL_PROMPT_CHALLENGES || 
                    (nextChallengeElem && nextChallengeElem.classList.contains('locked'));
            }
        } else {
            const prevButton = document.getElementById('prevChallenge');
            const nextButton = document.getElementById('nextChallenge');
            
            if (prevButton) {
                prevButton.disabled = currentChallenge <= 1;
            }
            
            if (nextButton) {
                const nextChallengeElem = document.querySelector('#python-challenges .challenge-item:nth-child(' + (currentChallenge + 1) + ')');
                nextButton.disabled = currentChallenge >= TOTAL_CHALLENGES || 
                    (nextChallengeElem && nextChallengeElem.classList.contains('locked'));
            }
        }
    }
}); 