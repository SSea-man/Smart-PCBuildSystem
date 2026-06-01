<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_auth('admin');

$total_users  = db_row('SELECT COUNT(*) c FROM `user`')['c'];
$total_comps  = db_row('SELECT COUNT(*) c FROM component')['c'];
$total_builds = db_row('SELECT COUNT(*) c FROM `build`')['c'];
$recent_users = db_query('SELECT user_id,user_name,email,role,created_at FROM `user` ORDER BY created_at DESC LIMIT 5');
$recent_builds= db_query(
    'SELECT b.build_id,b.name,b.total_price,b.score,b.created_at,u.user_name FROM `build` b
     JOIN `user` u ON u.user_id=b.user_id ORDER BY b.created_at DESC LIMIT 5'
);

$page_title = 'Admin Dashboard';
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

.admin-kpis-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
}

@media (max-width: 768px) {
  .admin-kpis-grid {
    grid-template-columns: 1fr;
  }
}

.admin-kpi-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: var(--shadow-sm);
  transition: transform 0.2s ease;
}

.admin-kpi-card:hover {
  transform: translateY(-2px);
}

.admin-kpi-title {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
}

.admin-kpi-value {
  font-family: var(--font-head);
  font-size: 2rem;
  font-weight: 800;
  color: #7c3aed; }

.admin-row-panels {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

@media (max-width: 992px) {
  .admin-row-panels {
    grid-template-columns: 1fr;
  }
}

.admin-panel-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.25rem;
  box-shadow: var(--shadow-sm);
}

.admin-panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.admin-panel-title {
  font-family: var(--font-head);
  font-weight: 700;
  font-size: 1rem;
  color: var(--text-primary);
  margin: 0;
}

.badge-role-admin {
  background: rgba(248, 81, 73, 0.15);
  color: #f85149;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 0.15rem 0.5rem;
  border-radius: 6px;
}

.badge-role-moderator {
  background: rgba(210, 153, 34, 0.15);
  color: #d29922;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 0.15rem 0.5rem;
  border-radius: 6px;
}

.badge-role-user {
  background: var(--accent-soft);
  color: var(--accent);
  font-size: 0.72rem;
  font-weight: 600;
  padding: 0.15rem 0.5rem;
  border-radius: 6px;
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
        <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-nav-link active">
          <i class="bi bi-grid"></i>Dashboard
        </a>
        <a href="<?= BASE_URL ?>/admin/components.php" class="sidebar-nav-link">
          <i class="bi bi-cpu"></i>Components
        </a>
        <a href="<?= BASE_URL ?>/admin/users.php" class="sidebar-nav-link">
          <i class="bi bi-people"></i>User Roles
        </a>
        <a href="<?= BASE_URL ?>/admin/prices.php" class="sidebar-nav-link">
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
        <input type="text" placeholder="Search system logs..." onclick="alert('System log search coming soon.')">
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

    <div class="d-flex justify-content-between align-items-center mb-1">
      <div>
        <h2 style="font-family: var(--font-head); font-weight:800; font-size:1.6rem; color:var(--text-primary); margin:0;">Admin Console Dashboard</h2>
        <p class="text-muted small" style="margin:0;">System usage statistics and configuration logs.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/components.php" class="btn btn-sm btn-outline-primary" style="border-radius:12px; font-weight:600;">Manage Parts</a>
        <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-sm btn-primary" style="background:#7c3aed; border-color:#7c3aed; border-radius:12px; font-weight:600;">Manage Roles</a>
      </div>
    </div>

    <div class="admin-kpis-grid">
      <div class="admin-kpi-card">
        <div class="admin-kpi-title"><i class="bi bi-people-fill me-1"></i>Total Users</div>
        <div class="admin-kpi-value"><?= number_format((int)$total_users) ?></div>
      </div>
      <div class="admin-kpi-card">
        <div class="admin-kpi-title"><i class="bi bi-cpu-fill me-1"></i>Component Database</div>
        <div class="admin-kpi-value"><?= number_format((int)$total_comps) ?></div>
      </div>
      <div class="admin-kpi-card">
        <div class="admin-kpi-title"><i class="bi bi-bookmark-fill me-1"></i>Saved Builds</div>
        <div class="admin-kpi-value"><?= number_format((int)$total_builds) ?></div>
      </div>
    </div>

    <div class="admin-row-panels">
      <div class="admin-panel-card">
        <div class="admin-panel-header">
          <h3 class="admin-panel-title">Recent Users</h3>
          <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-sm btn-outline-secondary" style="font-size:0.75rem; border-radius:10px;">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead><tr><th>User</th><th>Email</th><th>Role</th></tr></thead>
            <tbody>
              <?php foreach ($recent_users as $u): ?>
                <tr>
                  <td class="fw-600" style="font-size:0.85rem;"><?= sanitise($u['user_name']) ?></td>
                  <td class="text-muted small"><?= sanitise($u['email']) ?></td>
                  <td>
                    <?php
                      $rClass = match($u['role'] ?? 'user') {
                          'admin'     => 'badge-role-admin',
                          'moderator' => 'badge-role-moderator',
                          default     => 'badge-role-user',
                      };
                    ?>
                    <span class="badge <?= $rClass ?>"><?= ucfirst(sanitise($u['role'] ?? 'user')) ?></span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="admin-panel-card">
        <div class="admin-panel-header">
          <h3 class="admin-panel-title">Recent Builds</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead><tr><th>Build Name</th><th>Creator</th><th>Total Price</th><th>Score</th></tr></thead>
            <tbody>
              <?php foreach ($recent_builds as $b): ?>
                <tr>
                  <td class="fw-600" style="font-size:0.85rem;"><?= sanitise($b['name']) ?></td>
                  <td class="text-muted small"><?= sanitise($b['user_name']) ?></td>
                  <td class="text-accent fw-600" style="font-size:0.85rem;"><?= format_bdt((float)$b['total_price']) ?></td>
                  <td style="font-size:0.85rem; font-weight:600;"><?= number_format((float)$b['score'], 1) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
