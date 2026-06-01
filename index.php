<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title       = 'Smart PC Builder BD';
$page_description = 'Build your perfect PC with smart recommendations, live BDT prices from Star Tech, Ryans & Techland. Compatibility checked automatically.';
include __DIR__ . '/templates/header.php';
?>

<style>
:root {
  --beast-bg: #0b0f19;
}

body {
  background-color: var(--bg-base);
}

.hero-section {
  position: relative;
  padding: 8rem 0 6rem !important;
  background: transparent !important;
  border-bottom: none !important;
  overflow: hidden;
  z-index: 1;
}

.hero-glow-overlay {
  position: absolute;
  top: 30%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 900px;
  height: 900px;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, rgba(59, 130, 246, 0.03) 60%, rgba(0, 0, 0, 0) 100%);
  z-index: -1;
  pointer-events: none;
}

.hero-tag {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 1rem;
  border-radius: 30px;
  font-size: 0.8rem;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: var(--text-secondary);
  margin-bottom: 2rem;
}
[data-bs-theme="light"] .hero-tag {
  background: rgba(0, 0, 0, 0.03);
  border-color: rgba(0, 0, 0, 0.05);
  color: var(--text-primary);
}

.hero-title {
  font-family: var(--font-head);
  font-size: clamp(2.5rem, 5.5vw, 3.8rem);
  font-weight: 800;
  line-height: 1.1;
  letter-spacing: -1.5px;
  background: none !important;
  -webkit-text-fill-color: var(--text-primary) !important;
  color: var(--text-primary) !important;
}
.hero-title span {
  background: linear-gradient(135deg, var(--accent) 30%, #3b82f6 100%) !important;
  -webkit-background-clip: text !important;
  -webkit-text-fill-color: transparent !important;
  background-clip: text !important;
  display: inline-block;
}

.hero-desc {
  font-size: 1.05rem;
  line-height: 1.6;
  color: var(--text-secondary);
  max-width: 480px;
  margin-bottom: 2.5rem;
}

.btn-medora-solid {
  background: #a3e635 !important;
  color: #0b0f19 !important;
  border: none !important;
  font-weight: 700 !important;
  border-radius: 30px !important;
  padding: 0.8rem 2.2rem !important;
  transition: all var(--transition) !important;
  box-shadow: 0 4px 20px rgba(163, 230, 53, 0.3) !important;
  display: inline-block;
  text-decoration: none;
}
.btn-medora-solid:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 25px rgba(163, 230, 53, 0.45) !important;
}

.btn-medora-outline {
  background: rgba(255, 255, 255, 0.05) !important;
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  color: var(--text-primary) !important;
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
  font-weight: 600 !important;
  border-radius: 30px !important;
  padding: 0.8rem 2.2rem !important;
  transition: all var(--transition) !important;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
}
[data-bs-theme="light"] .btn-medora-outline {
  background: rgba(0, 0, 0, 0.03) !important;
  border-color: rgba(0, 0, 0, 0.08) !important;
}
.btn-medora-outline:hover {
  background: rgba(255, 255, 255, 0.1) !important;
  border-color: rgba(255, 255, 255, 0.2) !important;
  transform: translateY(-2px);
}
[data-bs-theme="light"] .btn-medora-outline:hover {
  background: rgba(0, 0, 0, 0.06) !important;
  border-color: rgba(0, 0, 0, 0.15) !important;
}

.hero-img-light {
  display: inline-block;
}
.hero-img-dark {
  display: none;
}
.hero-img-blend {
  max-height: 420px;
  object-fit: contain;
  mask-image: radial-gradient(circle, rgba(0,0,0,1) 60%, rgba(0,0,0,0) 95%);
  -webkit-mask-image: radial-gradient(circle, rgba(0,0,0,1) 60%, rgba(0,0,0,0) 95%);
}
[data-bs-theme="light"] .hero-img-light {
  display: inline-block !important;
  mix-blend-mode: multiply;
  filter: contrast(1.05) brightness(1.02);
}
[data-bs-theme="light"] .hero-img-dark {
  display: none !important;
}
[data-bs-theme="dark"] .hero-img-light {
  display: none !important;
}
[data-bs-theme="dark"] .hero-img-dark {
  display: inline-block !important;
  mix-blend-mode: initial;
}

.floating-widget-card {
  background: rgba(255, 255, 255, 0.03) !important;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
  border-radius: var(--radius-md) !important;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25) !important;
  transition: transform var(--transition) !important;
  color: var(--text-primary);
}
[data-bs-theme="light"] .floating-widget-card {
  background: rgba(255, 255, 255, 0.75) !important;
  border: 1px solid rgba(0, 0, 0, 0.05) !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
}
.floating-widget-card:hover {
  transform: translateY(-3px);
}

.bg-success-soft {
  background: rgba(16, 185, 129, 0.15) !important;
}

.bg-light-custom {
  background: rgba(255, 255, 255, 0.04) !important;
}
[data-bs-theme="light"] .bg-light-custom {
  background: rgba(0, 0, 0, 0.03) !important;
}

.border-light-custom {
  border-color: rgba(255, 255, 255, 0.06) !important;
}
[data-bs-theme="light"] .border-light-custom {
  border-color: rgba(0, 0, 0, 0.05) !important;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.9); }
}
.animate-pulse {
  animation: pulse 2s infinite;
}

.brand-bar-glass {
  background: rgba(255, 255, 255, 0.03) !important;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.06) !important;
  border-radius: var(--radius-lg) !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}
[data-bs-theme="light"] .brand-bar-glass {
  background: rgba(255, 255, 255, 0.6) !important;
  border: 1px solid rgba(0, 0, 0, 0.04) !important;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03) !important;
}

.marquee-track {
  overflow: hidden;
  width: 100%;
  position: relative;
  display: flex;
}
.marquee-content {
  display: flex;
  white-space: nowrap;
  animation: marquee-scroll 25s linear infinite;
  min-width: max-content;
}
.marquee-content span {
  display: inline-block;
  padding: 0 2rem;
  letter-spacing: 2px;
  font-size: 1.1rem;
  opacity: 0.55;
  transition: opacity 0.2s ease-in-out;
}
.marquee-content span:hover {
  opacity: 1;
}

@keyframes marquee-scroll {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-33.3333%);
  }
}

.faq-accordion .accordion-item {
  background: var(--bg-card);
  border: 1px solid var(--border) !important;
  border-radius: 8px !important;
  margin-bottom: 1rem;
  overflow: hidden;
}
.faq-accordion .accordion-button {
  background: var(--bg-card);
  color: var(--text-primary);
  font-weight: 700;
  font-size: 1.05rem;
  border: none;
  box-shadow: none !important;
  padding: 1.25rem 1.5rem;
  transition: all 0.2s ease;
}
.faq-accordion .accordion-button:not(.collapsed) {
  color: var(--accent);
  background: var(--bg-card);
}
.faq-accordion .accordion-body {
  background: var(--bg-card);
  color: var(--text-secondary);
  padding: 0 1.5rem 1.5rem 1.5rem;
  font-size: 0.98rem;
  line-height: 1.6;
}

.component-card-custom {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 8px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  text-align: center;
  padding: 2rem 1.2rem;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.component-card-custom:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
  border-color: var(--accent);
}
.component-card-custom img {
  width: auto;
  height: 96px;
  object-fit: contain;
  transition: transform 0.3s ease;
  margin-bottom: 1.2rem;
}
.component-card-custom:hover img {
  transform: scale(1.05);
}
.component-card-custom .card-title {
  font-size: 1.05rem;
  font-weight: 700;
  margin: 0;
  color: var(--text-primary);
}

.section-title-custom {
  font-size: 2.2rem;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.search-box-container {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 40px;
  padding: 6px;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.search-box-container:focus-within {
  border-color: var(--accent);
  box-shadow: 0 0 20px rgba(16, 185, 129, 0.25);
}
[data-bs-theme="light"] .search-box-container {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.05);
}
.search-input-group {
  border: none !important;
  background: transparent !important;
}
.search-icon-btn {
  background: transparent !important;
  border: none !important;
  padding-left: 1rem !important;
  font-size: 1.1rem;
}
.search-input {
  background: transparent !important;
  border: none !important;
  color: var(--text-primary) !important;
  font-size: 0.95rem;
  padding: 0.75rem 1rem !important;
  outline: none !important;
  box-shadow: none !important;
}
.search-input::placeholder {
  color: var(--text-secondary);
  opacity: 0.7;
}
.search-submit-btn {
  border-radius: 30px !important;
  font-size: 0.9rem !important;
}
</style>

<section class="hero-section">
  <div class="hero-glow-overlay"></div>
  <div class="container-xl">
    <div class="row align-items-center g-5">
      
      <div class="col-lg-5 col-md-12">
        <div class="hero-tag">
          <span class="d-inline-block animate-pulse" style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%;"></span>
                    Smart PC Build Assistant
        </div>
        
        <h1 class="hero-title mb-4">
          Smart<br>PC Building Starts<br>With <span>PCBuilder.</span>
        </h1>
        
        <p class="hero-desc">
          PCBuilder BD is a smart custom build platform designed to transform complex hardware data into clear, compatible, and actionable setups.
        </p>
        
        <div class="mb-4 mt-2" style="max-width: 500px;">
          <form action="<?= BASE_URL ?>/store.php" method="GET" class="search-box-container">
            <div class="input-group search-input-group">
              <span class="input-group-text search-icon-btn"><i class="bi bi-search text-accent"></i></span>
              <input type="text" name="search" class="form-control search-input" placeholder="Search 165+ components (e.g., RTX 4070, Ryzen 5)..." required>
              <button class="btn btn-accent px-4 fw-bold search-submit-btn" type="submit">Search</button>
            </div>
          </form>
        </div>

        <?php if (is_logged_in()): ?>
        <div class="d-flex flex-wrap gap-3 mb-2">
          <a href="<?= BASE_URL ?>/purpose.php" class="btn-medora-solid">Build Custom PC</a>
          <a href="<?= BASE_URL ?>/chatbot.php" class="btn-medora-outline">
            <i class="bi bi-chat-dots-fill me-1"></i>Chat with Assistant
          </a>
        </div>
        <?php else: ?>
        <div class="d-flex align-items-center gap-2 mt-3 p-3 rounded-4" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.15); max-width: 500px;">
          <i class="bi bi-shield-lock-fill text-accent fs-5"></i>
          <div class="small">
            <span class="text-white-50">Please</span> 
            <a href="<?= BASE_URL ?>/login.php" class="text-accent fw-bold text-decoration-none">Login</a> 
            <span class="text-white-50">or</span> 
            <a href="<?= BASE_URL ?>/register.php" class="text-accent fw-bold text-decoration-none">Register</a> 
            <span class="text-white-50">to unlock the Smart Build Wizard, Chatbot, and Forum.</span>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-4 col-md-6 text-center">
        <div class="hero-img-container">
          <img src="<?= BASE_URL ?>/assets/img/exploded_pc.png" alt="Exploded PC Render" class="img-fluid hero-img-light hero-img-blend">
          <img src="<?= BASE_URL ?>/assets/img/exploded_pc_dark.png" alt="Exploded PC Render Dark" class="img-fluid hero-img-dark hero-img-blend">
        </div>
      </div>

      <div class="col-lg-3 col-md-6 d-flex flex-column gap-4">
        
        <div class="card floating-widget-card p-3 border-0">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0 small text-muted-custom">Price Tracking Report</h6>
            <span class="badge bg-success-soft text-success small">Live</span>
          </div>
          <div style="height: 80px; width: 100%;">
            <svg viewBox="0 0 100 30" class="w-100 h-100" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="var(--accent)" stop-opacity="0.35"></stop>
                  <stop offset="100%" stop-color="var(--accent)" stop-opacity="0"></stop>
                </linearGradient>
              </defs>
              <path d="M 0,25 Q 15,10 30,18 T 60,8 T 90,15 L 100,5 L 100,30 L 0,30 Z" fill="url(#sparkGradient)"></path>
              <path d="M 0,25 Q 15,10 30,18 T 60,8 T 90,15 L 100,5" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round"></path>
              <circle cx="100" cy="5" r="3" fill="var(--accent)"></circle>
            </svg>
          </div>
          <div class="d-flex justify-content-between mt-2 pt-2 border-top border-light-custom small text-muted">
            <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span>
          </div>
        </div>

        <div class="card floating-widget-card p-3 border-0">
          <h6 class="fw-bold mb-3 small text-muted-custom">Build Compatibility</h6>
          
          <div class="d-flex align-items-center gap-3">
            <div class="position-relative" style="width: 76px; height: 76px; flex-shrink: 0;">
              <svg viewBox="0 0 36 36" class="w-100 h-100" style="transform: rotate(-90deg);">
                <path class="gauge-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="3"></path>
                <path class="gauge-fill" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--accent)" stroke-width="3" stroke-dasharray="98, 100"></path>
              </svg>
              <div class="position-absolute top-50 start-50 translate-middle text-center">
                <div class="fw-800 small text-accent" style="font-size: 0.85rem; line-height: 1;">98%</div>
                <small class="text-muted" style="font-size: 0.55rem; display: block; line-height: 1;">Match</small>
              </div>
            </div>
            
            <div class="flex-grow-1 d-flex flex-column gap-2" style="min-width: 0;">
              <div class="widget-stat-pill d-flex justify-content-between align-items-center py-1 px-2">
                <span class="small text-muted" style="font-size: 0.75rem;">Est. TDP</span>
                <span class="small fw-700 text-accent" style="font-size: 0.75rem;">320W</span>
              </div>
              <div class="widget-stat-pill d-flex justify-content-between align-items-center py-1 px-2">
                <span class="small text-muted" style="font-size: 0.75rem;">PSU Margin</span>
                <span class="small fw-700 text-success" style="font-size: 0.75rem;">150W</span>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
  
  <div class="container-xl mt-5">
    <div class="brand-bar-glass py-3">
      <div class="marquee-track">
        <div class="marquee-content">
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Intel</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">NVIDIA</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">AMD</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">ASUS</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">MSI</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Gigabyte</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Corsair</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Intel</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">NVIDIA</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">AMD</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">ASUS</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">MSI</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Gigabyte</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Corsair</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Intel</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">NVIDIA</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">AMD</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">ASUS</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">MSI</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Gigabyte</span>
          <span class="h5 fw-800 text-uppercase m-0 text-muted-custom">Corsair</span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="beast-section">
  <div class="container-xl">
    <div class="row align-items-center g-5">
      
      <div class="col-lg-4 col-md-6">
        <h2 class="mb-4">Brand new beast</h2>
        
        <ul class="list-unstyled mb-4">
          <li class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle-fill text-accent-green"></i>
            <span>Premium Custom PC</span>
          </li>
          <li class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle-fill text-accent-green"></i>
            <span>Ready for High Performance</span>
          </li>
          <li class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle-fill text-accent-green"></i>
            <span>Premium Full Steel Chassis</span>
          </li>
          <li class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-check-circle-fill text-accent-green"></i>
            <span>Beginner Friendly</span>
          </li>
        </ul>

        <div class="mb-4">
          <span class="text-muted small d-block">Starting at</span>
          <span class="beast-price">$2550</span>
        </div>

        <?php if (is_logged_in()): ?>
          <a href="<?= BASE_URL ?>/purpose.php" class="btn btn-configure text-decoration-none">Configure</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/login.php" class="btn btn-configure text-decoration-none">Log in to Configure</a>
        <?php endif; ?>
      </div>

      <div class="col-lg-5 col-md-6 text-center">
        <img src="<?= BASE_URL ?>/assets/img/brand_new_beast.png" alt="Brand New Beast PC" class="img-fluid" style="max-height: 480px; object-fit: contain;">
      </div>

      <div class="col-lg-3 col-md-12">
        <div class="row g-4">
          <div class="col-6 col-lg-12">
            <div class="spec-label">Processor</div>
            <div class="spec-value">Intel 13th Gen or Ryzen 7000 Processors</div>
          </div>
          <div class="col-6 col-lg-12">
            <div class="spec-label">Graphics Card</div>
            <div class="spec-value">Up to the NVIDIA GeForce RTX 3080</div>
          </div>
          <div class="col-6 col-lg-12">
            <div class="spec-label">Memory</div>
            <div class="spec-value">32 GB Ultra-fast 3600Mhz Memory</div>
          </div>
          <div class="col-6 col-lg-12">
            <div class="spec-label">Cooling</div>
            <div class="spec-value">Premium Custom Water cooled CPU</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<section class="components-section">
  <div class="container-xl">
    <h2 class="component-grid-title text-center text-lg-start">Components</h2>
    
    <div class="row g-4">
      
      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= BASE_URL ?>/store.php?category=CPU" class="text-decoration-none">
          <div class="component-card-custom">
            <img src="<?= BASE_URL ?>/assets/img/cat_cpu.png" alt="Processors" class="img-fluid">
            <h5 class="card-title">Processors</h5>
          </div>
        </a>
      </div>

      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= BASE_URL ?>/store.php?category=GPU" class="text-decoration-none">
          <div class="component-card-custom">
            <img src="<?= BASE_URL ?>/assets/img/cat_gpu.png" alt="Graphics Cards" class="img-fluid">
            <h5 class="card-title">Graphics Cards</h5>
          </div>
        </a>
      </div>

      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= BASE_URL ?>/store.php?category=Motherboard" class="text-decoration-none">
          <div class="component-card-custom">
            <img src="<?= BASE_URL ?>/assets/img/cat_motherboard.png" alt="Motherboards" class="img-fluid">
            <h5 class="card-title">Motherboards</h5>
          </div>
        </a>
      </div>

      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= BASE_URL ?>/store.php?category=RAM" class="text-decoration-none">
          <div class="component-card-custom">
            <img src="<?= BASE_URL ?>/assets/img/cat_ram.png" alt="Memory (RAM)" class="img-fluid">
            <h5 class="card-title">Memory (RAM)</h5>
          </div>
        </a>
      </div>

      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= BASE_URL ?>/store.php?category=Storage" class="text-decoration-none">
          <div class="component-card-custom">
            <img src="<?= BASE_URL ?>/assets/img/cat_storage.png" alt="Storage" class="img-fluid">
            <h5 class="card-title">Storage</h5>
          </div>
        </a>
      </div>

      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= BASE_URL ?>/store.php?category=PSU" class="text-decoration-none">
          <div class="component-card-custom">
            <img src="<?= BASE_URL ?>/assets/img/cat_psu.png" alt="Power Supply" class="img-fluid">
            <h5 class="card-title">Power Supply</h5>
          </div>
        </a>
      </div>

      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= BASE_URL ?>/store.php?category=Cooling" class="text-decoration-none">
          <div class="component-card-custom">
            <img src="<?= BASE_URL ?>/assets/img/cat_cooling.png" alt="System Cooling" class="img-fluid">
            <h5 class="card-title">System Cooling</h5>
          </div>
        </a>
      </div>

      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= BASE_URL ?>/store.php?category=Case" class="text-decoration-none">
          <div class="component-card-custom">
            <img src="<?= BASE_URL ?>/assets/img/cat_peripherals.png" alt="Gaming Peripherals" class="img-fluid">
            <h5 class="card-title">Gaming Peripherals</h5>
          </div>
        </a>
      </div>

    </div>
  </div>
</section>

<section class="faq-section">
  <div class="container-xl text-center mb-5">
    <h2>Frequently Asked Questions</h2>
    <p class="section-sub">Most asked questions all at one place.</p>
  </div>

  <div class="container-xl">
    <div class="accordion faq-accordion" id="faqAccordion">
      
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHead1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
            Can I customize my own PC using components from your website?
          </button>
        </h2>
        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHead1" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes, absolutely! At Your PC, we offer a wide range of components that you can use to customize your own PC build. Our website makes it easy to browse our selection of components, including CPUs, graphics cards, motherboards, RAM, storage drives, power supplies, and more.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHead2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
            Can you help me choose the right components for my custom PC?
          </button>
        </h2>
        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHead2" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes! Our smart recommendation system can guide you through selecting the ideal, compatible parts based on your specific budget and intended usage.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHead3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
            What components do I need to build my own PC?
          </button>
        </h2>
        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHead3" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            To build a complete PC, you typically need a Processor (CPU), Motherboard, Memory (RAM), Storage (SSD or HDD), Graphics Card (GPU) (unless the CPU has integrated graphics), Power Supply Unit (PSU), and a Case to house it all.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHead4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
            Do pre-build PCs come have a warranty?
          </button>
        </h2>
        <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHead4" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes, all components and pre-built systems come with official manufacturer warranties, and our customer support team is available to assist you with any warranty claims.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHead5">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
            What is the shipping cost and delivery time for my order?
          </button>
        </h2>
        <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHead5" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            We offer free standard delivery across major cities in Bangladesh, with typical shipping times ranging from 2 to 5 business days depending on your location.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHead6">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
            Do you offer any promotions or discounts?
          </button>
        </h2>
        <div id="faqCollapse6" class="accordion-collapse collapse" aria-labelledby="faqHead6" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes! We run regular promotional offers, discount packages for full builds, and cash-back deals during festive seasons. Check our store or sign up to stay updated.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<section class="about-us-section py-5" id="about-us" style="background: var(--bg-body); border-top: 1px solid var(--border);">
  <div class="container-xl text-center mb-5">
    <div class="hero-tag d-inline-flex align-items-center gap-2 mb-3">
      <span class="d-inline-block animate-pulse" style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%;"></span>
      ✦ Who We Are
    </div>
    <h2>Meet Our Team</h2>
    <p class="section-sub text-muted mx-auto" style="max-width: 600px;">The innovators driving the future of custom PC building in Bangladesh.</p>
  </div>

  <div class="container-xl">
    <div class="row g-4 justify-content-center">
      <div class="col-md-4">
        <div class="card floating-widget-card border-0 p-4 text-center h-100" style="background: var(--bg-card); border: 1px solid var(--border) !important; backdrop-filter: blur(16px);">
          <div class="d-flex justify-content-center mb-4">
            <div class="avatar-circle-lg" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-family: var(--font-head); font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, var(--accent) 0%, #3b82f6 100%);">
              SS
            </div>
          </div>
          <h3 class="fw-bold mb-1 h5">Shadman Shakib</h3>
          <span class="badge bg-accent-soft text-accent mb-3 px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">Founder and CEO</span>
          <p class="text-muted small mb-0">Directs the overall vision, business strategy, and operations of PCBuilder BD.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card floating-widget-card border-0 p-4 text-center h-100" style="background: var(--bg-card); border: 1px solid var(--border) !important; backdrop-filter: blur(16px);">
          <div class="d-flex justify-content-center mb-4">
            <div class="avatar-circle-lg" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-family: var(--font-head); font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);">
              SMS
            </div>
          </div>
          <h3 class="fw-bold mb-1 h5">Shah Mohammed Seaman</h3>
          <span class="badge bg-accent-soft text-accent mb-3 px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem; color: #3b82f6 !important; background: rgba(59, 130, 246, 0.1) !important;">CTO</span>
          <p class="text-muted small mb-0">Architects the system codebase, chatbot engine, and compatibility algorithms.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card floating-widget-card border-0 p-4 text-center h-100" style="background: var(--bg-card); border: 1px solid var(--border) !important; backdrop-filter: blur(16px);">
          <div class="d-flex justify-content-center mb-4">
            <div class="avatar-circle-lg" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-family: var(--font-head); font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, var(--accent) 0%, #84cc16 100%);">
              JH
            </div>
          </div>
          <h3 class="fw-bold mb-1 h5">Jim Hossain</h3>
          <span class="badge bg-accent-soft text-accent mb-3 px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem; color: #10b981 !important; background: rgba(16, 185, 129, 0.1) !important;">Co Founder and CFO</span>
          <p class="text-muted small mb-0">Manages company finances, component pricing structures, and analytics.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/templates/footer.php'; ?>
