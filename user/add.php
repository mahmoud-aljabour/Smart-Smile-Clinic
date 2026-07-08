<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
if ($_SESSION['ADMIN_ROLE'] != "Administrator") {
  redirect(web_root . "index.php");
}

$autonum = new Autonumber();
$res = $autonum->set_autonumber('USERID');
$userId = ($res && !empty($res->AUTO)) ? $res->AUTO : '';
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Add User</h1>
    <p class="text-muted small mb-0">Create a new clinic user account</p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=add" method="POST" autocomplete="off" id="userForm" class="form-add-page" novalidate>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-person-gear"></i> Account Details
    </div>
    <div class="card-body">
      <input id="user_id" name="user_id" type="hidden" value="<?php echo htmlspecialchars($userId); ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="U_NAME">Full Name <span class="required">*</span></label>
          <input class="form-control" id="U_NAME" name="U_NAME" placeholder="User full name" type="text" required>
          <div class="invalid-feedback">Full name is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="U_USERNAME">Username <span class="required">*</span></label>
          <input class="form-control" id="U_USERNAME" name="U_USERNAME" placeholder="Login username" type="text" required>
          <div class="invalid-feedback">Username is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="U_PASS">Password <span class="required">*</span></label>
          <input class="form-control" id="U_PASS" name="U_PASS" placeholder="Account password" type="password" minlength="3" required>
          <div class="invalid-feedback">Password is required (min 3 characters).</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="U_ROLE">Role <span class="required">*</span></label>
          <select class="form-select" name="U_ROLE" id="U_ROLE" required>
            <option value="Administrator">Administrator</option>
            <option value="Staff">Staff</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit">
          <i class="bi bi-check-lg"></i> Save User
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
  var form = document.getElementById('userForm');
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
