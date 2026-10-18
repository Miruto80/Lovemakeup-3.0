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
        openssl_free_key($privateKey);
        error_log('No se pudo firmar el JWT de Ángel.');
        return '';
    }

    openssl_free_key($privateKey);

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

        <div class="chat-legend">
            Las respuestas son basadas en IA
        </div>

        <div class="chat-footer">
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
  const historialConversacion = [];

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
    msgDiv.textContent = text;
    chatBody.appendChild(msgDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  async function enviarmensaje() {
    const text = userInput.value.trim();
    if (text === '') return;

    añadirmensaje(text, 'user');
    userInput.value = '';
    sendBtn.disabled = true;

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
          mensaje: text,
          historial: historialConversacion.slice(-12)
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
        { role: 'user', content: text },
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
      sendBtn.disabled = false;
      userInput.focus();
    }
  }

  function handleKeyPress(event) {
    if (event.key === 'Enter') {
      enviarmensaje();
    }
  }
</script>
