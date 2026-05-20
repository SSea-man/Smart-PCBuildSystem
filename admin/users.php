<?php
// admin/users.php — project_alpha: user table, user_id/user_name/email/role columns
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_auth('admin');

if (is_post() && input('action') === 'role') {
    verify_csrf();
    $uid  = (int)input('user_id');
    $role = in_array(input('role'),['user','admin']) ? input('role') : 'user';
    if ($uid !== (int)get_auth_user()['id']) {
        db_exec('UPDATE `user` SET role=? WHERE user_id=?', [$role, $uid]);
        flash_message('success','Role updated.');
    } else {
        flash_message('warning','You cannot change your own role.');
    }
    redirect('admin/users.php');
}

$search = trim(input('search',''));
$where  = $search ? 'WHERE user_name LIKE ? OR email LIKE ?' : '';
$params = $search ? ["%$search%","%$search%"] : [];
$total  = (int)db_row("SELECT COUNT(*) c FROM `user` $where",$params)['c'];
$pag    = paginate($total,(int)input('page',1),20);
$users  = db_query("SELECT * FROM `user` $where ORDER BY created_at DESC LIMIT 20 OFFSET {$pag['offset']}",$params);

$page_title = 'Manage Users';
include __DIR__ . '/../templates/header.php';
?>
<div class="container-xl py-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h1 class="h4 fw-800"><i class="bi bi-people me-2 text-accent"></i>User Management</h1>
    <a href="<?=BASE_URL?>/admin/index.php" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
  </div>
  <form method="GET" class="d-flex gap-2 mb-3">
    <input type="text" name="search" class="form-control form-control-sm" style="max-width:280px" placeholder="Search name or email…" value="<?=sanitise($search)?>">
    <button class="btn btn-outline-accent btn-sm">Search</button>
    <?php if($search):?><a href="?" class="btn btn-outline-secondary btn-sm">Clear</a><?php endif;?>
  </form>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Change Role</th></tr></thead>
        <tbody>
          <?php foreach($users as $u):?>
          <tr>
            <td class="text-muted small"><?=(int)$u['user_id']?></td>
            <td class="fw-600"><?=sanitise($u['user_name'])?></td>
            <td class="text-muted small"><?=sanitise($u['email'])?></td>
            <td><span class="badge <?=$u['role']==='admin'?'bg-danger':'bg-accent-soft'?>"><?=sanitise($u['role']??'user')?></span></td>
            <td>
              <form method="POST" class="d-flex gap-1 align-items-center">
                <?php csrf_field();?>
                <input type="hidden" name="action" value="role">
                <input type="hidden" name="user_id" value="<?=(int)$u['user_id']?>">
                <select name="role" class="form-select form-select-sm" style="width:100px">
                  <option value="user"  <?=($u['role']??'')==='user' ?'selected':''?>>user</option>
                  <option value="admin" <?=($u['role']??'')==='admin'?'selected':''?>>admin</option>
                </select>
                <button class="btn btn-sm btn-outline-accent"><i class="bi bi-check-lg"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
  <?php render_pagination($pag, BASE_URL.'/admin/users.php?search='.urlencode($search));?>
</div>
<?php include __DIR__ . '/../templates/footer.php';?>
