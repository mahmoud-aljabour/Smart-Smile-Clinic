<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "index.php");
}

$presc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($presc_id <= 0) {
    die("Invalid prescription ID.");
}

// Fetch prescription data + patient + doctor
$sql = "
    SELECT pr.*, 
           CONCAT(p.Fname, ' ', p.Lname) AS patient_name,
           p.Phone AS ContactNo,
           p.Age,
           p.Sex,
           p.F_Address AS address,
           'Doctor' AS doctor_name
    FROM prescriptions pr
    JOIN tblpatients p ON pr.patient_id = p.PatientID
    JOIN tblusers u ON pr.user_id = u.UserID
    WHERE pr.id = {$presc_id}
";
$mydb->setQuery($sql);
$prescription = $mydb->loadSingleResult();

if (!$prescription) {
    die("Prescription not found.");
}

$created_at = date('d/m/Y', strtotime($prescription->created_at));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Print Prescription</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo web_root; ?>bootstrap/css/bootstrap.min.css">
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

        .firstline {
            text-align: center;
            font-size: 20px;
            font-weight: bolder;
            text-transform: uppercase;
        }

        .secondline,
        .thirdline {
            text-align: center;
            font-size: 12px;
            font-weight: bolder;
            text-transform: uppercase;
        }

        @media print {
            @page {
                size: 8.5in 11in;
                margin: .5cm;
            }

            body {
                margin: 0;
                font-family: Arial, sans-serif;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="container" onload="window.print()">
    <!-- Clinic Header -->
    <?php
    $sql = "SELECT * FROM `tblprintheader`";
    $mydb->setQuery($sql);
    $header = $mydb->loadSingleResult();
    ?>
    <div class="col-md-12" style="margin-top: 20px;">
        <div class="firstline"><?php echo isset($header->FirstLine) ? htmlspecialchars($header->FirstLine) : "Medical Clinic"; ?></div>
        <div class="secondline"><?php echo isset($header->SecondLine) ? htmlspecialchars($header->SecondLine) : ""; ?></div>
        <div class="thirdline"><?php echo isset($header->ThirdLine) ? htmlspecialchars($header->ThirdLine) : ""; ?></div>
    </div>
    <hr>

    <!-- Patient Information -->
    <div class="col-md-6">
        <table class="table-client" style="width:100%;">
            <tr>
                <td><strong>Patient Name:</strong></td>
                <td><?php echo htmlspecialchars($prescription->patient_name); ?></td>
            </tr>
            <tr>
                <td><strong>Age:</strong></td>
                <td><?php echo (int)$prescription->Age; ?> years</td>
            </tr>
            <tr>
                <td><strong>Gender:</strong></td>
                <td><?php echo htmlspecialchars($prescription->Sex); ?></td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td><?php echo htmlspecialchars($prescription->address); ?></td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td><?php echo htmlspecialchars($prescription->ContactNo); ?></td>
            </tr>
        </table>
    </div>

    <!-- Prescription Details -->
    <div class="col-md-6">
        <table class="table-client" style="width:100%;">
            <tr>
                <td><strong>Prescription ID:</strong></td>
                <td><?php echo $presc_id; ?></td>
            </tr>
            <tr>
                <td><strong>Issue Date:</strong></td>
                <td><?php echo $created_at; ?></td>
            </tr>
            <tr>
                <td><strong>Doctor:</strong></td>
                <td><?php echo htmlspecialchars($prescription->doctor_name); ?></td>
            </tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr>

    <!-- Medication Table -->
    <div class="col-md-12">
        <table class="tables table table-bordered" style="width:100%; font-size:13px;">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Dosage</th>
                    <th>Timing</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($prescription->medicine_name); ?></td>
                    <td><?php echo htmlspecialchars($prescription->dosage); ?></td>
                    <td><?php echo htmlspecialchars($prescription->timing ?: 'Not specified'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Medical Advice -->
    <?php if (!empty($prescription->medical_advice)): ?>
        <div class="col-md-12" style="margin-top: 20px;">
            <h4 style="text-align: center; margin: 10px 0;">Medical Advice</h4>
            <p style="font-size:13px; line-height:1.6; text-align: justify;">
                <?php echo nl2br(htmlspecialchars($prescription->medical_advice)); ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Clinic Footer -->
    <?php
    $sql = "SELECT * FROM `tblprintfooter`";
    $mydb->setQuery($sql);
    $footer = $mydb->loadSingleResult();
    ?>
    <hr>
    <div class="container">
        <div style="text-transform: uppercase; text-align: center; font-size: 12px;">
            <?php echo isset($footer->FirstLine) ? htmlspecialchars($footer->FirstLine) : ""; ?>
        </div>
        <div style="text-transform: uppercase; text-align: center; font-size: 12px;">
            <?php echo isset($footer->SecondLine) ? htmlspecialchars($footer->SecondLine) : ""; ?>
        </div>
        <div style="text-transform: uppercase; text-align: center; font-size: 12px;">
            <?php echo isset($footer->ThirdLine) ? htmlspecialchars($footer->ThirdLine) : ""; ?>
        </div>
    </div>
</body>
</html>