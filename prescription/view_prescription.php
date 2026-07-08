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

$sql = "SELECT * FROM tplprintprescriptions LIMIT 1";
$mydb->setQuery($sql);
$print_data = $mydb->loadSingleResult();

$header1 = htmlspecialchars($print_data->header1 ?? 'Smart Smile Clinic');
$header2 = htmlspecialchars($print_data->header2 ?? '');
$header3 = htmlspecialchars($print_data->header3 ?? '');
$footer1 = htmlspecialchars($print_data->footer1 ?? '');
$footer2 = htmlspecialchars($print_data->footer2 ?? '');
$footer3 = htmlspecialchars($print_data->footer3 ?? '');
?>

<style>
  @media print {
    .no-print,
    .no-print * {
      display: none !important;
    }

    .prescription-view-page {
      background: #fff !important;
      border: none !important;
      box-shadow: none !important;
      padding: 0 !important;
    }

    .prescription-view-page::before,
    .prescription-view-page::after {
      display: none !important;
    }

    .form-page-card {
      background: #fff !important;
      border: 1px solid #ddd !important;
      box-shadow: none !important;
    }
  }
</style>

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
    <a href="printprescription.php?id=<?php echo $presc_id; ?>" class="btn btn-outline-primary" target="_blank">
      <i class="bi bi-box-arrow-up-right"></i> Print Page
    </a>
    <button type="button" onclick="window.print()" class="btn btn-primary">
      <i class="bi bi-printer"></i> Print
    </button>
  </div>
</div>

<div id="prescription-view-area" class="form-add-page prescription-view-page invoice-view-page">

  <div class="invoice-print-header text-center mb-4">
    <div class="line-main"><?php echo $header1; ?></div>
    <div class="line-sub"><?php echo $header2; ?></div>
    <div class="line-sub"><?php echo $header3; ?></div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-6">
      <div class="form-page-card h-100">
        <div class="card-header">
          <i class="bi bi-person-vcard"></i> Patient
        </div>
        <div class="card-body">
          <div class="patient-info-card">
            <div class="info-item full-width">
              <span class="info-label">Patient Name</span>
              <span class="info-value"><?php echo htmlspecialchars(trim($prescription->patient_name)); ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Age</span>
              <span class="info-value"><?php echo (int)$prescription->Age; ?> years</span>
            </div>
            <div class="info-item">
              <span class="info-label">Gender</span>
              <span class="info-value"><?php echo htmlspecialchars($prescription->Sex); ?></span>
            </div>
            <div class="info-item full-width">
              <span class="info-label">Address</span>
              <span class="info-value"><?php echo htmlspecialchars($prescription->address); ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Phone</span>
              <span class="info-value"><?php echo htmlspecialchars($prescription->patient_phone); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="form-page-card h-100">
        <div class="card-header">
          <i class="bi bi-file-earmark-medical"></i> Prescription Information
        </div>
        <div class="card-body">
          <div class="patient-info-card">
            <div class="info-item">
              <span class="info-label">Prescription ID</span>
              <span class="info-value"><?php echo htmlspecialchars($prescriptionNo); ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Issue Date</span>
              <span class="info-value"><?php echo htmlspecialchars($created_at); ?></span>
            </div>
            <div class="info-item full-width">
              <span class="info-label">Doctor</span>
              <span class="info-value"><?php echo htmlspecialchars($doctorName); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-capsule"></i> Medicine Details
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-modern table-hover table-bordered mb-0">
          <thead>
            <tr>
              <th width="22%">Medicine Name</th>
              <th width="22%">Dosage</th>
              <th width="18%">Timing</th>
              <th>Medical Advice</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo htmlspecialchars($prescription->medicine_name); ?></td>
              <td><?php echo htmlspecialchars($prescription->dosage); ?></td>
              <td><?php echo htmlspecialchars($prescription->timing ?: 'Not specified'); ?></td>
              <td><?php echo nl2br(htmlspecialchars($prescription->medical_advice ?: '—')); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="invoice-print-footer text-center">
    <div class="line-sub"><?php echo $footer1; ?></div>
    <div class="line-sub"><?php echo $footer2; ?></div>
    <div class="line-sub"><?php echo $footer3; ?></div>
  </div>

</div>
