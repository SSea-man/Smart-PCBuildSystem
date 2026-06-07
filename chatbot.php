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

<style>
.chatbot-container-wrapper {
  max-width: 1000px;
  margin: 0 auto;
}

.chat-layout {
  display: grid;
  grid-template-columns: 1fr 180px;
  gap: 1.5rem;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 1.5rem;
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  margin-top: 1rem;
}

@media (max-width: 768px) {
  .chat-layout {
    grid-template-columns: 1fr;
    padding: 1rem;
  }
  .assistant-status-sidebar {
    display: none !important;
  }
}

.chat-area {
  display: flex;
  flex-direction: column;
  height: 60vh;
  min-height: 480px;
  justify-content: space-between;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding-right: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 1rem;
}

.chat-messages::-webkit-scrollbar {
  width: 4px;
}
.chat-messages::-webkit-scrollbar-track {
  background: transparent;
}
.chat-messages::-webkit-scrollbar-thumb {
  background: var(--border);
  border-radius: 2px;
}

.msg.user {
  align-self: flex-end;
  max-width: 75%;
  display: flex;
  justify-content: flex-end;
}

.msg.user .msg-content {
  background: var(--bg-input);
  color: var(--text-primary);
  padding: 0.65rem 1.15rem;
  border-radius: 20px;
  font-size: 0.88rem;
  line-height: 1.5;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.msg.ai {
  align-self: flex-start;
  display: flex;
  gap: 0.75rem;
  max-width: 85%;
  align-items: flex-start;
}

.msg.ai .msg-avatar-img {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.msg.ai .msg-content {
  color: var(--text-primary);
  font-size: 0.88rem;
  line-height: 1.5;
  padding-top: 0.25rem;
}

.msg.ai .msg-content p {
  margin-bottom: 0.5rem;
}
.msg.ai .msg-content ul, .msg.ai .msg-content ol {
  padding-left: 1.25rem;
  margin-bottom: 0.5rem;
}

.chat-suggestions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-bottom: 0.75rem;
}

.suggestion-chip {
  background: var(--bg-card);
  border: 1px solid var(--border);
  color: var(--text-primary);
  padding: 0.4rem 1rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  box-shadow: var(--shadow-sm);
  transition: all 0.2s ease;
}

.suggestion-chip:hover {
  background: var(--bg-card-hover);
  border-color: var(--accent);
  transform: translateY(-1px);
}

.suggestion-chip i {
  font-size: 0.9rem;
}

.chat-input-container {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 0.75rem 1rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.chat-input-container textarea {
  background: transparent;
  border: none;
  color: var(--text-primary);
  font-size: 0.88rem;
  resize: none;
  width: 100%;
  outline: none;
  padding: 0;
  font-family: var(--font-body);
}

.chat-input-container textarea::placeholder {
  color: var(--text-muted);
}

.chat-input-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-input-actions-left {
  display: flex;
  align-items: center;
}

.chat-input-actions-right {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-circle-action {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  color: var(--text-secondary);
  transition: all 0.2s ease;
  cursor: pointer;
  font-size: 1.1rem;
}

.btn-circle-action:hover {
  background: var(--bg-input);
  color: var(--text-primary);
}

.btn-circle-action.btn-plus {
  font-size: 1.2rem;
  color: var(--text-secondary);
}

.btn-circle-action.btn-mic {
  background: var(--bg-input);
  color: var(--text-primary);
}

.btn-circle-action.btn-send {
  background: linear-gradient(135deg, #ff4757, #ff6b81);
  color: #ffffff;
}

.btn-circle-action.btn-send:hover {
  transform: scale(1.03);
  box-shadow: 0 0 10px rgba(255, 71, 87, 0.3);
}

.assistant-status-sidebar {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  padding-top: 0.5rem;
  border-left: 1px solid var(--border);
  padding-left: 1.5rem;
}

.assistant-profile-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-bottom: 2rem;
}

.assistant-profile-img {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 0.75rem;
  border: 1px solid var(--border);
}

.assistant-profile-name {
  font-weight: 700;
  font-size: 0.95rem;
  color: var(--text-primary);
  margin: 0;
}

.assistant-profile-role {
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin: 0;
}

.floating-toolbelt {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  box-shadow: var(--shadow-sm);
}

.floating-toolbelt .btn-tool {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  color: var(--text-secondary);
  transition: all 0.2s ease;
  cursor: pointer;
  font-size: 0.9rem;
}

.floating-toolbelt .btn-tool:hover {
  background: var(--bg-card);
  color: var(--text-primary);
}
</style>

<div class="container-xl py-4 chatbot-container-wrapper">
  <div class="text-center mb-3">
    <h1 class="section-title" style="font-size: 1.5rem;"><i class="bi bi-robot me-2 text-accent"></i>PC Builder AI Assistant</h1>
    <p class="section-sub" style="font-size: 0.85rem;">Get instant recommendations on builds, parts compatibility, and local pricing.</p>
  </div>

  <div class="chat-layout">
    <div class="chat-area">
      <div class="chat-messages" id="chat-messages">
        <div class="msg ai">
          <img src="<?= BASE_URL ?>/assets/img/assistant_avatar.png" class="msg-avatar-img" alt="Lina avatar">
          <div class="msg-content">
            Good morning! Here's your assistant brief:
            <ul>
              <li>Get custom build recommendations under your budget</li>
              <li>Instantly compare CPU & GPU bottlenecks</li>
              <li>Check real-time stock compatibility</li>
            </ul>
            What specs are you looking to build or upgrade today?
          </div>
        </div>
      </div>

      <div class="chat-input-container">
        <textarea id="chat-input" rows="1" placeholder="Type a message or ask anything..." autocomplete="off"></textarea>
        
        <div class="chat-input-actions">
          <div class="chat-input-actions-left">
            <button class="btn-circle-action btn-plus" aria-label="Add attachments">
              <i class="bi bi-plus-lg"></i>
            </button>
          </div>
          <div class="chat-input-actions-right">
            <button class="btn-circle-action btn-mic" aria-label="Voice input">
              <i class="bi bi-mic-fill"></i>
            </button>
            <button class="btn-circle-action btn-send" id="send-btn" aria-label="Send">
              <i class="bi bi-send-fill"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="assistant-status-sidebar">
      <div class="assistant-profile-card">
        <img src="<?= BASE_URL ?>/assets/img/assistant_avatar.png" class="assistant-profile-img" alt="Lina profile">
        <h3 class="assistant-profile-name">Lina</h3>
        <p class="assistant-profile-role">AI Assistant</p>
      </div>

      <div class="floating-toolbelt">
        <button class="btn-tool" title="Magic Assist"><i class="bi bi-magic"></i></button>
        <button class="btn-tool" title="Quick Settings"><i class="bi bi-gear-fill"></i></button>
        <button class="btn-tool" title="System Status"><i class="bi bi-cpu-fill"></i></button>
      </div>
    </div>
  </div>
</div>

<?php $inline_script = <<<'JS'
const messagesEl = document.getElementById('chat-messages');
const inputEl    = document.getElementById('chat-input');
const sendBtn    = document.getElementById('send-btn');
const history    = [];

inputEl.addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});

function appendMsg(role, text) {
  const div = document.createElement('div');
  div.className = 'msg ' + role;
  
  if (role === 'ai') {
    const avatar = `<img src="${window.BASE_URL}/assets/img/assistant_avatar.png" class="msg-avatar-img" alt="Lina">`;
    div.innerHTML = avatar + '<div class="msg-content">' + text.replace(/\n/g, '<br>') + '</div>';
  } else {
    div.innerHTML = '<div class="msg-content">' + text.replace(/\n/g, '<br>') + '</div>';
  }
  
  messagesEl.appendChild(div);
  messagesEl.scrollTop = messagesEl.scrollHeight;
}

function showTyping() {
  const div = document.createElement('div');
  div.className = 'msg ai'; 
  div.id = 'typing-indicator';
  
  const avatar = `<img src="${window.BASE_URL}/assets/img/assistant_avatar.png" class="msg-avatar-img" alt="Lina">`;
  div.innerHTML = avatar + '<div class="msg-content text-muted italic-text">Thinking...</div>';
  
  messagesEl.appendChild(div);
  messagesEl.scrollTop = messagesEl.scrollHeight;
}

function removeTyping() { 
  document.getElementById('typing-indicator')?.remove(); 
}

async function sendMessage(text) {
  if (!text.trim()) return;
  appendMsg('user', text);
  history.push({ role: 'user', content: text });
  inputEl.value = '';
  inputEl.style.height = 'auto';
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
      appendMsg('ai', 'Error: ' + data.error);
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
    appendMsg('ai', 'Connection error. Please try again.');
  } finally {
    sendBtn.disabled = false;
    inputEl.focus();
  }
}

sendBtn.addEventListener('click', () => sendMessage(inputEl.value));
inputEl.addEventListener('keydown', e => { 
  if (e.key === 'Enter' && !e.shiftKey) { 
    e.preventDefault(); 
    sendMessage(inputEl.value); 
  } 
});

window.sendChip = function(text) { 
  sendMessage(text); 
};

window.downloadPdf = function(jsonStr) {
    const build = JSON.parse(decodeURIComponent(jsonStr));
    let rows = '';
    let total = 0;
    build.forEach(item => {
        rows += `<tr>
            <td><strong>${item.category}</strong></td>
            <td>${item.name}</td>
            <td style="text-align:right">৳${item.price.toLocaleString()}</td>
        </tr>`;
        total += item.price;
    });

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>PC Build Summary</title>
            <style>
                body { font-family: system-ui, -apple-system, sans-serif; padding: 40px; color: #111; max-width: 800px; margin: 0 auto; }
                h1 { color: #10b981; margin-bottom: 5px; }
                p { color: #666; margin-top: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 30px; }
                th, td { border: 1px solid #ddd; padding: 12px 15px; text-align: left; }
                th { background-color: #f8f9fa; color: #333; text-transform: uppercase; font-size: 0.85rem; }
                .total-row { font-weight: bold; background-color: #f8f9fa; }
                .total-price { color: #10b981; font-size: 1.2rem; text-align: right; }
                .footer { margin-top: 40px; text-align: center; font-size: 0.85rem; color: #888; border-top: 1px solid #ddd; padding-top: 20px; }
            </style>
        </head>
        <body>
            <h1>PCBuilder BD</h1>
            <p>Custom PC Configuration Summary</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Component</th>
                        <th style="text-align:right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right">Total Price:</td>
                        <td class="total-price">৳${total.toLocaleString()}</td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="footer">
                Generated by PCBuilder BD AI Assistant on ${new Date().toLocaleDateString()}<br>
                For any questions, visit our website.
            </div>
            
            <script>
                window.onload = function() { 
                    setTimeout(() => {
                        window.print();
                    }, 500);
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
};
JS;
include __DIR__ . '/templates/footer.php'; ?>
