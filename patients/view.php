<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$patientId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($patientId <= 0) {
  redirect("index.php");
}

$sql = "SELECT * FROM tblpatients WHERE PatientID = {$patientId}";
$mydb->setQuery($sql);
$res = $mydb->loadSingleResult();

if (!$res) {
  message("Patient not found.", "error");
  redirect("index.php");
}

$fullName = trim($res->Fname . ' ' . $res->Mname . ' ' . $res->Lname);
$age = (int)$res->Age;
$currency = $setDefault->default_currency();

if ($age == 0) {
  $total_teeth = 0;
} elseif ($age == 1) {
  $total_teeth = 8;
} elseif ($age == 2) {
  $total_teeth = 16;
} elseif ($age >= 3 && $age <= 5) {
  $total_teeth = 20;
} elseif ($age >= 6 && $age <= 12) {
  $total_teeth = 24;
} elseif ($age >= 13 && $age <= 16) {
  $total_teeth = 28;
} else {
  $total_teeth = 32;
}

$birthDate = '';
if (!empty($res->BirthDate)) {
  $birthObj = date_create($res->BirthDate);
  if ($birthObj) {
    $birthDate = date_format($birthObj, 'm/d/Y');
  }
}

cleanup_patient_invoices($fullName);

$history = get_patient_treatment_history($fullName);

$historyTotal = 0;
foreach ($history as $row) {
  $historyTotal += $row->Price;
}

$treatedTeeth = array();
foreach (get_patient_treated_teeth($fullName) as $toothRow) {
  $treatedTeeth[(string)$toothRow->ToothNumber] = true;
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Patient Details</h1>
    <p class="text-muted small mb-0"><?php echo htmlspecialchars($fullName); ?> · ID #<?php echo (int)$res->PatientID; ?></p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to List
    </a>
    <a href="index.php?view=edit&id=<?php echo (int)$res->PatientID; ?>" class="btn btn-outline-primary">
      <i class="bi bi-pencil"></i> Edit Patient
    </a>
  </div>
</div>

<div class="form-add-page patient-view-page invoice-view-page">

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-person-vcard"></i> Patient Information
    </div>
    <div class="card-body">
      <div class="patient-info-card">
        <div class="info-item full-width">
          <span class="info-label">Patient Name</span>
          <span class="info-value"><?php echo htmlspecialchars($fullName); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Sex</span>
          <span class="info-value"><?php echo htmlspecialchars($res->Sex); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Age</span>
          <span class="info-value"><?php echo (int)$res->Age; ?> years</span>
        </div>
        <?php if ($birthDate): ?>
        <div class="info-item">
          <span class="info-label">Date of Birth</span>
          <span class="info-value"><?php echo htmlspecialchars($birthDate); ?></span>
        </div>
        <?php endif; ?>
        <div class="info-item">
          <span class="info-label">Phone</span>
          <span class="info-value"><?php echo htmlspecialchars($res->ContactNo); ?></span>
        </div>
        <div class="info-item full-width">
          <span class="info-label">Address</span>
          <span class="info-value"><?php echo htmlspecialchars($res->F_Address); ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Expected Teeth</span>
          <span class="info-value">
            <span class="badge text-bg-primary"><?php echo (int)$total_teeth; ?> teeth</span>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-clock-history"></i> Treatment History
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-modern table-hover table-bordered mb-0">
          <thead>
            <tr>
              <th>Date</th>
              <th>Service</th>
              <th>Tooth</th>
              <th class="text-end">Price (<?php echo htmlspecialchars($currency); ?>)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($history)): ?>
              <tr>
                <td colspan="4" class="text-center text-muted py-4">No treatment history found for this patient.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($history as $result): ?>
                <?php $serviceLabel = invoice_service_label($result, $result->CatalogService ?? '', $result->CatalogDescription ?? ''); ?>
                <tr>
                  <td><?php echo date('m/d/Y', strtotime($result->InvoiceDate)); ?></td>
                  <td><?php echo htmlspecialchars($serviceLabel); ?></td>
                  <td><?php echo htmlspecialchars($result->ToothNumber); ?></td>
                  <td class="text-end"><?php echo number_format($result->Price, 2); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (!empty($history)): ?>
        <div class="invoice-total-box mt-4">
          <span class="total-label">Total Treatments</span>
          <span class="total-value"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($historyTotal, 2); ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-emoji-smile"></i> Dental Chart
    </div>
    <div class="card-body">
      <?php if ($total_teeth > 0): ?>
        <div class="teeth-legend mb-3">
          <span class="teeth-legend-item"><span class="teeth-dot teeth-dot--treated"></span> Treated</span>
          <span class="teeth-legend-item"><span class="teeth-dot teeth-dot--healthy"></span> Not treated</span>
        </div>

        <div class="teeth-chart-grid">
          <div class="teeth-row-label">Upper</div>
          <div class="teeth-row">
            <?php
            $upper_end = (int)floor($total_teeth / 2);
            for ($i = 1; $i <= $upper_end; $i++) {
              $isTreated = isset($treatedTeeth[(string)$i]);
              echo '<div class="tooth-item' . ($isTreated ? ' is-treated' : '') . '">';
              echo '<span class="icon-iconfinder_Dental_-_Tooth_-_Dentist_-_Dentistry_01_2185089"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>';
              echo '<span class="tooth-number">' . $i . '</span>';
              echo '</div>';
            }
            ?>
          </div>

          <div class="teeth-row-label">Lower</div>
          <div class="teeth-row">
            <?php
            $lower_start = $upper_end + 1;
            for ($i = $lower_start; $i <= $total_teeth; $i++) {
              $isTreated = isset($treatedTeeth[(string)$i]);
              echo '<div class="tooth-item' . ($isTreated ? ' is-treated' : '') . '">';
              echo '<span class="icon-iconfinder_Dental_-_Tooth_-_Dentist_-_Dentistry_01_2185089"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>';
              echo '<span class="tooth-number">' . $i . '</span>';
              echo '</div>';
            }
            ?>
          </div>
        </div>
      <?php else: ?>
        <p class="text-center text-muted mb-0 py-3">No teeth chart available for this age group.</p>
      <?php endif; ?>
    </div>
  </div>

</div>
