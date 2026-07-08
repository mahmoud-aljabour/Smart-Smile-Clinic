<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$CurrencyID = (int)$_GET['id'];
$currency = new Currency();
$res = $currency->single_currency($CurrencyID);

if (!$res) {
  message("Currency not found.", "error");
  redirect("index.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Edit Currency</h1>
    <p class="text-muted small mb-0"><?php echo htmlspecialchars($res->CurrencySymbol); ?></p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=edit" method="POST" autocomplete="off" class="form-add-page" novalidate>
  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-currency-exchange"></i> Currency Details
    </div>
    <div class="card-body">
      <input type="hidden" name="CurrencyID" value="<?php echo (int)$res->CurrencyID; ?>">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="CurrencySymbol">Currency Symbol <span class="required">*</span></label>
          <input class="form-control" id="CurrencySymbol" name="CurrencySymbol" type="text" value="<?php echo htmlspecialchars($res->CurrencySymbol); ?>" required>
          <div class="invalid-feedback">Currency symbol is required.</div>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit"><i class="bi bi-check-lg"></i> Save Changes</button>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i> Cancel</a>
      </div>
    </div>
  </div>
</form>
