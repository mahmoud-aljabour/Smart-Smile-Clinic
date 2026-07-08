<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

if (isset($_POST['ClosedClientSession'])) {
  unset($_SESSION['Patients']);
  unset($_SESSION['PatientAgeGroupID']);

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
  $sql = "SELECT * FROM tblpatients WHERE CONCAT(Fname, ' ', Mname, ' ', Lname)='{$_SESSION['Patients']}'";
  $mydb->setQuery($sql);
  $cur = $mydb->executeQuery();
  $maxrow = $mydb->num_rows($cur);
  $res = $mydb->loadSingleResult();

  if ($maxrow > 0) {
    $patientAge = $res->Age;
    $sql_age_group = "SELECT AgeGroupID FROM tbl_age_groups 
                      WHERE {$patientAge} >= MinAge AND ({$patientAge} <= MaxAge OR MaxAge IS NULL)";
    $mydb->setQuery($sql_age_group);
    $ageGroupRes = $mydb->loadSingleResult();
    $_SESSION['PatientAgeGroupID'] = $ageGroupRes ? $ageGroupRes->AgeGroupID : null;
  }
}

if ($_SESSION['Patients'] == "NONE" || $_SESSION['Patients'] == "") {
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
      $selected = ($_SESSION['Patients'] == $fullName) ? 'selected' : '';
      echo '<option value="' . htmlspecialchars($fullName) . '" ' . $selected . '>' . htmlspecialchars($fullName) . '</option>';
    }
    ?>
  </select>
  <a id="client_modal" class="patient-add-link" data-bs-target="#addClientModal" data-bs-toggle="modal" href="#">
    <i class="bi bi-person-plus"></i> Add New Patient
  </a>
<?php } else {
  $Patients = $_SESSION['Patients'];
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
    <div class="patient-info-card">
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
        <span class="info-value"><?php echo htmlspecialchars($res->Age); ?></span>
      </div>
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
<script type="text/javascript">
  $("#Patients").on("change", function() {
    var Patients = $(this).val();
    var invno = document.getElementById("InvoiceNo").value;
    $.ajax({
      type: "POST", url: "loaddata.php", dataType: "text",
      data: { Patients: Patients, invno: invno },
      beforeSend: function() { $("#loading-client").show(); $("#invoicing-body").hide(); },
      success: function(data) { $("#loading-client").hide(); $("#invoicing-body").show(); $('#searchclient').show().html(data); }
    });
  });

  $("#closeClient").on("click", function() {
    var invno = document.getElementById("InvoiceNo").value;
    $.ajax({
      type: "POST", url: "loaddata.php", dataType: "text",
      beforeSend: function() { $("#loading-client").show(); $("#invoicing-body").hide(); },
      data: { ClosedClientSession: "closed", invno: invno },
      success: function(data) { $("#loading-client").hide(); $("#invoicing-body").show(); $('#searchclient').show().html(data); }
    });
  });

  $.clearFormFields = function() { $('#my_form').find('input[type=text], input[type=password], input[type=number], input[type=email], textarea').val(''); $("#successmsg").html(""); };
  $('#client_modal').on('click', function() { $.clearFormFields(); });

  if ($.fn.select2 && $("#Patients").length) {
    $("#Patients").select2({ width: '100%' });
  }
</script>
