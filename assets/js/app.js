/**

 */

'use strict';

(function () {
  const html = document.documentElement;
  const btn = document.getElementById('theme-toggle');
  const stored = localStorage.getItem('theme') || 'dark';
  html.setAttribute('data-bs-theme', stored);
  updateThemeIcon(stored);

  if (btn) {
    btn.addEventListener('click', () => {
      const next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-bs-theme', next);
      localStorage.setItem('theme', next);
      updateThemeIcon(next);
    });
  }

  function updateThemeIcon(theme) {
    if (!btn) return;
    btn.querySelector('i').className = theme === 'dark' ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
  }
})();

document.querySelectorAll('#flash-container .alert').forEach(el => {
  setTimeout(() => {
    const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
    if (bsAlert) bsAlert.close();
  }, 5000);
});

function csrfHeaders() {
  return { 'X-CSRF-Token': window.CSRF_TOKEN || '', 'Content-Type': 'application/json' };
}

async function apiFetch(url, body = null, method = 'POST') {
  const opts = {
    method,
    headers: csrfHeaders(),
    credentials: 'same-origin',
  };
  if (body !== null) opts.body = JSON.stringify(body);
  const res = await fetch(window.BASE_URL + url, opts);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.watchlist-btn');
  if (!btn || !window.IS_LOGGED_IN) return;
  e.preventDefault();

  const id = btn.dataset.id;
  const action = btn.dataset.action;
  btn.disabled = true;

  try {
    const data = await apiFetch('/api/watchlist.php', { action, component_id: parseInt(id) });
    if (data.success) {
      const isAdding = action === 'add';
      btn.dataset.action = isAdding ? 'remove' : 'add';
      btn.classList.toggle('btn-accent', isAdding);
      btn.classList.toggle('btn-outline-secondary', !isAdding);
      btn.querySelector('i').className = isAdding ? 'bi bi-bell-fill' : 'bi bi-bell';
      btn.title = isAdding ? 'Remove from watchlist' : 'Watch price';
      showToast(isAdding ? 'Added to watchlist' : 'Removed from watchlist', 'success');
    }
  } catch (err) {
    showToast('Failed to update watchlist', 'danger');
  } finally {
    btn.disabled = false;
  }
});

const compareIds = JSON.parse(sessionStorage.getItem('compare_ids') || '[]');

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.compare-toggle-btn');
  if (!btn) return;
  const id = parseInt(btn.dataset.id);
  const name = btn.dataset.name || '';
  const idx = compareIds.indexOf(id);

  if (idx === -1) {
    if (compareIds.length >= 4) {
      showToast('Maximum 4 components can be compared', 'warning'); return;
    }
    compareIds.push(id);
    btn.classList.add('active');
    btn.innerHTML = '<i class="bi bi-layout-split me-1"></i>Comparing';
    showToast(`"${name}" added to compare`, 'info');
  } else {
    compareIds.splice(idx, 1);
    btn.classList.remove('active');
    btn.innerHTML = '<i class="bi bi-layout-split me-1"></i>Compare';
  }
  sessionStorage.setItem('compare_ids', JSON.stringify(compareIds));
  updateCompareBar();
});

function updateCompareBar() {
  let bar = document.getElementById('compare-bar');
  if (compareIds.length === 0) { if (bar) bar.remove(); return; }
  if (!bar) {
    bar = document.createElement('div');
    bar.id = 'compare-bar';
    bar.style.cssText = 'position:fixed;bottom:0;left:0;right:0;z-index:1040;background:var(--bg-card);border-top:1px solid var(--border);padding:.75rem 1rem;display:flex;align-items:center;gap:1rem;';
    document.body.appendChild(bar);
  }
  bar.innerHTML = `
    <span class="text-muted small me-auto"><i class="bi bi-layout-split me-1"></i>${compareIds.length} component(s) selected</span>
    <a href="${window.BASE_URL}/compare.php?ids=${compareIds.join(',')}" class="btn btn-sm btn-accent">Compare Now</a>
    <button class="btn btn-sm btn-outline-secondary" onclick="clearCompare()">Clear</button>`;
}

window.clearCompare = function () {
  compareIds.length = 0;
  sessionStorage.removeItem('compare_ids');
  const bar = document.getElementById('compare-bar');
  if (bar) bar.remove();
  document.querySelectorAll('.compare-toggle-btn.active').forEach(b => {
    b.classList.remove('active');
    b.innerHTML = '<i class="bi bi-layout-split me-1"></i>Compare';
  });
};
updateCompareBar();

document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.save-build-btn');
  if (!btn) return;
  const buildData = JSON.parse(btn.dataset.build || '{}');
  btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
  try {
    const data = await apiFetch('/api/save_build.php', buildData);
    if (data.success) {
      btn.classList.replace('btn-accent', 'btn-success');
      btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Saved!';
      showToast('Build saved to dashboard!', 'success');
    } else {
      throw new Error(data.error || 'Failed');
    }
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-bookmark-plus me-1"></i>Save Build';
    showToast('Could not save build: ' + err.message, 'danger');
  }
});

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.share-build-btn');
  if (!btn) return;
  const url = window.location.href;
  if (navigator.clipboard) {
    navigator.clipboard.writeText(url).then(() => showToast('Link copied!', 'success'));
  } else {
    showToast('Copy this URL: ' + url, 'info');
  }
});

function showToast(message, type = 'info') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1100';
    document.body.appendChild(container);
  }
  const id = 'toast-' + Date.now();
  const icon = { success: 'bi-check-circle-fill', danger: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', info: 'bi-info-circle-fill' }[type] || 'bi-info-circle-fill';
  container.insertAdjacentHTML('beforeend', `
    <div id="${id}" class="toast align-items-center text-bg-${type === 'info' ? 'primary' : type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body"><i class="bi ${icon} me-2"></i>${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>`);
  const el = document.getElementById(id);
  bootstrap.Toast.getOrCreateInstance(el, { delay: 4000 }).show();
  el.addEventListener('hidden.bs.toast', () => el.remove());
}

window.showToast = showToast;
