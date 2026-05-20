<?php
// admin/prices.php — project_alpha: updates storeavailability.price + inserts into pricetracking
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_auth('admin');

if (is_post()) {
    verify_csrf();
    $ids    = $_POST['component_id'] ?? [];
    $prices = $_POST['price']        ?? [];
    $stocks = $_POST['stock_status'] ?? [];
    $updated = 0;
    foreach ($ids as $i => $cid) {
        $cid      = (int)$cid;
        $new_price= (float)($prices[$i] ?? 0);
        $stock    = sanitise($stocks[$i] ?? 'In Stock');
        if (!$cid || $new_price <= 0) continue;
        // Get old price
        $old = db_row('SELECT price FROM storeavailability WHERE component_id=? LIMIT 1', [$cid]);
        $old_price = $old ? (float)$old['price'] : 0;
        // Update storeavailability
        $exists = db_row('SELECT availability_id FROM storeavailability WHERE component_id=? LIMIT 1', [$cid]);
        if ($exists) {
            db_exec('UPDATE storeavailability SET price=?, stock_status=? WHERE component_id=?', [$new_price, $stock, $cid]);
        } else {
            db_exec('INSERT INTO storeavailability (store_id, component_id, stock_status, price) VALUES (1,?,?,?)', [$cid, $stock, $new_price]);
        }
        // Record in pricetracking
        db_exec('INSERT INTO pricetracking (component_id, old_price, new_price) VALUES (?,?,?)', [$cid, $old_price, $new_price]);
        $updated++;
    }
    flash_message('success', "$updated component(s) updated.");
    redirect('admin/prices.php');
}

$cat    = input('cat','');
$search = trim(input('search',''));
$base   = component_base_sql();
$extra  = []; $params = [];
if ($cat)    { $extra[] = 'c.type LIKE ?';    $params[] = "{$cat}%"; }
if ($search) { $extra[] = 'c.component_name LIKE ?'; $params[] = "%{$search}%"; }
$sql = $base . ($extra ? ' WHERE '.implode(' AND ',$extra) : '') . ' ORDER BY c.type, c.component_name LIMIT 50';
$components = db_query($sql, $params);
$categories = ['CPU','Motherboard','RAM','GPU','Storage','PSU','Case','Cooling'];

$page_title = 'Price & Stock Update';
include __DIR__ . '/../templates/header.php';
?>
<div class="container-xl py-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h1 class="h4 fw-800"><i class="bi bi-tags me-2 text-accent"></i>Price & Stock Update</h1>
    <a href="<?=BASE_URL?>/admin/index.php" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
  </div>
  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search…" value="<?=sanitise($search)?>"></div>
    <div class="col-md-3">
      <select name="cat" class="form-select form-select-sm">
        <option value="">All Categories</option>
        <?php foreach($categories as $c):?><option value="<?=$c?>" <?=$cat===$c?'selected':''?>><?=$c?></option><?php endforeach;?>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-accent btn-sm">Filter</button></div>
  </form>
  <form method="POST">
    <?php csrf_field();?>
    <div class="card">
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr><th>Component</th><th>Category</th><th>Current Price</th><th>New Price (BDT)</th><th>Stock Status</th></tr></thead>
          <tbody>
            <?php foreach($components as $c):?>
            <input type="hidden" name="component_id[]" value="<?=(int)$c['id']?>">
            <tr>
              <td class="fw-600"><?=sanitise($c['name'])?></td>
              <td><span class="badge bg-accent-soft small"><?=sanitise($c['category'])?></span></td>
              <td class="text-accent fw-600"><?=format_bdt((float)$c['price_bdt'])?></td>
              <td style="width:160px"><input type="number" name="price[]" class="form-control form-control-sm" value="<?=number_format((float)$c['price_bdt'],2,'.','')?>" step="0.01" min="0" required></td>
              <td style="width:160px">
                <select name="stock_status[]" class="form-select form-select-sm">
                  <?php foreach(['In Stock','Limited','Out of Stock'] as $s):?>
                  <option value="<?=$s?>" <?=($c['stock_status_raw']??'')===$s?'selected':''?>><?=$s?></option>
                  <?php endforeach;?>
                </select>
              </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-3">
      <button type="submit" class="btn btn-accent"><i class="bi bi-save me-1"></i>Save All (<?=count($components)?> items)</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../templates/footer.php';?>
