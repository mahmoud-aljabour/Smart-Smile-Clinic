<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$sku = isset($_GET['id']) ? trim($_GET['id']) : '';
if ($sku === '') {
  redirect("index.php");
}

global $mydb;
$sql = "SELECT s.*, ag.Description AS AgeGroupDesc, ag.ToothCount, ag.MinAge, ag.MaxAge
        FROM `tblservices` s
        LEFT JOIN `tbl_age_groups` ag ON s.AgeGroupID = ag.AgeGroupID
        WHERE s.SKU = '{$sku}'";
$mydb->setQuery($sql);
$service = $mydb->loadSingleResult();

if (!$service) {
  message("Service not found.", "error");
  redirect("index.php");
}

$currency = $setDefault->default_currency();
$ageGroup = $service->AgeGroupDesc ? $service->AgeGroupDesc : 'Not Specified';
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Service Details</h1>
    <p class="text-muted small mb-0"><?php echo htmlspecialchars($service->Services); ?> · <?php echo htmlspecialchars($service->SKU); ?></p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to List
    </a>
    <a href="index.php?view=edit&id=<?php echo urlencode($service->SKU); ?>" class="btn btn-outline-primary">
      <i class="bi bi-pencil"></i> Edit Service
    </a>
  </div>
</div>

<div class="form-add-page invoice-view-page">

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-clipboard2-pulse"></i> Service Information
    </div>
    <div class="card-body">
      <div class="patient-info-card">
        <div class="info-item">
          <span class="info-label">Service ID</span>
          <span class="info-value"><?php echo htmlspecialchars($service->SKU); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Service Name</span>
          <span class="info-value"><?php echo htmlspecialchars($service->Services); ?></span>
        </div>
        <div class="info-item full-width">
          <span class="info-label">Description</span>
          <span class="info-value"><?php echo $service->Description ? htmlspecialchars($service->Description) : '—'; ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Price</span>
          <span class="info-value">
            <span class="badge text-bg-primary"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($service->OriginalPrice, 2); ?></span>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-emoji-smile"></i> Dental Configuration
    </div>
    <div class="card-body">
      <div class="patient-info-card">
        <div class="info-item">
          <span class="info-label">Age Group</span>
          <span class="info-value"><?php echo htmlspecialchars($ageGroup); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Tooth Number(s)</span>
          <span class="info-value"><?php echo htmlspecialchars($service->ToothNumber); ?></span>
        </div>
        <?php if (!empty($service->ToothCount)): ?>
        <div class="info-item">
          <span class="info-label">Teeth in Age Group</span>
          <span class="info-value"><?php echo (int)$service->ToothCount; ?> teeth</span>
        </div>
        <?php endif; ?>
        <?php if ($service->MinAge !== null && $service->MaxAge !== null): ?>
        <div class="info-item">
          <span class="info-label">Age Range</span>
          <span class="info-value"><?php echo (int)$service->MinAge; ?> – <?php echo (int)$service->MaxAge; ?> years</span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>
