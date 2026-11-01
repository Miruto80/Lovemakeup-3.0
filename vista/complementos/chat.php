<?php
$loveMakeupRoot = dirname(__DIR__, 2);
require_once $loveMakeupRoot . '/vendor/autoload.php';
\Dotenv\Dotenv::createImmutable($loveMakeupRoot, 'passconfig.env')->safeLoad();

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
$angelUsuario = isset($_SESSION['id']) && !empty($_SESSION['id'])
    ? (string) $_SESSION['id']
    : null;

$angelApiUrl = $_ENV['ANGEL_API_URL'] ?? 'http://127.0.0.1:8000/api/chat';
$angelPrivateKeyPath = $_ENV['ANGEL_PRIVATE_KEY_PATH'] ?? '';
$angelPrivateKeyPassphrase = $_ENV['ANGEL_PRIVATE_KEY_PASSPHRASE'] ?? '';

$buildAngelJwt = static function (
    string $keyPath,
    string $passphrase = '',
    int $ttlSeconds = 3600
) use ($angelUsuario): string {
    if ($keyPath === '') {
        error_log('ANGEL_PRIVATE_KEY_PATH no está configurado.');
        return '';
    }

    if (!is_file($keyPath)) {
        $keyPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . ltrim($keyPath, '\\/');
    }

    $privateKeyContents = @file_get_contents($keyPath);
    $privateKey = $privateKeyContents === false
        ? false
        : openssl_pkey_get_private($privateKeyContents, $passphrase);
    if ($privateKey === false) {
        error_log('No se pudo cargar la clave privada JWT de Ángel.');
        return '';
    }

    $base64UrlEncode = static function (string $value): string {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    };

    $issuedAt = time();
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $payload = [
        'sub' => $angelUsuario !== null
            ? $angelUsuario
            : 'anon_' . bin2hex(random_bytes(4)),
        'iat' => $issuedAt,
        'exp' => $issuedAt + $ttlSeconds,
    ];
    $encodedHeader = $base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR));
    $encodedPayload = $base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));
    $signingInput = $encodedHeader . '.' . $encodedPayload;

    if (!openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        error_log('No se pudo firmar el JWT de Ángel.');
        return '';
    }

    return $signingInput . '.' . $base64UrlEncode($signature);
};

$angelJwt = $buildAngelJwt($angelPrivateKeyPath, $angelPrivateKeyPassphrase);
?>

<!-- CONTENEDOR DEL WIDGET -->
<div class="bot-widget-container" id="botWidgetContainer">
    <!-- Trigger Avatar Flotante -->
    <div class="bot-avatar-trigger" id="avatarTrigger" onclick="abrirchat()">
        <img src="assets/img/angel2.png" alt="Angel Avatar">
    </div>

    <!-- Ventana del Chat -->
    <div class="chat-window" id="chatWindow">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-header-info" onclick="abrirModalInfo()">
                <div class="story-ring-container">
                    <img src="assets/img/angel2.png" alt="Angel Avatar">
                </div>
                <div class="chat-header-text">
                    <h4>Angel, Asesor de Belleza</h4>
                    <span>Toca para más información</span>
                </div>
            </div>
            <button class="close-btn" onclick="cerrarchat()">&times;</button>
        </div>

        <!-- Historial / Cuerpo -->
        <div class="chat-body" id="chatBody">
            <div class="chat-row bot">
                <img src="assets/img/angel2.png" class="avatar-chat-small" alt="Angel">
                <div class="message-bubble bot">
                    ¡Hola! Soy Angel, Asesor de Belleza. ¿En qué puedo ayudarte hoy?
                </div>
            </div>
        </div>

        <!-- Previsualización de imágenes seleccionadas -->
        <div class="chat-image-preview" id="imagePreview" hidden aria-label="Imágenes seleccionadas"></div>

        <!-- Footer / Input -->
        <div class="chat-footer-wrapper">
            <div class="chat-input-row">
                <input type="file" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif,image/avif,.avif" multiple hidden>
                
                <button class="chat-action-icon" type="button" title="Adjuntar imágenes" onclick="document.getElementById('imageInput').click()">
                    <i class="fa-solid fa-image"></i>
                </button>
                
                <input type="text" id="userInput" placeholder="Escribe un mensaje..." onkeypress="handleKeyPress(event)">
                
                <button class="send-btn-circle" id="sendBtn" onclick="enviarmensaje()">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
            <div class="chat-disclaimer">
                Las respuestas son basadas en IA, pueden tener errores.
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE INFORMACIÓN DEL ASESOR (PARIDAD CON ASESOR.JSX) -->
<div class="chat-modal-overlay" id="modalInfoAsesor" style="display: none;">
    <div class="card-modal-container">
        <button class="card-close-btn" onclick="cerrarModalInfo()">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="card-story-ring">
            <img src="assets/img/angel2.png" alt="Angel">
        </div>
        <h3 class="card-title">Asesor de Belleza IA</h3>
        <span class="card-subtitle">LoveMakeup C.A</span>
        
        <div class="card-badge">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>Inteligencia Artificial</span>
        </div>
        
        <p class="card-description">
            ¡Hola! Soy Angel tu asesor de belleza, especializado en productos de cosmética y cuidado personal. Mis recomendaciones son basadas en inteligencia artificial experta en el área de productos cosméticos.
        </p>

        <div class="card-features">
            <div class="feature-item">
                <i class="fa-solid fa-circle-check"></i>
                <span>Recomendaciones personalizadas</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-circle-check"></i>
                <span>Análisis de imágenes, tu rostro</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-circle-check"></i>
                <span>Atención 24/7</span>
            </div>
        </div>

        <button class="card-action-btn" onclick="cerrarModalInfo()">Comenzar Consulta</button>
    </div>
</div>

<!-- MODAL LIGHTBOX / VISOR DE IMÁGENES -->
<div class="lightbox-modal" id="lightboxModal" style="display: none;">
    <button class="lightbox-close" onclick="cerrarLightbox()">&times;</button>
    <img id="lightboxImg" src="" alt="Imagen ampliada">
</div>

<script>
  // CONFIGURACIÓN Y CONSTANTES

  const ANGEL_API_URL = <?= json_encode($angelApiUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const ANGEL_TOKEN = <?= json_encode($angelJwt, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const ANGEL_USUARIO_LOGUEADO = <?= json_encode($angelUsuario !== null); ?>;

  const MAX_IMAGENES = 3;
  const MAX_RESPUESTAS_INVITADO = 3;
  const IMAGEN_MAX_BYTES = 5 * 1024 * 1024;
  const TIPOS_IMAGEN_PERMITIDOS = new Set(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif']);

  const REGEX_VALIDACION_INPUT = /^(?=(?:.*[a-zA-ZáéíóúÁÉÍÓÚñÑ]){4,})[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,:;%$#?¡¿!]+$/;
  
  
  // ESTADO Y REFERENCIAS AL DOM

  const avatarTrigger = document.getElementById('avatarTrigger');
  const chatWindow = document.getElementById('chatWindow');
  const chatBody = document.getElementById('chatBody');
  const userInput = document.getElementById('userInput');
  const sendBtn = document.getElementById('sendBtn');
  const imageInput = document.getElementById('imageInput');
  const imagePreview = document.getElementById('imagePreview');

  const historialConversacion = [
    { role: 'assistant', content: '¡Hola! Soy Angel, Asesor de Belleza. ¿En qué puedo ayudarte hoy?' }
  ];

  let imagePreviewUrls = [];
  let archivosImagenesSeleccionados = [];
  let respuestasInvitado = 0;
  let limiteInvitadoAlcanzado = false;
  let enviandoMensaje = false;

  
  // FUNCIONES DE CONTROL DE INTERFAZ Y MODALES

  function abrirchat() {
    avatarTrigger.style.display = 'none';
    chatWindow.style.display = 'flex';
    userInput.focus();
  }

  function cerrarchat() {
    chatWindow.style.display = 'none';
    avatarTrigger.style.display = 'block';
  }

  function abrirModalInfo() {
    document.getElementById('modalInfoAsesor').style.display = 'flex';
  }

  function cerrarModalInfo() {
    document.getElementById('modalInfoAsesor').style.display = 'none';
  }

  function abrirLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxModal').style.display = 'flex';
  }

  function cerrarLightbox() {
    document.getElementById('lightboxModal').style.display = 'none';
  }


  // FUNCIONES DE VALIDACIÓN Y MANEJO DE TECLADO

  function esFormularioValido() {
    if (limiteInvitadoAlcanzado || enviandoMensaje) return false;
    const tieneImagenes = archivosImagenesSeleccionados.length > 0;
    const textoValido = REGEX_VALIDACION_INPUT.test(userInput.value);
    return textoValido || tieneImagenes;
  }

  function actualizarEstadoBotonEnviar() {
    sendBtn.disabled = !esFormularioValido();
  }

  function handleKeyPress(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      if (esFormularioValido()) {
        enviarmensaje();
      }
    }
  }

  // FUNCIONES DE RENDERIZADO DE MENSAJES

  function añadirmensaje(text, sender) {
    const row = document.createElement('div');
    row.classList.add('chat-row', sender);

    if (sender === 'bot') {
      const avatar = document.createElement('img');
      avatar.src = 'assets/img/angel2.png';
      avatar.classList.add('avatar-chat-small');
      avatar.alt = 'Angel';
      row.appendChild(avatar);
    }

    const bubble = document.createElement('div');
    bubble.classList.add('message-bubble', sender);
    bubble.textContent = text;

    row.appendChild(bubble);
    chatBody.appendChild(row);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  function añadirmensajeConImagenes(text, imagenes) {
    const row = document.createElement('div');
    row.classList.add('chat-row', 'user');

    const bubble = document.createElement('div');
    bubble.classList.add('message-bubble', 'user');

    if (imagenes && imagenes.length > 0) {
      const grid = document.createElement('div');
      grid.classList.add('bubble-images-grid');

      imagenes.forEach((imgSrc) => {
        const img = document.createElement('img');
        img.src = imgSrc;
        img.onclick = () => abrirLightbox(imgSrc);
        grid.appendChild(img);
      });
      bubble.appendChild(grid);
    }

    if (text) {
      const textSpan = document.createElement('span');
      textSpan.textContent = text;
      bubble.appendChild(textSpan);
    }

    row.appendChild(bubble);
    chatBody.appendChild(row);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  function mostrarIndicadorEscribiendo() {
    const row = document.createElement('div');
    row.classList.add('chat-row', 'bot');

    const avatar = document.createElement('img');
    avatar.src = 'assets/img/angel2.png';
    avatar.classList.add('avatar-chat-small');
    avatar.alt = 'Angel';

    const bubble = document.createElement('div');
    bubble.classList.add('message-bubble', 'bot');
    bubble.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';

    row.append(avatar, bubble);
    chatBody.appendChild(row);
    chatBody.scrollTop = chatBody.scrollHeight;
    return row;
  }

  // FUNCIONES DE MANEJO DE IMÁGENES

  function procesarSeleccionImagenes(files) {
    const seleccionados = Array.from(files || []);
    const validos = [];

    seleccionados.forEach((archivo) => {
      if (TIPOS_IMAGEN_PERMITIDOS.has(archivo.type) && archivo.size <= IMAGEN_MAX_BYTES) {
        validos.push(archivo);
      }
    });

    archivosImagenesSeleccionados = validos.slice(0, MAX_IMAGENES);
    mostrarVistaPrevia(archivosImagenesSeleccionados);
    actualizarEstadoBotonEnviar();
  }

  function quitarImagen(index) {
    archivosImagenesSeleccionados.splice(index, 1);
    imageInput.value = '';
    mostrarVistaPrevia(archivosImagenesSeleccionados);
    actualizarEstadoBotonEnviar();
  }

  function mostrarVistaPrevia(archivos) {
    imagePreviewUrls.forEach((url) => URL.revokeObjectURL(url));
    imagePreviewUrls = [];
    imagePreview.replaceChildren();

    archivos.forEach((archivo, index) => {
      const url = URL.createObjectURL(archivo);
      imagePreviewUrls.push(url);

      const contenedor = document.createElement('div');
      contenedor.classList.add('chat-image-preview-item');

      const img = document.createElement('img');
      img.src = url;

      const botonQuitar = document.createElement('button');
      botonQuitar.type = 'button';
      botonQuitar.classList.add('chat-image-remove');
      botonQuitar.textContent = '×';
      botonQuitar.onclick = () => quitarImagen(index);

      contenedor.append(img, botonQuitar);
      imagePreview.appendChild(contenedor);
    });

    imagePreview.hidden = archivos.length === 0;
  }

  function limpiarimagenes() {
    imageInput.value = '';
    archivosImagenesSeleccionados = [];
    imagePreviewUrls.forEach((url) => URL.revokeObjectURL(url));
    imagePreviewUrls = [];
    imagePreview.replaceChildren();
    imagePreview.hidden = true;
  }

  // MANEJO DE PEGAR DESDE EL PORTAPAPELES 
  function manejarPegadoPortapapeles(event) {
    const elementos = (event.clipboardData || event.originalEvent.clipboardData)?.items;
    if (!elementos) return;

    const archivosPegados = [];

    for (let i = 0; i < elementos.length; i++) {
      const item = elementos[i];
      // Verificar si el elemento en el portapapeles es una imagen
      if (item.type.indexOf('image') !== -1) {
        const archivo = item.getAsFile();
        if (archivo) {
          archivosPegados.push(archivo);
        }
      }
    }

    if (archivosPegados.length > 0) {
      event.preventDefault(); 
      // Combinar las imágenes actuales con las nuevas pegadas
      const combinadas = [...archivosImagenesSeleccionados, ...archivosPegados];
      // Filtrar y aplicar las reglas de validación
      const validos = [];
      combinadas.forEach((archivo) => {
        if (TIPOS_IMAGEN_PERMITIDOS.has(archivo.type) && archivo.size <= IMAGEN_MAX_BYTES) {
          validos.push(archivo);
        }
      });

      archivosImagenesSeleccionados = validos.slice(0, MAX_IMAGENES); // límite de 3 imágenes
      // Actualizar vista previa y revalidar el botón de envío
      mostrarVistaPrevia(archivosImagenesSeleccionados);
      actualizarEstadoBotonEnviar();
    }
  }

  // Pegar en el input de texto
  userInput.addEventListener('paste', manejarPegadoPortapapeles);

  async function convertirimagenes() {
    const resultados = await Promise.all(archivosImagenesSeleccionados.map((file) =>
      new Promise((resolve) => {
        const lector = new FileReader();
        lector.onload = () => {
          const img = new Image();
          img.onload = () => {
            const escala = Math.min(1, 1024 / Math.max(img.width, img.height));
            const lienzo = document.createElement('canvas');
            lienzo.width = Math.max(1, Math.round(img.width * escala));
            lienzo.height = Math.max(1, Math.round(img.height * escala));
            const contexto = lienzo.getContext('2d');
            if (!contexto) {
              resolve({ error: `${file.name}: no se pudo procesar la imagen.` });
              return;
            }
            contexto.drawImage(img, 0, 0, lienzo.width, lienzo.height);
            const dataUrl = lienzo.toDataURL('image/jpeg', 0.85);
            resolve(dataUrl.length <= 4_000_000
              ? { dataUrl }
              : { error: `${file.name}: la imagen procesada supera el máximo permitido.` });
          };
          img.onerror = () => resolve({ error: 'El archivo seleccionado no es una imagen válida.' });
          img.src = lector.result;
        };
        lector.onerror = () => resolve({ error: `${file.name}: no se pudo leer el archivo.` });
        lector.readAsDataURL(file);
      })
    ));

    const errores = resultados.filter((res) => res.error).map((res) => res.error);
    if (errores.length > 0) añadirmensaje(errores.join(' '), 'bot');

    return resultados.filter((res) => res.dataUrl).map((res) => res.dataUrl);
  }

  // FUNCIONALIDAD PRINCIPAL DE ENVÍO
  function limitarChatInvitado() {
    limiteInvitadoAlcanzado = true;
    añadirmensaje('Necesitas iniciar sesión para seguir consultando con Ángel.', 'bot');
    userInput.disabled = true;
    userInput.placeholder = 'Inicia sesión para continuar';
    sendBtn.disabled = true;
    imageInput.disabled = true;
  }

  async function enviarmensaje() {
    if (!esFormularioValido()) return;

    enviandoMensaje = true;
    sendBtn.disabled = true;

    let imagenes;
    try {
      imagenes = await convertirimagenes();
    } catch (error) {
      const detalle = error instanceof Error ? error.message : 'Error inesperado.';
      añadirmensaje(`No pude procesar las imágenes: ${detalle}`, 'bot');
      enviandoMensaje = false;
      actualizarEstadoBotonEnviar();
      userInput.focus();
      return;
    }

    const conImagen = imagenes.length > 0;
    const texto = userInput.value.trim() || (conImagen ? 'Recomiéndame según la imagen que envié' : '');

    if (conImagen) {
      añadirmensajeConImagenes(texto, imagenes);
    } else {
      añadirmensaje(texto, 'user');
    }

    userInput.value = '';
    limpiarimagenes();
    sendBtn.disabled = true;
    const indicadorEscribiendo = mostrarIndicadorEscribiendo();

    try {
      const usarApi = !!ANGEL_TOKEN && !!ANGEL_API_URL && ANGEL_API_URL !== '' && ANGEL_API_URL !== 'null';
      if (!usarApi) throw new Error('El servicio de Ángel no está configurado.');

      const response = await fetch(ANGEL_API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ANGEL_TOKEN}`
        },
        body: JSON.stringify({
          mensaje: texto,
          historial: historialConversacion.slice(-12),
          imagenes: conImagen ? imagenes : undefined
        })
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        const detail = Array.isArray(data.detail)
          ? data.detail.map((err) => `${err.msg || 'Dato inválido.'}`).join(' ')
          : typeof data.detail === 'string' ? data.detail : 'Ángel no está disponible.';
        throw new Error(detail);
      }

      if (typeof data.respuesta !== 'string' || !data.respuesta.trim()) {
        throw new Error('Ángel devolvió una respuesta vacía.');
      }

      añadirmensaje(data.respuesta, 'bot');
      historialConversacion.push(
        { role: 'user', content: texto },
        { role: 'assistant', content: data.respuesta }
      );

      if (historialConversacion.length > 12) {
        historialConversacion.splice(0, historialConversacion.length - 12);
      }

      if (!ANGEL_USUARIO_LOGUEADO) {
        respuestasInvitado += 1;
        if (respuestasInvitado >= MAX_RESPUESTAS_INVITADO) {
          limitarChatInvitado();
        }
      }
    } catch (error) {
      const mensajeError = error instanceof Error
        ? `No pude conectar con Ángel: ${error.message}`
        : 'No pude conectar con Ángel en este momento.';
      añadirmensaje(mensajeError, 'bot');
    } finally {
      indicadorEscribiendo.remove();
      enviandoMensaje = false;
      if (!limiteInvitadoAlcanzado) {
        actualizarEstadoBotonEnviar();
        userInput.focus();
      }
    }
  }

  // INICIALIZACIÓN 
  userInput.addEventListener('input', actualizarEstadoBotonEnviar);
  imageInput.addEventListener('change', (e) => procesarSeleccionImagenes(e.target.files));

  // Estado inicial
  actualizarEstadoBotonEnviar();
</script>