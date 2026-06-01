<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
    exit;
}

$page_title = 'About Us';
include __DIR__ . '/templates/header.php';
?>

<div class="container-xl py-5">
  <div class="text-end mb-5">
    <div class="hero-tag d-inline-flex align-items-center gap-2 mb-3 ms-auto">
      <span class="d-inline-block animate-pulse" style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%;"></span>
      Who We Are
    </div>
    <h1 class="hero-title mb-4">Empowering <span>Builders.</span></h1>
    <p class="section-sub text-muted ms-auto" style="max-width: 700px;">
      PCBuilder BD is Bangladesh's leading custom hardware configuration platform. Our mission is to take the guesswork out of system compatibility and budget allocation, enabling everyone from casual gamers to enterprise developers to construct their perfect setup.
    </p>
  </div>

  <div class="row g-4 justify-content-center mt-4">
    <div class="col-lg-4 col-md-6">
      <div class="card floating-widget-card border-0 h-100 p-4 text-center">
        <div class="d-flex justify-content-center mb-4">
          <div class="avatar-circle-lg" style="background: linear-gradient(135deg, var(--accent) 0%, #3b82f6 100%);">
            SS
          </div>
        </div>
        <h3 class="fw-bold mb-1">Shadman Shakib</h3>
        <span class="badge bg-accent-soft text-accent mb-3 px-3 py-2 rounded-pill fw-bold">Founder & CEO</span>
        <p class="text-muted small mb-0">
          Shadman leads the overall vision, business strategy, and operations of PCBuilder BD, building strategic partnerships within the Bangladeshi hardware market.
        </p>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="card floating-widget-card border-0 h-100 p-4 text-center">
        <div class="d-flex justify-content-center mb-4">
          <div class="avatar-circle-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);">
            SMS
          </div>
        </div>
        <h3 class="fw-bold mb-1">Shah Mohammed Seaman</h3>
        <span class="badge bg-accent-soft text-accent mb-3 px-3 py-2 rounded-pill fw-bold" style="color: #3b82f6 !important; background: rgba(59, 130, 246, 0.1) !important;">CTO</span>
        <p class="text-muted small mb-0">
          Shah Mohammed Seaman architectures the system, chatbot logic, and compatibility engines, driving technical innovation across the builder platform.
        </p>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="card floating-widget-card border-0 h-100 p-4 text-center">
        <div class="d-flex justify-content-center mb-4">
          <div class="avatar-circle-lg" style="background: linear-gradient(135deg, var(--accent) 0%, #84cc16 100%);">
            JH
          </div>
        </div>
        <h3 class="fw-bold mb-1">Jim Hossain</h3>
        <span class="badge bg-accent-soft text-accent mb-3 px-3 py-2 rounded-pill fw-bold" style="color: #10b981 !important; background: rgba(16, 185, 129, 0.1) !important;">Co-Founder & CFO</span>
        <p class="text-muted small mb-0">
          Jim oversees the financial health, pricing analytics, component budgeting structures, and administrative framework at PCBuilder BD.
        </p>
      </div>
    </div>
  </div>
</div>

<style>
.avatar-circle-lg {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-family: var(--font-head);
  font-size: 2rem;
  font-weight: 800;
  box-shadow: var(--shadow-md);
  border: 3px solid var(--border);
}
</style>

<?php include __DIR__ . '/templates/footer.php'; ?>
