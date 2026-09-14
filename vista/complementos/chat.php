    <!-- WIDGET DEL CHATBOT -->
    <div class="bot-widget-container">
    
        <!-- Circulo Angel -->
        <div class="bot-avatar-trigger" id="avatarTrigger" onclick="abrirchat()">
            <img src="assets/img/angel.png" alt="Angel Avatar">
        </div>

        <!-- Ventana del Chat -->
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <div class="chat-header-info">
                    <!-- Miniatura -->
                    <img src="assets/img/angel.png" alt="Angel">
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
                Las respuetas son basadas en IA
            </div>

            <div class="chat-footer">
                <input type="text" id="userInput" placeholder="Escribe un mensaje..." onkeypress="handleKeyPress(event)">
                <button onclick="enviarmensaje()">Enviar</button>
            </div>
        </div>

    </div>

  <script>
    const avatarTrigger = document.getElementById('avatarTrigger');
    const chatWindow = document.getElementById('chatWindow');
    const chatBody = document.getElementById('chatBody');
    const userInput = document.getElementById('userInput');
    const widgetContainer = document.querySelector('.bot-widget-container');

    function abrirchat() {
       avatarTrigger.style.display = 'none';
        chatWindow.style.display = 'flex';
        userInput.focus();
    }

    function cerrarchat() {
        chatWindow.style.display = 'none';
        avatarTrigger.style.display = 'block';
    }

    function enviarmensaje() {
      const text = userInput.value.trim();
      if (text === '') return;

      // Mostrar mensaje del usuario
      añadirmensaje(text, 'user');
      userInput.value = '';

      // Simular respuesta de Angel
      setTimeout(() => {
        añadirmensaje("No disponible, por los momentos", 'bot');
      }, 800);
    }

    function añadirmensaje(text, sender) {
      const msgDiv = document.createElement('div');
      msgDiv.classList.add('message', sender);
      msgDiv.textContent = text;
      chatBody.appendChild(msgDiv);
      chatBody.scrollTop = chatBody.scrollHeight;
    }

    function handleKeyPress(event) {
      if (event.key === 'Enter') {
        enviarmensaje();
      }
    }
  </script>
