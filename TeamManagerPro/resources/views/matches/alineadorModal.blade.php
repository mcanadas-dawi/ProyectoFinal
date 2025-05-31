
<!-- Contenedor principal que engloba ambos modales -->
<div id="alineadorContainer" class="hidden fixed inset-0 bg-[#1E293B]/80 flex justify-center items-center z-50 p-2 sm:p-4">
<!-- Modal de alineación guardada -->
<div id="alineacionGuardada" class="hidden bg-[#1E3A8A] rounded-lg p-3 sm:p-6 w-full sm:w-3/4 max-h-[90vh] overflow-y-auto flex flex-col items-center justify-center text-white font-sans">
        <h3 class="text-lg sm:text-xl font-title text-[#FACC15] mt-3 sm:mt-4 uppercase">Alineación Guardada</h3>
        <img id="imagenAlineacion" src="" alt="Alineación Guardada" class="rounded-lg shadow-lg max-w-full h-auto mt-3 sm:mt-4">
        <div class="flex flex-wrap justify-center gap-2 sm:gap-4 mt-3 sm:mt-4">
            <button onclick="modificarAlineacion()" class="bg-[#00B140] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base">
                Modificar Alineación
            </button>
            <button onclick="closeAlineador()" class="bg-[#EF4444] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base">
                Cerrar
            </button>
        </div>
    </div>
<!-- Modal del Alineador -->
    <div id="alineadorModal" class="hidden fixed inset-0 bg-[#1E293B]/80 flex justify-center items-center z-50">
        <div class="bg-[#1E3A8A] rounded-lg p-3 sm:p-6 w-full sm:w-3/4 max-h-[90vh] overflow-y-auto flex flex-col text-white font-sans">
            <h2 class="text-xl sm:text-2xl font-title text-[#FACC15] mb-3 sm:mb-4 text-center uppercase no-capturar">Alineador Táctico</h2>

            <button type="button" class="absolute top-2 left-2 sm:top-4 sm:left-4 bg-[#EF4444] p-2 rounded-full shadow-lg no-capturar" aria-label="Close" onclick="closeAlineador()">
                <i class="bi bi-x-octagon-fill text-white"></i>
            </button>

            <!-- Seleccionar Formación -->
             <div class="no-capturar">
                <label class="block font-semibold mb-1 text-[#FACC15]"><h3 class="text-lg sm:text-xl font-title text-[#FACC15] mt-3 sm:mt-4 uppercase">Seleccionar formación:</h3></label>
                <br>
                <select id="formation-selector" class="w-full p-2 border rounded-lg mb-3 sm:mb-4 bg-white text-black" onchange="updateFormation()">
                    <option value="" disabled selected>Seleccionar...</option>
                    @if ($team->modalidad == 'F5')
                        <option value="1-1-2-1">1-1-2-1</option>
                        <option value="1-2-1-1">1-2-1-1</option>
                        <option value="libref5">Formación personalizada</option>
                    @elseif ($team->modalidad == 'F7')
                        <option value="1-3-2-1">1-3-2-1</option>
                        <option value="1-2-3-1">1-2-3-1</option>
                        <option value="libref7">Formación personalizada</option>
                    @elseif ($team->modalidad == 'F8')
                        <option value="1-3-3-1">1-3-3-1</option>
                        <option value="1-2-4-1">1-2-4-1</option>
                        <option value="libref8">Formación personalizada</option>
                    @elseif ($team->modalidad == 'F11')
                        <option value="1-4-4-2">1-4-4-2</option>
                        <option value="1-4-3-3">1-4-3-3</option>
                        <option value="1-5-3-2">1-5-3-2</option>
                        <option value="libref11">Formación personalizada</option>
                    @endif
                </select>
             </div>
            <!-- Lista de Jugadores Convocados -->
            <div id="convocados-section" class= "no-capturar">
                <h3 class="text-lg sm:text-xl font-title text-[#FACC15] mt-3 sm:mt-4 uppercase">Jugadores Convocados</h3>
                <br>
                <div id="convocados-list" class="bg-[#1E293B] p-2 rounded-lg min-h-[100px] sm:min-h-[150px] max-h-[30vh] sm:max-h-[40vh] overflow-y-auto shadow-inner">
                    <div id="convocados-body" class="flex flex-wrap gap-2">
                        <!-- Jugadores convocados aquí -->
                    </div>
                </div>
            </div>

            <!-- Campo de Fútbol -->
            <div id="field-container" class="relative bg-green-600 h-[240px] sm:h-96 w-full sm:w-4/5 mx-auto flex justify-center items-center mt-3 sm:mt-4 border border-white rounded-lg shadow">
                <img src="{{ asset('Imagenes/campo_futbol.jpg') }}" alt="Campo de Fútbol" class="w-full h-full rounded-lg">
                <div id="player-spots" class="absolute inset-0 flex justify-center items-center">
                    <!-- Posiciones de los jugadores -->
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-center gap-2 sm:gap-4 mt-3 sm:mt-4">
                <button id="edit-system-btn" class="hidden bg-[#1E3A8A] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 border border-white no-capturar text-sm sm:text-base" onclick="enableEditMode()">
                    Editar Formación
                </button>
                
                <button id="save-system-btn" class="hidden bg-[#00B140] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 no-capturar text-sm sm:text-base" onclick="saveFormationChanges()">
                    Guardar Formación
                </button>
            </div>

            <!-- Suplentes -->
            <h3 class="text-lg sm:text-xl font-title text-[#FACC15] mt-3 sm:mt-4 uppercase">Suplentes</h3>
            <br>
            <div id="suplentes-list" class="bg-[#1E293B] border border-white p-2 sm:p-3 rounded-lg shadow-inner">
                <div id="suplentes-body" class="flex flex-wrap gap-2 sm:gap-3 justify-center">
                    <!-- Jugadores suplentes aquí -->
                    <br id="br-placeholder">
                </div>
            </div>
        <div class="flex flex-wrap justify-center gap-2 sm:gap-4 mt-3 sm:mt-4 no-capturar">
        <button id="capturarBtn" class="bg-[#00B140] text-white px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base">Guardar alineación</button>
        <button id="descargarBtn" class="bg-[#FACC15] text-black px-3 sm:px-4 py-2 rounded-lg hover:brightness-110 text-sm sm:text-base">Descargar alineación</button>
        </div>

    </div>
</div>
</div>
<script>
 // ALINEADOR 
let alineadorData = document.getElementById("alineador-data");
let allPlayers = [];
let selectedPlayers = {}; 
let currentMatchId = null;
let editMode = false; // 🔹 Estado de edición

function fetchConvocados(matchId) {
    return new Promise((resolve, reject) => {
        fetch(`/matches/${matchId}/get-convocados`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    reject("Error al cargar convocados: " + data.message);
                } else {
                    resolve(data.convocados);
                }
            })
            .catch(error => {
                reject("Error al realizar la solicitud: " + error);
            });
    });
}

function openAlineador(matchId) {
    currentMatchId = matchId;
    let alineacionGuardadaDiv = document.getElementById('alineacionGuardada');
    let imagenAlineacion = document.getElementById('imagenAlineacion');
    let container = document.getElementById('alineadorContainer');
    
    // Mantener todo oculto inicialmente
    container.classList.add('hidden');
    alineacionGuardadaDiv.classList.add('hidden');
    document.getElementById('alineadorModal').classList.add('hidden');
    
    // Verificar si hay una imagen guardada para este partido
    fetch(`/matches/${currentMatchId}/get-alineacion`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.ruta) {
                // Precargar la imagen antes de mostrar el modal
                imagenAlineacion.onload = function() {
                    // Una vez que la imagen está cargada, mostrar el contenedor y la imagen
                    container.classList.remove('hidden');
                    alineacionGuardadaDiv.classList.remove('hidden');
                };
                
                // En caso de error al cargar la imagen
                imagenAlineacion.onerror = function() {
                    console.error("Error al cargar la imagen de alineación");
                    container.classList.remove('hidden');
                    
                };
                
                // Iniciar la carga de la imagen
                imagenAlineacion.src = data.url || `/storage/${data.ruta}`;
            } else {
                // Si no hay imagen, mostrar el contenedor y cargar los jugadores
                container.classList.remove('hidden');
                modificarAlineacion();
            }
        })
        .catch(error => {
            console.error("Error al verificar la alineación guardada:", error);
            // Si hay un error, mostrar el contenedor y el modal clásico
            container.classList.remove('hidden');
            cargarModalNormal();
        });
}


function modificarAlineacion() {
    // Ocultar el modal de alineación guardada
    document.getElementById('alineacionGuardada').classList.add('hidden');
    
    // Mostrar el modal de alineador
    let modalContent = document.getElementById('alineadorModal');
    modalContent.classList.remove('hidden');
    modalContent.style.visibility = 'hidden';
    
    // Limpiar TODOS los contenedores
    document.getElementById('player-spots').innerHTML = "";
    document.getElementById('convocados-body').innerHTML = "";
    document.getElementById('suplentes-body').innerHTML = "";
    
    // Agregar el placeholder para suplentes
    let suplentesBody = document.getElementById('suplentes-body');
    let placeholder = document.createElement('br');
    placeholder.id = "br-placeholder";
    suplentesBody.appendChild(placeholder);
    
    // Reiniciar selector de formación
    let formationSelector = document.getElementById('formation-selector');
    formationSelector.selectedIndex = 0;
    
    // Ocultar botones de edición
    document.getElementById('edit-system-btn').classList.add('hidden');
    document.getElementById('save-system-btn').classList.add('hidden');
    
    // Reiniciar TODAS las variables de estado
    selectedPlayers = {};
    allPlayers = [];
    editMode = false;
    
    // Mostrar la sección de convocados (que podría estar oculta)
    document.getElementById('convocados-section').classList.remove('hidden');
    
    // Cargar TODOS los jugadores desde cero
    fetchConvocados(currentMatchId)
        .then(convocados => {
            allPlayers = convocados;
            loadConvocados();
            modalContent.style.visibility = 'visible';
        })
        .catch(error => {
            console.error("Error al cargar convocados:", error);
            modalContent.style.visibility = 'visible';
        });
}

// 🔹 Activar modo edición (Mover libremente los círculos)
function enableEditMode() {
    editMode = true;
    document.getElementById('edit-system-btn').classList.add('hidden');
    document.getElementById('save-system-btn').classList.remove('hidden');

    document.querySelectorAll('.dropzone').forEach((positionDiv, index) => {
        if (index === 0) {
            positionDiv.style.cursor = "not-allowed";
            positionDiv.onmousedown = null;
            return; // Saltar el resto de la configuración para el portero
        }
        positionDiv.style.cursor = "grab";
        positionDiv.ondragstart = null; // Deshabilita el arrastre de jugadores

        positionDiv.onmousedown = function(event) {
            event.preventDefault();
            positionDiv.style.cursor = "grabbing";

            let initialX = event.clientX;
            let initialY = event.clientY;
            let startX = positionDiv.offsetLeft;
            let startY = positionDiv.offsetTop;

            function moveAt(event) {
                let newX = startX + (event.clientX - initialX);
                let newY = startY + (event.clientY - initialY);
                positionDiv.style.left = `${newX}px`;
                positionDiv.style.top = `${newY}px`;
            }

            function onMouseMove(event) {
                moveAt(event);
            }

            document.addEventListener('mousemove', onMouseMove);

            positionDiv.onmouseup = function() {
                document.removeEventListener('mousemove', onMouseMove);
                positionDiv.style.cursor = "grab";
                positionDiv.onmouseup = null;
            };
        };

        positionDiv.ondragstart = function() {
            return false;
        };
    });
}


// 🔹 Guardar posiciones y volver al modo normal
function saveFormationChanges() {
    editMode = false;
    document.getElementById('edit-system-btn').classList.remove('hidden');
    document.getElementById('save-system-btn').classList.add('hidden');

    document.querySelectorAll('.dropzone').forEach(positionDiv => {
        positionDiv.style.cursor = "pointer"; 
        positionDiv.onmousedown = null;
        enableDragDrop(positionDiv);
    });
}


// 🔹 Función para permitir arrastrar jugadores dentro del círculo
function enableDragDrop(positionDiv) {
    positionDiv.ondragover = function(event) {
        event.preventDefault();
    };

    positionDiv.ondrop = function(event) {
        event.preventDefault();
        let playerId = event.dataTransfer.getData("playerId");

        if (!playerId) return; 

        let existingPlayerId = positionDiv.getAttribute("data-player-id");

        if (existingPlayerId) {
            // Intercambiar jugadores si la posición está ocupada
            let sourceDiv = document.querySelector(`.dropzone[data-player-id='${playerId}']`);
            if (sourceDiv) {
                let tempPlayerId = existingPlayerId;
                sourceDiv.setAttribute("data-player-id", tempPlayerId);
                sourceDiv.textContent = allPlayers.find(p => p.id == tempPlayerId).dorsal;

                positionDiv.setAttribute("data-player-id", playerId);
                positionDiv.textContent = allPlayers.find(p => p.id == playerId).dorsal;
            }
        } else {
            // Mover jugador si la posición está vacía
            let player = allPlayers.find(p => p.id == playerId);
            if (player) {
                removeFromField(playerId);
                removeFromSuplentes(playerId);
                removeFromConvocados(playerId); 

                positionDiv.textContent = player.dorsal;
                positionDiv.setAttribute("data-player-id", player.id);
                selectedPlayers[positionDiv.getAttribute("data-index")] = player.id;
            }
        }
    };

    positionDiv.ondragstart = function(event) {
        let playerId = positionDiv.getAttribute("data-player-id");
        if (playerId) {
            event.dataTransfer.setData("playerId", playerId);
        }
    };
}

// 🔹 Modificar updateFormation para soportar modo edición y jugadores dentro de círculos
function updateFormation(formation = null, alineacionGuardada = []) {
    let formationSelector = document.getElementById('formation-selector');
    let selectedFormation = formation || formationSelector.value;
    let fieldContainer = document.getElementById('player-spots');
    fieldContainer.innerHTML = "";

    let formations = {
        '1-4-4-2': [[7, 44], [30, 76], [30, 53], [30, 33], [30, 10], [50, 76], [50, 53], [50, 33], [50, 10], [70, 53], [70, 33]],
        '1-4-3-3': [[7, 44], [30, 76], [30, 53], [30, 33], [30, 10], [50, 20], [50, 44], [50, 70], [70, 20], [70, 44], [70, 70]],
        '1-5-3-2': [[7, 44], [30, 22], [30, 44], [30, 66], [38, 76], [38, 10], [50, 22], [50, 44], [50, 66], [70, 53], [70, 33]],
        '1-1-2-1': [[7, 44], [30, 44], [50, 24], [50, 64], [70, 44]],
        '1-2-1-1': [[7, 44], [30, 33], [30, 53], [50, 44], [70, 44]],
        '1-3-2-1': [[7, 44], [30, 22], [30, 44], [30, 66], [50, 33], [50, 53], [70, 44]],
        '1-2-3-1': [[7, 44], [30, 33], [30, 53], [50, 20], [50, 44], [50, 68], [70, 44],],
        '1-2-4-1': [[7, 44], [30, 33], [30, 53], [50, 76], [50, 53], [50, 33], [50, 10], [70, 44],],
        '1-3-3-1': [[7, 44], [30, 22], [30, 44], [30, 66], [50, 22], [50, 44], [50, 66], [70, 44],],
        'libref5':[[7, 44],[20,44],[30,44],[40,44],[50,44]],
        'libref7':[[7, 44],[20,44],[30,44],[40,44],[50,44],[60,44],[70,44]],
        'libref8':[[7, 44],[20,44],[30,44],[40,44],[50,44],[60,44],[70,44],[80,44]],
        'libref11': [[7, 44], [20, 20], [20, 40], [20, 60], [20, 80], [30, 20], [30, 40], [30, 60], [30, 80], [40, 40], [40, 60]]

    };

    if (!formations[selectedFormation]) return;

      //Responsive para moviles
    const isMobile = window.innerWidth < 640; // sm breakpoint in Tailwind
    const spotSize = isMobile ? "w-8 h-8" : "w-12 h-12";
    const fontSize = isMobile ? "text-xs" : "text-base";

     formations[selectedFormation].forEach((pos, index) => {
        let positionDiv = document.createElement('div');
        positionDiv.className = `dropzone ${spotSize} bg-white border border-gray-800 rounded-full flex items-center justify-center cursor-pointer`;
        positionDiv.classList.add("text-black", "font-bold", fontSize);
        positionDiv.style.position = "absolute";
        positionDiv.style.top = `${pos[1]}%`;
        positionDiv.style.left = `${pos[0]}%`;
        positionDiv.setAttribute("data-index", index);
        positionDiv.setAttribute("draggable", true);

        
        let alineado = alineacionGuardada.find(a => a.posicion == index);
        if (alineado) {
            let player = allPlayers.find(p => p.id == alineado.player_id);
            if (player) {
                positionDiv.textContent = player.dorsal;
                positionDiv.setAttribute("data-player-id", player.id);
                selectedPlayers[index] = player.id;
            }
        }

        fieldContainer.append(positionDiv);

        enableDragDrop(positionDiv); // 🔹 Activar la función de arrastrar jugadores  
    });
    enableSuplentesDrop();
}


function loadConvocados() {
    let convocadosBody = document.getElementById('convocados-body');
    convocadosBody.innerHTML = "";

    if (!currentMatchId) {
        console.error(" Error: currentMatchId no está definido.");
        return;
    }

    // Iterar sobre los jugadores cargados desde el backend
    allPlayers.forEach(player => {
        let playerDiv = document.createElement('div');
        playerDiv.className = "draggable bg-blue-500 text-white px-3 py-1 rounded cursor-pointer w-full";
        playerDiv.innerHTML = `<span>${player.dorsal} - ${player.nombre} (${player.posicion})</span>`;
        playerDiv.setAttribute("draggable", true);
        playerDiv.setAttribute("data-player-id", player.id);

        playerDiv.ondragstart = function(event) {
            event.dataTransfer.setData("playerId", player.id);
        };

        convocadosBody.append(playerDiv);
    });
}


function addToConvocados(playerId) {
    let convocadosBody = document.getElementById('convocados-body');
    let player = allPlayers.find(p => p.id == playerId);

    if (!player) {
        console.warn(`⚠ No se encontró el jugador con ID ${playerId} en la lista de jugadores.`);
        return;
    }

    let playerDiv = document.createElement('div');
    playerDiv.className = "draggable bg-blue-500 text-white px-3 py-1 rounded cursor-pointer w-full";
    playerDiv.innerHTML = `<span>${player.dorsal} - ${player.nombre} (${player.posicion})</span>`;
    playerDiv.setAttribute("draggable", true);
    playerDiv.setAttribute("data-player-id", player.id);

    playerDiv.ondragstart = function(event) {
        event.dataTransfer.setData("playerId", player.id);
    };

    convocadosBody.append(playerDiv);
}


// 🔹 Permitir arrastrar del campo a suplentes
function enableSuplentesDrop() {
    let suplentesBody = document.getElementById('suplentes-body');

    suplentesBody.ondragover = function(event) {
        event.preventDefault();
    };

    suplentesBody.ondrop = function(event) {
        event.preventDefault();
        let playerId = event.dataTransfer.getData("playerId");

        if (playerId) {
            addToSuplentes(playerId);
            removeFromField(playerId);
            removeFromConvocados(playerId); 
        }
    };
}

function addToSuplentes(playerId) {
    let suplentesBody = document.getElementById('suplentes-body');
    removePlaceholderBr();
    if (suplentesBody.querySelector(`[data-player-id='${playerId}']`)) return;

    let player = allPlayers.find(p => p.id == playerId);
    if (player) {
        let playerDiv = document.createElement('div');
        playerDiv.className = "draggable bg-gray-500 text-white px-3 py-1 rounded cursor-pointer w-full";
        playerDiv.innerHTML = `<span>${player.dorsal} - ${player.nombre} (${player.posicion})</span>`;
        playerDiv.setAttribute("draggable", true);
        playerDiv.setAttribute("data-player-id", player.id);

        playerDiv.ondragstart = function(event) {
            event.dataTransfer.setData("playerId", player.id);
        };

        suplentesBody.append(playerDiv);
    }
}

function removeFromField(playerId) {
    let fieldPositions = document.querySelectorAll('.dropzone');

    fieldPositions.forEach(position => {
        if (position.getAttribute("data-player-id") === playerId) {
            position.textContent = "";
            position.removeAttribute("data-player-id");
            delete selectedPlayers[position.getAttribute("data-index")];
        }
    });
}

function removeFromSuplentes(playerId) {
    let playerDiv = document.querySelector(`#suplentes-body [data-player-id='${playerId}']`);
    if (playerDiv) {
        playerDiv.remove();
    }
}

function removeFromConvocados(playerId) {
    let playerDiv = document.querySelector(`#convocados-body [data-player-id='${playerId}']`);
    if (playerDiv) {
        playerDiv.remove();
    }
    toggleConvocadosSection();
}

function toggleConvocadosSection() {
    const section = document.getElementById('convocados-section');
    const body = document.getElementById('convocados-body');

    if (body && body.children.length === 0) {
        section.classList.add('hidden');
    } else {
        section.classList.remove('hidden');
    }
}


document.getElementById('formation-selector').addEventListener('change', function() {
    let formationSelector = document.getElementById('formation-selector');
    let editButton = document.getElementById('edit-system-btn');
    let saveButton = document.getElementById('save-system-btn');

    // Solo mostrar el botón si el usuario ha seleccionado una formación válida
    if (formationSelector.value && formationSelector.value !== "") {
        editButton.classList.remove('hidden');
        saveButton.classList.add('hidden');
    } else {
        editButton.classList.add('hidden');
        saveButton.classList.add('hidden');
    }
});

function removePlaceholderBr() {
        let placeholder = document.getElementById("br-placeholder");
        if (placeholder) {
            placeholder.remove();
        }
    }

    // Detectar cuando un jugador es soltado en la lista de suplentes
    document.getElementById("suplentes-body").addEventListener("drop", function(event) {
        removePlaceholderBr();
    });

    function closeAlineador() {
    const container = document.getElementById('alineadorContainer');
    if (container) {
        container.classList.add('hidden');
        // Ocultar también los modales internos
        document.getElementById('alineacionGuardada').classList.add('hidden');
        document.getElementById('alineadorModal').classList.add('hidden');
    }
}

function ocultarElementos(selector) {
    document.querySelectorAll(selector).forEach(el => {
        el.style.display = 'none';
    });
}

function mostrarElementos(selector) {
    document.querySelectorAll(selector).forEach(el => {
        el.style.display = '';
    });
}

document.getElementById("capturarBtn").addEventListener("click", function () {
    const contenedor = document.getElementById("alineadorModal");

    // Ocultar los botones antes de la captura
    ocultarElementos('.no-capturar');
        html2canvas(contenedor, {
            allowTaint: true,
            useCORS: true,
            logging: true,
            backgroundColor: null,
            scale: 2,
        }).then(function (canvas) {
            // Restaurar los botones
            mostrarElementos('.no-capturar');

            const imageData = canvas.toDataURL("image/png");

            fetch(`/matches/${currentMatchId}/guardar-alineacion`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({
                imagen: imageData
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                closeAlineador();
                openAlineador(currentMatchId);
            } else {
                alert("Error al guardar la alineación: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error al guardar la imagen:", error);
            alert("Error al guardar la imagen. Por favor, inténtalo de nuevo.");
        });
    });
});

document.getElementById("descargarBtn").addEventListener("click", function () {
    const contenedor = document.getElementById("alineadorModal");

    // Ocultar los botones antes de la captura
    ocultarElementos('.no-capturar');

    document.fonts.ready.then(() => {
            html2canvas(contenedor, {
                allowTaint: true,
                useCORS: true,
                backgroundColor: null,
                scale: 2,
            }).then(function (canvas) {
                // Restaurar los botones
                mostrarElementos('.no-capturar');

                const imageData = canvas.toDataURL("image/png");

                const link = document.createElement("a");
                link.href = imageData;
                link.download = "alineacion.png";
                document.body.append(link);
                link.click();
                document.body.removeChild(link);
            });
    });
});


</script>