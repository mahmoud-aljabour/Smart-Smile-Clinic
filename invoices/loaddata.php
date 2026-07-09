<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

if (isset($_POST['ClosedClientSession'])) {
  unset($_SESSION['Patients']);
  unset($_SESSION['PatientAgeGroupID']);
  unset($_SESSION['PatientAgeGroupLabel']);
  unset($_SESSION['PatientAgeGroupMaxTeeth']);

  if (isset($_POST['invno'])) {
    $invno = $_POST['invno'];
    $sql = "UPDATE `tblpayments` SET `Patients`='NONE' WHERE `InvoiceNo`='{$invno}'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
  }
}

if (!isset($_SESSION['Patients'])) {
  $_SESSION['Patients'] = isset($_POST['Patients']) ? $_POST['Patients'] : "";
} elseif (isset($_POST['Patients']) && $_POST['Patients'] !== $_SESSION['Patients']) {
  $_SESSION['Patients'] = $_POST['Patients'];
}

$Patients = $_SESSION['Patients'];
if ($Patients && $Patients !== 'NONE') {
  sync_patient_age_group_from_name($Patients);
} else {
  unset($_SESSION['PatientAgeGroupID'], $_SESSION['PatientAgeGroupLabel'], $_SESSION['PatientAgeGroupMaxTeeth']);
}

if ($Patients == "NONE" || $Patients == "") {
?>
  <label class="form-label" for="Patients">Select Patient</label>
  <select class="select2 form-select" id="Patients" name="Patients">
    <option value="None">Choose a patient...</option>
    <?php
    $sql = "SELECT * FROM tblpatients";
    $mydb->setQuery($sql);
    $res = $mydb->loadResultList();
    foreach ($res as $row) {
      $fullName = trim($row->Fname . ' ' . $row->Mname . ' ' . $row->Lname);
      echo '<option value="' . htmlspecialchars($fullName) . '">' . htmlspecialchars($fullName) . '</option>';
    }
    ?>
  </select>
  <a id="client_modal" class="patient-add-link" data-bs-target="#addClientModal" data-bs-toggle="modal" href="#">
    <i class="bi bi-person-plus"></i> Add New Patient
  </a>
<?php } else {
  if (isset($_POST['invno'])) {
    $invno = $_POST['invno'];
    $sql = "UPDATE `tblpayments` SET `Patients`='{$Patients}' WHERE `InvoiceNo`='{$invno}'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
  }

  $sql = "SELECT * FROM tblpatients WHERE CONCAT(Fname, ' ', Mname, ' ', Lname)='{$Patients}'";
  $mydb->setQuery($sql);
  $cur = $mydb->executeQuery();
  $maxrow = $mydb->num_rows($cur);
  $res = $mydb->loadSingleResult();
?>
  <button type="button" id="closeClient" class="patient-close-btn" title="Remove patient">
    <i class="bi bi-x-circle"></i> Remove patient
  </button>
  <?php if ($maxrow > 0) { ?>
    <div class="patient-info-card" data-patient-selected="1"
      data-age-group-label="<?php echo htmlspecialchars($_SESSION['PatientAgeGroupLabel'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
      data-age-group-max-teeth="<?php echo (int)($_SESSION['PatientAgeGroupMaxTeeth'] ?? 0); ?>">
      <div class="info-item full-width">
        <span class="info-label">Patient Name</span>
        <span class="info-value"><?php echo htmlspecialchars(trim($res->Fname . ' ' . $res->Mname . ' ' . $res->Lname)); ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">Sex</span>
        <span class="info-value"><?php echo htmlspecialchars($res->Sex); ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">Age</span>
        <span class="info-value"><?php echo htmlspecialchars($res->Age); ?> years</span>
      </div>
      <?php if (!empty($_SESSION['PatientAgeGroupLabel'])): ?>
      <div class="info-item full-width">
        <span class="info-label">Age Group</span>
        <span class="info-value">
          <span class="badge text-bg-primary"><?php echo htmlspecialchars($_SESSION['PatientAgeGroupLabel']); ?></span>
          <?php if (!empty($_SESSION['PatientAgeGroupMaxTeeth'])): ?>
            <span class="text-muted small ms-1"><?php echo (int)$_SESSION['PatientAgeGroupMaxTeeth']; ?> teeth</span>
          <?php endif; ?>
        </span>
      </div>
      <?php endif; ?>
      <div class="info-item full-width">
        <span class="info-label">Address</span>
        <span class="info-value"><?php echo htmlspecialchars($res->F_Address); ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">Phone</span>
        <span class="info-value"><?php echo htmlspecialchars($res->ContactNo); ?></span>
      </div>
    </div>
  <?php } ?>
<?php } ?>
