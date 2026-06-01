<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_auth();
$user = get_auth_user();
$uid  = $user['id'];  

$builds = db_query(
    'SELECT * FROM `build` WHERE user_id=? ORDER BY created_at DESC LIMIT 10', [$uid]
);

$watchlist = db_query(
    'SELECT c.component_id as id, c.component_name as name, c.type,
            COALESCE(sa.price,0) as price_bdt, COALESCE(s.store_name,"") as retailer,
            w.added_at
     FROM watchlist w
     JOIN component c ON c.component_id = w.component_id
     LEFT JOIN (SELECT component_id, MIN(price) as price, store_id FROM storeavailability GROUP BY component_id) sa ON sa.component_id = c.component_id
     LEFT JOIN store s ON s.store_id = sa.store_id
     WHERE w.user_id = ? ORDER BY w.added_at DESC LIMIT 8',
    [$uid]
);

$trend_labels = $trend_values = [];
if (!empty($watchlist)) {
    $first_id = $watchlist[0]['id'];
    $history  = db_query(
        'SELECT DATE(changed_at) as d, new_price FROM pricetracking
         WHERE component_id=? AND changed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY d',
        [$first_id]
    );
    foreach ($history as $h) { 
        $trend_labels[] = date('M j', strtotime($h['d'])); 
        $trend_values[] = (float)$h['new_price']; 
    }
}

$total_components = db_row('SELECT COUNT(*) c FROM component')['c'];
$total_stores     = db_row('SELECT COUNT(*) c FROM store')['c'];

$page_title = 'Dashboard';
include __DIR__ . '/templates/header.php';
?>

<style>
#main-nav {
  display: none !important;
}

#main-content {
  padding-top: 0 !important;
}

.dashboard-app-layout {
  display: grid;
  grid-template-columns: 240px 1fr;
  min-height: 100vh;
  background: var(--bg-base);
}

@media (max-width: 992px) {
  .dashboard-app-layout {
    grid-template-columns: 1fr;
  }
  .dash-sidebar {
    display: none !important;
  }
}

.dash-sidebar {
  background: var(--bg-card);
  border-right: 1px solid var(--border);
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100vh;
  position: sticky;
  top: 0;
}

.dash-brand {
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 1.3rem;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 2.5rem;
}

.dash-brand .brand-icon {
  background: #137333;
  color: #fff;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

.dash-nav-section-title {
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  color: var(--text-muted);
  letter-spacing: 0.08em;
  margin-bottom: 0.75rem;
  margin-top: 1.5rem;
}

.dash-nav-links {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.dash-nav-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.65rem 0.85rem;
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
  border-radius: 10px;
  text-decoration: none;
  transition: all 0.2s ease;
}

.dash-nav-item:hover {
  background: var(--bg-card-hover);
  color: var(--text-primary);
}

.dash-nav-item.active {
  background: rgba(19, 115, 51, 0.1);
  color: #137333;
  font-weight: 600;
  border-left: 3px solid #137333;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

[data-bs-theme="dark"] .dash-nav-item.active {
  color: #3fb950;
  background: rgba(63, 185, 80, 0.1);
  border-left-color: #3fb950;
}

.dash-nav-item i {
  font-size: 1.1rem;
  margin-right: 0.5rem;
}

.dash-nav-badge {
  background: #137333;
  color: #fff;
  font-size: 0.7rem;
  padding: 0.15rem 0.4rem;
  border-radius: 8px;
  font-weight: 700;
}

.sidebar-promo-card {
  background: linear-gradient(135deg, #137333, #0f5132);
  border-radius: 16px;
  padding: 1rem;
  color: #ffffff;
  text-align: center;
  margin-top: 2rem;
}

.sidebar-promo-card h4 {
  font-size: 0.85rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.sidebar-promo-card p {
  font-size: 0.72rem;
  opacity: 0.8;
  margin-bottom: 0.75rem;
}

.sidebar-promo-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: #ffffff;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.4rem 1rem;
  border-radius: 8px;
  width: 100%;
  transition: all 0.2s ease;
}

.sidebar-promo-btn:hover {
  background: #ffffff;
  color: #137333;
}

.dash-main-area {
  padding: 1.5rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.dash-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
}

.dash-search-box {
  position: relative;
  width: 300px;
}

.dash-search-box input {
  width: 100%;
  padding: 0.5rem 1rem 0.5rem 2.5rem;
  border-radius: 20px;
  border: 1px solid var(--border);
  background: var(--bg-card);
  color: var(--text-primary);
  font-size: 0.88rem;
  outline: none;
}

.dash-search-box i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
}

.dash-search-shortcut {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.7rem;
  color: var(--text-muted);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 0.1rem 0.3rem;
}

.dash-header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.dash-icon-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid var(--border);
  background: var(--bg-card);
  color: var(--text-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  position: relative;
}

.dash-icon-btn:hover {
  background: var(--bg-card-hover);
}

.dash-user-profile {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dash-user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #137333;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
}

.dash-user-info {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.dash-user-name {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--text-primary);
}

.dash-user-email {
  font-size: 0.72rem;
  color: var(--text-secondary);
}

.dash-title-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  border-bottom: 1px solid var(--border);
  padding-bottom: 1.25rem;
}

.dash-title-text h2 {
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 1.8rem;
  margin: 0 0 0.25rem 0;
  color: var(--text-primary);
}

.dash-title-text p {
  color: var(--text-secondary);
  font-size: 0.85rem;
  margin: 0;
}

.dash-title-buttons {
  display: flex;
  gap: 0.75rem;
}

.btn-dash-primary {
  background: #137333;
  color: #ffffff;
  border: none;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.5rem 1.25rem;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  text-decoration: none;
}

.btn-dash-primary:hover {
  background: #0f5132;
  color: #ffffff;
}

.btn-dash-secondary {
  background: var(--bg-card);
  border: 1px solid var(--border);
  color: var(--text-primary);
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.5rem 1.25rem;
  border-radius: 20px;
  text-decoration: none;
}

.btn-dash-secondary:hover {
  background: var(--bg-card-hover);
}

.dash-kpis-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

@media (max-width: 1200px) {
  .dash-kpis-row {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 576px) {
  .dash-kpis-row {
    grid-template-columns: 1fr;
  }
}

.dash-kpi-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  transition: transform 0.2s ease;
}

.dash-kpi-card:hover {
  transform: translateY(-2px);
}

.dash-kpi-card.featured-green {
  background: linear-gradient(135deg, #137333, #0f5132);
  color: #ffffff;
  border: none;
}

.dash-kpi-title {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--text-secondary);
}

.dash-kpi-card.featured-green .dash-kpi-title {
  color: rgba(255, 255, 255, 0.8);
}

.dash-kpi-value {
  font-family: var(--font-head);
  font-size: 2.2rem;
  font-weight: 800;
  margin: 0.5rem 0;
  color: var(--text-primary);
}

.dash-kpi-card.featured-green .dash-kpi-value {
  color: #ffffff;
}

.dash-kpi-trend {
  font-size: 0.72rem;
  color: #3fb950;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.dash-kpi-card.featured-green .dash-kpi-trend {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.15);
  padding: 0.15rem 0.4rem;
  border-radius: 6px;
  width: fit-content;
}

.dash-kpi-arrow {
  position: absolute;
  top: 1.25rem;
  right: 1.25rem;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--bg-input);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  color: var(--text-secondary);
}

.dash-kpi-card.featured-green .dash-kpi-arrow {
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
}

.dash-content-grid {
  display: grid;
  grid-template-columns: 2fr 1.2fr;
  gap: 1.5rem;
}

@media (max-width: 1100px) {
  .dash-content-grid {
    grid-template-columns: 1fr;
  }
}

.dash-left-col {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.dash-right-col {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.dash-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.25rem;
}

.dash-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.dash-card-title {
  font-family: var(--font-head);
  font-weight: 700;
  font-size: 1rem;
  color: var(--text-primary);
  margin: 0;
}

.btn-card-action {
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-primary);
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-card-action:hover {
  background: var(--bg-card-hover);
}

.builds-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.build-item-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 0.5rem;
  border-bottom: 1px solid var(--border);
}

.build-item-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.build-item-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

.build-item-icon.gaming { background: rgba(79, 142, 247, 0.15); color: #58a6ff; }
.build-item-icon.office { background: rgba(63, 185, 80, 0.15); color: #3fb950; }
.build-item-icon.editing { background: rgba(210, 153, 34, 0.15); color: #d29922; }

.build-item-meta {
  display: flex;
  flex-direction: column;
  line-height: 1.3;
}

.build-item-name {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--text-primary);
}

.build-item-purpose {
  font-size: 0.72rem;
  color: var(--text-secondary);
}

.build-item-status-col {
  text-align: right;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.build-item-price {
  font-weight: 700;
  font-size: 0.88rem;
  color: #137333;
}
[data-bs-theme="dark"] .build-item-price {
  color: #3fb950;
}

.build-item-badge {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.15rem 0.5rem;
  border-radius: 6px;
}

.build-item-badge.completed { background: rgba(63, 185, 80, 0.15); color: #3fb950; }
.build-item-badge.in-progress { background: rgba(210, 153, 34, 0.15); color: #d29922; }

.watchlist-items {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.watch-item-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border);
}

.watch-item-left {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.watch-item-category-icon {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-secondary);
}

.watch-item-details {
  display: flex;
  flex-direction: column;
  line-height: 1.3;
}

.watch-item-name {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-primary);
  max-width: 180px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.watch-item-retailer {
  font-size: 0.68rem;
  color: var(--text-muted);
}

.watch-item-price {
  font-weight: 700;
  font-size: 0.82rem;
  color: var(--text-primary);
}

.reminder-box {
  background: var(--bg-card-hover);
  border-left: 3px solid #137333;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.reminder-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.15rem;
}

.reminder-subtitle {
  font-size: 0.72rem;
  color: var(--text-secondary);
}

.btn-reminder-action {
  background: #137333;
  color: #fff;
  border: none;
  font-weight: 600;
  font-size: 0.8rem;
  padding: 0.4rem 1rem;
  border-radius: 12px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  text-decoration: none;
}

.btn-reminder-action:hover {
  background: #0f5132;
  color: #fff;
}

.gauge-container {
  position: relative;
  width: 100%;
  height: 120px;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

.gauge-value-text {
  position: absolute;
  bottom: 0px;
  text-align: center;
  font-family: var(--font-head);
}

.gauge-percent {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--text-primary);
}

.gauge-label {
  font-size: 0.7rem;
  color: var(--text-secondary);
}

.budget-tracker-card {
  background: linear-gradient(135deg, #0d2c1d, #091e14);
  border-radius: 16px;
  padding: 1.25rem;
  color: #ffffff;
  position: relative;
  overflow: hidden;
}

.budget-tracker-card::after {
  content: "";
  position: absolute;
  right: -20px;
  bottom: -20px;
  width: 100px;
  height: 100px;
  background: radial-gradient(circle, rgba(63, 185, 80, 0.15) 0%, transparent 70%);
  pointer-events: none;
}

.budget-card-title {
  font-size: 0.8rem;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 0.5rem;
}

.budget-card-amount {
  font-family: var(--font-head);
  font-size: 1.8rem;
  font-weight: 800;
  color: #ffffff;
  margin-bottom: 1rem;
}

.budget-card-controls {
  display: flex;
  gap: 0.75rem;
}

.btn-budget-control {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-budget-control:hover {
  background: #ffffff;
  color: #0f5132;
}

.btn-budget-control.btn-stop {
  background: rgba(248, 81, 73, 0.2);
  color: #f85149;
}

.btn-budget-control.btn-stop:hover {
  background: #f85149;
  color: #ffffff;
}
</style>

<div class="dashboard-app-layout">
  <aside class="dash-sidebar">
    <div>
      <div class="dash-brand">
        <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
        <span>PC Builder BD</span>
      </div>

      <div class="dash-nav-section-title">Menu</div>
      <div class="dash-nav-links">
        <a href="<?= BASE_URL ?>/dashboard.php" class="dash-nav-item active">
          <span><i class="bi bi-grid-fill"></i>Dashboard</span>
        </a>
        <a href="<?= BASE_URL ?>/purpose.php" class="dash-nav-item">
          <span><i class="bi bi-magic"></i>Build Wizard</span>
          <span class="dash-nav-badge">New</span>
        </a>
        <a href="<?= BASE_URL ?>/store.php" class="dash-nav-item">
          <span><i class="bi bi-shop"></i>Product Store</span>
        </a>
        <a href="<?= BASE_URL ?>/compare.php" class="dash-nav-item">
          <span><i class="bi bi-layout-split"></i>Compare Tool</span>
        </a>
        <a href="<?= BASE_URL ?>/chatbot.php" class="dash-nav-item">
          <span><i class="bi bi-robot"></i>AI Chatbot</span>
        </a>
      </div>

      <div class="dash-nav-section-title">General</div>
      <div class="dash-nav-links">
        <?php if (is_admin()): ?>
        <a href="<?= BASE_URL ?>/admin/index.php" class="dash-nav-item text-purple" style="color: #7c3aed;">
          <span><i class="bi bi-shield-fill-check"></i>Admin Panel</span>
        </a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/upgrade.php" class="dash-nav-item">
          <span><i class="bi bi-arrow-up-circle"></i>Upgrade Advisor</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php" class="dash-nav-item">
          <span><i class="bi bi-house"></i>Home Page</span>
        </a>
        <a href="<?= BASE_URL ?>/logout.php" class="dash-nav-item text-danger">
          <span><i class="bi bi-box-arrow-right"></i>Logout</span>
        </a>
      </div>
    </div>

    <div class="sidebar-promo-card">
      <h4>Download PC Guide</h4>
      <p>Get instant building guides offline</p>
      <button class="sidebar-promo-btn" onclick="alert('Offline guide downloads coming soon!')">Download</button>
    </div>
  </aside>

  <main class="dash-main-area">
    <div class="dash-header-row">
      <div class="dash-search-box">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Search components..." onkeydown="if(event.key === 'Enter') window.location.href='<?= BASE_URL ?>/store.php?search=' + encodeURIComponent(this.value)">
        <span class="dash-search-shortcut">⌘F</span>
      </div>

      <div class="dash-header-actions">
        <a href="<?= BASE_URL ?>/chatbot.php" class="dash-icon-btn" title="Messages">
          <i class="bi bi-envelope"></i>
        </a>
        <a href="#" class="dash-icon-btn" title="Notifications" onclick="alert('You are all caught up!')">
          <i class="bi bi-bell"></i>
        </a>
        <div class="dropdown">
          <button class="btn btn-sm btn-ghost dropdown-toggle d-flex align-items-center gap-2 text-start p-1"
                  type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: transparent;">
            <div class="dash-user-avatar">
              <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div class="dash-user-info d-none d-md-flex">
              <span class="dash-user-name" style="font-size: 0.88rem; font-weight:600; color:var(--text-primary);"><?= sanitise($user['name']) ?></span>
              <span class="dash-user-email" style="font-size: 0.72rem; color:var(--text-secondary);"><?= sanitise($user['email']) ?></span>
            </div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end glass-dropdown">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-grid me-2"></i>Dashboard</a></li>
            <?php if (is_admin()): ?>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/index.php" style="color: #7c3aed !important;"><i class="bi bi-shield-fill-check me-2"></i>Admin Panel</a></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/upgrade.php"><i class="bi bi-arrow-up-circle me-2"></i>Upgrade Advisor</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/index.php"><i class="bi bi-house me-2"></i>Home Page</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="dash-title-row">
      <div class="dash-title-text">
        <h2>Dashboard</h2>
        <p>Plan, prioritize, and assemble your dream PC builds with ease.</p>
      </div>
      <div class="dash-title-buttons">
        <a href="<?= BASE_URL ?>/purpose.php" class="btn-dash-primary">
          <i class="bi bi-plus-lg"></i>Add Build
        </a>
        <a href="<?= BASE_URL ?>/store.php" class="btn-dash-secondary">Browse Store</a>
      </div>
    </div>

    <div class="dash-kpis-row">
      <div class="dash-kpi-card featured-green">
        <div class="dash-kpi-title">Saved Builds</div>
        <div class="dash-kpi-value"><?= count($builds) ?></div>
        <div class="dash-kpi-trend"><i class="bi bi-arrow-up-right"></i> 5% Increased</div>
        <div class="dash-kpi-arrow"><i class="bi bi-arrow-up-right"></i></div>
      </div>

      <div class="dash-kpi-card">
        <div class="dash-kpi-title">Watchlist Items</div>
        <div class="dash-kpi-value"><?= count($watchlist) ?></div>
        <div class="dash-kpi-trend" style="color:#d29922;"><i class="bi bi-bell-fill"></i> Watch active</div>
        <div class="dash-kpi-arrow"><i class="bi bi-arrow-up-right"></i></div>
      </div>

      <div class="dash-kpi-card">
        <div class="dash-kpi-title">System Components</div>
        <div class="dash-kpi-value"><?= number_format($total_components) ?></div>
        <div class="dash-kpi-trend"><i class="bi bi-arrow-up-right"></i> 2% Added</div>
        <div class="dash-kpi-arrow"><i class="bi bi-arrow-up-right"></i></div>
      </div>

      <div class="dash-kpi-card">
        <div class="dash-kpi-title">Tracked Retailers</div>
        <div class="dash-kpi-value"><?= $total_stores ?></div>
        <div class="dash-kpi-trend" style="color:var(--text-muted);">Fully Synced</div>
        <div class="dash-kpi-arrow"><i class="bi bi-arrow-up-right"></i></div>
      </div>
    </div>

    <div class="dash-content-grid">
      <div class="dash-left-col">
        <div class="dash-card">
          <div class="dash-card-header">
            <h3 class="dash-card-title">
              <?php if (!empty($watchlist)): ?>
                Price Analytics (<?= sanitise($watchlist[0]['name']) ?>)
              <?php else: ?>
                Price Analytics (No data)
              <?php endif; ?>
            </h3>
            <span class="text-muted small">Last 30 days</span>
          </div>
          <?php if (empty($trend_labels)): ?>
            <div class="text-center py-5 text-muted">
              <i class="bi bi-graph-up display-6 d-block mb-2"></i>
              Add components to your watchlist to track market price fluctuation trends.
            </div>
          <?php else: ?>
            <div style="height: 220px; width: 100%;">
              <canvas id="price-trend-chart"></canvas>
            </div>
          <?php endif; ?>
        </div>

        <div class="dash-card">
          <div class="dash-card-header">
            <h3 class="dash-card-title">Saved PC Builds</h3>
            <a href="<?= BASE_URL ?>/purpose.php" class="btn-card-action">+ Add Build</a>
          </div>
          
          <div class="builds-list">
            <?php if (empty($builds)): ?>
              <div class="text-center py-4 text-muted">
                <i class="bi bi-bookmark-dash d-block display-6 mb-2"></i>
                You haven't saved any PC configurations yet.
              </div>
            <?php else: ?>
              <?php foreach ($builds as $b): 
                $iconClass = 'office';
                $purposeName = purpose_label($b['purpose']);
                if (stripos($purposeName, 'gaming') !== false) {
                    $iconClass = 'gaming';
                } elseif (stripos($purposeName, 'editing') !== false || stripos($purposeName, 'workstation') !== false) {
                    $iconClass = 'editing';
                }
              ?>
                <div class="build-item-row">
                  <div class="build-item-info">
                    <div class="build-item-icon <?= $iconClass ?>">
                      <i class="bi bi-pc-display"></i>
                    </div>
                    <div class="build-item-meta">
                      <span class="build-item-name"><?= sanitise($b['name']) ?></span>
                      <span class="build-item-purpose">Specs: <?= sanitise($purposeName) ?></span>
                    </div>
                  </div>

                  <div class="build-item-status-col">
                    <span class="build-item-price"><?= format_bdt((float)$b['total_price']) ?></span>
                    <span class="build-item-badge completed"><?= (int)$b['fps'] ?> FPS</span>
                    
                    <form method="POST" action="<?= BASE_URL ?>/api/delete_build.php" class="d-inline" id="delete-form-<?= (int)$b['build_id'] ?>">
    <?= csrf_field(); ?>
    <input type="hidden" name="build_id" value="<?= (int)$b['build_id'] ?>">
    <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('Delete this build?')" style="font-size: 1.1rem; border: none; background: transparent; cursor: pointer;">
        <i class="bi bi-trash"></i>
    </button>
</form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="dash-right-col">
        <div class="dash-card">
          <h3 class="dash-card-title mb-3">Upgrade Suggestion</h3>
          <div class="reminder-box">
            <div class="reminder-title">Ryzen 5 5600X Deal</div>
            <div class="reminder-subtitle">Price dropped by 3.2% in Star Tech today.</div>
          </div>
          <a href="<?= BASE_URL ?>/upgrade.php" class="btn-reminder-action">
            <i class="bi bi-arrow-up-circle"></i>View Upgrade Advisor
          </a>
        </div>

        <div class="dash-card">
          <div class="dash-card-header">
            <h3 class="dash-card-title">Average Build Performance</h3>
          </div>
          <div class="gauge-container">
            <canvas id="performance-gauge"></canvas>
            <div class="gauge-value-text">
              <div class="gauge-percent">78%</div>
              <div class="gauge-label">Gaming Score</div>
            </div>
          </div>
        </div>

        <div class="dash-card">
          <div class="dash-card-header">
            <h3 class="dash-card-title">Price Watchlist</h3>
            <a href="<?= BASE_URL ?>/store.php" class="btn-card-action">+ Add</a>
          </div>

          <div class="watchlist-items">
            <?php if (empty($watchlist)): ?>
              <div class="text-center py-4 text-muted">
                <i class="bi bi-bell-slash d-block mb-1" style="font-size: 1.5rem;"></i>
                No items watched.
              </div>
            <?php else: ?>
              <?php foreach ($watchlist as $w): 
                $catIcon = 'bi-cpu';
                if ($w['type'] === 'gpu') $catIcon = 'bi-gpu-card';
                elseif ($w['type'] === 'ram') $catIcon = 'bi-memory';
                elseif ($w['type'] === 'ssd' || $w['type'] === 'hdd') $catIcon = 'bi-hdd-fill';
                elseif ($w['type'] === 'psu') $catIcon = 'bi-plug';
              ?>
                <div class="watch-item-row">
                  <div class="watch-item-left">
                    <div class="watch-item-category-icon">
                      <i class="bi <?= $catIcon ?>"></i>
                    </div>
                    <div class="watch-item-details">
                      <span class="watch-item-name" title="<?= sanitise($w['name']) ?>"><?= sanitise($w['name']) ?></span>
                      <span class="watch-item-retailer"><?= sanitise($w['retailer']) ?></span>
                    </div>
                  </div>
                  <span class="watch-item-price"><?= format_bdt((float)$w['price_bdt']) ?></span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="budget-tracker-card">
          <div class="budget-card-title">Live Build Budget</div>
          <div class="budget-card-amount">
            <?php
              $totalBudget = 0;
              if (!empty($builds)) {
                  $totalBudget = (float)$builds[0]['total_price'];
              }
              echo format_bdt($totalBudget);
            ?>
          </div>
          <div class="budget-card-controls">
            <button class="btn-budget-control" onclick="window.location.href='<?= BASE_URL ?>/purpose.php'" title="Edit latest build">
              <i class="bi bi-pencil-fill"></i>
            </button>
            <button class="btn-budget-control btn-stop" onclick="alert('Cleared temporary build progress.')" title="Clear build tracking">
              <i class="bi bi-x-circle-fill"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php
$trend_json  = json_encode($trend_values);
$labels_json = json_encode($trend_labels);
$inline_script = <<<JS
(function(){
  const ctx = document.getElementById('price-trend-chart');
  if (ctx) {
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: {$labels_json},
        datasets: [{
          label: 'Price (BDT)',
          data: {$trend_json},
          borderColor: '#137333',
          backgroundColor: 'rgba(19, 115, 51, 0.08)',
          fill: true,
          tension: 0.35,
          borderWidth: 2.5,
          pointBackgroundColor: '#137333',
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            grid: { color: 'rgba(255, 255, 255, 0.05)' },
            ticks: { color: '#8b949e', font: { size: 10 } }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#8b949e', font: { size: 10 } }
          }
        }
      }
    });
  }

  const gtx = document.getElementById('performance-gauge');
  if (gtx) {
    new Chart(gtx, {
      type: 'doughnut',
      data: {
        labels: ['Completed', 'Pending'],
        datasets: [{
          data: [78, 22],
          backgroundColor: ['#137333', 'rgba(255,255,255,0.06)'],
          borderWidth: 0
        }]
      },
      options: {
        rotation: -90,
        circumference: 180,
        cutout: '80%',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: false }
        }
      }
    });
  }
})();
JS;
include __DIR__ . '/templates/footer.php';
?>
