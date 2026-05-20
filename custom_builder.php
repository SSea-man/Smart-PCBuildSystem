<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/wattage.php';

$categories = ['CPU','Motherboard','RAM','GPU','Storage','PSU','Case','Cooling'];
$purpose    = $_SESSION['purpose'] ?? 'general';

$all_components = [];
foreach ($categories as $cat) {
    $all_components[$cat] = get_components_by_category($cat);
}

$page_title    = 'Custom PC Builder';

$footer_scripts = ['custom_builder.js'];
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4">
  <div class="text-center mb-5">
    <h1 class="section-title"><i class="bi bi-sliders me-2 text-accent"></i>Custom PC Builder</h1>
    <p class="section-sub">Hand-pick every component. Live compatibility check on every selection.</p>
  </div>

  <div class="row g-4">

  <div class="col-lg-8">
      <div class="card p-4">
        <div id="compat-result" class="d-none mb-3"></div>

        <div class="row g-3">
          <?php foreach ($categories as $cat):
            $icon = match($cat) {
              'CPU'=>'bi-cpu','Motherboard'=>'bi-motherboard','RAM'=>'bi-memory',
              'GPU'=>'bi-gpu-card','Storage'=>'bi-device-hdd','PSU'=>'bi-lightning-charge',
              'Case'=>'bi-pc','Cooling'=>'bi-thermometer-snow', default=>'bi-box'
            };
          ?>
          <div class="col-md-6">
            <label class="form-label fw-600">
              <i class="<?= $icon ?> me-1 text-accent"></i><?= $cat ?>
            </label>
            <select class="form-select component-select" data-category="<?= $cat ?>" id="select-<?= strtolower($cat) ?>">
              <option value="">— Select <?= $cat ?> —</option>
              <?php foreach ($all_components[$cat] as $comp): ?>
              <option value="<?= (int)$comp['id'] ?>"
                      data-price="<?= (float)$comp['price_bdt'] ?>"
                      data-tdp="<?= (int)$comp['tdp_watts'] ?>">
                <?= sanitise($comp['name']) ?> — <?= format_bdt((float)$comp['price_bdt']) ?>
                <?= $comp['stock_status'] !== 'in_stock' ? ' [Out of Stock]' : '' ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card p-4 sticky-top" style="top:80px">
        <h5 class="fw-700 mb-3"><i class="bi bi-receipt me-2 text-accent"></i>Build Summary</h5>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Total Price</span>
          <span class="fw-700 text-accent fs-5" id="builder-total">৳0</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Est. TDP</span>
          <span class="fw-600" id="builder-tdp">0W</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span class="text-muted">Min PSU</span>
          <span class="fw-600 text-warning" id="builder-min-psu">—</span>
        </div>
        <hr style="border-color:var(--border)">
        <p class="text-muted small mb-3">
          Compatibility is checked automatically as you select components.
        </p>
        <?php if (is_logged_in()): ?>
        <button class="btn btn-accent w-100" id="save-custom-btn">
          <i class="bi bi-bookmark-plus me-1"></i>Save Build
        </button>
        <?php else: ?>
        <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline-accent w-100">
          <i class="bi bi-person me-1"></i>Login to Save
        </a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/compare.php" class="btn btn-outline-secondary w-100 mt-2">
          <i class="bi bi-layout-split me-1"></i>Compare Components
        </a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
