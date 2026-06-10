<?php
require_once("../include/initialize.php");
//checkAdmin();
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

if (isset($_POST['ClosedClientSession'])) {
  unset($_SESSION['Patients']);
  unset($_SESSION['PatientAgeGroupID']); // حذف عند الإغلاق

  if (isset($_POST['invno'])) {
    $invno = $_POST['invno'];
    $sql = "UPDATE `tblpayments` SET `Patients`='NONE' WHERE `InvoiceNo`='{$invno}'";
    $mydb->setQuery($sql);
    $mydb->executeQuery();
  }
}

// تحديث Patients وAgeGroupID فقط عند اختيار مريض جديد
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
  <label>Patient : </label>
  <select class="select2 form-control" id="Patients" name="Patients">
    <option value="None">Select</option>
    <?php
    $sql = "SELECT * FROM tblpatients";
    $mydb->setQuery($sql);
    $res = $mydb->loadResultList();
    foreach ($res as $row) {
      $fullName = $row->Fname . ' ' . $row->Mname . ' ' . $row->Lname;
      $selected = ($_SESSION['Patients'] == $fullName) ? 'selected' : '';
      echo '<option value="' . $fullName . '" ' . $selected . '>' . $fullName . '</option>';
    }
    ?>
  </select>
  <a id="client_modal" data-target="#addClientModal" data-toggle="modal" href="#">Add New</a>
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
  <style type="text/css">
    .table-client { width: 100%; }
    .table-client tr td { border-bottom: 1px solid #ddd; padding: 10px 0px 0px 0px; }
  </style>
  <div id="closeClient" style="text-align: right;cursor: pointer;color: red;font-weight: bolder;">x</div>
  <?php if ($maxrow > 0) { ?>
    <table class="table-client">
      <tr><td>Patient Name</td><td><?php echo $res->Fname . ' ' . $res->Mname . ' ' . $res->Lname; ?></td></tr>
      <tr><td>Sex</td><td><?php echo $res->Sex; ?></td></tr>
      <tr><td>Age</td><td><?php echo $res->Age; ?></td></tr>
      <tr><td>Address</td><td><?php echo $res->F_Address; ?></td></tr>
      <tr><td>Phone #</td><td><?php echo $res->ContactNo; ?></td></tr>
    </table>
  <?php } ?>
<?php } ?>
<script type="text/javascript" src="<?php echo web_root; ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
<script type="text/javascript" src="<?php echo web_root; ?>plugins/select2/select2.full.min.js"></script>
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
</script>