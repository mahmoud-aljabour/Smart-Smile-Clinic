<?php
require_once("include/initialize.php");

if (isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "index.php");
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login — <?php echo app_name; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?php echo web_root; ?>assets/css/theme.css" rel="stylesheet">
</head>
<body class="login-page">
  <div class="container-fluid g-0">
    <div class="row g-0 min-vh-100">
      <div class="col-lg-6 d-none d-lg-flex login-brand-panel">
        <img src="<?php echo app_logo; ?>" alt="<?php echo htmlspecialchars(app_name); ?>">
        <h2 class="fw-bold text-center mb-3"><?php echo htmlspecialchars(app_name); ?></h2>
        <p class="text-center opacity-75 mb-0" style="max-width: 340px;">
          <?php echo htmlspecialchars(app_tagline); ?>
        </p>
        <div class="mt-4 d-flex gap-3 opacity-75">
          <span><i class="bi bi-shield-check me-1"></i> Secure</span>
          <span><i class="bi bi-heart-pulse me-1"></i> Clinical</span>
          <span><i class="bi bi-clock me-1"></i> Efficient</span>
        </div>
      </div>

      <div class="col-lg-6 login-form-wrap">
        <div class="w-100" style="max-width: 420px;">
          <div class="text-center d-lg-none mb-4">
            <img src="<?php echo app_logo; ?>" alt="Logo" style="max-width: 120px;">
          </div>

          <div class="card login-card">
            <div class="card-body p-4 p-md-5">
              <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width: 56px; height: 56px; background: var(--dc-primary-light); color: var(--dc-primary);">
                  <i class="bi bi-person-lock fs-4"></i>
                </div>
                <h1 class="h4 fw-bold mb-1">Welcome back</h1>
                <p class="text-muted small mb-0">Sign in to your clinic dashboard</p>
              </div>

              <div class="mb-3"><?php check_message(); ?></div>

              <form method="POST" action="process.php">
                <div class="mb-3">
                  <label for="user_email" class="form-label small fw-semibold">Username</label>
                  <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="user_email" name="user_email"
                           placeholder="Enter username" required autofocus>
                  </div>
                </div>
                <div class="mb-4">
                  <label for="user_pass" class="form-label small fw-semibold">Password</label>
                  <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="user_pass" name="user_pass"
                           placeholder="Enter password" required>
                  </div>
                </div>
                <button type="submit" name="btnLogin" class="btn btn-primary w-100 py-2 fw-semibold">
                  <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </button>
              </form>
            </div>
          </div>

          <p class="text-center text-muted small mt-4 mb-0">
            &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(app_name); ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo web_root; ?>assets/js/theme.js"></script>
</body>
</html>
