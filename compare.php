<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$ids_param  = input('ids', '');
$ids        = array_filter(array_map('intval', explode(',', $ids_param)));
$ids        = array_slice(array_unique($ids), 0, 4);
$components = [];

if (!empty($ids)) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $components   = db_query(
        component_base_sql() . " WHERE c.component_id IN ($placeholders)", $ids
    );
    foreach ($components as &$c) { $c['stock_status'] = normalize_stock($c['stock_status_raw'] ?? ''); }
    unset($c);
}

$spec_rows = [
    ['label'=>'Category',        'field'=>'category',        'metric'=>'text'],
    ['label'=>'Brand',           'field'=>'brand',           'metric'=>'text'],
    ['label'=>'Price (BDT)',     'field'=>'price_bdt',       'metric'=>'lower'],
    ['label'=>'Benchmark Score', 'field'=>'benchmark_score', 'metric'=>'higher'],
    ['label'=>'TDP (Watts)',     'field'=>'tdp_watts',       'metric'=>'lower'],
    ['label'=>'Socket',          'field'=>'socket',          'metric'=>'text'],
    ['label'=>'RAM Gen',         'field'=>'ram_gen',         'metric'=>'text'],
    ['label'=>'Form Factor',     'field'=>'form_factor',     'metric'=>'text'],
    ['label'=>'M.2 Slots',      'field'=>'m2_slots',        'metric'=>'higher'],
    ['label'=>'RAM Slots',      'field'=>'ram_slots',       'metric'=>'higher'],
    ['label'=>'PSU Wattage (W)','field'=>'psu_wattage',     'metric'=>'higher'],
    ['label'=>'Stock',          'field'=>'stock_status',    'metric'=>'text'],
    ['label'=>'Retailer',       'field'=>'retailer',        'metric'=>'text'],
];

$page_title     = 'Compare Components';
$footer_scripts = ['compare.js'];
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
      <h1 class="section-title mb-1"><i class="bi bi-layout-split me-2 text-accent"></i>Component Comparison</h1>
      <p class="text-muted mb-0">Side-by-side spec view. Winners highlighted in each row.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?=BASE_URL?>/store.php" class="btn btn-outline-accent"><i class="bi bi-plus-lg me-1"></i>Add Components</a>
      <button onclick="clearCompare()" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg me-1"></i>Clear</button>
    </div>
  </div>

  <?php if (count($components) < 2): ?>
  <div class="text-center py-5">
    <i class="bi bi-layout-split display-4 text-muted mb-3 d-block"></i>
    <h4>Select 2–4 components to compare</h4>
    <p class="text-muted">Use the <strong>Compare</strong> button on any component card in the store.</p>
    <a href="<?=BASE_URL?>/store.php" class="btn btn-accent mt-2">Browse Store</a>
  </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table compare-table">
      <thead>
        <tr>
          <th style="width:180px">Specification</th>
          <?php foreach ($components as $c): ?>
          <th class="text-center" style="min-width:200px">
            <?php if (!empty($c['image_url'])): ?>
            <div class="mb-2">
              <?php $img_src = str_starts_with($c['image_url'], 'http') ? $c['image_url'] : BASE_URL . '/' . $c['image_url']; ?>
              <img src="<?= sanitise($img_src) ?>" alt="<?= sanitise($c['name']) ?>" class="img-fluid rounded" style="max-height: 80px; object-fit: contain;">
            </div>
            <?php endif; ?>
            <div class="fw-700"><?=sanitise($c['name'])?></div>
            <div class="text-accent fw-700"><?=format_bdt((float)$c['price_bdt'])?></div>
            <span class="badge <?=$c['stock_status']==='in_stock'?'badge-stock-in':'badge-stock-out'?> mt-1">
              <?=$c['stock_status']==='in_stock'?'In Stock':'Out of Stock'?>
            </span>
          </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($spec_rows as $row): ?>
        <tr data-compare-row="<?=$row['metric']?>">
          <td class="text-muted fw-600 small"><?=$row['label']?></td>
          <?php foreach ($components as $c):
            $val = $c[$row['field']] ?? '—';
            $display = ($row['field']==='price_bdt') ? format_bdt((float)$val) : ($val ?: '—');
          ?>
          <td class="text-center" data-value="<?=is_numeric($val)?$val:0?>"><?=sanitise((string)$display)?></td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
