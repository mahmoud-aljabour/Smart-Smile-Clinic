<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$USERID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($USERID <= 0) {
  redirect("index.php");
}

$user = new User();
$singleuser = $user->single_user($USERID);

if (!$singleuser) {
  message("User not found.", "error");
  redirect("index.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Edit User</h1>
    <p class="text-muted small mb-0"><?php echo htmlspecialchars($singleuser->FullName); ?></p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=edit" method="POST" autocomplete="off" id="userEditForm" class="form-add-page" novalidate>

  <input id="USERID" name="USERID" type="hidden" value="<?php echo (int)$singleuser->UserID; ?>">

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-person-gear"></i> Account Details
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="U_NAME">Full Name <span class="required">*</span></label>
          <input class="form-control" id="U_NAME" name="U_NAME" placeholder="Account name" type="text" value="<?php echo htmlspecialchars($singleuser->FullName); ?>" required>
          <div class="invalid-feedback">Full name is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="U_USERNAME">Username <span class="required">*</span></label>
          <input class="form-control" id="U_USERNAME" name="U_USERNAME" placeholder="Login username" type="text" value="<?php echo htmlspecialchars($singleuser->Username); ?>" required>
          <div class="invalid-feedback">Username is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="U_PASS">Password <span class="required">*</span></label>
          <input class="form-control" id="U_PASS" name="U_PASS" placeholder="Enter new password" type="password" minlength="3" required>
          <div class="form-hint">
            <i class="bi bi-info-circle"></i>
            <span>Enter a new password to update this account.</span>
          </div>
          <div class="invalid-feedback">Password is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="U_ROLE">Role <span class="required">*</span></label>
          <select class="form-select" name="U_ROLE" id="U_ROLE" required>
            <option value="Administrator" <?php echo ($singleuser->Role == 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
            <option value="Staff" <?php echo ($singleuser->Role == 'Staff') ? 'selected' : ''; ?>>Staff</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit">
          <i class="bi bi-check-lg"></i> Save Changes
        </button>
        <a href="index.php" class="btn btn-outline-secondary">
          <i class="bi bi-x-lg"></i> Cancel
        </a>
      </div>
    </div>
  </div>

</form>

<script>
(function () {
  var form = document.getElementById('userEditForm');
  form.addEventListener('submit', function (e) {
    var valid = form.checkValidity();
    if (!valid) {
      e.preventDefault();
      e.stopPropagation();
    }
    form.classList.add('was-validated');
  });
})();
</script>
