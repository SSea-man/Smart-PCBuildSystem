<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/scoring.php';
require_once __DIR__ . '/includes/budget_allocator.php';

$purpose = $_SESSION['purpose'] ?? 'general';
$budget  = (float)($_SESSION['budget']  ?? 80000);

if ($budget < 1) {
    flash_message('warning', 'Please set a budget first.');
    redirect('budget.php');
}

$builds = get_top_builds($purpose, $budget);

$page_title = 'Top 3 Builds — Build Wizard';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-5">
  <div class="text-center mb-5">
    <span class="badge bg-accent-soft pill px-3 py-2 mb-3"><i class="bi bi-stars me-1"></i>Step 3 of 3</span>
    <h1 class="section-title">Your Top <?= count($builds) ?> Recommended Builds</h1>
    <p class="section-sub">
      <strong class="text-accent"><?= sanitise(purpose_label($purpose)) ?></strong> build ·
      Budget <strong class="text-accent"><?= format_bdt($budget) ?></strong> ·
      Ranked by AI composite score
    </p>
  </div>

  <?php if (empty($builds)): ?>
  <div class="text-center py-5">
    <i class="bi bi-exclamation-circle display-4 text-warning mb-3 d-block"></i>
    <h4>Not enough components in the database</h4>
    <p class="text-muted">Ask the admin to add component data, then try again.</p>
    <a href="<?= BASE_URL ?>/budget.php" class="btn btn-outline-accent">← Change Budget</a>
  </div>
  <?php else: ?>
  <div class="row g-4">
    <?php foreach ($builds as $i => $build):
      $build_index   = $i + 1;
      $build_data    = $build;
      $show_save     = true;
      include __DIR__ . '/templates/build_card.php';
    endforeach; ?>
  </div>

  <div class="d-flex gap-3 justify-content-center flex-wrap mt-5">
    <a href="<?= BASE_URL ?>/budget.php" class="btn btn-outline-secondary px-4">
      <i class="bi bi-arrow-left me-1"></i>Change Budget
    </a>
    <a href="<?= BASE_URL ?>/custom_builder.php" class="btn btn-outline-accent px-4">
      <i class="bi bi-sliders me-1"></i>Custom Builder
    </a>
    <a href="<?= BASE_URL ?>/chatbot.php" class="btn btn-outline-light px-4">
      <i class="bi bi-robot me-1"></i>Ask AI Chatbot
    </a>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
