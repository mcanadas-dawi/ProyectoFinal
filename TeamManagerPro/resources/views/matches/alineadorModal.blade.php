<!-- Modal del Alineador -->
<div id="alineadorModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-3/4 max-h-[90vh] overflow-y-auto flex flex-col">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Alineador Táctico</h2>
        <button type="button" class="btn btn-danger p-1 position-absolute" aria-label="Close" onclick="closeAlineador()" style="top: 25px; left: 200px; z-index: 1051;">
    <i class="bi bi-x-octagon-fill text-white"></i>
</button>


        <!-- Seleccionar Formación -->
        <label class="block text-gray-700 font-semibold">Seleccionar Formación:</label>
        <select id="formation-selector" class="w-full p-2 border rounded-lg mb-4" onchange="updateFormation()">
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

        <!-- Lista de Jugadores Convocados -->
        <h3 class="text-xl font-semibold text-gray-800 mt-4">Jugadores Convocados</h3>
        <div id="convocados-list" class="bg-gray-100 p-2 rounded-lg min-h-[150px] max-h-[40vh] overflow-y-auto">
            <div id="convocados-body" class="flex flex-wrap gap-2">
                <!-- Aquí se inyectarán los jugadores convocados -->
            </div>
        </div>

        <!-- Campo de Fútbol -->
        <div id="field-container" class="relative bg-green-500 h-96 w-4/5 mx-auto flex justify-center items-center mt-4">
            <img src="{{ asset('Imagenes/campo_futbol.jpg') }}" alt="Campo de Fútbol" class="w-full h-full object-cover">
            <div id="player-spots" class="absolute inset-0 flex justify-center items-center">
                <!-- Aquí se inyectarán las posiciones según la formación -->
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-center mt-4">
            <button id="edit-system-btn" class="hidden bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" onclick="enableEditMode()">
                Editar Formación
            </button>
            
            <button id="save-system-btn" class="hidden bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700" onclick="saveFormationChanges()">
                Guardar Formación
            </button>
        </div>

        <!-- Lista de Suplentes (Ahora sin scroll y con ajuste automático de filas) -->
        <h3 class="text-xl font-semibold text-gray-800 mt-4">Suplentes</h3>
        <div id="suplentes-list" class="bg-gray-200 border border-gray-300 p-3 rounded-lg shadow-md">
            <div id="suplentes-body" class="flex flex-wrap gap-3 justify-center">
                <!-- Aquí se inyectarán los suplentes -->
                 <br id="br-placeholder">
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

function openAlineador(matchId) {
    currentMatchId = matchId;
    let formationSelector = document.getElementById('formation-selector');
    let editButton = document.getElementById('edit-system-btn');
    let saveButton = document.getElementById('save-system-btn');

    // Restablecer el selector de formación a "Seleccionar..."
    formationSelector.selectedIndex = 0;

    let fieldContainer = document.getElementById('player-spots');
    fieldContainer.innerHTML = "";
    document.getElementById('alineadorModal').classList.remove('hidden');

    // Ocultar botones al abrir el modal
    editButton.classList.add('hidden');
    saveButton.classList.add('hidden');

    fetch(`/matches/${currentMatchId}/get-convocados`)
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error("Error al cargar convocados:", data.message);
            return;
        }

        // Asignar los jugadores obtenidos a la variable global
        allPlayers = data.convocados;
        console.log("✅ Jugadores cargados desde el backend:", allPlayers);

        loadConvocados();
    })
    .catch(error => console.error("Error al cargar jugadores convocados:", error));
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
                removeFromConvocados(playerId); // 🔹 Ahora también lo elimina de la lista de convocados

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
        '1-4-4-2': [[10, 45], [30, 80], [30, 55], [30, 35], [30, 10], [50, 80], [50, 55], [50, 35], [50, 10], [70, 55], [70, 35]],
        '1-4-3-3': [[10, 45], [30, 80], [30, 55], [30, 35], [30, 10], [50, 20], [50, 45], [50, 70], [70, 20], [70, 45], [70, 70]],
        '1-5-3-2': [[10, 45], [30, 20], [30, 45], [30, 70], [40, 87], [40, 0], [50, 20], [50, 45], [50, 70], [70, 55], [70, 35]],
        '1-1-2-1': [[10, 45], [30, 45], [50,25 ], [50, 65], [70, 45]],
        '1-2-1-1': [[10, 45], [30, 25], [30, 65], [50, 45], [70, 45]],
        '1-3-2-1': [[10, 45], [30, 20], [30, 45], [30, 70], [50, 30], [50, 60], [70, 45]],
        '1-2-3-1': [[10, 45], [30, 30], [30, 60], [50, 20], [50, 45], [50, 70], [70, 45],],
        '1-2-4-1': [[10, 45], [30, 30], [30, 60], [50, 30], [50, 87], [50, 0], [50, 60], [70, 45],],
        '1-3-3-1': [[10, 45], [30, 20], [30, 45], [30, 70], [50, 20], [50, 45], [50, 70], [70, 45],],
        'libref5':[[10, 45],[20,45],[30,45],[40,45],[50,45]],
        'libref7':[[10, 45],[20,45],[30,45],[40,45],[50,45],[60,45],[70,45]],
        'libref8':[[10, 45],[20,45],[30,45],[40,45],[50,45],[60,45],[70,45],[80,45]],
        'libref11': [[10, 45], [20, 20], [20, 40], [20, 60], [20, 80], [30, 20], [30, 40], [30, 60], [30, 80], [40, 40], [40, 60]]

    };

    if (!formations[selectedFormation]) return;

    formations[selectedFormation].forEach((pos, index) => {
        let positionDiv = document.createElement('div');
        positionDiv.className = "dropzone w-12 h-12 bg-white border border-gray-800 rounded-full flex items-center justify-center cursor-pointer";
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

        fieldContainer.appendChild(positionDiv);

        enableDragDrop(positionDiv); // 🔹 Activar la función de arrastrar jugadores  
    });
    enableSuplentesDrop();
}


function loadConvocados() {
    let convocadosBody = document.getElementById('convocados-body');
    convocadosBody.innerHTML = "";

    if (!currentMatchId) {
        console.error("⚠ Error: currentMatchId no está definido.");
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

        convocadosBody.appendChild(playerDiv);
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

    convocadosBody.appendChild(playerDiv);
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
            removeFromConvocados(playerId); // 🔹 También lo elimina de la lista de convocados
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

        suplentesBody.appendChild(playerDiv);
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
    const modal = document.getElementById('alineadorModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

</script>