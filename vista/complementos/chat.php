<?php
$loveMakeupRoot = dirname(__DIR__, 2);
require_once $loveMakeupRoot . '/vendor/autoload.php';
\Dotenv\Dotenv::createImmutable($loveMakeupRoot, 'passconfig.env')->safeLoad();

$angelApiUrl = $_ENV['ANGEL_API_URL'] ?? 'http://127.0.0.1:8000/api/chat';
$angelPrivateKeyPath = $_ENV['ANGEL_PRIVATE_KEY_PATH'] ?? '';
$angelPrivateKeyPassphrase = $_ENV['ANGEL_PRIVATE_KEY_PASSPHRASE'] ?? '';

$buildAngelJwt = static function (
    string $keyPath,
    string $passphrase = '',
    int $ttlSeconds = 3600
): string {
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
        'sub' => 'love_makeup_user',
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
<style>
  .chat-image-button { position: relative; }
  .chat-image-count {
    position: absolute; top: -5px; right: -5px;
    background: #e91e63; color: #fff; border-radius: 50%;
    font-size: 10px; line-height: 14px; min-width: 14px;
    text-align: center; font-weight: 600;
  }
</style>

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
            <input type="file" id="imageInput" accept="image/*" multiple hidden>
            <button
                class="chat-image-button"
                id="imageButton"
                type="button"
                aria-label="Seleccionar imágenes"
                title="Seleccionar imágenes"
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
  const historialConversacion = [];
  let imagePreviewUrls = [];

  imageInput.addEventListener('change', () => {
    const archivos = Array.from(imageInput.files || []).slice(0, 3);
    const count = archivos.length;
    imageButton.dataset.count = count > 0 ? String(count) : '';
    imageButton.title = count > 0
      ? `${count} imagen(es) seleccionada(s)`
      : 'Seleccionar imágenes';
    imageButton.innerHTML = count > 0
      ? `<i class="fa-solid fa-image" aria-hidden="true"></i><span class="chat-image-count">${count}</span>`
      : '<i class="fa-solid fa-image" aria-hidden="true"></i>';
    mostrarVistaPrevia(archivos);
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
        const detail = typeof data.detail === 'string' ? data.detail : 'Ángel no está disponible.';
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
    imageButton.dataset.count = '';
    imageButton.title = 'Seleccionar imágenes';
    imageButton.innerHTML = '<i class="fa-solid fa-image" aria-hidden="true"></i>';
    imagePreviewUrls.forEach((url) => URL.revokeObjectURL(url));
    imagePreviewUrls = [];
    imagePreview.replaceChildren();
    imagePreview.hidden = true;
  }

  function mostrarVistaPrevia(archivos) {
    imagePreviewUrls.forEach((url) => URL.revokeObjectURL(url));
    imagePreviewUrls = [];
    imagePreview.replaceChildren();

    archivos.forEach((archivo, index) => {
      const url = URL.createObjectURL(archivo);
      imagePreviewUrls.push(url);
      const img = document.createElement('img');
      img.src = url;
      img.alt = `Vista previa de imagen ${index + 1}`;
      imagePreview.appendChild(img);
    });
    imagePreview.hidden = archivos.length === 0;
  }

  // Reduce la imagen en el navegador (máx 1024 px, JPEG 0.85) y devuelve
  // data URLs base64: mantiene el payload liviano para /api/chat.
  function convertirimagenes() {
    const promesas = Array.from(imageInput.files || [])
      .slice(0, 3)
      .map((file) => new Promise((resolve) => {
        const lector = new FileReader();
        lector.onload = () => {
          const img = new Image();
          img.onload = () => {
            const escala = Math.min(1, 1024 / Math.max(img.width, img.height));
            const lienzo = document.createElement('canvas');
            lienzo.width = Math.max(1, Math.round(img.width * escala));
            lienzo.height = Math.max(1, Math.round(img.height * escala));
            lienzo.getContext('2d').drawImage(img, 0, 0, lienzo.width, lienzo.height);
            resolve(lienzo.toDataURL('image/jpeg', 0.85));
          };
          img.onerror = () => resolve('');
          img.src = lector.result;
        };
        lector.onerror = () => resolve('');
        lector.readAsDataURL(file);
      }));
    return Promise.all(promesas).then((lista) => lista.filter(Boolean));
  }

  function handleKeyPress(event) {
    if (event.key === 'Enter') {
      enviarmensaje();
    }
  }
</script>
