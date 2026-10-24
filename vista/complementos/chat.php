<?php
$loveMakeupRoot = dirname(__DIR__, 2);
require_once $loveMakeupRoot . '/vendor/autoload.php';
\Dotenv\Dotenv::createImmutable($loveMakeupRoot, 'passconfig.env')->safeLoad();

// Identidad real para la memoria del chat: la BD de Ángel asocia cada
// conversación al sub del JWT. Si hay sesión iniciada, sub = id del usuario;
// invitados: anon_* (su conversación no se guarda).
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

<!-- WIDGET DEL CHATBOT -->
<div class="bot-widget-container">
    <!-- Circulo Angel -->
    <div class="bot-avatar-trigger" id="avatarTrigger" onclick="abrirchat()">
        <img src="assets/img/angel2.png" alt="Angel Avatar">
    </div>

    <!-- Ventana del Chat -->
    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <div class="chat-header-info">
                <img src="assets/img/angel2.png" alt="Angel">
                <h4>Angel, Asesor de Belleza </h4>
            </div>
            <button class="close-btn" onclick="cerrarchat()">&times;</button>
        </div>

        <div class="chat-body" id="chatBody">
            <div class="message bot">
                ¡Hola! Soy Angel, Asesor de Belleza. ¿En qué puedo ayudarte hoy?
            </div>
        </div>

        <div class="chat-image-preview" id="imagePreview" hidden aria-label="Imágenes seleccionadas"></div>

        <div class="chat-legend">
            Las respuestas son basadas en IA
        </div>

        <div class="chat-footer">
            <input type="file" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif,image/avif,.avif" multiple hidden>
            <button
                class="chat-image-button"
                id="imageButton"
                type="button"
                aria-label="Seleccionar imágenes"
                title="Hasta 3 imágenes JPEG, PNG, WEBP, GIF o AVIF; máximo 5 MB cada una"
                onclick="document.getElementById('imageInput').click()"
            >
                <i class="fa-solid fa-image" aria-hidden="true"></i>
            </button>
            <input type="text" id="userInput" placeholder="Escribe un mensaje..." onkeypress="handleKeyPress(event)">
            <button id="sendBtn" onclick="enviarmensaje()">Enviar</button>
        </div>
    </div>

</div>

<script>
  const ANGEL_API_URL = <?= json_encode($angelApiUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const ANGEL_TOKEN = <?= json_encode($angelJwt, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  const avatarTrigger = document.getElementById('avatarTrigger');
  const chatWindow = document.getElementById('chatWindow');
  const chatBody = document.getElementById('chatBody');
  const userInput = document.getElementById('userInput');
  const sendBtn = document.getElementById('sendBtn');
  const imageInput = document.getElementById('imageInput');
  const imageButton = document.getElementById('imageButton');
  const imagePreview = document.getElementById('imagePreview');
  const MAX_IMAGENES = 3;
  const IMAGEN_MAX_BYTES = 5 * 1024 * 1024;
  const TIPOS_IMAGEN_PERMITIDOS = new Set([
    'image/jpeg',
    'image/png',
    'image/webp',
    'image/gif',
    'image/avif'
  ]);
  const historialConversacion = [
    {
      role: 'assistant',
      content: '¡Hola! Soy Angel, Asesor de Belleza. ¿En qué puedo ayudarte hoy?'
    }
  ];
  let imagePreviewUrls = [];
  let archivosImagenesSeleccionados = [];

  imageInput.addEventListener('change', () => {
    const seleccionados = Array.from(imageInput.files || []);
    const validos = [];
    const rechazados = [];

    seleccionados.forEach((archivo) => {
      if (!TIPOS_IMAGEN_PERMITIDOS.has(archivo.type)) {
        rechazados.push('Formato de archivo no permitido.');
      } else if (archivo.size > IMAGEN_MAX_BYTES) {
        rechazados.push(`${archivo.name}: supera el máximo de 5 MB.`);
      } else {
        validos.push(archivo);
      }
    });

    if (validos.length > MAX_IMAGENES) {
      rechazados.push(`Solo puedes adjuntar hasta ${MAX_IMAGENES} imágenes por mensaje.`);
    }
    archivosImagenesSeleccionados = validos.slice(0, MAX_IMAGENES);

    if (rechazados.length > 0) {
      añadirmensaje(rechazados.join(' '), 'bot');
    }

    actualizarBotonImagenes();
    mostrarVistaPrevia(archivosImagenesSeleccionados);
  });

  function abrirchat() {
    avatarTrigger.style.display = 'none';
    chatWindow.style.display = 'flex';
    userInput.focus();
  }

  function cerrarchat() {
    chatWindow.style.display = 'none';
    avatarTrigger.style.display = 'block';
  }

  function añadirmensaje(text, sender) {
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('message', sender);
    msgDiv.append(document.createTextNode(text));
    chatBody.appendChild(msgDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  function añadirmensajeConImagenes(text, imagenes) {
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('message', 'user');
    msgDiv.append(document.createTextNode(text));

    imagenes.forEach((imagen, index) => {
      const img = document.createElement('img');
      img.src = imagen;
      img.alt = `Imagen enviada ${index + 1}`;
      msgDiv.appendChild(img);
    });

    chatBody.appendChild(msgDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  async function enviarmensaje() {
    const imagenes = await convertirimagenes();
    const conImagen = imagenes.length > 0;
    const texto = userInput.value.trim()
      || (conImagen ? 'Recomiéndame según la imagen que envié' : '');
    if (texto === '') return;

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

      if (!usarApi) {
        throw new Error('El servicio de Ángel no está configurado.');
      }

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
          ? data.detail.map((error) => {
            const location = Array.isArray(error.loc)
              ? error.loc.filter((part) => part !== 'body').join('.')
              : '';
            return `${location ? `${location}: ` : ''}${error.msg || 'Dato inválido.'}`;
          }).join(' ')
          : typeof data.detail === 'string'
            ? data.detail
            : 'Ángel no está disponible.';
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
    } catch (error) {
      const mensajeError = error instanceof Error
        ? `No pude conectar con Ángel: ${error.message}`
        : 'No pude conectar con Ángel en este momento.';
      añadirmensaje(mensajeError, 'bot');
    } finally {
      indicadorEscribiendo.remove();
      sendBtn.disabled = false;
      userInput.focus();
    }
  }

  function mostrarIndicadorEscribiendo() {
    const indicador = document.createElement('div');
    indicador.classList.add('message', 'bot', 'typing-indicator');
    indicador.setAttribute('role', 'status');
    indicador.setAttribute('aria-label', 'Ángel está escribiendo');
    indicador.innerHTML = '<span></span><span></span><span></span>';
    chatBody.appendChild(indicador);
    chatBody.scrollTop = chatBody.scrollHeight;
    return indicador;
  }

  function limpiarimagenes() {
    imageInput.value = '';
    archivosImagenesSeleccionados = [];
    imageButton.dataset.count = '';
    imageButton.title = 'Hasta 3 imágenes JPEG, PNG, WEBP, GIF o AVIF; máximo 5 MB cada una';
    imageButton.innerHTML = '<i class="fa-solid fa-image" aria-hidden="true"></i>';
    imagePreviewUrls.forEach((url) => URL.revokeObjectURL(url));
    imagePreviewUrls = [];
    imagePreview.replaceChildren();
    imagePreview.hidden = true;
  }

  function actualizarBotonImagenes() {
    const count = archivosImagenesSeleccionados.length;
    imageButton.dataset.count = count > 0 ? String(count) : '';
    imageButton.title = count > 0
      ? `${count} imagen(es) seleccionada(s) de ${MAX_IMAGENES} máximo`
      : 'Hasta 3 imágenes JPEG, PNG, WEBP, GIF o AVIF; máximo 5 MB cada una';
    imageButton.innerHTML = '<i class="fa-solid fa-image" aria-hidden="true"></i>';
  }

  function quitarImagen(index) {
    archivosImagenesSeleccionados.splice(index, 1);
    imageInput.value = '';
    actualizarBotonImagenes();
    mostrarVistaPrevia(archivosImagenesSeleccionados);
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
      img.alt = `Vista previa de imagen ${index + 1}`;
      const botonQuitar = document.createElement('button');
      botonQuitar.type = 'button';
      botonQuitar.classList.add('chat-image-remove');
      botonQuitar.setAttribute('aria-label', `Quitar imagen ${index + 1}`);
      botonQuitar.title = `Quitar imagen ${index + 1}`;
      botonQuitar.textContent = '×';
      botonQuitar.addEventListener('click', () => quitarImagen(index));
      contenedor.append(img, botonQuitar);
      imagePreview.appendChild(contenedor);
    });
    imagePreview.hidden = archivos.length === 0;
  }

  // Reduce cada imagen antes de enviarla y respeta el límite de payload de la API.
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
    const errores = resultados.filter((resultado) => resultado.error).map((resultado) => resultado.error);
    if (errores.length > 0) {
      añadirmensaje(errores.join(' '), 'bot');
    }
    return resultados
      .filter((resultado) => resultado.dataUrl)
      .map((resultado) => resultado.dataUrl);
  }

  function handleKeyPress(event) {
    if (event.key === 'Enter') {
      enviarmensaje();
    }
  }
</script>
