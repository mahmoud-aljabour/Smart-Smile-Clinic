<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$presc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($presc_id <= 0) {
  redirect("index.php?view=prescriptions");
}

$sql = "SELECT pr.*,
        CONCAT(p.Fname, ' ', p.Mname, ' ', p.Lname) AS patient_name,
        p.ContactNo AS patient_phone,
        p.Age,
        p.Sex,
        p.F_Address AS address,
        u.FullName AS doctor_name
        FROM prescriptions pr
        JOIN tblpatients p ON pr.patient_id = p.PatientID
        LEFT JOIN tblusers u ON pr.user_id = u.UserID
        WHERE pr.id = '{$presc_id}'";
$mydb->setQuery($sql);
$prescription = $mydb->loadSingleResult();

if (!$prescription) {
  message("Prescription not found.", "error");
  redirect("index.php?view=prescriptions");
}

$created_at = date_format(date_create($prescription->created_at), "m/d/Y");
$prescriptionNo = !empty($prescription->prescription_no) ? $prescription->prescription_no : ('PRESC_' . $prescription->id);
$doctorName = !empty($prescription->doctor_name) ? $prescription->doctor_name : 'Not assigned';
$patientDisplayName = trim($prescription->patient_name);

$sql = "SELECT * FROM tplprintprescriptions LIMIT 1";
$mydb->setQuery($sql);
$print_data = $mydb->loadSingleResult();
?>

<div class="page-header-bar no-print">
  <div>
    <h1 class="h3 mb-1">Prescription Details</h1>
    <p class="text-muted small mb-0"><?php echo htmlspecialchars($prescriptionNo); ?></p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="index.php?view=prescriptions" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to List
    </a>
    <?php if ($_SESSION['ADMIN_ROLE'] == 'Administrator' || $_SESSION['ADMIN_ROLE'] == 'admin'): ?>
      <a href="index.php?view=edit_prescription&presc_id=<?php echo $presc_id; ?>" class="btn btn-outline-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    <?php endif; ?>
    <button type="button" onclick="window.print()" class="btn btn-primary">
      <i class="bi bi-printer"></i> Print Prescription
    </button>
  </div>
</div>

<div id="prescription-view-area" class="prescription-document-page invoice-document-page">

  <div class="invoice-print-header">
    <div class="line-doc">Prescription</div>
    <div class="line-main"><?php echo htmlspecialchars($print_data->header1 ?? app_name); ?></div>
    <?php if (!empty($print_data->header2)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($print_data->header2); ?></div>
    <?php endif; ?>
    <?php if (!empty($print_data->header3)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($print_data->header3); ?></div>
    <?php endif; ?>
  </div>

  <div class="content-card invoice-print-area">
    <div class="card-body">
      <div class="invoice-doc-head text-center mb-4">
        <div class="invoice-doc-type">Prescription</div>
        <div class="invoice-doc-number"><?php echo htmlspecialchars($prescriptionNo); ?></div>
      </div>

      <div class="invoice-info-grid mb-4">
        <div class="invoice-info-block">
          <h3 class="invoice-block-title">Patient</h3>
          <div class="invoice-detail-list">
            <div class="invoice-detail-item full-width">
              <span class="detail-label">Patient Name</span>
              <span class="detail-value"><?php echo htmlspecialchars($patientDisplayName); ?></span>
            </div>
            <div class="invoice-detail-item">
              <span class="detail-label">Age</span>
              <span class="detail-value"><?php echo (int)$prescription->Age; ?> years</span>
            </div>
            <div class="invoice-detail-item">
              <span class="detail-label">Gender</span>
              <span class="detail-value"><?php echo htmlspecialchars($prescription->Sex); ?></span>
            </div>
            <div class="invoice-detail-item full-width">
              <span class="detail-label">Address</span>
              <span class="detail-value"><?php echo htmlspecialchars($prescription->address); ?></span>
            </div>
            <div class="invoice-detail-item">
              <span class="detail-label">Phone</span>
              <span class="detail-value"><?php echo htmlspecialchars($prescription->patient_phone); ?></span>
            </div>
          </div>
        </div>

        <div class="invoice-info-block">
          <h3 class="invoice-block-title">Prescription Details</h3>
          <div class="invoice-detail-list">
            <div class="invoice-detail-item">
              <span class="detail-label">Prescription No.</span>
              <span class="detail-value"><?php echo htmlspecialchars($prescriptionNo); ?></span>
            </div>
            <div class="invoice-detail-item">
              <span class="detail-label">Issue Date</span>
              <span class="detail-value"><?php echo htmlspecialchars($created_at); ?></span>
            </div>
            <div class="invoice-detail-item full-width">
              <span class="detail-label">Doctor</span>
              <span class="detail-value"><?php echo htmlspecialchars($doctorName); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="invoice-table-wrap">
        <table class="table table-modern table-bordered invoice-doc-table prescription-doc-table mb-0">
          <thead>
            <tr>
              <th class="col-medicine">Medicine Name</th>
              <th class="col-dosage">Dosage</th>
              <th class="col-timing">Timing</th>
              <th class="col-advice">Medical Advice</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="col-medicine"><?php echo htmlspecialchars($prescription->medicine_name); ?></td>
              <td class="col-dosage"><?php echo htmlspecialchars($prescription->dosage); ?></td>
              <td class="col-timing"><?php echo htmlspecialchars($prescription->timing ?: 'Not specified'); ?></td>
              <td class="col-advice"><?php echo nl2br(htmlspecialchars($prescription->medical_advice ?: '—')); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="invoice-print-footer">
    <?php if (!empty($print_data->footer1)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($print_data->footer1); ?></div>
    <?php endif; ?>
    <?php if (!empty($print_data->footer2)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($print_data->footer2); ?></div>
    <?php endif; ?>
    <?php if (!empty($print_data->footer3)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($print_data->footer3); ?></div>
    <?php endif; ?>
  </div>

</div>
