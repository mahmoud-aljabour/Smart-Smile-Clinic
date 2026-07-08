<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$AUTOID = (int)$_GET['id'];
$autonumber = new Autonumber();
$singleauto = $autonumber->single_autonumber($AUTOID);

if (!$singleauto) {
  message("Autonumber not found.", "error");
  redirect("index.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Edit Autonumber</h1>
    <p class="text-muted small mb-0"><?php echo htmlspecialchars($singleauto->AUTOKEY); ?></p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=edit" method="POST" autocomplete="off" class="form-add-page" novalidate>
  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-123"></i> Sequence Details
    </div>
    <div class="card-body">
      <input type="hidden" name="AUTOID" value="<?php echo (int)$singleauto->AUTOID; ?>">
      <input type="hidden" name="AUTOKEY" value="<?php echo htmlspecialchars($singleauto->AUTOKEY); ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Key</label>
          <input class="form-control" type="text" value="<?php echo htmlspecialchars($singleauto->AUTOKEY); ?>" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="AUTOSTART">Prefix <span class="required">*</span></label>
          <input class="form-control" id="AUTOSTART" name="AUTOSTART" type="text" value="<?php echo htmlspecialchars($singleauto->AUTOSTART); ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="AUTOEND">Sequence # <span class="required">*</span></label>
          <input class="form-control" id="AUTOEND" name="AUTOEND" type="text" value="<?php echo htmlspecialchars($singleauto->AUTOEND); ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="AUTOINC">Increment Value <span class="required">*</span></label>
          <input class="form-control" id="AUTOINC" name="AUTOINC" type="text" value="<?php echo htmlspecialchars($singleauto->AUTOINC); ?>" required>
        </div>
        <div class="col-md-12">
          <div class="info-badge">
            <i class="bi bi-info-circle"></i>
            <span>Next generated number: <strong><?php echo htmlspecialchars($singleauto->AUTOSTART . $singleauto->AUTOEND); ?></strong></span>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit"><i class="bi bi-check-lg"></i> Save Changes</button>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Cancel</a>
      </div>
    </div>
  </div>
</form>
