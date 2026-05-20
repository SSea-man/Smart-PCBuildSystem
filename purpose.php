<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/budget_allocator.php';

if (is_post()) {
    $purpose = input('purpose');
    if (!in_array($purpose, ['gaming','video_editing','office','general'])) {
        $purpose = 'general';
    }
    $_SESSION['purpose'] = $purpose;
    redirect('budget.php');
}

$page_title = 'Select Purpose — Build Wizard';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-5">
  <div class="text-center mb-5">
    <span class="badge bg-accent-soft pill px-3 py-2 mb-3"><i class="bi bi-magic me-1"></i>Step 1 of 3</span>
    <h1 class="section-title">What will you use this PC for?</h1>
    <p class="section-sub">Your choice shapes the component budget split and scoring weights.</p>
  </div>

  <form method="POST" id="purpose-form">
    <?php csrf_field(); ?>
    <input type="hidden" name="purpose" id="purpose-input" value="">
    <div class="row g-4 justify-content-center" style="max-width:900px;margin:0 auto">
      <?php
      $cards = [
        ['val'=>'gaming',       'icon'=>'bi-controller',   'title'=>'Gaming',        'desc'=>'Maximise FPS and GPU performance for gaming at 1080p–4K.', 'color'=>'#4f8ef7'],
        ['val'=>'video_editing','icon'=>'bi-camera-video', 'title'=>'Video Editing', 'desc'=>'High CPU & RAM for 4K editing, rendering and colour grading.','color'=>'#9b59b6'],
        ['val'=>'office',       'icon'=>'bi-briefcase',    'title'=>'Office / Work',  'desc'=>'Balanced build for productivity, multitasking and reliability.','color'=>'#3fb950'],
        ['val'=>'general',      'icon'=>'bi-house',        'title'=>'General Use',   'desc'=>'Everyday computing — browsing, media, light work and gaming.','color'=>'#d29922'],
      ];
      $cur = $_SESSION['purpose'] ?? '';
      foreach ($cards as $c): ?>
      <div class="col-sm-6 col-lg-3">
        <div class="purpose-card <?= $cur === $c['val'] ? 'selected' : '' ?>"
             onclick="selectPurpose('<?= $c['val'] ?>')"
             role="button" tabindex="0" aria-label="Select <?= $c['title'] ?>"
             onkeydown="if(event.key==='Enter'||event.key===' ')selectPurpose('<?= $c['val'] ?>')">
          <div class="purpose-icon" style="--p-color:<?= $c['color'] ?>;background:<?= $c['color'] ?>22;color:<?= $c['color'] ?>">
            <i class="<?= $c['icon'] ?>"></i>
          </div>
          <h5 class="fw-700 mb-2"><?= $c['title'] ?></h5>
          <p class="text-muted small mb-0"><?= $c['desc'] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
      <button type="submit" class="btn btn-accent btn-lg px-5" id="next-btn" disabled>
        <i class="bi bi-arrow-right me-2"></i>Next: Set Budget
      </button>
    </div>
  </form>
</div>

<?php $inline_script = <<<JS
function selectPurpose(val) {
  document.getElementById('purpose-input').value = val;
  document.querySelectorAll('.purpose-card').forEach(c => c.classList.remove('selected'));
  event.currentTarget.classList.add('selected');
  document.getElementById('next-btn').disabled = false;
}
JS;
include __DIR__ . '/templates/footer.php'; ?>
