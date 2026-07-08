<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Add Supplier</h1>
    <p class="text-muted small mb-0">Register a new supplier</p>
  </div>
  <a href="index.php?view=list" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=add" method="POST" autocomplete="off" class="form-add-page" novalidate>
  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-truck"></i> Supplier Details
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="Suplier">Supplier Name <span class="required">*</span></label>
          <input class="form-control" id="Suplier" name="Suplier" placeholder="Supplier name" type="text" required>
          <div class="invalid-feedback">Supplier name is required.</div>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit"><i class="bi bi-check-lg"></i> Save</button>
        <a href="index.php?view=list" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Cancel</a>
      </div>
    </div>
  </div>
</form>
