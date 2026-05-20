<?php
// admin/index.php — project_alpha schema
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
<div class="container-xl py-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h1 class="h3 fw-800 mb-0"><i class="bi bi-shield-fill-check me-2 text-accent"></i>Admin Dashboard</h1>
    <div class="d-flex gap-2">
      <a href="<?=BASE_URL?>/admin/components.php" class="btn btn-accent btn-sm">Manage Components</a>
      <a href="<?=BASE_URL?>/admin/users.php"      class="btn btn-outline-accent btn-sm">Manage Users</a>
    </div>
  </div>
  <div class="row g-3 mb-4">
    <?php foreach([
      ['bi-people-fill', $total_users,  'Total Users',  'var(--accent)'],
      ['bi-cpu-fill',    $total_comps,  'Components',   'var(--success)'],
      ['bi-bookmark-fill',$total_builds,'Saved Builds', 'var(--warning)'],
    ] as [$icon,$val,$label,$color]):?>
    <div class="col-sm-4">
      <div class="kpi-card">
        <div class="kpi-label mb-1"><i class="<?=$icon?> me-1"></i><?=$label?></div>
        <div class="kpi-value" style="color:<?=$color?>"><?=number_format((int)$val)?></div>
      </div>
    </div>
    <?php endforeach;?>
  </div>
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-700"><i class="bi bi-people me-2 text-accent"></i>Recent Users</h6>
          <a href="<?=BASE_URL?>/admin/users.php" class="btn btn-sm btn-outline-accent">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
            <tbody>
              <?php foreach($recent_users as $u):?>
              <tr>
                <td class="fw-600"><?=sanitise($u['user_name'])?></td>
                <td class="text-muted small"><?=sanitise($u['email'])?></td>
                <td><span class="badge <?=$u['role']==='admin'?'bg-danger':'bg-accent-soft'?>"><?=sanitise($u['role']??'user')?></span></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><h6 class="mb-0 fw-700"><i class="bi bi-bookmark me-2 text-accent"></i>Recent Builds</h6></div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>Build</th><th>User</th><th>Total</th><th>Score</th></tr></thead>
            <tbody>
              <?php foreach($recent_builds as $b):?>
              <tr>
                <td class="fw-600"><?=sanitise($b['name'])?></td>
                <td class="text-muted small"><?=sanitise($b['user_name'])?></td>
                <td class="text-accent fw-600"><?=format_bdt((float)$b['total_price'])?></td>
                <td><?=number_format((float)$b['score'],1)?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php';?>
