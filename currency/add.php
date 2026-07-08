<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Add Currency</h1>
    <p class="text-muted small mb-0">Add a new currency symbol</p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=add" method="POST" autocomplete="off" class="form-add-page" novalidate>
  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-currency-exchange"></i> Currency Details
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="CurrencySymbol">Currency Symbol <span class="required">*</span></label>
          <input class="form-control" id="CurrencySymbol" name="CurrencySymbol" placeholder="e.g. USD, SAR, EGP" type="text" required>
          <div class="invalid-feedback">Currency symbol is required.</div>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit"><i class="bi bi-check-lg"></i> Save</button>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Cancel</a>
      </div>
    </div>
  </div>
</form>
