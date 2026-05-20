<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) redirect('dashboard.php');

$error = '';
$email = '';

if (is_post()) {
    verify_csrf();
    $email    = strtolower(trim(input('email')));
    $password = input('password');

    $user = attempt_login(input('email'), $password);
    if ($user) {
        login_user($user);
        flash_message('success', 'Welcome back, ' . $user['user_name'] . '!');
        redirect('dashboard.php');
    } else {
        $error = 'Invalid email or password.';
    }
}

$page_title = 'Sign In';
include __DIR__ . '/templates/header.php';
?>
<div class="container py-5">
  <div class="auth-card">
    <div class="auth-logo">
      <a href="<?= BASE_URL ?>/index.php" class="navbar-brand fw-800 d-inline-flex align-items-center gap-2">
        <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
        <span>PC<span class="text-accent">Builder</span> BD</span>
      </a>
    </div>
    <h2 class="h4 fw-700 mb-1 text-center">Welcome back</h2>
    <p class="text-muted text-center small mb-4">Sign in to your account</p>

    <?php if ($error): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= sanitise($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <?php csrf_field(); ?>

      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com"
                 value="<?= sanitise($email) ?>" required autocomplete="email">
        </div>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" id="password" name="password" class="form-control"
                 placeholder="Your password" required autocomplete="current-password">
          <button class="btn btn-outline-secondary" type="button" id="toggle-pw" aria-label="Show password">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-accent w-100 btn-lg fw-600">
        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
      </button>
    </form>

    <p class="text-center text-muted small mt-4 mb-0">
      Don't have an account? <a href="<?= BASE_URL ?>/register.php" class="fw-600">Create one</a>
    </p>
  </div>
</div>

<?php $inline_script = <<<JS
document.getElementById('toggle-pw').addEventListener('click', function() {
  const pw = document.getElementById('password');
  const icon = this.querySelector('i');
  if (pw.type === 'password') { pw.type = 'text'; icon.className = 'bi bi-eye-slash'; }
  else { pw.type = 'password'; icon.className = 'bi bi-eye'; }
});
JS;
include __DIR__ . '/templates/footer.php'; ?>
