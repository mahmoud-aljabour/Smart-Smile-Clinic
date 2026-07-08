<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$sql = "SELECT * FROM `tplprintprescriptions` LIMIT 1";
$mydb->setQuery($sql);
$res = $mydb->loadSingleResult();
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Prescription Print Setup</h1>
    <p class="text-muted small mb-0">Configure header and footer for printed prescriptions</p>
  </div>
</div>

<form action="controller.php?action=edit" method="POST" class="form-add-page">

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-file-earmark-medical"></i> Prescription Header
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label" for="HFirstLine">First Line</label>
          <textarea class="form-control" id="HFirstLine" name="HFirstLine" rows="2"><?php echo htmlspecialchars($res ? $res->header1 : ''); ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="HSecondLine">Second Line</label>
          <textarea class="form-control" id="HSecondLine" name="HSecondLine" rows="2"><?php echo htmlspecialchars($res ? $res->header2 : ''); ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="HThirdLine">Third Line</label>
          <textarea class="form-control" id="HThirdLine" name="HThirdLine" rows="2"><?php echo htmlspecialchars($res ? $res->header3 : ''); ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-layout-text-window-reverse"></i> Prescription Footer
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label" for="FFirstLine">First Line</label>
          <textarea class="form-control" id="FFirstLine" name="FFirstLine" rows="2"><?php echo htmlspecialchars($res ? $res->footer1 : ''); ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="FSecondLine">Second Line</label>
          <textarea class="form-control" id="FSecondLine" name="FSecondLine" rows="2"><?php echo htmlspecialchars($res ? $res->footer2 : ''); ?></textarea>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="FThirdLine">Third Line</label>
          <textarea class="form-control" id="FThirdLine" name="FThirdLine" rows="2"><?php echo htmlspecialchars($res ? $res->footer3 : ''); ?></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit"><i class="bi bi-check-lg"></i> Save Settings</button>
      </div>
    </div>
  </div>

</form>
