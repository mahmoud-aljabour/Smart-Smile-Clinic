<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

// ------------------------------------------------------------------
// 1. جلب بيانات الوصفة الطبية الحالية
// ------------------------------------------------------------------

// $presc_id = $_GET['presc_id'];
// $client = new Patients();
// $res = $client->single_patient($PatientID);

 
global $mydb; 

// 1. جلب ID الوصفة من الرابط (Query String) والتحقق منه فوراً
// ⭐ ملاحظة: إذا كان المتغير $presc_id غير مُعرَّف، فسيتم تعيينه إلى 0
$presc_id = isset($_GET['presc_id']) ? (int)$_GET['presc_id'] : 0;

// التحقق من صلاحية ID
if ($presc_id == 0) {
    message("Invalid Prescription ID. Could not find ID in URL.", "error");
    redirect("index.php?view=list");
    // يجب وضع 'exit;' أو 'return;' هنا لضمان عدم استمرار تنفيذ الكود
    exit; 
}


// 2. جلب تفاصيل الوصفة بناءً على ID
// هذا هو السطر الذي كان يسبب الخطأ في الاستعلام
$sql_presc = "SELECT * FROM prescriptions WHERE id = {$presc_id}";
$mydb->setQuery($sql_presc);
$prescription = $mydb->loadSingleResult();

if (!$prescription) {
    message("Prescription not found.", "error");
    redirect("index.php?view=list");
    exit;
}

// 3. جلب المرضى للقائمة المنسدلة
$sql_patients = "SELECT PatientID, CONCAT(Fname, ' ' , Mname , ' ' , Lname) AS patient_name FROM tblpatients ORDER BY Fname ASC";
$mydb->setQuery($sql_patients);
$patients = $mydb->loadResultList();


?>

 
<div class="center wow fadeInDown">
    <h2 class="page-header">Edit Prescription </h2>
</div>

<form class="form-horizontal span6 wow fadeInDown" action="controller.php?action=edit_prescription" method="POST" enctype="multipart/form-data" autocomplete="off">

    <input type="hidden" name="presc_id" value="<?= htmlspecialchars($prescription->id) ?>">
    <input type="hidden" name="prescription_no" value="<?= htmlspecialchars($prescription->prescription_no) ?>">
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($prescription->user_id) ?>">

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="PrescriptionNo">Prescription IDs:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="PrescriptionNo" type="text" 
                       value="<?= htmlspecialchars($prescription->prescription_no) ?>" readonly="true">
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="PatientID">Patient:</label>
            <div class="col-md-8">
                <select class="form-control input-sm" id="PatientID" name="patient_id" required>
                    <option value="">-- Select Patient --</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= $patient->PatientID ?>" 
                            <?= $patient->PatientID == $prescription->patient_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($patient->patient_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="MedicineName">Medicine Name:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="MedicineName" name="medicine_name" placeholder="Medicine Name" type="text" 
                       value="<?= htmlspecialchars($prescription->medicine_name) ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="Dosage">Dosage:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="Dosage" name="dosage" placeholder="e.g., Tablet twice daily" 
                       value="<?= htmlspecialchars($prescription->dosage) ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="Timing">Timing:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="Timing" name="timing" placeholder="e.g., After meal / Before sleep" 
                       value="<?= htmlspecialchars($prescription->timing) ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="MedicalAdvice">Medical Advice:</label>
            <div class="col-md-8">
                <textarea class="form-control input-sm" id="MedicalAdvice" name="medical_advice" placeholder="Medical Advice"><?= htmlspecialchars($prescription->medical_advice) ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="idno"></label>
            <div class="col-md-8">
                <button class="btn btn-success btn-md" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Update Prescription</button>
                <a href="index.php?view=list" class="btn btn-md btn-default"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;<strong>Back</strong></a>
            </div>
        </div>
    </div>

</form>