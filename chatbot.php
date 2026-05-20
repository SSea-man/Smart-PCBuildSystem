<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_auth();
$user = get_auth_user();

$page_title = 'PC Builder Chatbot';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4">
  <div class="text-center mb-4">
    <h1 class="section-title"><i class="bi bi-robot me-2 text-accent"></i>PC Builder Assistant</h1>
    <p class="section-sub">Ask anything about PC building, components, or BDT prices.</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-8">

    <div class="chip-row mb-2">
        <?php
        $chips = [
          'Best gaming PC under ৳80,000?',
          'CPU vs GPU bottleneck explained',
          'DDR4 vs DDR5 in Bangladesh?',
          'Best motherboard for Ryzen 7',
          'How much PSU wattage do I need?',
        ];
        foreach ($chips as $chip): ?>
        <span class="chip" onclick="sendChip(this)"><?= sanitise($chip) ?></span>
        <?php endforeach; ?>
      </div>

      <!-- Chat container -->
      <div class="chat-container">
        <div class="chat-messages" id="chat-messages">
          <div class="msg ai">
            <div class="msg-avatar"><i class="bi bi-robot"></i></div>
            <div class="msg-bubble">
              Hi <?= sanitise($user['name']) ?>! 👋 I'm your PC building assistant. Ask me about component compatibility, budget recommendations, or the best deals from Star Tech, Ryans, and Techland. What are you building today?
            </div>
          </div>
        </div>

        <div class="chat-input-area">
          <div class="d-flex gap-2">
            <input type="text" id="chat-input" class="form-control"
                   placeholder="Ask about PC builds, components, prices…"
                   aria-label="Chat message" autocomplete="off">
            <button class="btn btn-accent px-3" id="send-btn" aria-label="Send message">
              <i class="bi bi-send-fill"></i>
            </button>
          </div>
          <div class="d-flex justify-content-between mt-2">
            <small class="text-muted"><i class="bi bi-shield-check me-1"></i>Responses may not reflect live prices</small>
            <small class="text-muted" id="rate-info">Unlimited Messages</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $inline_script = <<<'JS'
const messagesEl = document.getElementById('chat-messages');
const inputEl    = document.getElementById('chat-input');
const sendBtn    = document.getElementById('send-btn');
const history    = [];

function appendMsg(role, text) {
  const div = document.createElement('div');
  div.className = 'msg ' + role;
  const avatar = role === 'ai'
    ? '<div class="msg-avatar"><i class="bi bi-robot"></i></div>'
    : '<div class="msg-avatar"><i class="bi bi-person"></i></div>';
  div.innerHTML = avatar + '<div class="msg-bubble">' + text.replace(/\n/g,'<br>') + '</div>';
  messagesEl.appendChild(div);
  messagesEl.scrollTop = messagesEl.scrollHeight;
}

function showTyping() {
  const div = document.createElement('div');
  div.className = 'msg ai'; div.id = 'typing-indicator';
  div.innerHTML = '<div class="msg-avatar"><i class="bi bi-robot"></i></div><div class="msg-bubble"><div class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div></div>';
  messagesEl.appendChild(div);
  messagesEl.scrollTop = messagesEl.scrollHeight;
}
function removeTyping() { document.getElementById('typing-indicator')?.remove(); }

async function sendMessage(text) {
  if (!text.trim()) return;
  appendMsg('user', text);
  history.push({ role: 'user', content: text });
  inputEl.value = '';
  sendBtn.disabled = true;
  showTyping();

  try {
    const res = await fetch(window.BASE_URL + '/api/chatbot_proxy.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.CSRF_TOKEN },
      credentials: 'same-origin',
      body: JSON.stringify({ messages: history })
    });
    const data = await res.json();
    removeTyping();
    if (data.error) {
      appendMsg('ai', '⚠️ ' + data.error);
    } else {
      const reply = data.content || data.text || 'Sorry, I could not generate a response.';
      appendMsg('ai', reply);
      history.push({ role: 'assistant', content: reply });
      
      if (data.action) {
        const html = document.documentElement;
        const btnIcon = document.querySelector('#theme-toggle i');
        if (data.action === 'set_theme_dark') {
          html.setAttribute('data-bs-theme', 'dark');
          localStorage.setItem('theme', 'dark');
          if (btnIcon) btnIcon.className = 'bi bi-moon-stars-fill';
        } else if (data.action === 'set_theme_light') {
          html.setAttribute('data-bs-theme', 'light');
          localStorage.setItem('theme', 'light');
          if (btnIcon) btnIcon.className = 'bi bi-sun-fill';
        }
      }
    }
  } catch (err) {
    removeTyping();
    appendMsg('ai', '❌ Connection error. Please try again.');
  } finally {
    sendBtn.disabled = false;
    inputEl.focus();
  }
}

sendBtn.addEventListener('click', () => sendMessage(inputEl.value));
inputEl.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(inputEl.value); } });

window.sendChip = function(el) { sendMessage(el.textContent); };
JS;
include __DIR__ . '/templates/footer.php'; ?>
