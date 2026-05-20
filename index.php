<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title       = 'AI-Powered PC Builder for Bangladesh';
$page_description = 'Build your perfect PC with AI-powered recommendations, live BDT prices from Star Tech, Ryans & Techland. Compatibility checked automatically.';
include __DIR__ . '/templates/header.php';
?>

<!-- Hero -->
<section class="hero">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="container-xl position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="fade-in-up">
          <span class="badge bg-accent-soft pill mb-3 px-3 py-2">
            <i class="bi bi-stars me-1"></i>AI-Powered · Bangladesh Market
          </span>
          <h1 class="hero-title mb-4">Build Your Dream PC<br>in Minutes</h1>
          <p class="hero-sub mb-4">
            Get AI-optimised PC builds with live BDT prices from Star Tech, Ryans &amp; Techland.
            Automatic compatibility checks, FPS estimates, and budget allocation — all in one place.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="<?= BASE_URL ?>/purpose.php" class="btn btn-accent btn-lg px-4">
              <i class="bi bi-magic me-2"></i>Start Building
            </a>
            <a href="<?= BASE_URL ?>/store.php" class="btn btn-outline-light btn-lg px-4">
              <i class="bi bi-shop me-2"></i>Browse Components
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-6 fade-in-up-d2">
        <!-- Animated stat cards -->
        <div class="row g-3">
          <?php
          $stats = [
            ['icon'=>'bi-cpu-fill',      'val'=>'500+',  'label'=>'Components', 'color'=>'#4f8ef7'],
            ['icon'=>'bi-shop',          'val'=>'3',     'label'=>'BD Retailers','color'=>'#3fb950'],
            ['icon'=>'bi-shield-check',  'val'=>'8',     'label'=>'Compat Rules','color'=>'#d29922'],
            ['icon'=>'bi-robot',         'val'=>'AI',    'label'=>'Chatbot',    'color'=>'#9b59b6'],
          ];
          foreach ($stats as $s): ?>
          <div class="col-6">
            <div class="glass-card card p-3 text-center">
              <div style="font-size:2rem;color:<?= $s['color'] ?>" class="mb-1">
                <i class="<?= $s['icon'] ?>"></i>
              </div>
              <div class="fw-800" style="font-family:var(--font-head);font-size:1.6rem;color:<?= $s['color'] ?>">
                <?= $s['val'] ?>
              </div>
              <div class="text-muted small"><?= $s['label'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Feature cards -->
<section class="py-6 container-xl" style="padding-top:5rem;padding-bottom:5rem">
  <div class="text-center mb-5">
    <h2 class="section-title">Everything You Need to Build Right</h2>
    <p class="section-sub">From budget planning to AI compatibility — we've got you covered.</p>
  </div>
  <div class="row g-4">
    <?php
    $features = [
      ['icon'=>'bi-magic',         'title'=>'Build Wizard',         'desc'=>'Select your use-case and budget. Get 3 AI-scored, compatible builds instantly.', 'href'=>'purpose.php'],
      ['icon'=>'bi-shield-fill-check','title'=>'Compatibility Engine','desc'=>'8-rule hardware validation: sockets, RAM gen, GPU clearance, PSU headroom and more.','href'=>'custom_builder.php'],
      ['icon'=>'bi-graph-up-arrow','title'=>'Price Tracking',       'desc'=>'Historical price charts from BD retailers. Know the best time to buy.', 'href'=>'price_history.php'],
      ['icon'=>'bi-layout-split',  'title'=>'Side-by-Side Compare',  'desc'=>'Compare up to 4 components spec-by-spec. Winners highlighted automatically.','href'=>'compare.php'],
      ['icon'=>'bi-arrow-up-circle','title'=>'Upgrade Advisor',     'desc'=>'Detect bottlenecks in your current build. Get targeted upgrade suggestions.', 'href'=>'upgrade.php'],
      ['icon'=>'bi-robot',         'title'=>'AI Chatbot',           'desc'=>'Ask anything about PC building in Bangladesh. Powered by Claude AI.', 'href'=>'chatbot.php'],
    ];
    foreach ($features as $i => $f): ?>
    <div class="col-md-6 col-lg-4 fade-in-up" style="animation-delay:<?= $i * 0.08 ?>s;opacity:0;animation-fill-mode:forwards">
      <a href="<?= BASE_URL ?>/<?= $f['href'] ?>" class="text-decoration-none">
        <div class="feature-card h-100">
          <div class="feature-icon"><i class="<?= $f['icon'] ?>"></i></div>
          <h5 class="fw-700 mb-2"><?= $f['title'] ?></h5>
          <p class="text-muted small mb-0"><?= $f['desc'] ?></p>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- CTA -->
<section class="py-5" style="background:linear-gradient(135deg,rgba(79,142,247,.08),rgba(124,58,237,.08))">
  <div class="container-xl text-center">
    <h2 class="section-title mb-3">Ready to Build Your Perfect PC?</h2>
    <p class="section-sub mx-auto" style="max-width:500px">
      Join thousands of Bangladeshi PC builders. No guesswork — just smart, data-driven builds.
    </p>
    <?php if (!is_logged_in()): ?>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="<?= BASE_URL ?>/register.php" class="btn btn-accent btn-lg px-5">
        <i class="bi bi-person-plus me-2"></i>Create Free Account
      </a>
      <a href="<?= BASE_URL ?>/purpose.php" class="btn btn-outline-light btn-lg px-5">
        Try Without Account
      </a>
    </div>
    <?php else: ?>
    <a href="<?= BASE_URL ?>/purpose.php" class="btn btn-accent btn-lg px-5">
      <i class="bi bi-magic me-2"></i>Start Build Wizard
    </a>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/templates/footer.php'; ?>
