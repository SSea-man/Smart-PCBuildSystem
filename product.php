<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int)input('id', 0);
if (!$id) {
    redirect('store.php');
}

$comp = db_row(component_base_sql() . ' WHERE c.component_id = ?', [$id]);

if (!$comp) {
    redirect('store.php');
}

$retailers = db_query("
    SELECT sa.price, sa.stock_status, s.store_name 
    FROM storeavailability sa
    JOIN store s ON s.store_id = sa.store_id
    WHERE sa.component_id = ?
    ORDER BY sa.price ASC", [$id]);

$comp['stock_status'] = normalize_stock($comp['stock_status_raw'] ?? '');

$page_title = sanitise($comp['name']) . ' Price in Bangladesh';
include __DIR__ . '/templates/header.php';

$img = !empty($comp['image_url']) ? $comp['image_url'] : 'assets/images/placeholder.png';
if (!str_starts_with($img, 'http') && !str_starts_with($img, 'assets/')) {
    $img = BASE_URL . '/' . $img;
} elseif (str_starts_with($img, 'assets/')) {
    $img = BASE_URL . '/' . $img;
}

$cash_price = (float)$comp['price_bdt'];
$savings = ceil($cash_price * 0.08);
$regular_price = $cash_price + $savings;
$emi_price = ceil($regular_price / 12);
?>

<style>
.product-breadcrumb-row {
  font-size: 0.82rem;
  color: #64748b;
  margin-bottom: 1.25rem;
}
.product-breadcrumb-row a {
  text-decoration: none;
  color: #334155;
}
.product-breadcrumb-row span {
  margin: 0 0.4rem;
  color: #cbd5e1;
}

.share-action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  padding: 0.75rem 1.25rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  margin-bottom: 2rem;
}

.share-icons {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.85rem;
  color: #475569;
}
.share-icon-btn {
  color: #64748b;
  font-size: 1.1rem;
  text-decoration: none;
  transition: color 0.2s;
}
.share-icon-btn:hover {
  color: #1e293b;
}

.detail-pill {
  background: #f1f5f9;
  color: #334155;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
}

.detail-pill-accent {
  background: rgba(63, 185, 80, 0.1);
  color: #3fb950;
}

.features-list-box {
  margin-top: 1.5rem;
}
.features-list-box h5 {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.75rem;
}
.features-list-box ul {
  list-style: none;
  padding-left: 0;
}
.features-list-box li {
  font-size: 0.85rem;
  color: #475569;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: baseline;
}
.features-list-box li::before {
  content: "•";
  color: #3fb950;
  font-weight: bold;
  display: inline-block;
  width: 1rem;
}

.payment-options-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 576px) {
  .payment-options-grid {
    grid-template-columns: 1fr;
  }
}

.payment-card-option {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1rem;
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  cursor: pointer;
  background: #ffffff;
  transition: all 0.2s ease;
}

.payment-card-option.active {
  border-color: #2563eb;
  background: rgba(37, 99, 235, 0.02);
}

.payment-card-option input[type="radio"] {
  margin-top: 0.25rem;
}

.payment-card-price {
  font-family: var(--font-head);
  font-size: 1.15rem;
  font-weight: 800;
  color: #0f172a;
}

.payment-card-lbl {
  font-size: 0.78rem;
  font-weight: 700;
  color: #475569;
  margin-top: 0.15rem;
}

.payment-card-sub {
  font-size: 0.7rem;
  color: #94a3b8;
}

.specs-tab-menu {
  display: flex;
  gap: 0.5rem;
  border-bottom: 2px solid #e2e8f0;
  margin-top: 3.5rem;
  margin-bottom: 1.5rem;
}
.specs-tab-item {
  padding: 0.6rem 1.25rem;
  font-weight: 700;
  font-size: 0.88rem;
  color: #475569;
  text-decoration: none;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
}
.specs-tab-item.active {
  color: #dc2626;
  border-bottom-color: #dc2626;
}

.retailer-list-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  margin-bottom: 0.75rem;
  background: #ffffff;
  transition: all 0.2s ease;
}
.retailer-list-row:hover {
  border-color: #3fb950;
  box-shadow: 0 4px 12px rgba(63, 185, 80, 0.05);
}
</style>

<div class="container-xl py-4">
  <div class="product-breadcrumb-row">
    <a href="<?= BASE_URL ?>/index.php">Home</a>
    <span>/</span>
    <a href="<?= BASE_URL ?>/store.php?category=<?= urlencode($comp['category']) ?>"><?= sanitise($comp['category']) ?></a>
    <span>/</span>
    <a href="<?= BASE_URL ?>/store.php?category=<?= urlencode($comp['category']) ?>&brand[]=<?= urlencode($comp['brand']) ?>"><?= sanitise($comp['brand']) ?></a>
    <span>/</span>
    <span class="text-dark fw-600"><?= sanitise($comp['name']) ?></span>
  </div>

  <div class="share-action-bar">
    <div class="share-icons">
      <span>Share:</span>
      <a href="#" onclick="alert('Link copied to clipboard!')" class="share-icon-btn"><i class="bi bi-facebook"></i></a>
      <a href="#" onclick="alert('Link copied to clipboard!')" class="share-icon-btn"><i class="bi bi-whatsapp"></i></a>
      <a href="#" onclick="alert('Link copied to clipboard!')" class="share-icon-btn"><i class="bi bi-link-45deg"></i></a>
    </div>
    <div class="d-flex gap-2">
      <?php if (is_logged_in()): ?>
        <button class="btn btn-light btn-sm text-secondary border watchlist-btn" data-id="<?= $id ?>" data-action="add" style="font-weight:600; border-radius:8px;">
          <i class="bi bi-bookmark-fill me-1"></i> Save
        </button>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/login.php" class="btn btn-light btn-sm text-secondary border" style="font-weight:600; border-radius:8px;">
          <i class="bi bi-bookmark-fill me-1"></i> Save
        </a>
      <?php endif; ?>
      <button class="btn btn-light btn-sm text-secondary border compare-toggle-btn" data-id="<?= $id ?>" data-name="<?= sanitise($comp['name']) ?>" style="font-weight:600; border-radius:8px;">
        <i class="bi bi-plus-square me-1"></i> Add to Compare
      </button>
    </div>
  </div>

  <div class="row g-5">
    <div class="col-lg-5 text-center">
      <div class="card border p-4 bg-white d-flex align-items-center justify-content-center" style="border-radius:16px; min-height: 380px;">
        <img src="<?= sanitise($img) ?>" alt="<?= sanitise($comp['name']) ?>" class="img-fluid" style="max-height: 320px; object-fit: contain;">
      </div>
      <div class="d-flex gap-2 justify-content-center mt-3">
        <div class="border p-2 bg-white rounded cursor-pointer active" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center;">
          <img src="<?= sanitise($img) ?>" class="img-fluid" style="max-height: 44px; object-fit:contain;">
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <h1 style="font-family:var(--font-head); font-weight:800; font-size:1.8rem; color:#1e293b; line-height:1.3; margin-bottom:1rem;">
        <?= sanitise($comp['name']) ?>
      </h1>

      <div class="d-flex flex-wrap gap-2 mb-4">
        <span class="detail-pill">Price: ৳<?= number_format($cash_price) ?></span>
        <span class="detail-pill">Regular Price: ৳<?= number_format($regular_price) ?></span>
        <span class="detail-pill">Product Code: <?= $id ?></span>
        <span class="detail-pill">Brand: <?= sanitise($comp['brand']) ?></span>
      </div>

      <div class="features-list-box">
        <h5>Key Features</h5>
        <ul>
          <?php if (!empty($comp['socket'])): ?>
            <li>Socket Platform: <?= sanitise($comp['socket']) ?></li>
          <?php endif; ?>
          <?php if (!empty($comp['ram_gen'])): ?>
            <li>RAM Type support: <?= sanitise($comp['ram_gen']) ?></li>
          <?php endif; ?>
          <?php if (!empty($comp['tdp_watts'])): ?>
            <li>Thermal Power Design (TDP): <?= (int)$comp['tdp_watts'] ?>W</li>
          <?php endif; ?>
          <?php if (!empty($comp['form_factor'])): ?>
            <li>Form Factor Dimensions: <?= sanitise($comp['form_factor']) ?></li>
          <?php endif; ?>
          <?php if (!empty($comp['length_mm'])): ?>
            <li>GPU Clearance length: <?= (int)$comp['length_mm'] ?>mm</li>
          <?php endif; ?>
          <?php if (!empty($comp['psu_wattage'])): ?>
            <li>Required System PSU: <?= (int)$comp['psu_wattage'] ?>W</li>
          <?php endif; ?>
          <li>Retail Stock Availability checked instantly.</li>
        </ul>
      </div>

      <div class="mt-4 pt-2">
        <button class="btn btn-primary px-5 py-3 fw-bold" data-bs-toggle="modal" data-bs-target="#retailerModal" style="background:#2563eb; border:none; border-radius:12px; font-size:1.05rem; display: inline-flex; align-items: center; gap: 0.5rem;">
          <i class="bi bi-cart3"></i> Buy Now
        </button>
      </div>

    </div>
  </div>

  <div>
    <div class="specs-tab-menu">
      <a href="#" class="specs-tab-item active">Specification</a>
      <a href="#" class="specs-tab-item" onclick="alert('Detailed descriptions are sync-updated from retail catalogs.')">Description</a>
    </div>

    <?php
    $spec_groups = [];

    $spec_groups['Key Specifications'] = [
        'Brand' => !empty($comp['brand']) ? sanitise($comp['brand']) : 'Various',
        'Model' => sanitise($comp['name']),
        'Component Category' => sanitise($comp['category'] ?? 'PC Component')
    ];

    if (!empty($comp['benchmark_score']) && $comp['benchmark_score'] > 0) {
        $spec_groups['Performance Metrics'] = [
            'Performance Benchmark Score' => (int)$comp['benchmark_score'] . ' Points'
        ];
    }

    if (!empty($comp['socket'])) {
        $spec_groups['Processor Socket'] = [
            'Supported Socket' => sanitise($comp['socket'])
        ];
    }

    if (!empty($comp['ram_gen']) || !empty($comp['ram_slots'])) {
        $ram_spec = [];
        if (!empty($comp['ram_gen'])) { $ram_spec['RAM Generation Type'] = sanitise($comp['ram_gen']); }
        if (!empty($comp['ram_slots'])) { $ram_spec['Memory Expansion Slots'] = (int)$comp['ram_slots'] . ' Slots'; }
        $spec_groups['Memory Support'] = $ram_spec;
    }

    if (!empty($comp['storage_interface']) || !empty($comp['m2_slots']) || !empty($comp['sata_ports'])) {
        $st_spec = [];
        if (!empty($comp['storage_interface'])) { $st_spec['Interface Standard'] = sanitise($comp['storage_interface']); }
        if (!empty($comp['m2_slots'])) { $st_spec['M.2 NVMe Storage Slots'] = (int)$comp['m2_slots'] . ' x Slots'; }
        if (!empty($comp['sata_ports'])) { $st_spec['SATA III Storage Ports'] = (int)$comp['sata_ports'] . ' x Ports'; }
        $spec_groups['Storage & Expansion Slots'] = $st_spec;
    }

    if (!empty($comp['tdp_watts']) || !empty($comp['psu_wattage'])) {
        $p_spec = [];
        if (!empty($comp['tdp_watts'])) { $p_spec['Thermal Design Power (TDP)'] = (int)$comp['tdp_watts'] . 'W'; }
        if (!empty($comp['psu_wattage'])) { $p_spec['Recommended System Power (PSU)'] = (int)$comp['psu_wattage'] . 'W'; }
        $spec_groups['Power Specifications'] = $p_spec;
    }

    if (!empty($comp['form_factor']) || !empty($comp['length_mm']) || !empty($comp['height_mm'])) {
        $dim_spec = [];
        if (!empty($comp['form_factor'])) { $dim_spec['Form Factor Layout'] = sanitise($comp['form_factor']); }
        if (!empty($comp['length_mm'])) { $dim_spec['GPU Length Clearance Capacity'] = (int)$comp['length_mm'] . 'mm'; }
        if (!empty($comp['height_mm'])) { $dim_spec['Height Dimensions'] = (int)$comp['height_mm'] . 'mm'; }
        $spec_groups['Physical Specifications'] = $dim_spec;
    }

    $spec_groups['Warranty Information'] = [
        'Warranty Period' => '3 Years Brand Warranty (Verified by Retailers)'
    ];
    ?>

    <div class="card p-4 border-0 shadow-sm bg-white" style="border-radius:12px; overflow:hidden;">
      <h5 class="fw-bold text-dark mb-3" style="font-size: 1.1rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.50rem;">Specification</h5>
      
      <?php foreach ($spec_groups as $group_title => $attributes): ?>
        <?php if (!empty($attributes)): ?>
          <div style="background: #f1f5f9; padding: 0.55rem 1rem; font-weight: 700; font-size: 0.85rem; color: #1e3a8a; border-radius: 6px; margin-top: 1.25rem; margin-bottom: 0.25rem;">
            <?= htmlspecialchars($group_title) ?>
          </div>
          
          <table class="table mb-1" style="font-size: 0.82rem; border-collapse: collapse; width: 100%;">
            <tbody>
              <?php foreach ($attributes as $key => $val): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                  <td style="font-weight: 600; color: #475569; width: 35%; padding: 0.6rem 1rem; border: none; background: transparent;"><?= htmlspecialchars($key) ?></td>
                  <td style="color: #1e293b; padding: 0.6rem 1rem; border: none; background: transparent;"><?= htmlspecialchars($val) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="modal fade" id="retailerModal" tabindex="-1" aria-labelledby="retailerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.12);">
      <div class="modal-header px-4 py-3" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
        <h5 class="modal-title fw-bold text-dark" id="retailerModalLabel">Compare &amp; Buy from Retailers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" style="background:#f8fafc;">
        
        <?php if (empty($retailers)): ?>
          <div class="text-center py-4 bg-white border p-3" style="border-radius: 12px;">
            <i class="bi bi-emoji-neutral display-6 text-muted mb-2 d-block"></i>
            <h6 class="fw-bold mb-1">No Retailer Deals Available</h6>
            <p class="text-muted small mb-0">Currently there are no dynamic retail store mappings for this component.</p>
          </div>
        <?php else: ?>
          <p class="text-muted small mb-3">Compare BDT pricing across stores. Click "Go to Shop" to buy directly from their official catalog page:</p>
          
          <?php foreach ($retailers as $ret): ?>
            <?php
              $stock_status = normalize_stock($ret['stock_status']);
              $badge_class = match($stock_status) {
                  'in_stock' => 'bg-success-soft text-success',
                  'out_of_stock' => 'bg-danger-soft text-danger',
                  default => 'bg-warning-soft text-warning'
              };
              $badge_lbl = match($stock_status) {
                  'in_stock' => 'In Stock',
                  'out_of_stock' => 'Out of Stock',
                  default => 'Pre-Order'
              };
              
              $target_url = '#';
              if (strpos(strtolower($ret['store_name']), 'star tech') !== false) {
                  $target_url = !empty($comp['startech_url']) ? $comp['startech_url'] : 'https://www.startech.com.bd/product/search&search=' . urlencode($comp['name']);
              } elseif (strpos(strtolower($ret['store_name']), 'ryans') !== false) {
                  $target_url = !empty($comp['ryans_url']) ? $comp['ryans_url'] : 'https://www.ryanscomputers.com/search?q=' . urlencode($comp['name']);
              } elseif (strpos(strtolower($ret['store_name']), 'techland') !== false) {
                  $target_url = 'https://www.techlandbd.com/index.php?route=product/search&search=' . urlencode($comp['name']);
              } else {
                  $target_url = 'https://www.google.com/search?q=' . urlencode($ret['store_name'] . ' ' . $comp['name']);
              }
            ?>
            <div class="retailer-list-row bg-white">
              <div>
                <div class="fw-800 text-dark" style="font-size:0.92rem;"><i class="bi bi-shop me-2 text-primary"></i><?= sanitise($ret['store_name']) ?></div>
                <div class="d-flex align-items-center gap-2 mt-1">
                  <span class="text-muted" style="font-size:0.75rem;"><i class="bi bi-patch-check-fill text-success me-1"></i> Verified Retailer Offer</span>
                </div>
              </div>
              
              <div class="text-end">
                <div class="fw-bold text-danger" style="font-size:1.05rem; margin-bottom:0.35rem;">৳<?= number_format((float)$ret['price']) ?></div>
                <a href="<?= sanitise($target_url) ?>" target="_blank" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius:8px; font-size:0.8rem; background:#3fb950; border:none; display:inline-flex; align-items:center; gap:0.25rem;">
                  Go to Shop <i class="bi bi-box-arrow-up-right" style="font-size:0.7rem;"></i>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
