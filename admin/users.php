<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_auth('admin');

if (is_post() && input('action') === 'role') {
    verify_csrf();
    $uid  = (int)input('user_id');
    $role = in_array(input('role'), ['user', 'moderator', 'admin']) ? input('role') : 'user';
    if ($uid !== (int)get_auth_user()['id']) {
        db_exec('UPDATE `user` SET role=? WHERE user_id=?', [$role, $uid]);
        flash_message('success', 'User role updated to ' . $role . '.');
    } else {
        flash_message('warning', 'You cannot change your own role.');
    }
    redirect('admin/users.php');
}

$search = trim(input('search',''));
$filter_role = input('role', '');

$conditions = [];
$params = [];

if ($search) {
    $conditions[] = '(user_name LIKE ? OR email LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter_role) {
    $conditions[] = 'role = ?';
    $params[] = $filter_role;
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$total  = (int)db_row("SELECT COUNT(*) c FROM `user` $where",$params)['c'];
$pag    = paginate($total,(int)input('page',1),15);
$users  = db_query("SELECT * FROM `user` $where ORDER BY created_at DESC LIMIT 15 OFFSET {$pag['offset']}",$params);

$page_title = 'User Management';
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

.admin-tabs {
  display: flex;
  gap: 1.5rem;
  border-bottom: 1px solid var(--border);
  margin-top: 0.5rem;
}

.admin-tab {
  padding: 0.5rem 0.25rem;
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--text-secondary);
  text-decoration: none;
  position: relative;
  transition: color 0.2s ease;
}

.admin-tab:hover {
  color: var(--text-primary);
}

.admin-tab.active {
  color: #7c3aed;
  font-weight: 600;
}

.admin-tab.active::after {
  content: "";
  position: absolute;
  bottom: -1px;
  left: 0;
  right: 0;
  height: 2px;
  background: #7c3aed;
}

.admin-row-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.admin-row-header-labels {
  display: grid;
  grid-template-columns: 40px 100px 2fr 2.5fr 1.5fr 1fr 60px;
  padding: 0.5rem 1.25rem;
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

@media (max-width: 768px) {
  .admin-row-header-labels {
    display: none;
  }
}

.admin-row-card {
  display: grid;
  grid-template-columns: 40px 100px 2fr 2.5fr 1.5fr 1fr 60px;
  align-items: center;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 0.75rem 1.25rem;
  box-shadow: var(--shadow-sm);
  transition: all 0.2s ease;
}

@media (max-width: 768px) {
  .admin-row-card {
    grid-template-columns: 1fr;
    gap: 0.5rem;
    padding: 1rem;
  }
}

.admin-row-card:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
  border-color: #7c3aed50;
}

.admin-row-card.selected {
  border-color: #3fb950;
  background: rgba(63, 185, 80, 0.03);
  box-shadow: 0 0 10px rgba(63, 185, 80, 0.05);
}

.col-cb {
  display: flex;
  align-items: center;
}

.col-id {
  font-size: 0.8rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.col-user-profile {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.col-user-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #7c3aed;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.75rem;
}

.col-user-name {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-primary);
}

.col-email {
  font-size: 0.8rem;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.col-date {
  font-size: 0.8rem;
  color: var(--text-secondary);
}

.col-role {
  display: flex;
  align-items: center;
}

.col-action {
  display: flex;
  justify-content: flex-end;
  position: relative;
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

.dropdown-menu-custom {
  background: var(--bg-card) !important;
  border: 1px solid var(--border) !important;
  box-shadow: var(--shadow-lg) !important;
}

.dropdown-item-custom {
  color: var(--text-primary) !important;
  font-size: 0.82rem;
  padding: 0.4rem 1rem;
}

.dropdown-item-custom:hover {
  background: var(--bg-card-hover) !important;
  color: #7c3aed !important;
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
        <a href="<?= BASE_URL ?>/admin/users.php" class="sidebar-nav-link active">
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
        <form method="GET" action="">
          <input type="text" name="search" placeholder="Search users..." value="<?= sanitise($search) ?>">
          <?php if($filter_role): ?>
            <input type="hidden" name="role" value="<?= sanitise($filter_role) ?>">
          <?php endif; ?>
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

    <div>
      <h2 style="font-family: var(--font-head); font-weight:800; font-size:1.6rem; color:var(--text-primary); margin:0;">User Management</h2>
    </div>

    <div class="admin-tabs">
      <a href="?role=<?= $search ? '&search='.urlencode($search) : '' ?>" class="admin-tab <?= $filter_role === '' ? 'active' : '' ?>">All</a>
      <a href="?role=admin<?= $search ? '&search='.urlencode($search) : '' ?>" class="admin-tab <?= $filter_role === 'admin' ? 'active' : '' ?>">Admins</a>
      <a href="?role=moderator<?= $search ? '&search='.urlencode($search) : '' ?>" class="admin-tab <?= $filter_role === 'moderator' ? 'active' : '' ?>">Moderators</a>
      <a href="?role=user<?= $search ? '&search='.urlencode($search) : '' ?>" class="admin-tab <?= $filter_role === 'user' ? 'active' : '' ?>">Regular Users</a>
    </div>

    <div class="admin-row-list">
      <div class="admin-row-header-labels">
        <div></div>
        <div>User ID</div>
        <div>User Name</div>
        <div>Email Address</div>
        <div>Join Date</div>
        <div>Role</div>
        <div></div>
      </div>

      <?php if(empty($users)): ?>
        <div class="text-center py-5 text-muted bg-card border rounded-3">
          <i class="bi bi-people display-6 d-block mb-2"></i>
          No users match the active filter criteria.
        </div>
      <?php else: ?>
        <?php foreach ($users as $idx => $u): ?>
          <div class="admin-row-card">
            <div class="col-cb">
              <input class="form-check-input" type="checkbox" style="cursor:pointer;">
            </div>
            
            <div class="col-id">USR-<?= str_pad($u['user_id'], 5, '0', STR_PAD_LEFT) ?></div>
            
            <div class="col-user-profile">
              <div class="col-user-avatar">
                <?= strtoupper(substr($u['user_name'], 0, 1)) ?>
              </div>
              <span class="col-user-name"><?= sanitise($u['user_name']) ?></span>
            </div>

            <div class="col-email" title="<?= sanitise($u['email']) ?>"><?= sanitise($u['email']) ?></div>

            <div class="col-date"><?= date('d/m/Y', strtotime($u['created_at'] ?? 'now')) ?></div>

            <div class="col-role">
              <?php
                $roleClass = match($u['role'] ?? 'user') {
                    'admin'     => 'badge-role-admin',
                    'moderator' => 'badge-role-moderator',
                    default     => 'badge-role-user',
                };
              ?>
              <span class="badge <?= $roleClass ?>"><?= ucfirst(sanitise($u['role'] ?? 'user')) ?></span>
            </div>

            <div class="col-action">
              <div class="dropdown">
                <button class="btn btn-link text-secondary p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:1.15rem; text-decoration:none;">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                  <li><h6 class="dropdown-header text-muted" style="font-size:0.7rem; text-transform:uppercase;">Change Role</h6></li>
                  <li>
                    <form method="POST" action="">
                      <?php csrf_field(); ?>
                      <input type="hidden" name="action" value="role">
                      <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                      <input type="hidden" name="role" value="user">
                      <button type="submit" class="dropdown-item dropdown-item-custom" <?= ((int)$u['user_id'] === (int)get_auth_user()['id']) ? 'disabled' : '' ?>>
                        Set as User
                      </button>
                    </form>
                  </li>
                  <li>
                    <form method="POST" action="">
                      <?php csrf_field(); ?>
                      <input type="hidden" name="action" value="role">
                      <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                      <input type="hidden" name="role" value="moderator">
                      <button type="submit" class="dropdown-item dropdown-item-custom" <?= ((int)$u['user_id'] === (int)get_auth_user()['id']) ? 'disabled' : '' ?>>
                        Set as Moderator
                      </button>
                    </form>
                  </li>
                  <li>
                    <form method="POST" action="">
                      <?php csrf_field(); ?>
                      <input type="hidden" name="action" value="role">
                      <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                      <input type="hidden" name="role" value="admin">
                      <button type="submit" class="dropdown-item dropdown-item-custom" <?= ((int)$u['user_id'] === (int)get_auth_user()['id']) ? 'disabled' : '' ?>>
                        Set as Admin
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="mt-4">
      <?php render_pagination($pag, BASE_URL.'/admin/users.php?role='.urlencode($filter_role).'&search='.urlencode($search)); ?>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
