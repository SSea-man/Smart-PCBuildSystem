<?php
// upgrade.php — uses component table (component_id PK) and component_base_sql()
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/scoring.php';

require_auth();

$suggestion = null;
$errors     = [];

if (is_post()) {
    verify_csrf();
    $budget        = (float)input('budget', 0);
    $cpu_id        = (int)input('current_cpu', 0);
    $gpu_id        = (int)input('current_gpu', 0);
    $purpose_input = input('purpose', 'gaming');

    if ($budget < 1000) $errors[] = 'Upgrade budget must be at least ৳1,000.';

    if (empty($errors)) {
        $cpu = $cpu_id ? db_row(component_base_sql() . ' WHERE c.component_id=?', [$cpu_id]) : null;
        $gpu = $gpu_id ? db_row(component_base_sql() . ' WHERE c.component_id=?', [$gpu_id]) : null;

        $bottleneck = null;
        if ($cpu && $gpu) {
            $cpu_s = (float)$cpu['benchmark_score'];
            $gpu_s = (float)$gpu['benchmark_score'];
            $ratio = $gpu_s > 0 ? $cpu_s / $gpu_s : 1;
            if ($ratio < 0.65)       $bottleneck = 'CPU';
            elseif ($ratio > 1.55)   $bottleneck = 'GPU';
        }

        $upgrade_cat   = $bottleneck ?? ($purpose_input === 'gaming' ? 'GPU' : 'CPU');
        $current_price = 0;
        if ($bottleneck === 'CPU' && $cpu) $current_price = (float)$cpu['price_bdt'];
        if ($bottleneck === 'GPU' && $gpu) $current_price = (float)$gpu['price_bdt'];
        $max_price = $current_price + $budget;

        // Find best upgrade using JOIN query
        $upgrade = db_row(
            component_base_sql() .
            " WHERE c.type LIKE ? AND COALESCE(sa.price,0) <= ? AND COALESCE(sa.price,0) > ?
              ORDER BY c.benchmark_score DESC LIMIT 1",
            ["{$upgrade_cat}%", $max_price, $current_price]
        );

        $suggestion = [
            'bottleneck'=>$bottleneck,'upgrade_cat'=>$upgrade_cat,
            'current_cpu'=>$cpu,'current_gpu'=>$gpu,'upgrade'=>$upgrade,'budget'=>$budget,
        ];

        db_exec('INSERT INTO upgradesuggestion (user_id, build_id, component_id) VALUES (?,0,?)',
            [get_auth_user()['id'], $upgrade ? (int)$upgrade['id'] : 0]);
    }
}

$cpus = db_query("SELECT component_id as id, component_name as name, benchmark_score,
    COALESCE(sa.price,0) as price_bdt FROM component c
    LEFT JOIN (SELECT component_id, MIN(price) as price FROM storeavailability GROUP BY component_id) sa ON sa.component_id=c.component_id
    WHERE c.type LIKE 'CPU%' ORDER BY COALESCE(sa.price,0)");
$gpus = db_query("SELECT component_id as id, component_name as name, benchmark_score,
    COALESCE(sa.price,0) as price_bdt FROM component c
    LEFT JOIN (SELECT component_id, MIN(price) as price FROM storeavailability GROUP BY component_id) sa ON sa.component_id=c.component_id
    WHERE c.type LIKE 'GPU%' ORDER BY COALESCE(sa.price,0)");

$page_title = 'Upgrade Advisor';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4" style="max-width:900px">
  <div class="text-center mb-5">
    <h1 class="section-title"><i class="bi bi-arrow-up-circle me-2 text-accent"></i>Upgrade Advisor</h1>
    <p class="section-sub">Select your current CPU & GPU. We'll detect the bottleneck and suggest the best upgrade within budget.</p>
  </div>

  <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?=sanitise($e)?></div><?php endforeach; ?>

  <div class="card p-4 mb-4">
    <form method="POST">
      <?php csrf_field(); ?>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-600"><i class="bi bi-cpu me-1 text-accent"></i>Current CPU</label>
          <select name="current_cpu" class="form-select">
            <option value="">— Select your CPU —</option>
            <?php foreach ($cpus as $c): ?><option value="<?=$c['id']?>"><?=sanitise($c['name'])?> (Score: <?=number_format((float)$c['benchmark_score'],0)?>)</option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-600"><i class="bi bi-gpu-card me-1 text-accent"></i>Current GPU</label>
          <select name="current_gpu" class="form-select">
            <option value="">— Select your GPU —</option>
            <?php foreach ($gpus as $g): ?><option value="<?=$g['id']?>"><?=sanitise($g['name'])?> (Score: <?=number_format((float)$g['benchmark_score'],0)?>)</option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-600">Use Case</label>
          <select name="purpose" class="form-select">
            <option value="gaming">Gaming</option><option value="video_editing">Video Editing</option>
            <option value="office">Office</option><option value="general">General</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-600">Upgrade Budget (BDT)</label>
          <div class="input-group"><span class="input-group-text fw-600">৳</span>
            <input type="number" name="budget" class="form-control" placeholder="e.g. 20000" min="1000" step="500">
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-accent px-5"><i class="bi bi-search me-2"></i>Analyze & Suggest Upgrade</button>
        </div>
      </div>
    </form>
  </div>

  <?php if ($suggestion): ?>
  <div class="card p-4 fade-in-up">
    <h5 class="fw-700 mb-3"><i class="bi bi-lightbulb me-2 text-accent"></i>Upgrade Recommendation</h5>
    <?php if ($suggestion['bottleneck']): ?>
    <div class="alert alert-warning mb-3"><i class="bi bi-exclamation-triangle me-2"></i><strong>Bottleneck Detected:</strong> Your <strong><?=$suggestion['bottleneck']?></strong> is limiting performance.</div>
    <?php else: ?>
    <div class="alert alert-info mb-3"><i class="bi bi-info-circle me-2"></i>No significant bottleneck detected. Upgrading your <strong><?=$suggestion['upgrade_cat']?></strong> will improve performance.</div>
    <?php endif; ?>
    <?php if ($suggestion['upgrade']): $u = $suggestion['upgrade']; ?>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="card p-3" style="border-color:var(--border)">
          <div class="text-muted small mb-1">Recommended Upgrade</div>
          <div class="fw-700"><?=sanitise($u['name'])?></div>
          <div class="text-accent fw-700 fs-5"><?=format_bdt((float)$u['price_bdt'])?></div>
          <div class="text-muted small"><?=sanitise($u['retailer'])?></div>
          <div class="mt-2"><span class="badge bg-accent-soft">Score: <?=number_format((float)$u['benchmark_score'],0)?></span></div>
        </div>
      </div>
      <div class="col-md-6">
        <?php
        $before = $suggestion['bottleneck']==='CPU'?(float)($suggestion['current_cpu']['benchmark_score']??0):(float)($suggestion['current_gpu']['benchmark_score']??0);
        $after  = (float)$u['benchmark_score'];
        $pct_gain = $before>0?round((($after-$before)/$before)*100):0;
        ?>
        <div class="card p-3 h-100" style="border-color:var(--border)">
          <div class="text-muted small mb-2">Expected Performance Gain</div>
          <div class="fw-800 text-success" style="font-size:2.5rem">+<?=$pct_gain?>%</div>
          <div class="progress mb-2 mt-3" style="height:10px"><div class="progress-bar bg-warning" style="width:<?=min(100,round($before/3))?>%"></div></div>
          <small class="text-muted">Before: <?=number_format($before,0)?></small>
          <div class="progress mt-2 mb-2" style="height:10px"><div class="progress-bar bg-success" style="width:<?=min(100,round($after/3))?>%"></div></div>
          <small class="text-muted">After: <?=number_format($after,0)?></small>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">No better component found within <?=format_bdt($suggestion['budget'])?>. Try increasing your budget.</div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
