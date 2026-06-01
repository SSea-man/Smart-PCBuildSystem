<?php
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

        $old = db_row('SELECT price FROM storeavailability WHERE component_id=? LIMIT 1', [$cid]);
        $old_price = $old ? (float)$old['price'] : 0;

        $exists = db_row('SELECT availability_id FROM storeavailability WHERE component_id=? LIMIT 1', [$cid]);
        if ($exists) {
            db_exec('UPDATE storeavailability SET price=?, stock_status=? WHERE component_id=?', [$new_price, $stock, $cid]);
        } else {
            db_exec('INSERT INTO storeavailability (store_id, component_id, stock_status, price) VALUES (1,?,?,?)', [$cid, $stock, $new_price]);
        }

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

<style>
#main-nav {
  display: none !important;
}

#main-content {
  padding-top: 0 !important;
}

.admin-layout-wrapper {
  display: grid;
  grid-template-columns: 240px 1fr;
  min-height: 100vh;
  background: var(--bg-base);
}

@media (max-width: 992px) {
  .admin-layout-wrapper {
    grid-template-columns: 1fr;
  }
  .admin-sidebar {
    display: none !important;
  }
}

.admin-sidebar {
  background: var(--bg-card);
  border-right: 1px solid var(--border);
  padding: 1.5rem 1rem;
  display: flex;
  flex-direction: column;
  height: 100vh;
  position: sticky;
  top: 0;
  justify-content: space-between;
}

.admin-brand {
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 1.35rem;
  color: #7c3aed; display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 2rem;
  padding-left: 0.5rem;
  text-decoration: none;
}

.admin-brand i {
  font-size: 1.5rem;
}

.sidebar-group-header {
  font-size: 0.72rem;
  font-weight: 700;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 0.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sidebar-group-content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  margin-bottom: 1.5rem;
}

.sidebar-nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0.75rem;
  font-size: 0.88rem;
  font-weight: 500;
  color: var(--text-secondary);
  border-radius: 10px;
  text-decoration: none;
  transition: all 0.2s ease;
}

.sidebar-nav-link:hover {
  background: var(--bg-card-hover);
  color: var(--text-primary);
}

.sidebar-nav-link.active {
  background: #7c3aed; color: #ffffff !important;
  font-weight: 600;
}

.sidebar-nav-link i {
  font-size: 1.1rem;
}

.admin-workspace {
  padding: 1.5rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
}

.admin-search {
  position: relative;
  width: 300px;
}

.admin-search input {
  width: 100%;
  padding: 0.45rem 1rem 0.45rem 2.2rem;
  border-radius: 20px;
  border: 1px solid var(--border);
  background: var(--bg-card);
  color: var(--text-primary);
  font-size: 0.85rem;
  outline: none;
}

.admin-search i {
  position: absolute;
  left: 0.85rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
  font-size: 0.85rem;
}

.admin-header-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.btn-header-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-secondary);
  border: 1px solid var(--border);
  background: var(--bg-card);
  font-size: 0.95rem;
  cursor: pointer;
}

.btn-header-icon:hover {
  background: var(--bg-card-hover);
  color: var(--text-primary);
}

.admin-user-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.admin-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #7c3aed;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.85rem;
}

.admin-username {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-primary);
}
</style>

<div class="admin-layout-wrapper">
  <aside class="admin-sidebar">
    <div>
      <a href="<?= BASE_URL ?>/admin/index.php" class="admin-brand" style="color: var(--text-primary);">
        <i class="bi bi-cpu-fill" style="color: #7c3aed;"></i>
        <span>PC Builder BD</span>
      </a>

      <div class="sidebar-group-header">
        <span>My View</span>
        <i class="bi bi-chevron-down text-muted" style="font-size:0.75rem;"></i>
      </div>
      <div class="sidebar-group-content mb-2">
        <a href="<?= BASE_URL ?>/dashboard.php" class="sidebar-nav-link">
          <i class="bi bi-grid-fill"></i>User Dashboard
        </a>
        <a href="<?= BASE_URL ?>/index.php" class="sidebar-nav-link">
          <i class="bi bi-house"></i>Home Page
        </a>
      </div>

      <div class="sidebar-group-header text-primary">
        <span>Admin View</span>
        <i class="bi bi-chevron-down" style="font-size:0.75rem;"></i>
      </div>
      
      <div class="sidebar-group-content">
        <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-nav-link">
          <i class="bi bi-grid"></i>Dashboard
        </a>
        <a href="<?= BASE_URL ?>/admin/components.php" class="sidebar-nav-link">
          <i class="bi bi-cpu"></i>Components
        </a>
        <a href="<?= BASE_URL ?>/admin/users.php" class="sidebar-nav-link">
          <i class="bi bi-people"></i>User Roles
        </a>
        <a href="<?= BASE_URL ?>/admin/prices.php" class="sidebar-nav-link active">
          <i class="bi bi-tags"></i>Price Config
        </a>
      </div>
    </div>

    <div class="text-center text-muted" style="font-size:0.7rem;">
      &copy; <?= date('Y') ?> PC Builder BD Admin
    </div>
  </aside>

  <main class="admin-workspace">
    <div class="admin-header">
      <div class="admin-search">
        <i class="bi bi-search"></i>
        <form method="GET" action="">
          <input type="text" name="search" placeholder="Search pricing catalog..." value="<?= sanitise($search) ?>">
          <?php if($cat): ?><input type="hidden" name="cat" value="<?= sanitise($cat) ?>"><?php endif; ?>
        </form>
      </div>

      <div class="admin-header-actions">
        <button class="btn-header-icon" title="Settings" onclick="alert('Admin settings are managed globally.')">
          <i class="bi bi-gear"></i>
        </button>
        <button class="btn-header-icon" title="Notifications" onclick="alert('No new system alerts.')">
          <i class="bi bi-bell"></i>
        </button>
        
        <div class="admin-user-info">
          <div class="admin-avatar">
            <?= strtoupper(substr(get_auth_user()['name'], 0, 1)) ?>
          </div>
          <span class="admin-username"><?= sanitise(get_auth_user()['name']) ?></span>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
      <div>
        <h2 style="font-family: var(--font-head); font-weight:800; font-size:1.6rem; color:var(--text-primary); margin:0;">Price & Stock Configuration</h2>
        <p class="text-muted small" style="margin:0;">Update retail prices and stock availability in real time.</p>
      </div>
      <div class="d-flex gap-1 flex-wrap">
        <a href="?search=<?= urlencode($search) ?>" class="btn btn-xs <?= $cat===''?'btn-accent':'btn-outline-secondary' ?>" style="font-size:0.75rem; border-radius:8px; padding:0.25rem 0.6rem;">All</a>
        <?php foreach ($categories as $c): ?>
          <a href="?cat=<?= urlencode($c) ?><?= $search ? '&search='.urlencode($search) : '' ?>" class="btn btn-xs <?= $cat===$c?'btn-accent':'btn-outline-secondary' ?>" style="font-size:0.75rem; border-radius:8px; padding:0.25rem 0.6rem;">
            <?= $c ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <form method="POST">
      <?php csrf_field(); ?>
      
      <div class="card p-0" style="border-radius:16px; overflow:hidden; border:1px solid var(--border);">
        <div class="table-responsive">
          <table class="table table-hover table-sm mb-0">
            <thead>
              <tr style="background:var(--bg-card-hover);">
                <th class="ps-3 py-2">Component Name</th>
                <th class="py-2">Category</th>
                <th class="py-2">Current BDT Price</th>
                <th class="py-2">New BDT Price</th>
                <th class="pe-3 py-2">Stock Level</th>
              </tr>
            </thead>
            <tbody>
              <?php if(empty($components)): ?>
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bi bi-tag display-6 d-block mb-2"></i>
                    No components found matching search/category filters.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($components as $c): ?>
                  <input type="hidden" name="component_id[]" value="<?= (int)$c['id'] ?>">
                  <tr>
                    <td class="fw-600 ps-3 py-2" style="font-size:0.85rem; max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                      <?= sanitise($c['name']) ?>
                    </td>
                    <td class="py-2">
                      <span class="badge bg-accent-soft text-accent" style="font-size:0.7rem; font-weight:600;"><?= sanitise($c['category']) ?></span>
                    </td>
                    <td class="text-accent fw-600 py-2" style="font-size:0.85rem;"><?= format_bdt((float)$c['price_bdt']) ?></td>
                    <td class="py-2" style="width:180px;">
                      <input type="number" name="price[]" class="form-control form-control-sm" value="<?= number_format((float)$c['price_bdt'],2,'.','') ?>" step="0.01" min="0" required style="border-radius:8px;">
                    </td>
                    <td class="pe-3 py-2" style="width:180px;">
                      <select name="stock_status[]" class="form-select form-select-sm" style="border-radius:8px;">
                        <?php foreach (['In Stock','Limited','Out of Stock'] as $s): ?>
                          <option value="<?= $s ?>" <?= ($c['stock_status_raw']??'')===$s?'selected':'' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php if(!empty($components)): ?>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary" style="background:#7c3aed; border-color:#7c3aed; border-radius:12px; font-weight:600; font-size:0.88rem; padding:0.5rem 1.25rem;">
            <i class="bi bi-save me-1"></i>Save All Changes (<?= count($components) ?> items)
          </button>
        </div>
      <?php endif; ?>
    </form>
  </main>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
