<?php
$in_wl   = $in_watchlist ?? false;
$cmp_ids = $compare_ids  ?? [];
$in_cmp  = in_array((int)($comp['id'] ?? 0), array_map('intval', $cmp_ids));

$stock_lbl = sanitise($comp['stock_status_raw'] ?? 'In Stock');
$savings   = ceil((float)$comp['price_bdt'] * 0.08);
$old_price = (float)$comp['price_bdt'] + $savings;

$cat_icon = match($comp['category'] ?? '') {
    'CPU'         => 'bi-cpu',
    'Motherboard' => 'bi-motherboard',
    'RAM'         => 'bi-memory',
    'GPU'         => 'bi-gpu-card',
    'Storage'     => 'bi-device-hdd',
    'PSU'         => 'bi-lightning-charge',
    'Case'        => 'bi-pc',
    'Cooling'     => 'bi-thermometer-snow',
    default       => 'bi-box',
};

$specs = [];
if (!empty($comp['socket'])) $specs[] = 'Socket: ' . sanitise($comp['socket']);
if (!empty($comp['ram_gen'])) $specs[] = 'RAM Gen: ' . sanitise($comp['ram_gen']);
if (!empty($comp['tdp_watts'])) $specs[] = 'TDP Watts: ' . (int)$comp['tdp_watts'] . 'W';
if (!empty($comp['form_factor'])) $specs[] = 'Form Factor: ' . sanitise($comp['form_factor']);
if (!empty($comp['length_mm'])) $specs[] = 'GPU Length clearance: ' . (int)$comp['length_mm'] . 'mm';
if (!empty($comp['psu_wattage'])) $specs[] = 'PSU Wattage: ' . (int)$comp['psu_wattage'] . 'W';
$specs = array_slice($specs, 0, 3);
?>
<div class="col-md-4 col-sm-6 mb-4">
  <div class="card h-100 product-card-startech border-0" data-component-id="<?= (int)$comp['id'] ?>" style="background: var(--bg-card); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between; position: relative;">
    
    <?php if ($comp['price_bdt'] > 0): ?>
      <span class="save-badge-corner" style="position: absolute; top: 0; left: 0; background: linear-gradient(135deg, var(--accent), #3b82f6); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.6rem; border-bottom-right-radius: 8px; z-index: 5; box-shadow: 0 2px 8px rgba(16,185,129,0.3);">
        Save: ৳<?= number_format($savings) ?>
      </span>
    <?php endif; ?>

    <div style="padding: 1.25rem 1.25rem 0.5rem 1.25rem;">
      <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$comp['id'] ?>" class="text-decoration-none">
        <div class="text-center my-2" style="height: 160px; display: flex; align-items: center; justify-content: center;">
          <?php if (!empty($comp['image_url'])): ?>
            <?php $img_src = str_starts_with($comp['image_url'], 'http') ? $comp['image_url'] : BASE_URL . '/' . $comp['image_url']; ?>
            <img src="<?= sanitise($img_src) ?>" alt="<?= sanitise($comp['name']) ?>" class="img-fluid" style="max-height: 140px; object-fit: contain;">
          <?php else: ?>
            <div class="text-muted opacity-25">
              <i class="<?= $cat_icon ?>" style="font-size: 4.5rem;"></i>
            </div>
          <?php endif; ?>
        </div>
      </a>

      <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$comp['id'] ?>" class="text-decoration-none text-reset">
        <h6 class="product-title-text mb-3" style="font-size: 0.88rem; font-weight: 700; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px; color: var(--text-primary);">
          <?= sanitise($comp['name']) ?>
        </h6>
      </a>

      <ul class="text-secondary small mb-4 ps-3" style="list-style-type: disc; line-height: 1.6; font-size: 0.78rem; min-height: 75px;">
        <?php foreach ($specs as $spec): ?>
          <li class="mb-1"><?= $spec ?></li>
        <?php endforeach; ?>
        <?php if (empty($specs)): ?>
          <li class="mb-1">Brand: <?= sanitise($comp['brand'] ?? 'Various') ?></li>
          <li class="mb-1">Category: <?= sanitise($comp['category'] ?? 'PC Component') ?></li>
        <?php endif; ?>
      </ul>
    </div>

    <div style="border-top: 1px solid var(--border); padding: 0.75rem 1.25rem 1.25rem 1.25rem; text-align: center;">
      <div class="mb-3">
        <?php if ($comp['price_bdt'] > 0): ?>
          <span class="text-danger fw-bold" style="font-size: 1.05rem;">৳<?= number_format((float)$comp['price_bdt']) ?></span>
          <span class="text-muted text-decoration-line-through ms-2" style="font-size: 0.82rem;">৳<?= number_format($old_price) ?></span>
        <?php else: ?>
          <span class="text-muted fw-bold" style="font-size: 0.95rem;">Price Unlisted</span>
        <?php endif; ?>
      </div>

      <div class="d-flex flex-column gap-2">

        <div class="d-flex justify-content-center align-items-center mt-1">
          <button class="btn btn-link text-decoration-none text-muted p-0 small compare-toggle-btn <?= $in_cmp ? 'text-accent' : '' ?>"
                  data-id="<?= (int)$comp['id'] ?>"
                  data-name="<?= sanitise($comp['name']) ?>"
                  style="font-size: 0.72rem; font-weight:600;">
            <i class="bi <?= $in_cmp ? 'bi-check-circle-fill' : 'bi-plus-square' ?> me-1"></i> 
            <?= $in_cmp ? 'Added' : 'Compare' ?>
          </button>
        </div>
      </div>
    </div>

  </div>
</div>
