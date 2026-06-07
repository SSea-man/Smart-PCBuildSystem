<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$category_types = [
    'CPU' => 'CPU%',
    'Motherboard' => 'Motherboard%',
    'RAM' => 'RAM%',
    'Storage' => 'Storage%',
    'GPU' => 'GPU%',
    'PSU' => 'PSU%',
    'Case' => 'Case%',
    'Cooling' => 'Cooling%'
];

$cat      = input('category', '');
$search   = trim(input('search', ''));
$min_p    = (int)input('min_price', 0);
$max_p    = (int)input('max_price', 0);
$sort     = input('sort', 'default');
$show_limit = (int)input('show', 15);
$page_num = max(1, (int)input('page', 1));
$selected_brands = $_GET['brand'] ?? [];

$base = component_base_sql();

$price_bounds = db_row("
    SELECT MIN(sub.price_bdt) as min_val, MAX(sub.price_bdt) as max_val
    FROM ({$base}) sub
    WHERE sub.category = ? OR ? = ''", [$cat, $cat]);
$db_min = $price_bounds['min_val'] ? (int)$price_bounds['min_val'] : 0;
$db_max = $price_bounds['max_val'] ? (int)$price_bounds['max_val'] : 450000;

if (!$max_p) { $max_p = $db_max; }

$brands_query = db_query("
    SELECT DISTINCT sub.brand 
    FROM ({$base}) sub 
    WHERE (sub.category = ? OR ? = '') AND sub.brand IS NOT NULL AND sub.brand != ''
    ORDER BY sub.brand", [$cat, $cat]);
$available_brands = array_column($brands_query, 'brand');

$where = []; $params = [];

if ($cat) {
    $where[] = 'sub.category = ?';
    $params[] = $cat;
}
if ($search) {
    $where[] = 'sub.name LIKE ?';
    $params[] = "%{$search}%";
}
if ($min_p > 0) {
    $where[] = 'sub.price_bdt >= ?';
    $params[] = $min_p;
}
if ($max_p > 0) {
    $where[] = 'sub.price_bdt <= ?';
    $params[] = $max_p;
}

if (!empty($selected_brands)) {
    $brand_placeholders = [];
    foreach ($selected_brands as $sb) {
        $brand_placeholders[] = '?';
        $params[] = $sb;
    }
    $where[] = 'sub.brand IN (' . implode(',', $brand_placeholders) . ')';
}

$where_sql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$total_count = (int)db_row("SELECT COUNT(*) c FROM ({$base}) sub {$where_sql}", $params)['c'];
$pag         = paginate($total_count, $page_num, $show_limit);

$order_by = 'sub.name ASC';
if ($sort === 'price_asc') {
    $order_by = 'sub.price_bdt ASC';
} elseif ($sort === 'price_desc') {
    $order_by = 'sub.price_bdt DESC';
} elseif ($sort === 'score_desc') {
    $order_by = 'sub.benchmark_score DESC';
}

$components = db_query("SELECT sub.* FROM ({$base}) sub {$where_sql} ORDER BY {$order_by} LIMIT {$show_limit} OFFSET {$pag['offset']}", $params);
foreach ($components as &$c) {
    $c['stock_status'] = normalize_stock($c['stock_status_raw'] ?? '');
}
unset($c);

$watchlist_ids = [];
if (is_logged_in()) {
    $rows = db_query('SELECT component_id FROM watchlist WHERE user_id = ?', [get_auth_user()['id']]);
    $watchlist_ids = array_column($rows, 'component_id');
}

$compare_ids = [];
$page_title  = $cat ? "$cat Price in Bangladesh" : 'Computer Component Store';
include __DIR__ . '/templates/header.php';
?>

<style>
.store-breadcrumb {
  font-size: 0.8rem;
  color: var(--text-secondary);
  margin-bottom: 1.25rem;
}

.store-breadcrumb a {
  text-decoration: none;
  color: var(--accent);
}

.store-breadcrumb span {
  margin: 0 0.4rem;
  color: var(--text-muted);
}

.store-category-header h2 {
  font-family: var(--font-head);
  font-size: 1.6rem;
  font-weight: 800;
<<<<<<< Updated upstream
=======
  color: var(--text-primary);
>>>>>>> Stashed changes
  margin-bottom: 0.5rem;
}

.store-category-header p {
  font-size: 0.82rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.quick-pills-row {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.quick-pill-badge {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 0.35rem 0.9rem;
  font-size: 0.78rem;
  color: var(--text-secondary);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;
}

.quick-pill-badge:hover, .quick-pill-badge.active {
  border-color: var(--accent);
  color: var(--accent);
  background: var(--accent-soft);
}

.filter-sidebar-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  margin-bottom: 1.25rem;
  overflow: hidden;
}

.filter-sidebar-header {
  padding: 0.85rem 1.25rem;
<<<<<<< Updated upstream
  background: var(--bg-input);
  border-bottom: 1px solid var(--border);
  font-weight: 700;
  font-size: 0.88rem;
=======
  background: rgba(0,0,0,0.1);
  border-bottom: 1px solid var(--border);
  font-weight: 700;
  font-size: 0.88rem;
  color: var(--text-primary);
>>>>>>> Stashed changes
}

.filter-sidebar-body {
  padding: 1.25rem;
}

.checkbox-filter-lbl {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.82rem;
  color: var(--text-primary);
  cursor: pointer;
  margin-bottom: 0.6rem;
  user-select: none;
}

.checkbox-filter-lbl input {
  cursor: pointer;
}
</style>

<div class="container-xl py-4">
  <div class="store-breadcrumb">
    <a href="<?= BASE_URL ?>/index.php">Home</a>
    <span>/</span>
    <a href="<?= BASE_URL ?>/store.php">Components</a>
    <?php if ($cat): ?>
      <span>/</span>
      <span class="text-primary fw-600"><?= sanitise($cat) ?></span>
    <?php endif; ?>
  </div>

  <div class="store-category-header">
    <h2><?= $cat ? sanitise($cat) . ' Price in Bangladesh' : 'Computer Components Price in Bangladesh' ?></h2>
    <p>
      <?= $cat ? sanitise($cat) : 'Computer component' ?> price in Bangladesh starts from BDT <?= number_format($db_min) ?> and depending on brand and features, price may go up to BDT <?= number_format($db_max) ?>. Buy original computer components at the best retail rates from PC Builder BD. Browse below and order yours now!
    </p>

    <div class="quick-pills-row">
      <a href="?category=<?= $cat ? urlencode($cat) : '' ?>" class="quick-pill-badge <?= empty($selected_brands) ? 'active' : '' ?>">All Brands</a>
      <?php foreach ($available_brands as $brand): ?>
        <?php
          $brand_active = in_array($brand, $selected_brands);
          $query_data = $_GET;
          if ($brand_active) {
              $query_data['brand'] = array_diff($query_data['brand'] ?? [], [$brand]);
          } else {
              $query_data['brand'][] = $brand;
          }
          $target_url = '?' . http_build_query($query_data);
        ?>
        <a href="<?= $target_url ?>" class="quick-pill-badge <?= $brand_active ? 'active' : '' ?>"><?= sanitise($brand) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-3">
      <form method="GET" action="" id="filter-form">
        <?php if($cat): ?><input type="hidden" name="category" value="<?= sanitise($cat) ?>"><?php endif; ?>
        <?php if($search): ?><input type="hidden" name="search" value="<?= sanitise($search) ?>"><?php endif; ?>
        
        <div class="filter-sidebar-card">
          <div class="filter-sidebar-header">Price Range</div>
          <div class="filter-sidebar-body">
            <div class="d-flex align-items-center gap-2 mb-3">
              <input type="number" name="min_price" class="form-control form-control-sm text-center" placeholder="Min" value="<?= $min_p ?: '' ?>" style="border-radius:6px;">
              <span class="text-muted">-</span>
              <input type="number" name="max_price" class="form-control form-control-sm text-center" placeholder="Max" value="<?= $max_p !== $db_max ? $max_p : '' ?>" placeholder="<?= $db_max ?>" style="border-radius:6px;">
            </div>
            <button type="submit" class="btn btn-sm btn-dark w-100" style="background:#0f172a; border-radius:8px; font-weight:600;">Apply Range</button>
          </div>
        </div>

        <div class="filter-sidebar-card">
          <div class="filter-sidebar-header">Brand</div>
          <div class="filter-sidebar-body" style="max-height: 280px; overflow-y: auto;">
            <?php if(empty($available_brands)): ?>
              <p class="text-muted small mb-0">No brands listed</p>
            <?php else: ?>
              <?php foreach ($available_brands as $brand): ?>
                <label class="checkbox-filter-lbl">
                  <input type="checkbox" name="brand[]" value="<?= sanitise($brand) ?>" <?= in_array($brand, $selected_brands)?'checked':'' ?> onchange="this.form.submit()">
                  <?= sanitise($brand) ?>
                </label>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

      </form>
    </div>

    <div class="col-lg-9">
<<<<<<< Updated upstream
      <div class="card p-2 border-0 shadow-sm mb-4" style="border-radius:12px;">
=======
      <div class="card p-2 border-0 shadow-sm mb-4" style="background:var(--bg-card); border-radius:12px;">
>>>>>>> Stashed changes
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-2">
          <div class="fw-bold fs-6 text-primary"><?= $cat ? sanitise($cat) : 'All Components' ?></div>
          
          <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
              <label class="small text-muted mb-0 text-nowrap">Show:</label>
              <select name="show" class="form-select form-select-sm" style="width:75px; border-radius:6px;" onchange="document.getElementById('hidden-show').value=this.value; window.location.href='?' + new URLSearchParams(new FormData(document.getElementById('filter-form'))).toString() + '&show=' + this.value">
                <option value="15" <?= $show_limit===15?'selected':'' ?>>15</option>
                <option value="30" <?= $show_limit===30?'selected':'' ?>>30</option>
                <option value="50" <?= $show_limit===50?'selected':'' ?>>50</option>
              </select>
            </div>
            
            <div class="d-flex align-items-center gap-2">
              <label class="small text-muted mb-0 text-nowrap">Sort By:</label>
              <select name="sort" class="form-select form-select-sm" style="width:140px; border-radius:6px;" onchange="window.location.href='?' + new URLSearchParams(new FormData(document.getElementById('filter-form'))).toString() + '&sort=' + this.value + '&show=<?= $show_limit ?>'">
                <option value="default" <?= $sort==='default'?'selected':'' ?>>Default</option>
                <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price (Low > High)</option>
                <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price (High > Low)</option>
                <option value="score_desc" <?= $sort==='score_desc'?'selected':'' ?>>Benchmark Score</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <?php if (empty($components)): ?>
        <div class="text-center py-5 border rounded-3" style="background:var(--bg-card); border-color:var(--border) !important; border-radius:12px;">
          <i class="bi bi-inbox display-4 text-muted mb-3 d-block"></i>
          <h5 class="fw-700">No components found</h5>
          <p class="text-muted small">No catalog entries matched your search criteria or price parameters.</p>
          <a href="<?= BASE_URL ?>/store.php" class="btn btn-dark btn-sm px-4 mt-2" style="border-radius:8px;">Reset Filters</a>
        </div>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($components as $comp): ?>
            <?php
              $in_watchlist = in_array((int)$comp['id'], $watchlist_ids);
              include __DIR__ . '/templates/component_card.php';
            ?>
          <?php endforeach; ?>
        </div>

        <div class="mt-4">
          <?php
            $query_params = array_filter([
                'category' => $cat,
                'search' => $search,
                'min_price' => $min_p,
                'max_price' => $max_p !== $db_max ? $max_p : null,
                'sort' => $sort !== 'default' ? $sort : null,
                'show' => $show_limit !== 15 ? $show_limit : null,
                'brand' => $selected_brands
            ]);
            $url_base = BASE_URL . '/store.php?' . http_build_query($query_params);
            render_pagination($pag, $url_base);
          ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
