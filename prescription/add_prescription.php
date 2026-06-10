<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

 
$autonum = new Autonumber();
$res = $autonum->set_autonumber('PRESCRIPTION');  
 

// Fetch patients for dropdown
global $mydb; 
$sql = "SELECT PatientID, CONCAT(Fname, ' ' , Mname , ' ' , Lname) AS patient_name FROM tblpatients ORDER BY Fname ASC";
$mydb->setQuery($sql);
$patients = $mydb->loadResultList();

 
?>
 
<div class="center wow fadeInDown">
    <h2 class="page-header">Add New Prescription</h2>
</div>

<form class="form-horizontal span6 wow fadeInDown" action="controller.php?action=save_prescription" method="POST" enctype="multipart/form-data" autocomplete="off">

    <input type="hidden" name="presc_id" value="0">

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="PrescriptionNo">Prescription ID:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="PrescriptionNo" name="prescription_no" placeholder="Prescription ID" type="text" 
                       value="<?php echo $res->AUTO; ?>" readonly="true">
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
                        <option value="<?= $patient->PatientID ?>"><?= htmlspecialchars($patient->patient_name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

     

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="MedicineName">Medicine Name:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="MedicineName" name="medicine_name" placeholder="Medicine Name" type="text" value="" required>
            </div>
            
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="Dosage">Dosage:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="Dosage" name="dosage" placeholder="e.g., Tablet twice daily" value="" required>
            </div>
            
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="Timing">Timing:</label>
            <div class="col-md-8">
                <input class="form-control input-sm" id="Timing" name="timing" placeholder="e.g., After meal / Before sleep" value="">
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="MedicalAdvice">Medical Advice:</label>
            <div class="col-md-8">
                <textarea class="form-control input-sm" id="MedicalAdvice" name="medical_advice" placeholder="Medical Advice"></textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-8">
            <label class="col-md-4 control-label" for="idno"></label>
            <div class="col-md-8">
                <button class="btn btn-primary btn-md" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save Prescription</button>
                <a href="index.php" class="btn btn-md btn-default"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;<strong>Back</strong></a>
            </div>
        </div>
    </div>

</form>