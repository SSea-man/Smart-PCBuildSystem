<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$retailers = db_query('SELECT DISTINCT store_name FROM store ORDER BY store_name');
$retailer_names = array_column($retailers, 'store_name');
$category_types = [
    'CPU'=>'CPU%','Motherboard'=>'Motherboard%','RAM'=>'RAM%',
    'GPU'=>'GPU%','Storage'=>'Storage%','PSU'=>'PSU%','Case'=>'Case%','Cooling'=>'Cooling%'
];

// Filters
$cat      = input('category', '');
$retailer = input('retailer', '');
$search   = trim(input('search', ''));
$min_p    = (int)input('min_price', 0);
$max_p    = (int)input('max_price', 0);
$stock    = input('stock', '');
$page_num = max(1, (int)input('page', 1));
$per_page = 12;

// Build WHERE against component + storeavailability join
$base  = component_base_sql();
$where = []; $params = [];
if ($cat)      { $where[] = 'c.type LIKE ?';                     $params[] = "{$cat}%"; }
if ($retailer) { $where[] = 's.store_name = ?';                  $params[] = $retailer; }
if ($search)   { $where[] = 'c.component_name LIKE ?';           $params[] = "%{$search}%"; }
if ($min_p)    { $where[] = 'COALESCE(sa.price,0) >= ?';         $params[] = $min_p; }
if ($max_p)    { $where[] = 'COALESCE(sa.price,0) <= ?';         $params[] = $max_p; }
if ($stock === 'in_stock')    $where[] = "LOWER(sa.stock_status) IN ('in stock','limited')";
if ($stock === 'out_of_stock') $where[] = "LOWER(sa.stock_status) = 'out of stock'";

$where_sql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$total_count = (int)db_row("SELECT COUNT(*) c FROM ({$base}{$where_sql}) sub", $params)['c'];
$pag         = paginate($total_count, $page_num, $per_page);

$components = db_query(
    "{$base}{$where_sql} ORDER BY c.component_name ASC LIMIT {$per_page} OFFSET {$pag['offset']}",
    $params
);
foreach ($components as &$c) { $c['stock_status'] = normalize_stock($c['stock_status_raw'] ?? ''); }
unset($c);

// Watchlist IDs
$watchlist_ids = [];
if (is_logged_in()) {
    $rows = db_query('SELECT component_id FROM watchlist WHERE user_id = ?', [get_auth_user()['id']]);
    $watchlist_ids = array_column($rows, 'component_id');
}

$compare_ids = [];
$page_title  = 'Component Store';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4">
  <!-- Top Section: Title & Pills -->
  <div class="mb-4">
    <h3 class="text-accent mb-2">Computer Components Price in Bangladesh</h3>
    <p class="text-muted small mb-3">
      PC Component Price in Bangladesh varies depending on the product type and brand. Build your PC with the latest components at the best price. Browse below and order yours now!
    </p>
    <div class="d-flex flex-wrap gap-2">
      <?php foreach (array_keys($category_types) as $c): ?>
      <a href="?category=<?=urlencode($c)?>" class="btn btn-outline-secondary btn-sm rounded-pill <?= $cat===$c?'active':'' ?> px-3"><?= $c ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="row g-4">
    <!-- Sidebar -->
    <div class="col-lg-3">
      <form method="GET" id="filter-form">
        <!-- Preserve other query params like category, search, etc. -->
        <?php if($cat): ?><input type="hidden" name="category" value="<?=sanitise($cat)?>"><?php endif; ?>
        <?php if($search): ?><input type="hidden" name="search" value="<?=sanitise($search)?>"><?php endif; ?>
        <?php if($retailer): ?><input type="hidden" name="retailer" value="<?=sanitise($retailer)?>"><?php endif; ?>

        <!-- Price Range Card -->
        <div class="card shadow-sm border-0 mb-3">
          <div class="card-body">
            <h6 class="fw-bold mb-3">Price Range</h6>
            <div class="d-flex gap-2 align-items-center">
              <input type="number" name="min_price" class="form-control form-control-sm text-center" placeholder="0" value="<?=$min_p?:''?>">
              <span class="text-muted">-</span>
              <input type="number" name="max_price" class="form-control form-control-sm text-center" placeholder="Max" value="<?=$max_p?:''?>">
            </div>
            <button type="submit" class="btn btn-accent btn-sm w-100 mt-3">Apply Price</button>
          </div>
        </div>

        <!-- Availability Card -->
        <div class="card shadow-sm border-0 mb-3">
          <div class="card-body">
            <h6 class="fw-bold mb-3 d-flex justify-content-between align-items-center">
              Availability <i class="bi bi-chevron-up"></i>
            </h6>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="stock" id="stock-all" value="" <?=!$stock?'checked':''?> onchange="this.form.submit()">
              <label class="form-check-label small" for="stock-all">All</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="stock" id="stock-in" value="in_stock" <?=$stock==='in_stock'?'checked':''?> onchange="this.form.submit()">
              <label class="form-check-label small" for="stock-in">In Stock</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="stock" id="stock-out" value="out_of_stock" <?=$stock==='out_of_stock'?'checked':''?> onchange="this.form.submit()">
              <label class="form-check-label small" for="stock-out">Out of Stock</label>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Grid -->
    <div class="col-lg-9">
      <!-- Top Bar of Grid -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="fw-bold fs-5">Component</div>
          <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
              <label class="small text-muted mb-0 text-nowrap">Show:</label>
              <select class="form-select form-select-sm" style="width:70px">
                <option>12</option>
                <option>24</option>
              </select>
            </div>
            <div class="d-flex align-items-center gap-2">
              <label class="small text-muted mb-0 text-nowrap">Sort By:</label>
              <select class="form-select form-select-sm" style="width:120px">
                <option>Default</option>
                <option>Price (Low > High)</option>
                <option>Price (High > Low)</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <?php if (empty($components)): ?>
      <div class="text-center py-5">
        <i class="bi bi-inbox display-4 text-muted mb-3 d-block"></i>
        <p class="text-muted">No components match your filters.</p>
        <a href="<?=BASE_URL?>/store.php" class="btn btn-outline-accent btn-sm">Clear Filters</a>
      </div>
      <?php else: ?>
      <div class="row g-3">
        <?php foreach ($components as $comp):
          $in_watchlist = in_array((int)$comp['id'], $watchlist_ids);
          include __DIR__ . '/templates/component_card.php';
        endforeach; ?>
      </div>
      <div class="mt-4">
        <?php
        $url_base = BASE_URL . '/store.php?' . http_build_query(array_filter([
            'category'=>$cat,'retailer'=>$retailer,'search'=>$search,
            'min_price'=>$min_p,'max_price'=>$max_p,'stock'=>$stock
        ]));
        render_pagination($pag, $url_base);
        ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
