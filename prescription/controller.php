<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    // التأكد من أن المستخدم مسجل للدخول
    redirect(web_root . "admin/index.php");
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    
    // يمكنك إضافة حالات أخرى هنا (مثل حفظ/حذف المرضى، الأطباء، إلخ)
    
    // =============== Prescription Management ===============
    case 'save_prescription':
        doSavePrescription(); // للإضافة الجديدة
        break;

    case 'edit_prescription': 
        doUpdatePrescription(); // للتعديل
        break;

    case 'delete_prescription':
        doDeletePrescription();
        break;
        // ===================================================
}

// ===================================================
// =============== Prescription Functions ===============

/**
 * دالة حفظ الوصفة الطبية الجديدة (INSERT)
 * تعتمد على رقم تسلسلي جديد وتزيد العداد بعد الحفظ الناجح.
 */
function doSavePrescription()
{
    global $mydb;

    // 1. التحقق من الصلاحيات والـ POST
    if (!isset($_SESSION['ADMIN_USERID'])) {
        message("You must log in as a doctor.", "error");
        redirect("index.php");
        return;
    }
    // يجب أن يأتي user_id من جلسة تسجيل الدخول، وليس بالضرورة من النموذج
    $user_id = (int)$_SESSION['ADMIN_USERID']; 
    
    $presc_id       = isset($_POST['presc_id']) ? (int)$_POST['presc_id'] : 0;
    
    // إذا لم يكن ID الوصفة صفرًا، فهناك خطأ، هذه الدالة للإضافة فقط
    if ($presc_id != 0) {
         message("Error: Attempting to save a new record in the update function context.", "error");
         redirect("index.php?view=list");
         return;
    }

    // 2. جلب وتجهيز البيانات
    $patient_id     = (int)$_POST['patient_id'];
    $medicine_name  = $_POST['medicine_name'];
    $dosage         = $_POST['dosage'];
    $timing         = $_POST['timing'] ?? '';
    $medical_advice = $_POST['medical_advice'] ?? '';

    // 3. توليد الرقم التسلسلي الجديد
    $autonum = new Autonumber();
    $res = $autonum->set_autonumber('PRESCRIPTION');
    $prescription_no = $res->AUTO ?? 'PRESC_001'; 
    
    // 4. بناء استعلام INSERT
    $sql = "INSERT INTO prescriptions 
            (patient_id, user_id, medicine_name, dosage, timing, medical_advice, prescription_no, created_at)
            VALUES 
            ({$patient_id}, {$user_id}, '{$medicine_name}', '{$dosage}', '{$timing}', '{$medical_advice}', '{$prescription_no}', NOW())";

    // 5. التنفيذ والتحديث
    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        // ⭐ تحديث العداد بعد نجاح الإضافة (لحل مشكلة التكرار)
        $autonum->auto_update('PRESCRIPTION'); 
        message("Prescription saved successfully!", "success");
    } else {
        message("Failed to save prescription. Database Error.", "error"); 
    }
    redirect("index.php?view=list");
}

/**
 * دالة تعديل الوصفة الطبية الموجودة (UPDATE)
 * تحافظ على الرقم التسلسلي الحالي ولا تزيد العداد.
 */
function doUpdatePrescription()
{
    global $mydb;

    // 1. التحقق من الصلاحيات
    if (!isset($_SESSION['ADMIN_USERID'])) {
        message("You must log in to perform this action.", "error");
        redirect("index.php");
        return;
    }
    
    // 2. جلب ID الوصفة
    $presc_id       = isset($_POST['presc_id']) ? (int)$_POST['presc_id'] : 0;

    if ($presc_id <= 0) {
        message("Invalid Prescription ID for update.", "error");
        redirect("index.php?view=list");
        return;
    }

    // 3. جلب وتجهيز البيانات
    $patient_id     = (int)$_POST['patient_id'];
    $user_id        = (int)$_POST['user_id']; 
    $prescription_no = $_POST['prescription_no']; // الحفاظ على الرقم الحالي

    $medicine_name  = $_POST['medicine_name'];
    $dosage         = $_POST['dosage'];
    $timing         = $_POST['timing'] ?? '';
    $medical_advice = $_POST['medical_advice'] ?? '';

    // 4. بناء استعلام UPDATE
    $sql = "UPDATE prescriptions SET 
                patient_id = {$patient_id},
                user_id = {$user_id},
                medicine_name = '{$medicine_name}',
                dosage = '{$dosage}',
                timing = '{$timing}',
                medical_advice = '{$medical_advice}',
                updated_at = NOW()
            WHERE id = {$presc_id}";

    // 5. التنفيذ
    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        message("Prescription updated successfully!", "success");
    } else {
        message("Failed to update prescription. Database Error.", "error"); 
    }
    
    redirect("index.php?view=list");
}

/**
 * دالة حذف الوصفة الطبية (DELETE)
 */
function doDeletePrescription()
{
    global $mydb;

    // التحقق من الصلاحيات (يفترض أن يكون للمسؤول/الطبيب فقط)
    if ($_SESSION['ADMIN_ROLE'] != 'Administrator' && $_SESSION['ADMIN_ROLE'] != 'doctor') {
        message("You do not have permission to delete prescriptions.", "error");
        redirect("index.php?view=list");
        return;
    }

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        message("Invalid prescription ID.", "error");
        redirect("index.php?view=list");
        return;
    }

    $sql = "DELETE FROM prescriptions WHERE id = {$id}";
    $mydb->setQuery($sql);
    $mydb->executeQuery();

    message("Prescription has been deleted successfully!", "success");
    redirect("index.php?view=list");
}