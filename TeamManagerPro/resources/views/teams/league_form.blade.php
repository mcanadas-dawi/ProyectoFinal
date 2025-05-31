@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-[#1E293B] text-white font-sans px-4 sm:px-6 md:px-8 py-4 sm:py-6">
    <div class="bg-[#1E3A8A] p-4 sm:p-6 md:p-8 lg:p-10 rounded-lg w-full shadow-lg">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-title text-[#FACC15] mb-4 sm:mb-6 md:mb-8 uppercase">Crear Liga</h1>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="bg-[#00B140]/10 border border-[#00B140] text-[#00B140] px-3 sm:px-4 py-2 sm:py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-[#EF4444]/10 border border-[#EF4444] text-[#EF4444] px-3 sm:px-4 py-2 sm:py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-[#EF4444]/10 border border-[#EF4444] text-[#EF4444] px-3 sm:px-4 py-2 sm:py-3 rounded mb-4">
                <strong>Errores:</strong>
                <ul class="list-disc pl-5 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Contenedor para mensajes de error de validación JS -->
        <div id="validationErrors" class="bg-[#EF4444]/10 border border-[#EF4444] text-[#EF4444] px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 hidden">
            <strong>Errores de validación:</strong>
            <ul class="list-disc pl-5 mt-2" id="errorList"></ul>
        </div>

        <form action="{{ route('rivales_liga.store') }}" method="POST" class="w-full" id="ligaForm">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">

            <!-- Grid de 3 columnas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                <!-- Nombre -->
                <div class="col-span-1">
                    <label class="block mb-1 font-semibold text-[#FACC15]">Nombre de la Liga:</label>
                    <input type="text" name="nombre_liga" id="nombre_liga" class="w-full border p-2 rounded bg-white text-black" required>
                    <p id="nombreError" class="text-[#EF4444] text-sm mt-1 hidden">Solo letras y espacios permitidos</p>
                </div>

                <!-- Número de rivales -->
                <div class="col-span-1">
                    <label class="block mb-1 font-semibold text-[#FACC15]">Número de Rivales:</label>
                    <input type="number" id="numero_rivales" name="numero_rivales"
                        class="w-full border p-2 rounded bg-white text-black"
                        min="5" max="25" required onchange="validarFormulario()">
                    <p id="rivalesError" class="text-[#EF4444] text-sm mt-1 hidden">Mínimo 5 y Máximo 25</p>
                </div>

                <!-- Ida / Vuelta -->
                <div class="col-span-1 flex items-center pt-4 sm:pt-6">
                    <input type="hidden" name="solo_ida" value="0">
                    <input type="checkbox" id="solo_ida" name="solo_ida" value="1" class="mr-2 accent-[#00B140]" onchange="validarFormulario()">
                    <label for="solo_ida" class="text-white">Solo Ida</label>
                </div>
            </div>

            <!-- Jornadas dinámicas -->
            <div id="jornadasContainer" class="space-y-3 sm:space-y-4 mt-4 sm:mt-6 w-full"></div>
            <p id="localesError" class="text-[#EF4444] text-sm mt-1 hidden">El número de partidos locales debe ser exactamente la mitad</p>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end mt-6 sm:mt-8 space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="{{ route('teams.show', ['team' => $team->id]) }}"
                   class="bg-[#EF4444] text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:brightness-110 text-center">
                    Cancelar
                </a>
                <button type="submit" id="submitButton" class="bg-[#00B140] text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:brightness-110">
                    Crear Liga
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// Expresión regular para verificar que solo hay letras y espacios
const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;

// Función para validar el nombre de la liga
function validarNombreLiga() {
    const nombreLiga = document.getElementById('nombre_liga');
    const nombreError = document.getElementById('nombreError');
    
    if (!nombreRegex.test(nombreLiga.value) && nombreLiga.value.trim() !== '') {
        nombreError.classList.remove('hidden');
        nombreLiga.classList.add('border-red-500');
        return false;
    } else {
        nombreError.classList.add('hidden');
        nombreLiga.classList.remove('border-red-500');
        return true;
    }
}

// Función para validar el número de rivales
function validarNumeroRivales() {
    const numeroRivales = document.getElementById('numero_rivales');
    const rivalesError = document.getElementById('rivalesError');
    
    const valor = parseInt(numeroRivales.value);
    
    if (isNaN(valor) || valor < 5 || valor > 25) {
        rivalesError.classList.remove('hidden');
        numeroRivales.classList.add('border-red-500');
        return false;
    } else {
        rivalesError.classList.add('hidden');
        numeroRivales.classList.remove('border-red-500');
        return true;
    }
}

// Función para validar los partidos locales
function validarPartidosLocales() {
    const numeroRivales = parseInt(document.getElementById('numero_rivales').value) || 0;
    const soloIda = document.getElementById('solo_ida').checked;
    const localesError = document.getElementById('localesError');
    
    if (numeroRivales <= 0 || soloIda) {
        localesError.classList.add('hidden');
        return true;
    }
    
    // Contar cuántos checkbox de local están marcados
    let partidosLocales = 0;
    for (let i = 1; i <= numeroRivales; i++) {
        const checkbox = document.getElementById(`local_${i}`);
        if (checkbox && checkbox.checked) {
            partidosLocales++;
        }
    }
    
    // Calcular el rango válido
    let valido = false;
    
    if (numeroRivales % 2 === 0) { 
        // Si es par, debe ser exactamente la mitad
        valido = partidosLocales === (numeroRivales / 2);
    } else {
        // Si es impar, puede ser n/2 redondeado hacia arriba o hacia abajo
        const mitadInferior = Math.floor(numeroRivales / 2);
        const mitadSuperior = Math.ceil(numeroRivales / 2);
        valido = partidosLocales === mitadInferior || partidosLocales === mitadSuperior;
    }
    
    if (!valido && numeroRivales >= 5) {
        let mensajeError;
        if (numeroRivales % 2 === 0) {
            mensajeError = `Debes marcar exactamente ${numeroRivales/2} partidos como local.`;
        } else {
            const mitadInferior = Math.floor(numeroRivales / 2);
            const mitadSuperior = Math.ceil(numeroRivales / 2);
            mensajeError = `Debes marcar exactamente ${mitadInferior} o ${mitadSuperior} partidos como local.`;
        }
        
        localesError.textContent = mensajeError;
        localesError.classList.remove('hidden');
        return false;
    } else {
        localesError.classList.add('hidden');
        return true;
    }
}

// Función para validar un nombre de rival individual
function validarNombreRival(inputRival) {
    const valor = inputRival.value.trim();

    // Validación de formato
    if (!nombreRegex.test(valor) && valor !== '') {
        mostrarError(inputRival, 'Solo letras y espacios permitidos');
        return false;
    }

    // Si está vacío, no marcar error, pero ocultar si había
    if (valor === '') {
        ocultarError(inputRival);
        inputRival.classList.remove('border-red-500');
        return true;
    }

    // Validar duplicados
    if (verificarDuplicados(inputRival)) {
        mostrarError(inputRival, 'Nombre de rival duplicado');
        return false;
    }

    ocultarError(inputRival);
    inputRival.classList.remove('border-red-500');
    return true;
}

function verificarDuplicados(inputActual) {
    const valorActual = inputActual.value.trim().toLowerCase();
    if (!valorActual) return false;

    const numeroRivales = parseInt(document.getElementById('numero_rivales').value) || 0;

    for (let i = 1; i <= numeroRivales; i++) {
        const otroInput = document.getElementById(`rival_${i}`);
        if (!otroInput || otroInput === inputActual) continue;

        const valorOtro = otroInput.value.trim().toLowerCase();

        // Comparación exacta, sin falsos positivos
        if (valorOtro === valorActual) {
            return true;
        }
    }

    return false;
}

function mostrarError(input, mensaje) {
    input.classList.add('border-red-500');
    let errorMsg = input.nextElementSibling;

    if (!errorMsg || !errorMsg.classList.contains('rival-error')) {
        errorMsg = document.createElement('p');
        errorMsg.className = 'text-[#EF4444] text-sm mt-1 rival-error';
        input.parentNode.insertBefore(errorMsg, input.nextSibling);
    }

    errorMsg.textContent = mensaje;
    errorMsg.classList.remove('hidden');
}

function ocultarError(input) {
    const errorMsg = input.nextElementSibling;
    if (errorMsg && errorMsg.classList.contains('rival-error')) {
        errorMsg.classList.add('hidden');
    }
    input.classList.remove('border-red-500');
}



// Función para validar todos los nombres de rivales
function validarTodosRivales() {
    const numeroRivales = parseInt(document.getElementById('numero_rivales').value) || 0;
    let todosValidos = true;
    
    // Reset de todos los errores de duplicados
    for (let i = 1; i <= numeroRivales; i++) {
        const inputRival = document.getElementById(`rival_${i}`);
        if (inputRival) {
            const esValido = validarNombreRival(inputRival);
            todosValidos = todosValidos && esValido;
        }
    }
    
    return todosValidos;
}

// Función para generar jornadas (modificada para incluir validación)
function generarJornadas() {
    const numeroRivales = parseInt(document.getElementById('numero_rivales').value) || 0;
    const jornadasContainer = document.getElementById('jornadasContainer');
    jornadasContainer.innerHTML = '';

    if (numeroRivales < 5 || numeroRivales > 25) {
        return;
    }

    const soloIda = document.getElementById('solo_ida').checked;

    if (!soloIda) {
        const notaLocal = document.createElement('div');
        notaLocal.className = "bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded mb-4 text-sm";
        notaLocal.innerHTML = `<strong>Nota:</strong> Marca "Local" en las jornadas que tu equipo juegue en casa.`;
        jornadasContainer.append(notaLocal);
    }

    for (let i = 1; i <= numeroRivales; i++) {
        const jornadaDiv = document.createElement('div');
        jornadaDiv.className = "bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm mb-3";

        const label = document.createElement('label');
        label.className = "block font-medium text-gray-800 mb-2";
        label.textContent = `Jornada ${i}`;
        label.setAttribute('for', `rival_${i}`);

        const inputRival = document.createElement('input');
        inputRival.type = "text";
        inputRival.name = `rivales[${i}]`;
        inputRival.id = `rival_${i}`;
        inputRival.placeholder = "Nombre del rival";
        inputRival.required = true;
        inputRival.className = "w-full border p-2 rounded bg-white text-gray-800";
        // Añadir evento para validar el nombre y duplicados
        inputRival.addEventListener('input', function() {
            validarNombreRival(this);
            validarFormulario();
        });
        // Añadir evento blur para validar duplicados cuando termine de escribir
        inputRival.addEventListener('blur', function() {
            validarTodosRivales();
            validarFormulario();
        });

        jornadaDiv.append(label);
        jornadaDiv.append(inputRival);

        if (!soloIda) {
            const checkboxWrapper = document.createElement('div');
            checkboxWrapper.className = "flex items-center mt-2";

            const localCheckbox = document.createElement('input');
            localCheckbox.type = "checkbox";
            localCheckbox.name = `local[${i}]`;
            localCheckbox.id = `local_${i}`;
            localCheckbox.className = "mr-2";
            localCheckbox.onchange = function() {
                validarPartidosLocales();
                validarFormulario();
            };

            const localLabel = document.createElement('label');
            localLabel.setAttribute('for', `local_${i}`);
            localLabel.textContent = "Local";
            localLabel.className = "text-gray-800";
            
            checkboxWrapper.append(localCheckbox);
            checkboxWrapper.append(localLabel);

            jornadaDiv.append(checkboxWrapper);
        }

        jornadasContainer.append(jornadaDiv);
    }

    if (!soloIda) {
        const notaVuelta = document.createElement('div');
        notaVuelta.className = "bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded mt-4 text-sm";
        notaVuelta.innerHTML = `<strong>Nota:</strong> Las jornadas de la segunda vuelta se generarán automáticamente.`;
        jornadasContainer.append(notaVuelta);
    }
}

// Función principal de validación actualizada para incluir rivales
function validarFormulario() {
    const validoNombre = validarNombreLiga();
    const validoRivales = validarNumeroRivales();
    
    if (validoRivales) {
        if (document.getElementById('jornadasContainer').children.length === 0) {
            generarJornadas();
        }
    }
    
    const validoLocales = validarPartidosLocales();
    const validoNombresRivales = validarTodosRivales();
    
    const submitButton = document.getElementById('submitButton');
    const validacionCompleta = validoNombre && validoRivales && validoLocales && validoNombresRivales;
    
    submitButton.disabled = !validacionCompleta;
    if (!validacionCompleta) {
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    
    return validacionCompleta;
}

// Envío del formulario
document.getElementById('ligaForm').addEventListener('submit', function(event) {
    if (!validarFormulario()) {
        event.preventDefault();
    }
});

// Asignar eventos de validación
document.getElementById('nombre_liga').addEventListener('input', validarNombreLiga);
document.getElementById('numero_rivales').addEventListener('input', validarNumeroRivales);
document.getElementById('solo_ida').addEventListener('change', validarPartidosLocales);

// Validar inicialmente
validarFormulario();
</script>
@endsection