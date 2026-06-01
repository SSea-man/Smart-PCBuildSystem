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

    $user = attempt_login($email, $password);
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

<style>
.auth-page-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 120px);
  padding: 2.5rem 0;
}

.login-card-split {
  display: grid;
  grid-template-columns: 1.15fr 1fr;
  width: 100%;
  max-width: 960px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: var(--shadow-lg);
}

@media (max-width: 768px) {
  .login-card-split {
    grid-template-columns: 1fr;
  }
  .login-banner-side {
    display: none !important;
  }
}

.login-banner-side {
  background-image: linear-gradient(rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.75)), url('<?= BASE_URL ?>/assets/img/tech_login_banner.png');
  background-size: cover;
  background-position: center;
  padding: 3.5rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 560px;
  color: #ffffff;
  position: relative;
}

.banner-top-badge {
  font-family: var(--font-head);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: #3fb950;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.banner-top-badge::after {
  content: "";
  display: inline-block;
  width: 50px;
  height: 1px;
  background: rgba(255, 255, 255, 0.25);
}

.banner-text-bottom h1 {
  font-family: var(--font-head);
  font-size: 2.2rem;
  font-weight: 800;
  line-height: 1.25;
  margin-bottom: 0.75rem;
  color: #ffffff;
}

.banner-text-bottom p {
  font-size: 0.88rem;
  opacity: 0.85;
  line-height: 1.5;
  margin: 0;
}

.login-form-side {
  padding: 3.5rem 3rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.login-brand-header {
  margin-bottom: 2rem;
}

.login-form-side h2 {
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 1.8rem;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.login-form-side .subtitle {
  font-size: 0.85rem;
  color: var(--text-secondary);
  margin-bottom: 1.5rem;
}

.form-label-custom {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.4rem;
  display: block;
}

.input-group-custom {
  position: relative;
  display: flex;
  align-items: center;
  margin-bottom: 1.25rem;
}

.input-group-custom input {
  width: 100%;
  padding: 0.65rem 1rem;
  border-radius: 12px;
  border: 1px solid var(--border);
  background: var(--bg-input);
  color: var(--text-primary);
  font-size: 0.9rem;
  outline: none;
  transition: all 0.2s ease;
}

.input-group-custom input:focus {
  border-color: #3fb950;
  box-shadow: 0 0 0 3px rgba(63, 185, 80, 0.15);
}

.input-group-custom .btn-toggle-pw {
  position: absolute;
  right: 1rem;
  background: transparent;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-actions-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-size: 0.82rem;
}

.btn-sign-in {
  background: #111827;
  color: #ffffff;
  border: none;
  font-weight: 600;
  padding: 0.7rem;
  border-radius: 12px;
  width: 100%;
  font-size: 0.92rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

[data-bs-theme="dark"] .btn-sign-in {
  background: #ffffff;
  color: #111827;
}

.btn-sign-in:hover {
  background: #1f2937;
}

[data-bs-theme="dark"] .btn-sign-in:hover {
  background: #f3f4f6;
}
</style>

<div class="container-xl">
  <div class="auth-page-wrapper">
    <div class="login-card-split">
      <div class="login-banner-side">
        <div class="banner-top-badge">
          BUILD YOUR DREAM RIG
        </div>
        <div class="banner-text-bottom">
          <h1>Get Everything You Want</h1>
          <p>You can get everything you want if you search smart, verify compatibility, and optimize your budget system.</p>
        </div>
      </div>

      <div class="login-form-side">
        <div class="login-brand-header">
          <a href="<?= BASE_URL ?>/index.php" class="navbar-brand fw-800 d-flex align-items-center gap-2">
            <span class="brand-icon" style="background:#3fb950; color:#fff; width:30px; height:30px; border-radius:6px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-cpu-fill"></i></span>
            <span style="font-size:1.15rem; color:var(--text-primary);">PC Builder <span style="color:#3fb950;">BD</span></span>
          </a>
        </div>

        <h2>Welcome Back</h2>
        <p class="subtitle">Enter your email and password to access your account</p>

        <?php if ($error): ?>
          <div class="alert alert-danger p-2 small mb-3" style="border-radius:10px;">
            <i class="bi bi-exclamation-circle me-1"></i><?= sanitise($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <?php csrf_field(); ?>

          <div>
            <label class="form-label-custom">Email</label>
            <div class="input-group-custom">
              <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= sanitise($email) ?>" required autocomplete="email">
            </div>
          </div>

          <div>
            <label class="form-label-custom">Password</label>
            <div class="input-group-custom">
              <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
              <button class="btn-toggle-pw" type="button" id="toggle-pw" aria-label="Show password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-actions-row">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember" style="cursor:pointer;">
              <label class="form-check-label text-secondary" for="remember" style="cursor:pointer; user-select:none;">Remember me</label>
            </div>
            <a href="#" onclick="alert('Password recovery is managed by your account administrator.')" class="text-secondary text-decoration-none">Forgot Password</a>
          </div>

          <button type="submit" class="btn-sign-in">
            Sign In
          </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
          Don't have an account? <a href="<?= BASE_URL ?>/register.php" class="fw-700 text-decoration-none" style="color:var(--text-primary);">Sign Up</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php 
$inline_script = <<<JS
(function(){
  const btn = document.getElementById('toggle-pw');
  if (btn) {
    btn.addEventListener('click', function() {
      const pw = document.getElementById('password');
      const icon = this.querySelector('i');
      if (pw.type === 'password') { 
        pw.type = 'text'; 
        icon.className = 'bi bi-eye-slash'; 
      } else { 
        pw.type = 'password'; 
        icon.className = 'bi bi-eye'; 
      }
    });
  }
})();
JS;
include __DIR__ . '/templates/footer.php'; 
?>