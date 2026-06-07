<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/wattage.php';

require_auth();

$categories = ['CPU','Motherboard','RAM','GPU','Storage','PSU','Case','Cooling','Keyboard','Mouse','Monitor'];
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
              'Case'=>'bi-pc','Cooling'=>'bi-thermometer-snow', 'Keyboard'=>'bi-keyboard',
              'Mouse'=>'bi-mouse', 'Monitor'=>'bi-display', default=>'bi-box'
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
                      data-tdp="<?= (int)$comp['tdp_watts'] ?>"
                      data-psu-wattage="<?= (int)($comp['psu_wattage'] ?? 0) ?>">
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
        <div class="d-flex justify-content-between mb-2">
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
        <button class="btn btn-outline-info w-100 mt-2" id="export-build-btn" data-bs-toggle="modal" data-bs-target="#exportModal">
          <i class="bi bi-share me-1"></i>Share / Export Build
        </button>
        <a href="<?= BASE_URL ?>/compare.php" class="btn btn-outline-secondary w-100 mt-2">
          <i class="bi bi-layout-split me-1"></i>Compare Components
        </a>
        <button class="btn btn-outline-success w-100 mt-2" id="print-custom-btn" onclick="window.print()">
          <i class="bi bi-printer me-1"></i>Print Build
        </button>
      </div>

    </div>
  </div>

  <div class="d-none d-print-block mt-4" id="print-invoice-area" style="font-family: var(--font-body);">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
      <div>
        <h2 class="fw-800 m-0"><span class="brand-icon"><i class="bi bi-cpu-fill"></i></span> PCBuilder BD</h2>
        <small class="text-muted">Generated on: <span id="print-date"></span></small>
      </div>
      <div class="text-end">
        <h4 class="m-0 text-accent">Custom PC Configuration</h4>
        <small class="text-muted">Compatibility: Verified</small>
      </div>
    </div>

    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Category</th>
          <th>Product Name</th>
          <th class="text-end" style="width: 150px;">Price</th>
        </tr>
      </thead>
      <tbody id="print-table-body">
        </tbody>
      <tfoot>
        <tr>
          <th colspan="2" class="text-end">Estimated TDP:</th>
          <td id="print-tdp-val" class="fw-600 text-end">0W</td>
        </tr>
        <tr>
          <th colspan="2" class="text-end">Recommended PSU Wattage:</th>
          <td id="print-psu-val" class="fw-600 text-end">—</td>
        </tr>
        <tr>
          <th colspan="2" class="text-end">Total Price:</th>
          <td id="print-total-val" class="fw-bold text-accent text-end" style="font-size: 1.15rem;">৳0</td>
        </tr>
      </tfoot>
    </table>

    <div class="mt-5 pt-3 border-top text-center text-muted small">
      <p class="mb-1">Thank you for choosing PCBuilder BD. Compatibility and TDP estimates are verified using PCBuilder Smart system.</p>
      <p class="mb-0">Visit us at <strong>localhost/myproject</strong> to edit or upgrade your build.</p>
    </div>
  </div>

  <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border);">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="modal-title fw-700" id="exportModalLabel"><i class="bi bi-share-fill me-2 text-accent"></i>Share / Export Your Build</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small">Copy the generated markup below to easily share your custom PC build on the Community Forum, Reddit, or social media.</p>
          
          <ul class="nav nav-tabs border-bottom" id="exportTab" role="tablist" style="border-color: var(--border) !important;">
            <li class="nav-item" role="presentation">
              <button class="nav-link active text-reset border-0 px-3 py-2 fw-600" id="markdown-tab" data-bs-toggle="tab" data-bs-target="#markdown-pane" type="button" role="tab" aria-controls="markdown-pane" aria-selected="true">
                <i class="bi bi-markdown me-1 text-accent"></i>Forum Markdown
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link text-reset border-0 px-3 py-2 fw-600" id="text-tab" data-bs-toggle="tab" data-bs-target="#text-pane" type="button" role="tab" aria-controls="text-pane" aria-selected="false">
                <i class="bi bi-file-text me-1 text-accent"></i>Plain Text
              </button>
            </li>
          </ul>
          
          <div class="tab-content pt-3" id="exportTabContent">
            <div class="tab-pane fade show active" id="markdown-pane" role="tabpanel" aria-labelledby="markdown-tab" tabindex="0">
              <textarea class="form-control text-monospace text-white border-0 p-3 small" id="export-markdown-text" rows="10" readonly style="font-family:monospace; font-size: 0.85rem; background: rgba(0,0,0,0.3) !important; color: #fff !important;"></textarea>
            </div>
            <div class="tab-pane fade" id="text-pane" role="tabpanel" aria-labelledby="text-tab" tabindex="0">
              <textarea class="form-control text-monospace text-white border-0 p-3 small" id="export-plain-text" rows="10" readonly style="font-family:monospace; font-size: 0.85rem; background: rgba(0,0,0,0.3) !important; color: #fff !important;"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-accent px-4" id="copy-export-btn">
            <i class="bi bi-clipboard me-1"></i>Copy to Clipboard
          </button>
        </div>
      </div>
    </div>
  </div>

</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
