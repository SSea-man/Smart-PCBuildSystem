<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= sanitise($page_description ?? 'AI-powered PC Builder for the Bangladeshi market. Get optimised builds, compatibility checks, and price comparisons.') ?>">
  <title><?= sanitise($page_title ?? 'PC Builder BD') ?> — PC Builder BD</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top" id="main-nav">
  <div class="container-xl">
    <a class="navbar-brand fw-800 d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
      <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
      <span>PC<span class="text-accent">Builder</span> BD</span>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto gap-1">
        <?php
        $nav_links = [
          ['href' => 'store.php',          'icon' => 'bi-shop',           'label' => 'Store'],
          ['href' => 'purpose.php',        'icon' => 'bi-magic',          'label' => 'Build Wizard'],
          ['href' => 'compare.php',        'icon' => 'bi-layout-split',   'label' => 'Compare'],
          ['href' => 'forum.php',          'icon' => 'bi-chat-square-text', 'label' => 'Forum'],
          ['href' => 'chatbot.php',        'icon' => 'bi-robot',          'label' => 'AI Chat'],
        ];
        $cur = basename($_SERVER['PHP_SELF']);
        foreach ($nav_links as $link):
          $active = ($cur === $link['href']) ? 'active' : '';
        ?>
        <li class="nav-item">
          <a class="nav-link <?= $active ?>" href="<?= BASE_URL ?>/<?= $link['href'] ?>">
            <i class="<?= $link['icon'] ?> me-1"></i><?= $link['label'] ?>
          </a>
        </li>
        <?php endforeach; ?>

        <?php if (is_admin()): ?>
        <li class="nav-item">
          <a class="nav-link <?= str_starts_with($cur, 'admin') ? 'active' : '' ?>"
             href="<?= BASE_URL ?>/admin/index.php">
            <i class="bi bi-shield-fill-check me-1"></i>Admin
          </a>
        </li>
        <?php endif; ?>
      </ul>

      <form action="<?= BASE_URL ?>/store.php" method="GET" class="d-flex align-items-center me-3 d-none d-lg-flex" style="width: 250px;">
        <div class="input-group input-group-sm">
          <input type="text" name="search" class="form-control" placeholder="Search products..." aria-label="Search" value="<?= sanitise($_GET['search'] ?? '') ?>">
          <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </div>
      </form>

      <div class="d-flex align-items-center gap-2">

      <button class="btn btn-sm btn-ghost" id="theme-toggle" aria-label="Toggle theme" title="Toggle theme">
          <i class="bi bi-moon-stars-fill"></i>
        </button>

        <?php if (is_logged_in()):
          $cu = get_auth_user(); ?>
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-accent dropdown-toggle d-flex align-items-center gap-2"
                  type="button" data-bs-toggle="dropdown" aria-expanded="false" id="user-menu-btn">
            <span class="avatar-sm"><?= strtoupper(substr($cu['name'], 0, 1)) ?></span>
            <span class="d-none d-md-inline"><?= sanitise($cu['name']) ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end glass-dropdown">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-grid me-2"></i>Dashboard</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/upgrade.php"><i class="bi bi-arrow-up-circle me-2"></i>Upgrade Advisor</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
          </ul>
        </div>
        <?php else: ?>
        <a class="btn btn-sm btn-outline-light" href="<?= BASE_URL ?>/login.php">
          <i class="bi bi-box-arrow-in-right me-1"></i>Login
        </a>
        <a class="btn btn-sm btn-accent" href="<?= BASE_URL ?>/register.php">
          <i class="bi bi-person-plus me-1"></i>Register
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<div class="container-xl mt-3" id="flash-container">
  <?php render_flash(); ?>
</div>

<main id="main-content">
