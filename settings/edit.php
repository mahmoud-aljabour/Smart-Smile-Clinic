<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$sql = "SELECT * FROM `tblprintheader` LIMIT 1";
$mydb->setQuery($sql);
$header = $mydb->loadSingleResult();

$sql = "SELECT * FROM `tblprintfooter` LIMIT 1";
$mydb->setQuery($sql);
$footer = $mydb->loadSingleResult();
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Invoice Print Setup</h1>
    <p class="text-muted small mb-0">Configure header and footer for printed invoices</p>
  </div>
</div>

<form action="controller.php?action=edit" method="POST" class="form-add-page">

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-layout-text-window"></i> Invoice Header
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label" for="HFirstLine">First Line</label>
          <textarea class="form-control" id="HFirstLine" name="HFirstLine" rows="2"><?php echo htmlspecialchars($header ? $header->FirstLine : ''); ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="HSecondLine">Second Line</label>
          <textarea class="form-control" id="HSecondLine" name="HSecondLine" rows="2"><?php echo htmlspecialchars($header ? $header->SecondLine : ''); ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="HThirdLine">Third Line</label>
          <textarea class="form-control" id="HThirdLine" name="HThirdLine" rows="2"><?php echo htmlspecialchars($header ? $header->ThirdLine : ''); ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-layout-text-window-reverse"></i> Invoice Footer
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label" for="FFirstLine">First Line</label>
          <textarea class="form-control" id="FFirstLine" name="FFirstLine" rows="2"><?php echo htmlspecialchars($footer ? $footer->FirstLine : ''); ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="FSecondLine">Second Line</label>
          <textarea class="form-control" id="FSecondLine" name="FSecondLine" rows="2"><?php echo htmlspecialchars($footer ? $footer->SecondLine : ''); ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="FThirdLine">Third Line</label>
          <textarea class="form-control" id="FThirdLine" name="FThirdLine" rows="2"><?php echo htmlspecialchars($footer ? $footer->ThirdLine : ''); ?></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit"><i class="bi bi-check-lg"></i> Save Settings</button>
      </div>
    </div>
  </div>

</form>
