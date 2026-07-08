<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Add Autonumber</h1>
    <p class="text-muted small mb-0">Create a new auto-numbering sequence</p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=add" method="POST" autocomplete="off" class="form-add-page" novalidate>
  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-123"></i> Sequence Details
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="AUTOKEY">Key <span class="required">*</span></label>
          <input class="form-control" id="AUTOKEY" name="AUTOKEY" placeholder="e.g. SKU, USERID" type="text" required>
          <div class="invalid-feedback">Key is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="AUTOSTART">Prefix / Start <span class="required">*</span></label>
          <input class="form-control" id="AUTOSTART" name="AUTOSTART" placeholder="e.g. INV_2025" type="text" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="AUTOEND">Sequence # <span class="required">*</span></label>
          <input class="form-control" id="AUTOEND" name="AUTOEND" placeholder="e.g. 1" type="text" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="AUTOINC">Increment <span class="required">*</span></label>
          <input class="form-control" id="AUTOINC" name="AUTOINC" placeholder="e.g. 1" type="text" value="1" required>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit"><i class="bi bi-check-lg"></i> Save</button>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Cancel</a>
      </div>
    </div>
  </div>
</form>
