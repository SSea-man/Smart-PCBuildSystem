</main>
<!-- ── Footer ──────────────────────────────────────────────────────────────── -->
<footer class="site-footer mt-5">
  <div class="container-xl">
    <div class="row g-4 py-5">
      <div class="col-lg-4">
        <a class="navbar-brand fw-800 d-flex align-items-center gap-2 mb-3" href="<?= BASE_URL ?>/index.php">
          <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
          <span>PC<span class="text-accent">Builder</span> BD</span>
        </a>
        <p class="text-muted small">AI-powered PC build recommendations for the Bangladeshi market. Live prices from Star Tech, Ryans &amp; Techland.</p>
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
          <li><a href="<?= BASE_URL ?>/chatbot.php" class="footer-link">AI Chatbot</a></li>
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
  window.BASE_URL  = '<?= BASE_URL ?>';
  window.CSRF_TOKEN = '<?= csrf_token() ?>';
  window.IS_LOGGED_IN = <?= is_logged_in() ? 'true' : 'false' ?>;
</script>
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
</html>
