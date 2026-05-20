<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/budget_allocator.php';

$purpose = $_SESSION['purpose'] ?? 'general';

if (is_post()) {
    verify_csrf();
    $budget = (float)input('budget');
    if ($budget < 10000) {
        flash_message('warning', 'Minimum budget is ৳10,000.');
        redirect('budget.php');
    }
    $_SESSION['budget'] = $budget;
    redirect('builds.php');
}

$purpose_label = purpose_label($purpose);
$profile       = get_budget_profile($purpose);
$sample_budget = 80000;

$page_title = 'Set Your Budget — Build Wizard';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-5" style="max-width:860px">
  <div class="text-center mb-5">
    <span class="badge bg-accent-soft pill px-3 py-2 mb-3"><i class="bi bi-cash-coin me-1"></i>Step 2 of 3</span>
    <h1 class="section-title">Set Your Budget</h1>
    <p class="section-sub">Building for <strong class="text-accent"><?= sanitise($purpose_label) ?></strong>. How much do you want to spend?</p>
  </div>

  <form method="POST" id="budget-form">
    <?php csrf_field(); ?>
    <div class="card p-4 mb-4">
      <label for="budget-range" class="form-label fw-600 mb-3">
        Budget: <span id="budget-display" class="text-accent fw-800">৳80,000</span>
      </label>
      <input type="range" class="form-range mb-3" id="budget-range"
             min="10000" max="500000" step="5000" value="80000">
      <div class="d-flex justify-content-between text-muted small">
        <span>৳10,000</span><span>৳2,50,000</span><span>৳5,00,000</span>
      </div>
      <div class="mt-3">
        <label for="budget" class="form-label small text-muted">Or type exact amount (BDT)</label>
        <div class="input-group" style="max-width:260px">
          <span class="input-group-text fw-600">৳</span>
          <input type="number" id="budget" name="budget" class="form-control"
                 min="10000" max="1000000" step="1000" value="80000" required>
        </div>
      </div>
    </div>

    <!-- Live allocation chart -->
    <div class="card p-4 mb-4">
      <h5 class="fw-700 mb-3"><i class="bi bi-pie-chart me-2 text-accent"></i>Budget Allocation Preview</h5>
      <div class="row align-items-center g-3">
        <div class="col-md-5">
          <canvas id="alloc-chart" style="max-height:220px"></canvas>
        </div>
        <div class="col-md-7">
          <div id="alloc-breakdown" class="row g-2"></div>
        </div>
      </div>
    </div>

    <div class="d-flex gap-3 justify-content-between">
      <a href="<?= BASE_URL ?>/purpose.php" class="btn btn-outline-secondary px-4">
        <i class="bi bi-arrow-left me-1"></i>Back
      </a>
      <button type="submit" class="btn btn-accent btn-lg px-5">
        <i class="bi bi-stars me-2"></i>Generate Builds
      </button>
    </div>
  </form>
</div>

<?php
$profile_json = json_encode($profile);
$inline_script = <<<JS
const profile = {$profile_json};
const labels  = Object.keys(profile);
const pcts    = Object.values(profile).map(v => Math.round(v * 100));
const colors  = ['#4f8ef7','#3fb950','#d29922','#9b59b6','#e74c3c','#1abc9c','#e67e22','#58a6ff'];

const ctx = document.getElementById('alloc-chart').getContext('2d');
const chart = new Chart(ctx, {
  type: 'doughnut',
  data: { labels, datasets: [{ data: pcts, backgroundColor: colors, borderWidth: 2, borderColor: 'var(--bg-card)' }] },
  options: {
    responsive: true, maintainAspectRatio: true, cutout: '65%',
    plugins: { legend: { display: false }, tooltip: { callbacks: {
      label: ctx => ` \${ctx.label}: \${ctx.raw}% (৳\${Math.round(getBudget() * Object.values(profile)[ctx.dataIndex]).toLocaleString('en-BD')})`
    }}}
  }
});

function getBudget() { return parseFloat(document.getElementById('budget').value) || 80000; }

function updateAll() {
  const b = getBudget();
  document.getElementById('budget-display').textContent = '৳' + b.toLocaleString('en-BD');
  document.getElementById('budget-range').value = Math.min(b, 500000);

  const bd = document.getElementById('alloc-breakdown');
  bd.innerHTML = labels.map((cat, i) =>
    `<div class="col-6">
      <div class="d-flex align-items-center gap-2 mb-1">
        <span style="width:10px;height:10px;background:\${colors[i]};border-radius:2px;flex-shrink:0"></span>
        <small class="text-muted">\${cat}</small>
      </div>
      <div class="fw-600 small">৳\${Math.round(b * profile[cat]).toLocaleString('en-BD')}</div>
    </div>`
  ).join('');
  chart.update();
}

document.getElementById('budget').addEventListener('input', updateAll);
document.getElementById('budget-range').addEventListener('input', function() {
  document.getElementById('budget').value = this.value;
  updateAll();
});

updateAll();
JS;
include __DIR__ . '/templates/footer.php'; ?>
