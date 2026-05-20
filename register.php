<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Already logged in
if (is_logged_in()) redirect('dashboard.php');

$errors = [];
$name = $email = '';

if (is_post()) {
    verify_csrf();
    $name     = trim(input('name'));
    $email    = strtolower(trim(input('email')));
    $password = input('password');
    $confirm  = input('confirm_password');

    if (strlen($name) < 2)              $errors[] = 'Full name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8)          $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm)         $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $existing = db_row('SELECT user_id FROM `user` WHERE email = ?', [$email]);
        if ($existing) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            db_exec('INSERT INTO `user` (user_name, email, user_password) VALUES (?,?,?)', [$name, $email, $hash]);
            flash_message('success', 'Account created! Please log in.');
            redirect('login.php');
        }
    }
}

$page_title = 'Create Account';
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
    <h2 class="h4 fw-700 mb-1 text-center">Create your account</h2>
    <p class="text-muted text-center small mb-4">Start building your dream PC today</p>

    <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0 ps-3">
        <?php foreach ($errors as $e): ?><li><?= sanitise($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <?php csrf_field(); ?>

      <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" id="name" name="name" class="form-control" placeholder="Your full name"
                 value="<?= sanitise($name) ?>" required autocomplete="name">
        </div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com"
                 value="<?= sanitise($email) ?>" required autocomplete="email">
        </div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" id="password" name="password" class="form-control"
                 placeholder="Min. 8 characters" required autocomplete="new-password">
          <button class="btn btn-outline-secondary" type="button" id="toggle-pw" aria-label="Show password">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <div class="mb-4">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                 placeholder="Repeat password" required autocomplete="new-password">
        </div>
      </div>

      <button type="submit" class="btn btn-accent w-100 btn-lg fw-600">
        <i class="bi bi-person-plus me-2"></i>Create Account
      </button>
    </form>

    <p class="text-center text-muted small mt-4 mb-0">
      Already have an account? <a href="<?= BASE_URL ?>/login.php" class="fw-600">Sign in</a>
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
