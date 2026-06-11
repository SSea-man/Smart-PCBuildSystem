</main>
<footer class="site-footer mt-5">
  <div class="container-xl">
    <div class="row g-4 py-5">
      <div class="col-lg-4">
        <a class="navbar-brand fw-800 d-flex align-items-center gap-2 mb-3" href="<?= BASE_URL ?>/index.php">
          <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
          <span>PC<span class="text-accent">Builder</span> BD</span>
        </a>
        <p class="text-muted small mb-2">Smart PC build recommendations for the Bangladeshi market. Live prices from Star Tech, Ryans &amp; Techland.</p>
        <p class="text-muted small mb-0"><i class="bi bi-geo-alt-fill text-accent me-1"></i>Location: United City, Madani Avenue, Dhaka</p>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-heading">Build</h6>
        <ul class="list-unstyled">
          <li><a href="<?= BASE_URL ?>/purpose.php" class="footer-link">Build Wizard</a></li>
          <li><a href="<?= BASE_URL ?>/custom_builder.php" class="footer-link">Custom Builder</a></li>
          <li><a href="<?= BASE_URL ?>/upgrade.php" class="footer-link">Upgrade Advisor</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-heading">Explore</h6>
        <ul class="list-unstyled">
          <li><a href="<?= BASE_URL ?>/store.php" class="footer-link">Store</a></li>
          <li><a href="<?= BASE_URL ?>/compare.php" class="footer-link">Compare</a></li>
          <li><a href="<?= BASE_URL ?>/price_history.php" class="footer-link">Price History</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-heading">Account</h6>
        <ul class="list-unstyled">
          <li><a href="<?= BASE_URL ?>/dashboard.php" class="footer-link">Dashboard</a></li>
          <li><a href="<?= BASE_URL ?>/register.php" class="footer-link">Register</a></li>
          <li><a href="<?= BASE_URL ?>/chatbot.php" class="footer-link">Chatbot</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-heading">Retailers</h6>
        <ul class="list-unstyled">
          <li><span class="footer-link text-muted">Star Tech</span></li>
          <li><span class="footer-link text-muted">Ryans Computers</span></li>
          <li><span class="footer-link text-muted">Techland BD</span></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom py-3">
      <p class="text-muted small mb-0 text-center">
        &copy; <?= date('Y') ?> PC Builder BD &mdash; Built for Bangladesh &mdash;
        Prices in BDT (৳) &mdash; Data updated manually
      </p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
  window.BASE_URL  = '<?= rtrim(parse_url(BASE_URL, PHP_URL_PATH), "/") ?>';
  window.CSRF_TOKEN = '<?= csrf_token() ?>';
  window.IS_LOGGED_IN = <?= is_logged_in() ? 'true' : 'false' ?>;
</script>

<div class="modal fade" id="globalConfirmModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
    <div class="modal-content border-0" style="border-radius:20px; overflow:hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.3);">
      <div class="modal-body text-center p-0">
        <div id="gcm-icon-area" style="padding:2rem 2rem 0.75rem; background:linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
          <div id="gcm-icon" style="width:64px;height:64px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.8rem;margin-bottom:0.5rem;"></div>
        </div>
        <div style="padding:1.25rem 2rem 0.5rem;">
          <h5 id="gcm-title" style="font-weight:800;font-size:1.15rem;margin-bottom:0.5rem;"></h5>
          <p id="gcm-message" style="font-size:0.88rem;color:#6b7280;margin-bottom:1.25rem;line-height:1.5;"></p>
        </div>
        <div id="gcm-actions" style="display:flex;gap:0.75rem;padding:0 2rem 1.75rem;justify-content:center;"></div>
      </div>
    </div>
  </div>
</div>

<div id="globalToastContainer" style="position:fixed;top:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

<script>
function showConfirm({title, message, icon, iconBg, confirmText, confirmClass, onConfirm}) {
    const modal = document.getElementById('globalConfirmModal');
    document.getElementById('gcm-title').textContent = title || 'Are you sure?';
    document.getElementById('gcm-message').textContent = message || '';
    const iconEl = document.getElementById('gcm-icon');
    iconEl.innerHTML = icon || '<i class="bi bi-exclamation-triangle-fill"></i>';
    iconEl.style.background = iconBg || 'rgba(239,68,68,0.15)';
    iconEl.style.color = iconBg ? '#fff' : '#ef4444';
    if (iconBg) { iconEl.style.background = iconBg; iconEl.style.color = '#fff'; }
    else { iconEl.style.background = 'rgba(239,68,68,0.15)'; iconEl.style.color = '#ef4444'; }

    const actions = document.getElementById('gcm-actions');
    actions.innerHTML = `
        <button type="button" class="btn px-4 py-2" data-bs-dismiss="modal"
            style="border-radius:12px;font-weight:600;font-size:0.88rem;background:#f3f4f6;color:#374151;border:none;min-width:100px;">
            Cancel
        </button>
        <button type="button" class="btn px-4 py-2 ${confirmClass || 'btn-danger'}" id="gcm-confirm-btn"
            style="border-radius:12px;font-weight:600;font-size:0.88rem;min-width:100px;border:none;
                   ${!confirmClass ? 'background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;' : ''}">
            ${confirmText || 'Delete'}
        </button>
    `;

    const bsModal = new bootstrap.Modal(modal);
    const confirmBtn = document.getElementById('gcm-confirm-btn');
    const handler = () => { bsModal.hide(); if (onConfirm) onConfirm(); };
    confirmBtn.onclick = handler;
    bsModal.show();
}

function showToast(message, type) {
    const container = document.getElementById('globalToastContainer');
    const colors = {
        success: { bg: 'linear-gradient(135deg,#10b981,#059669)', icon: 'bi-check-circle-fill' },
        error:   { bg: 'linear-gradient(135deg,#ef4444,#dc2626)', icon: 'bi-x-circle-fill' },
        warning: { bg: 'linear-gradient(135deg,#f59e0b,#d97706)', icon: 'bi-exclamation-triangle-fill' },
        info:    { bg: 'linear-gradient(135deg,#3b82f6,#2563eb)', icon: 'bi-info-circle-fill' }
    };
    const c = colors[type] || colors.info;
    const toast = document.createElement('div');
    toast.style.cssText = `background:${c.bg};color:#fff;padding:0.85rem 1.25rem;border-radius:14px;
        font-size:0.88rem;font-weight:600;display:flex;align-items:center;gap:0.6rem;
        box-shadow:0 8px 30px rgba(0,0,0,0.2);pointer-events:auto;min-width:280px;
        animation:toastSlideIn 0.35s cubic-bezier(0.22,1,0.36,1);`;
    toast.innerHTML = `<i class="bi ${c.icon}" style="font-size:1.2rem;"></i><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'toastSlideOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<style>
@keyframes toastSlideIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
@keyframes toastSlideOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(40px); } }
#globalConfirmModal .modal-content { transition: transform 0.3s cubic-bezier(0.22,1,0.36,1); }
</style>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<?php if (!empty($footer_scripts)): ?>
  <?php foreach ($footer_scripts as $src): ?>
    <script src="<?= BASE_URL ?>/assets/js/<?= sanitise($src) ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($inline_script)): ?>
<script><?= $inline_script ?></script>
<?php endif; ?>
</body>
