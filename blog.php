<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Blog & Articles';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-5">
  <div class="text-center mb-5">
    <h1 class="section-title"><i class="bi bi-journal-richtext me-2 text-accent"></i>Blog & Articles</h1>
    <p class="section-sub">Stay up-to-date with latest hardware trends, PC building tips, and peripheral guides.</p>
  </div>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card blog-card border-0 h-100 shadow-sm">
        <img src="<?= BASE_URL ?>/assets/img/blog_build_pc.png" alt="Building PC Tips" class="card-img-top blog-card-img" style="height: 200px; object-fit: cover;">
        <div class="card-body d-flex flex-column justify-content-between p-4">
          <div>
            <span class="badge bg-accent-soft text-accent mb-2">Guides</span>
            <h4 class="blog-card-title h5 fw-700 mb-2">Building PC Tips</h4>
            <p class="blog-card-excerpt text-muted-custom small">
              Get to know everything needed before you start buying components for your custom PC. Learn about airflow, static electricity, and correct CPU placement.
            </p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-light-custom">
            <span class="blog-card-date text-muted small"><i class="bi bi-calendar3 me-1"></i>January 2025</span>
            <a href="#" class="text-accent fw-600 small text-decoration-none">Read More <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card blog-card border-0 h-100 shadow-sm">
        <img src="<?= BASE_URL ?>/assets/img/blog_peripherals.png" alt="Peripherals Importance" class="card-img-top blog-card-img" style="height: 200px; object-fit: cover;">
        <div class="card-body d-flex flex-column justify-content-between p-4">
          <div>
            <span class="badge bg-accent-soft text-accent mb-2">Hardware</span>
            <h4 class="blog-card-title h5 fw-700 mb-2">Peripherals Importance</h4>
            <p class="blog-card-excerpt text-muted-custom small">
              Better quality peripherals will get you a better gaming experience. Understand the impact of polling rates, switch types, and high-DPI mouse sensors.
            </p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-light-custom">
            <span class="blog-card-date text-muted small"><i class="bi bi-calendar3 me-1"></i>December 2022</span>
            <a href="#" class="text-accent fw-600 small text-decoration-none">Read More <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card blog-card border-0 h-100 shadow-sm">
        <img src="<?= BASE_URL ?>/assets/img/blog_maintenance.png" alt="Maintain your PC healthy" class="card-img-top blog-card-img" style="height: 200px; object-fit: cover;">
        <div class="card-body d-flex flex-column justify-content-between p-4">
          <div>
            <span class="badge bg-accent-soft text-accent mb-2">Maintenance</span>
            <h4 class="blog-card-title h5 fw-700 mb-2">Maintain your PC healthy</h4>
            <p class="blog-card-excerpt text-muted-custom small">
              Best tips to maintain your PC clean, healthy, and extend its overall operational lifetime. Safe practices for dusting and thermal paste reapplication.
            </p>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-light-custom">
            <span class="blog-card-date text-muted small"><i class="bi bi-calendar3 me-1"></i>November 2022</span>
            <a href="#" class="text-accent fw-600 small text-decoration-none">Read More <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
