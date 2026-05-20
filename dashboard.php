<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_auth();
$user = get_auth_user();
$uid  = $user['id'];  // normalised id from session

// Saved builds (build table, user_id FK)
$builds = db_query(
    'SELECT * FROM `build` WHERE user_id=? ORDER BY created_at DESC LIMIT 10', [$uid]
);

// Watchlist (created by migration)
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

// Price trend for first watched item
$trend_labels = $trend_values = [];
if (!empty($watchlist)) {
    $first_id = $watchlist[0]['id'];
    $history  = db_query(
        'SELECT DATE(changed_at) as d, new_price FROM pricetracking
         WHERE component_id=? AND changed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY d',
        [$first_id]
    );
    foreach ($history as $h) { $trend_labels[] = $h['d']; $trend_values[] = (float)$h['new_price']; }
}

$page_title = 'Dashboard';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
      <h1 class="h3 fw-800 mb-1">Welcome back, <?= sanitise($user['name']) ?> 👋</h1>
      <p class="text-muted mb-0">Your builds and watchlist at a glance.</p>
    </div>
    <a href="<?= BASE_URL ?>/purpose.php" class="btn btn-accent"><i class="bi bi-plus-lg me-2"></i>New Build</a>
  </div>

  <div class="row g-3 mb-4">
    <?php
    $kpis = [
      ['icon'=>'bi-bookmark-fill','val'=>count($builds),    'label'=>'Saved Builds', 'color'=>'var(--accent)'],
      ['icon'=>'bi-bell-fill',    'val'=>count($watchlist), 'label'=>'Watchlist',    'color'=>'var(--success)'],
      ['icon'=>'bi-cpu',          'val'=>db_row('SELECT COUNT(*) c FROM component')['c'], 'label'=>'Components', 'color'=>'var(--warning)'],
    ];
    foreach ($kpis as $k): ?>
    <div class="col-sm-4">
      <div class="kpi-card">
        <div class="kpi-label mb-1"><i class="<?=$k['icon']?> me-1"></i><?=$k['label']?></div>
        <div class="kpi-value" style="color:<?=$k['color']?>"><?=$k['val']?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-700"><i class="bi bi-bookmark me-2 text-accent"></i>Saved Builds</h5>
          <a href="<?=BASE_URL?>/purpose.php" class="btn btn-sm btn-outline-accent">New Build</a>
        </div>
        <div class="card-body p-0">
          <?php if (empty($builds)): ?>
          <div class="text-center py-5">
            <i class="bi bi-inbox display-4 text-muted mb-3 d-block"></i>
            <p class="text-muted">No saved builds yet.</p>
            <a href="<?=BASE_URL?>/purpose.php" class="btn btn-accent btn-sm">Start a Build</a>
          </div>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead><tr><th>Name</th><th>Purpose</th><th>Total</th><th>FPS</th><th></th></tr></thead>
              <tbody>
                <?php foreach ($builds as $b): ?>
                <tr>
                  <td class="fw-600"><?=sanitise($b['name'])?></td>
                  <td><span class="badge bg-accent-soft"><?=sanitise(purpose_label($b['purpose']))?></span></td>
                  <td class="fw-600 text-accent"><?=format_bdt((float)$b['total_price'])?></td>
                  <td><?=(int)$b['fps']?> fps</td>
                  <td>
                    <form method="POST" action="<?=BASE_URL?>/api/delete_build.php" class="d-inline">
                      <?php csrf_field(); ?>
                      <input type="hidden" name="build_id" value="<?=(int)$b['build_id']?>">
                      <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-700"><i class="bi bi-bell me-2 text-accent"></i>Watchlist</h5>
          <a href="<?=BASE_URL?>/store.php" class="btn btn-sm btn-outline-accent">Browse</a>
        </div>
        <div class="card-body p-0">
          <?php if (empty($watchlist)): ?>
          <div class="text-center py-5">
            <i class="bi bi-bell-slash display-4 text-muted mb-3 d-block"></i>
            <p class="text-muted">No components watched.</p>
          </div>
          <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($watchlist as $w): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center" style="background:transparent;border-color:var(--border)">
              <div>
                <div class="fw-600 small"><?=sanitise($w['name'])?></div>
                <small class="text-muted"><?=sanitise(type_to_category($w['type']))?> · <?=sanitise($w['retailer'])?></small>
              </div>
              <span class="fw-700 text-accent"><?=format_bdt((float)$w['price_bdt'])?></span>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php if (!empty($trend_labels)): ?>
  <div class="card mt-4">
    <div class="card-header"><h5 class="mb-0 fw-700"><i class="bi bi-graph-up me-2 text-accent"></i>Price Trend — <?=sanitise($watchlist[0]['name'])?></h5></div>
    <div class="card-body"><div class="chart-container" style="height:200px"><canvas id="price-trend-chart"></canvas></div></div>
  </div>
  <?php endif; ?>
</div>

<?php
$trend_json  = json_encode($trend_values);
$labels_json = json_encode($trend_labels);
$inline_script = <<<JS
(function(){
  const ctx = document.getElementById('price-trend-chart');
  if(!ctx) return;
  new Chart(ctx,{type:'line',data:{labels:{$labels_json},datasets:[{label:'Price (৳)',data:{$trend_json},borderColor:'#4f8ef7',backgroundColor:'rgba(79,142,247,.12)',fill:true,tension:.4,pointBackgroundColor:'#4f8ef7'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{grid:{color:'rgba(255,255,255,.06)'},ticks:{color:'#8b949e'}},x:{grid:{display:false},ticks:{color:'#8b949e'}}}}});
})();
JS;
include __DIR__ . '/templates/footer.php';
?>
