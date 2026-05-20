<?php
/**
 */
$in_wl   = $in_watchlist ?? false;
$cmp_ids = $compare_ids  ?? [];
$in_cmp  = in_array((int)($comp['id'] ?? 0), array_map('intval', $cmp_ids));

$stock_class = match($comp['stock_status'] ?? '') {
    'in_stock'    => 'badge-stock-in',
    'out_of_stock'=> 'badge-stock-out',
    default       => 'badge-stock-pre',
};
$stock_label = match($comp['stock_status'] ?? '') {
    'in_stock'    => 'In Stock',
    'out_of_stock'=> 'Out of Stock',
    default       => 'Pre-Order',
};

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
if (!empty($comp['ram_gen'])) $specs[] = 'RAM: ' . sanitise($comp['ram_gen']);
if (!empty($comp['tdp_watts'])) $specs[] = 'TDP: ' . (int)$comp['tdp_watts'] . 'W';
if (!empty($comp['form_factor'])) $specs[] = 'Form Factor: ' . sanitise($comp['form_factor']);
if (!empty($comp['length_mm'])) $specs[] = 'Length: ' . (int)$comp['length_mm'] . 'mm';
if (!empty($comp['psu_wattage'])) $specs[] = 'Wattage: ' . (int)$comp['psu_wattage'] . 'W';

$specs = array_slice($specs, 0, 4);
?>
<div class="card component-card h-100 border-0 shadow-sm" data-component-id="<?= (int)$comp['id'] ?>">
  <div class="card-body d-flex flex-column p-3 position-relative">
    
    <div class="d-flex justify-content-between align-items-center position-absolute w-100" style="top: 10px; left: 0; padding: 0 10px; z-index: 1;">
      <?php if (!empty($comp['benchmark_score']) && $comp['benchmark_score'] > 80): ?>
      <span class="badge bg-danger" style="border-radius: 4px;">Top Tier</span>
      <?php else: ?>
      <span></span>
      <?php endif; ?>
      <span class="badge <?= $stock_class ?>" style="border-radius: 4px;"><?= $stock_label ?></span>
    </div>

    <div class="text-center my-3" style="min-height: 180px; display: flex; align-items: center; justify-content: center;">
      <?php if (!empty($comp['image_url'])): ?>
        <?php $img_src = str_starts_with($comp['image_url'], 'http') ? $comp['image_url'] : BASE_URL . '/' . $comp['image_url']; ?>
        <img src="<?= sanitise($img_src) ?>" alt="<?= sanitise($comp['name']) ?>" class="img-fluid" style="max-height: 160px; object-fit: contain;">
      <?php else: ?>
        <div class="text-muted opacity-25">
          <i class="<?= $cat_icon ?>" style="font-size: 5rem;"></i>
        </div>
      <?php endif; ?>
    </div>

    <h6 class="fw-bold mb-3" style="font-size: 0.95rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= sanitise($comp['name']) ?></h6>

    <ul class="text-muted small mb-3 ps-3" style="list-style-type: disc;">
      <?php foreach ($specs as $spec): ?>
      <li class="mb-1"><?= $spec ?></li>
      <?php endforeach; ?>
      <?php if (empty($specs)): ?>
      <li class="mb-1">Brand: <?= sanitise($comp['brand'] ?? 'Unknown') ?></li>
      <li class="mb-1">Type: <?= sanitise($comp['category'] ?? 'Component') ?></li>
      <?php endif; ?>
    </ul>

    <div class="mt-auto">
      <div class="text-center mb-3">
        <span class="text-danger fw-bold fs-5"><?= format_bdt((float)$comp['price_bdt']) ?></span>
      </div>

      <a href="<?= !empty($comp['retailer_url']) ? sanitise($comp['retailer_url']) : '#' ?>" 
         class="btn btn-light text-primary fw-bold w-100 mb-2" 
         style="background: #f0f4f9; border-radius: 6px;"
         target="_blank" rel="noopener">
        <i class="bi bi-cart3 me-2"></i>Buy Now
      </a>

      <div class="text-center">
        <button class="btn btn-link text-decoration-none text-muted p-0 small compare-toggle-btn <?= $in_cmp ? 'text-accent' : '' ?>"
                data-id="<?= (int)$comp['id'] ?>"
                data-name="<?= sanitise($comp['name']) ?>"
                style="font-size: 0.85rem;">
          <i class="bi <?= $in_cmp ? 'bi-check-circle-fill' : 'bi-plus-square' ?> me-1"></i> 
          <?= $in_cmp ? 'Added to Compare' : 'Add to Compare' ?>
        </button>
      </div>
    </div>

  </div>
</div>
