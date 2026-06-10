<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "index.php");
}

$presc_id = isset($_GET['id']) ? (int)$_GET['id'] : "";
if ($presc_id == "") {
  redirect("index.php");
}
 

$sql = "SELECT pr.*, 
        CONCAT(p.Fname, ' ', p.Lname) AS patient_name,
        p.ContactNo AS patient_phone,
        p.Age,
        p.Sex,
        p.F_Address AS address,
        FullName AS doctor_name
        FROM prescriptions pr
        JOIN tblpatients p ON pr.patient_id = p.PatientID
        JOIN tblusers u ON pr.user_id = u.UserID
        WHERE pr.id = '{$presc_id}'";
$mydb->setQuery($sql);
$prescription = $mydb->loadSingleResult();
// $prescription->prescription_no

if (!$prescription) {
  message("Prescription not found.", "error");
  redirect("index.php?view=prescriptions");
}

$created_at = date_format(date_create($prescription->created_at), "m/d/Y");

// Fetch header and footer data from tplprintprescriptions
$sql = "SELECT * FROM tplprintprescriptions LIMIT 1";
$mydb->setQuery($sql);
$print_data = $mydb->loadSingleResult();
$header1 = htmlspecialchars($print_data ? $print_data->header1 : 'Header 1 Default');
$header2 = htmlspecialchars($print_data ? $print_data->header2 : 'Header 2 Default');
$header3 = htmlspecialchars($print_data ? $print_data->header3 : 'Header 3 Default');
$footer1 = htmlspecialchars($print_data ? $print_data->footer1 : 'Footer 1 Default');
$footer2 = htmlspecialchars($print_data ? $print_data->footer2 : 'Footer 2 Default');
$footer3 = htmlspecialchars($print_data ? $print_data->footer3 : 'Footer 3 Default');
?>

<style type="text/css">
  .table-summary {
    width: 100%;
    font-size: 15px;
    font-weight: bold;
  }

  .table-summary tr td {
    border-bottom: 1px solid #ddd;
    padding: 10px 0px 0px 0px;
  }

  .right {
    text-align: right;
  }

  #loading-client {
    display: none;
    /*visibility: hidden;*/
  }

  .header {
    display: inline-block;
  }

  @media print {
    @page {
      size: 8.5in 11in;
      margin: -30px 0px 1cm 0px;
    }

    body {
      margin: 0cm;
    }

    .no-print {
      display: none !important;
    }
  }

  @media print {
    @page {
      size: 8.5in 11in;
      margin: -30px 0px 1cm 0px;
    }

    body {
      margin: 0cm;
      border: 0px;
    }

    .tables {
      font-size: 11px;
    }

    .tables tr td {
      padding: 0px 0px 0px 10px;
      margin: 0px;
    }

    thead {
      display: table-header-group;
    }

    .page-break {
      /*page-break-after:  always;*/
      page-break-before: always;
      break-before: always;
    }

    .tables tr:nth-child(even) {
      background-color: #f2f2f2 !important;
      -webkit-print-color-adjust: exact;
    }
  }

  .tables {
    font-size: 11px;
    width: 100%;
  }

  .tables tr th {
    padding: 10px;
    border-bottom: 1px #ddd solid;
  }

  .tables tbody {
    border-bottom: 1px #ddd solid;
  }

  @media screen {
    .tables tr td {
      padding: 10px;
    }
  }

  .firstline {
    text-align: center;
    font-size: 20px;
    font-weight: bolder;
    text-transform: uppercase;
  }

  .secondline {
    text-align: center;
    font-size: 12px;
    font-weight: bolder;
    text-transform: uppercase;
  }

  .thirdline {
    text-align: center;
    font-size: 12px;
    font-weight: bolder;
    text-transform: uppercase;
  }

  .clearfix:after {
    content: " ";
    /* Older browser do not support empty content */
    visibility: hidden;
    display: block;
    height: 0;
    clear: both;
  }
</style>

<div class="col-md-12 no-print">
  <div class="">
    <button type="button" onclick="window.print()" name="save" class="btn btn-primary"><i class="fa fa-print"></i> PRINT</button>
    <!-- <a href="export_prescription.php?id=<?php echo $pr->prescription_no; ?>" name="save" class="btn btn-primary"><i class="fa fa-upload"></i> Export to Excel</a> -->
  </div>
</div>

<div id="p">
  <div class="col-md-12" style="margin-top: 40px">
    <div class="firstline"><?php echo $header1; ?></div>
    <div class="secondline"><?php echo $header2; ?></div>
    <div class="thirdline"><?php echo $header3; ?></div>
  </div>
  <hr>

  <style type="text/css">
    .table-client {
      width: 100%;
    }

    .table-client tr td {
      border-bottom: 1px solid #ddd;
      padding: 5px 3px 5px 3px;
    }

    #tablecontiner {
      width: 100%;
    }

    #tablecontiner tr td {
      padding: 0px 5px;
    }
  </style>

  <div class="col-md-6 col-sm-6 col-xs-6 pull-left">
    <table class="table-client">
      <tr>
        <td>Patient Name :</td>
        <td><?php echo isset($prescription->patient_name) ? $prescription->patient_name : "None" ?></td>
      </tr>
      <tr>
        <td>Age :</td>
        <td><?php echo (int)$prescription->Age; ?> years</td>
      </tr>
      <tr>
        <td>Gender :</td>
        <td><?php echo htmlspecialchars($prescription->Sex); ?></td>
      </tr>
      <tr>
        <td>Address :</td>
        <td><?php echo htmlspecialchars($prescription->address); ?></td>
      </tr>
      <tr>
        <td>Phone : #</td>
        <td><?php echo htmlspecialchars($prescription->patient_phone); ?></td>
      </tr>
    </table>
  </div>

  <div class="col-md-6 col-sm-6 col-xs-6 pull-rigt">
    <table class="table-client">
      <tr>
        <td>Prescription ID</td>
        <td> <?php echo isset($prescription->prescription_no) ? $prescription->prescription_no : "None" ?> </td>
      </tr>
      <tr>
        <td>Issue Date</td>
        <td><?php echo $created_at; ?></td>
      </tr>
      <tr>
        <td>Doctor</td>
        <td><?php echo htmlspecialchars($prescription->doctor_name); ?></td>
      </tr>
    </table>
  </div>

  <table class="tables">
    <thead>
      <tr>
        <th width="12%">Medicine Name</th>
        <th>Dosage</th>
        <th>Timing</th>
        <th>Medical Advice</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo htmlspecialchars($prescription->medicine_name); ?></td>
        <td><?php echo htmlspecialchars($prescription->dosage); ?></td>
        <td><?php echo htmlspecialchars($prescription->timing ?: 'Not specified'); ?></td>
        <td><?php echo nl2br(htmlspecialchars($prescription->medical_advice)); ?></td>
      </tr>
    </tbody>
  </table>

  <br>

  <div class="container">
    <div style="text-transform: uppercase;text-align: center;font-size: 12px"><?php echo $footer1; ?></div>
    <div style="text-transform: uppercase;text-align: center;font-size: 12px"><?php echo $footer2; ?></div>
    <div style="text-transform: uppercase;text-align: center;font-size: 12px"><?php echo $footer3; ?></div>
  </div>

  <script>
    function print_prescription() {
      window.print();
    }
  </script>