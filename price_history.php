<?php
// price_history.php — uses component (via component_base_sql) + pricetracking tables
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$component_id = (int)input('id', 0);
$range        = input('range', '30');
$range        = in_array($range, ['30','90','180']) ? $range : '30';

$component = null;
$labels = $values = [];

if ($component_id) {
    $component = db_row(component_base_sql() . ' WHERE c.component_id = ?', [$component_id]);
    if ($component) {
        $history = db_query(
            'SELECT DATE(changed_at) as d, new_price FROM pricetracking
             WHERE component_id=? AND changed_at >= DATE_SUB(NOW(), INTERVAL ? DAY) ORDER BY changed_at',
            [$component_id, (int)$range]
        );
        foreach ($history as $h) { $labels[] = $h['d']; $values[] = (float)$h['new_price']; }
    }
}

// All components for picker
$all_components = db_query('SELECT component_id as id, component_name as name, type FROM component ORDER BY type, component_name');

$page_title = 'Price History';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4">
  <h1 class="section-title mb-1"><i class="bi bi-graph-up-arrow me-2 text-accent"></i>Price History</h1>
  <p class="section-sub mb-4">Track price trends from BD retailers over time.</p>

  <div class="card p-4 mb-4">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-6">
        <label class="form-label fw-600">Component</label>
        <select name="id" class="form-select">
          <option value="">— Select a component —</option>
          <?php foreach ($all_components as $c): ?>
          <option value="<?=(int)$c['id']?>" <?=$component_id===$c['id']?'selected':''?>>
            [<?=sanitise(type_to_category($c['type']))?>] <?=sanitise($c['name'])?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-600">Date Range</label>
        <select name="range" class="form-select">
          <option value="30"  <?=$range==='30' ?'selected':''?>>Last 30 days</option>
          <option value="90"  <?=$range==='90' ?'selected':''?>>Last 90 days</option>
          <option value="180" <?=$range==='180'?'selected':''?>>Last 180 days</option>
        </select>
      </div>
      <div class="col-md-3"><button type="submit" class="btn btn-accent w-100">View Trend</button></div>
    </form>
  </div>

  <?php if ($component): ?>
  <div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <div>
        <h5 class="fw-700 mb-1"><?=sanitise($component['name'])?></h5>
        <span class="badge bg-accent-soft"><?=sanitise($component['category'])?></span>
        <span class="ms-2 fw-700 text-accent"><?=format_bdt((float)$component['price_bdt'])?> current</span>
      </div>
      <div class="d-flex gap-2">
        <?php foreach (['30'=>'30d','90'=>'90d','180'=>'180d'] as $r=>$lbl): ?>
        <a href="?id=<?=$component_id?>&range=<?=$r?>" class="btn btn-sm <?=$range===$r?'btn-accent':'btn-outline-secondary'?>"><?=$lbl?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if (empty($labels)): ?>
    <div class="text-center py-5 text-muted">
      <i class="bi bi-bar-chart-line display-4 mb-3 d-block"></i>
      No price history recorded yet for this component.
    </div>
    <?php else:
      $min_p = min($values); $max_p = max($values);
      $change = count($values)>1 ? round((($values[array_key_last($values)]-$values[0])/$values[0])*100,1) : 0;
    ?>
    <div class="row g-3 mb-4">
      <div class="col-4 text-center"><div class="text-muted small">Min</div><div class="fw-700 text-success"><?=format_bdt($min_p)?></div></div>
      <div class="col-4 text-center"><div class="text-muted small">Max</div><div class="fw-700 text-danger"><?=format_bdt($max_p)?></div></div>
      <div class="col-4 text-center"><div class="text-muted small"><?=$range?>-day Change</div><div class="fw-700 <?=$change<=0?'text-success':'text-danger'?>"><?=$change>0?'+':''?><?=$change?>%</div></div>
    </div>
    <div class="chart-container" style="height:300px"><canvas id="price-chart"></canvas></div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php
$lj = json_encode($labels); $vj = json_encode($values);
$inline_script = <<<JS
(function(){
  const ctx = document.getElementById('price-chart');
  if(!ctx) return;
  new Chart(ctx,{type:'line',data:{labels:{$lj},datasets:[{label:'Price (৳)',data:{$vj},borderColor:'#4f8ef7',backgroundColor:'rgba(79,142,247,.1)',fill:true,tension:.35,pointBackgroundColor:'#4f8ef7',pointRadius:4}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>' ৳'+ctx.raw.toLocaleString('en-BD')}}},scales:{y:{grid:{color:'rgba(255,255,255,.05)'},ticks:{color:'#8b949e',callback:v=>'৳'+v.toLocaleString()}},x:{grid:{display:false},ticks:{color:'#8b949e'}}}}});
})();
JS;
include __DIR__ . '/templates/footer.php'; ?>
