<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$autonum = new Autonumber();
$res = $autonum->set_autonumber('PRESCRIPTION');
$prescriptionNo = ($res && !empty($res->AUTO)) ? $res->AUTO : 'PRESC_001';

global $mydb;
$sql = "SELECT PatientID, CONCAT(Fname, ' ', Mname, ' ', Lname) AS patient_name FROM tblpatients ORDER BY Fname ASC";
$mydb->setQuery($sql);
$patients = $mydb->loadResultList();
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Add Prescription</h1>
    <p class="text-muted small mb-0">Create a new medical prescription for a patient</p>
  </div>
  <a href="index.php?view=prescriptions" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=save_prescription" method="POST" enctype="multipart/form-data" autocomplete="off" id="prescriptionForm" class="form-add-page" novalidate>

  <input type="hidden" name="presc_id" value="0">

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-file-earmark-medical"></i> Prescription Details
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label" for="PrescriptionNo">Prescription ID</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-hash"></i></span>
            <input class="form-control" id="PrescriptionNo" name="prescription_no" type="text" value="<?php echo htmlspecialchars($prescriptionNo); ?>" readonly>
          </div>
          <div class="form-hint">
            <i class="bi bi-info-circle"></i>
            <span>Auto-generated unique identifier</span>
          </div>
        </div>
        <div class="col-md-8">
          <label class="form-label" for="PatientID">Patient <span class="required">*</span></label>
          <select class="form-select select2" id="PatientID" name="patient_id" required>
            <option value="">Select patient...</option>
            <?php foreach ($patients as $patient): ?>
              <option value="<?php echo (int)$patient->PatientID; ?>">
                <?php echo htmlspecialchars(trim($patient->patient_name)); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Please select a patient.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-capsule"></i> Medicine Information
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="MedicineName">Medicine Name <span class="required">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-prescription2"></i></span>
            <input class="form-control" id="MedicineName" name="medicine_name" placeholder="e.g. Amoxicillin 500mg" type="text" required>
          </div>
          <div class="invalid-feedback">Medicine name is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="Dosage">Dosage <span class="required">*</span></label>
          <input class="form-control" id="Dosage" name="dosage" placeholder="e.g. 1 tablet twice daily" required>
          <div class="invalid-feedback">Dosage is required.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="Timing">Timing</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-clock"></i></span>
            <input class="form-control" id="Timing" name="timing" placeholder="e.g. After meal / Before sleep">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-journal-medical"></i> Medical Advice
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label" for="MedicalAdvice">Additional Notes</label>
          <textarea class="form-control" id="MedicalAdvice" name="medical_advice" placeholder="Any special instructions or medical advice for the patient..." rows="3"></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit">
          <i class="bi bi-check-lg"></i> Save Prescription
        </button>
        <a href="index.php?view=prescriptions" class="btn btn-outline-secondary">
          <i class="bi bi-x-lg"></i> Cancel
        </a>
      </div>
    </div>
  </div>

</form>

<script>
(function () {
  var form = document.getElementById('prescriptionForm');

  function shakeField(field) {
    field.classList.add('form-shake');
    setTimeout(function () { field.classList.remove('form-shake'); }, 400);
  }

  form.addEventListener('submit', function (e) {
    var isValid = true;
    var requiredFields = ['PatientID', 'MedicineName', 'Dosage'];

    requiredFields.forEach(function (id) {
      var field = document.getElementById(id);
      var empty = !field.value.trim();
      field.classList.toggle('is-invalid', empty);
      if (empty) {
        isValid = false;
        shakeField(field);
      }
    });

    if (!isValid) {
      e.preventDefault();
      var firstInvalid = form.querySelector('.is-invalid');
      if (firstInvalid) {
        firstInvalid.focus();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  });

  form.querySelectorAll('.form-control, .form-select').forEach(function (field) {
    field.addEventListener('input', function () {
      if (field.classList.contains('is-invalid') && field.value.trim()) {
        field.classList.remove('is-invalid');
      }
    });
    field.addEventListener('change', function () {
      if (field.classList.contains('is-invalid') && field.value.trim()) {
        field.classList.remove('is-invalid');
      }
    });
  });

  if ($.fn.select2) {
    $('#PatientID').select2({ width: '100%', placeholder: 'Select patient...' });
    $('#PatientID').on('change', function () {
      if ($(this).val()) {
        $(this).removeClass('is-invalid');
      }
    });
  }
})();
</script>
