<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}


$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'add':
        doInsert();
        break;

    case 'edit':
        doEdit();
        break;

    case 'delete':
        doDelete();
        break;

    case 'insertevent':
        doInsertvents();
        break;
    case 'updateevent':
        doUpdateEvents();
        break;

    case 'deleteevent':
        doDeleteEvents();
        break;

    case 'loadevent':
        doLoadEvents();
        break;


    case 'getevent':  // 🟢 لجلب بيانات الموعد للتعديل 🟢
        doGetEvent();
        break;
}

// ==========================================================
// وظائف إدارة سجلات المرضى (Patients)
// ==========================================================

function doInsert()
{
    global $mydb;

    // 1. التحقق من تكرار الاسم
    $sql = "SELECT * FROM tblpatients WHERE Fname='" . trim($_POST['Fname']) . "' AND Lname='" . trim($_POST['Lname']) . "'  AND Mname='" . trim($_POST['Mname']) . "'";
    $mydb->setQuery($sql);
    $cur = $mydb->executeQuery();
    $maxrow = $mydb->num_rows($cur);

    if ($maxrow > 0) {

        if (isset($_GET['modal'])) {
            echo '<script>alert("Name already exist.");</script>';
        } else {
            message("Name already exist!", "error");
            redirect("index.php?view=add");
        }
    } else {

        // 🟢 تفعيل حفظ بيانات المريض 🟢
        $patient = new Patients();
        $patient->Fname      = trim($_POST['Fname']);
        $patient->Mname      = trim($_POST['Mname']);
        $patient->Lname      = trim($_POST['Lname']);
        $patient->F_Address  = $_POST['F_Address'];
        $patient->Sex        = $_POST['Sex'];
        $patient->Age        = $_POST['Age'];
        $patient->ContactNo  = $_POST['ContactNo'];
        $patient->create(); // تنفيذ أمر الحفظ


        if (isset($_GET['modal'])) {
            echo "New Patient created successfully!";
        } else {
            message("New Patient created successfully!", "success");
            redirect("index.php");
        }
    }
}

function doEdit()
{
    if (isset($_POST['save'])) {

        // 🟢 تفعيل تعديل بيانات المريض 🟢
        $patient = new Patients();
        $patient->Fname      = $_POST['Fname'];
        $patient->Mname      = $_POST['Mname'];
        $patient->Lname      = $_POST['Lname'];
        $patient->F_Address  = $_POST['F_Address'];
        $patient->Sex        = $_POST['Sex'];
        $patient->Age        = $_POST['Age'];
        $patient->ContactNo  = $_POST['ContactNo'];
        $patient->update($_POST['PatientID']); // تنفيذ أمر التعديل

        message("Patient has been updated!", "success");
        redirect("index.php");
    }
}


function doDelete()
{
    global $mydb;

    $id = $_GET['id'];

    // حذف المريض وسجل المستخدم المرتبط به
    $patient = new Patients();
    $patient->delete($id);

    $user = new User();
    $user->delete($id);

    // حذف السجلات المرتبطة من الجداول الأخرى
    $sql = "DELETE FROM tblinvoice WHERE PatientID=" . $id;
    $mydb->setQuery($sql);
    $mydb->executeQuery();

    $sql = "DELETE FROM tblpayments WHERE PatientID=" . $id;
    $mydb->setQuery($sql);
    $mydb->executeQuery();

    // يجب إضافة حذف من جدول tblappointments بناءً على PatientID إذا كان الجدول يدعم ذلك.
    // نستخدم AppoinmentID حالياً، لكن يجب عليك ربط الموعد بـ PatientID في نظامك.

    message("Patient has been Deleted!", "info");
    redirect('index.php');
}

// ==========================================================
// وظائف إدارة المواعيد (Appointments) - باستخدام tblappointments
// ==========================================================

function doInsertvents()
{
    global $mydb;

    // 1. الحصول على البيانات
    $Fname      = trim(isset($_POST['Fname']) ? $_POST['Fname'] : '');
    $Mname      = trim(isset($_POST['Mname']) ? $_POST['Mname'] : '');
    $Lname      = trim(isset($_POST['Lname']) ? $_POST['Lname'] : '');
    $Services   = isset($_POST['Services']) ? $_POST['Services'] : '';
    $A_Date     = isset($_POST['A_Date']) ? $_POST['A_Date'] : '';
    $A_Time     = isset($_POST['A_Time']) ? $_POST['A_Time'] : '';

    // التحقق من الحقول الإلزامية
    if (empty($Fname) || empty($Lname) || empty($Services) || empty($A_Date) || empty($A_Time)) {
        echo 'Please select the patient, service, and enter the time.';
        return;
    }

    // 🟢 التحقق الجديد في PHP: منع الحجز يوم الجمعة 🟢
    $selectedDate = new DateTime($A_Date);
    if ($selectedDate->format('N') == 5) { // 5 = Friday (ISO-8601: 1=Mon, 5=Fri)
        echo 'Sorry, Friday is not available for bookings.';

        return;
    }

    // 🟢 التحقق الجديد: دعم 12 ساعة مع AM/PM + تحويل إلى 24 ساعة 🟢
    $time_lower = strtolower($A_Time);  // للتعامل مع 'pm' أو 'PM'
    if (strpos($time_lower, 'pm') !== false) {
        // استخراج الساعة والدقائق قبل 'pm'
        $time_clean = str_replace(' pm', '', $A_Time);  // أو 'PM'
        $timeParts = explode(':', $time_clean);
        $hour12 = (int)$timeParts[0];
        $minute = isset($timeParts[1]) ? (int)$timeParts[1] : 0;
        $hour = $hour12 + 12;  // تحويل PM إلى 24 ساعة (مثال: 3 PM = 15)
    } else {
        // AM أو تنسيق 24 ساعة
        $timeParts = explode(':', $A_Time);
        $hour = (int)$timeParts[0];
        $minute = isset($timeParts[1]) ? (int)$timeParts[1] : 0;
    }

    // الآن تحقق من الساعة بالـ24 ساعة
    if ($hour < 8 || $hour > 16) {
        echo 'The time must be between 8 AM and 4 PM.';
        return;
    }

    // إضافي: تحقق من الدقائق (اختياري، لو عايز تمنع دقائق غريبة)
    if ($minute > 59 || $minute < 0) {
        echo 'Invalid minutes.';
        return;
    }

    // 🟢 الشرط الأول: منع الحجز في الماضي 🟢
    $appointment_datetime_str = $A_Date . ' ' . $A_Time;

    try {
        $now = new DateTime('now', new DateTimeZone('Asia/Gaza'));
        $appointment_datetime = new DateTime($appointment_datetime_str, new DateTimeZone('Asia/Gaza'));

        // المقارنة: إذا كان موعد الحجز أصغر من الوقت الحالي (أي في الماضي)
        if ($appointment_datetime < $now) {
            echo 'Cannot book an appointment in the past. Please select a future time.';
            return;
        }
    } catch (Exception $e) {
        // في حال حدوث خطأ في تنسيق التاريخ
        echo 'Error in date or time format.';
        return;
    }

    // 🟢 الشرط الثاني: منع حجز آخر موعد في نفس التاريخ والوقت (تجنب التداخل) 🟢
    $sql_overlap = "SELECT * FROM tblappointments WHERE 
        A_Date = '{$A_Date}' AND A_Time = '{$A_Time}' AND Status='Approved'";
    $mydb->setQuery($sql_overlap);
    $cur_overlap = $mydb->executeQuery();
    $maxrow_overlap = $mydb->num_rows($cur_overlap);

    if ($maxrow_overlap > 0) {
        echo 'This appointment is already booked at this time. Please choose another time.';
        return;
    }

    // 4. الإضافة في حال النجاح
    $sql = "INSERT INTO `tblappointments` (`Fname`, `Mname`, `Lname`, `Services`, `A_Date`, `A_Time`, `Status`) 
             VALUES ('{$Fname}', '{$Mname}', '{$Lname}', '{$Services}', '{$A_Date}', '{$A_Time}', 'Approved')";

    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        // echo "success"; // 🔴 غير هذا إلى "success" بدل "تم حجز الموعد بنجاح!"
        message("The appointment has been booked successfully.!", "success");
        redirect("index.php");
    } else {
        echo "Failed to book the appointment.";
    }
}

function doUpdateEvents()
{
    global $mydb;

    // استخدام AppoinmentID بدلاً من id
    $AppoinmentID = (int)$_POST['id'];

    $Fname    = trim(isset($_POST['Fname']) ? $_POST['Fname'] : '');
    $Mname    = trim(isset($_POST['Mname']) ? $_POST['Mname'] : '');
    $Lname    = trim(isset($_POST['Lname']) ? $_POST['Lname'] : '');
    $Services = isset($_POST['Services']) ? $_POST['Services'] : '';
    $A_Date   = isset($_POST['A_Date']) ? $_POST['A_Date'] : '';
    $A_Time   = isset($_POST['A_Time']) ? $_POST['A_Time'] : '';
    $Status   = isset($_POST['Status']) ? $_POST['Status'] : 'Approved';

    // نفس التحققات كما في doInsertvents()
    if (empty($Fname) || empty($Lname) || empty($Services) || empty($A_Date) || empty($A_Time)) {
        echo 'Please select the patient, service, and enter the time.';
        return;
    }

    $selectedDate = new DateTime($A_Date);
    if ($selectedDate->format('N') == 5) {
        echo 'Sorry, Friday is not available for bookings.';
        return;
    }

    $timeParts = explode(':', $A_Time);
    $hour = (int)$timeParts[0];
    if ($hour < 8 || $hour > 16) {
        echo 'The time must be between 8 AM and 4 PM.';
        return;
    }

    $appointment_datetime_str = $A_Date . ' ' . $A_Time;
    try {
        $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $appointment_datetime = new DateTime($appointment_datetime_str, new DateTimeZone('Asia/Manila'));
        if ($appointment_datetime < $now) {
            echo 'Cannot book an appointment in the past. Please select a future time.';
            return;
        }
    } catch (Exception $e) {
        echo 'Error in date or time format.';
        return;
    }

    // تحقق التداخل (باستثناء السجل الحالي)
    $sql_overlap = "SELECT * FROM tblappointments WHERE 
        A_Date = '{$A_Date}' AND A_Time = '{$A_Time}' AND Status='Approved' AND AppoinmentID != {$AppoinmentID}";
    $mydb->setQuery($sql_overlap);
    $cur_overlap = $mydb->executeQuery();
    $maxrow_overlap = $mydb->num_rows($cur_overlap);

    if ($maxrow_overlap > 0) {
        echo 'This appointment is already booked at this time. Please choose another time.';
        return;
    }

    // تحديث الأعمدة واسم الجدول
    $sql = "UPDATE tblappointments SET 
        Fname='{$Fname}', 
        Mname='{$Mname}', 
        Lname='{$Lname}', 
        Services='{$Services}', 
        A_Date='{$A_Date}', 
        A_Time='{$A_Time}', 
        Status='{$Status}' 
        WHERE AppoinmentID={$AppoinmentID}";

    $mydb->setQuery($sql);
    if ($mydb->executeQuery()) {
        // echo "success";
        message("The appointment has been updated successfully..!", "success");
        redirect("index.php");
    } else {
        echo "Failed to update the appointment.";
    }
}

function doDeleteEvents()
{
    global $mydb;

    // استخدام AppoinmentID بدلاً من id
    $AppoinmentID = (int)$_POST['id'];

    // تغيير اسم الجدول واستخدام AppoinmentID
    $sql = "DELETE FROM tblappointments WHERE AppoinmentID=" . $AppoinmentID;
    $mydb->setQuery($sql);
    $mydb->executeQuery();
    // echo "success";
    message("The appointment was successfully deleted.!", "info");
    redirect("index.php");
}

function doLoadEvents()
{
    global $mydb;

    // تغيير اسم الجدول
    $sql = "SELECT * FROM tblappointments ";
    $mydb->setQuery($sql);
    $result = $mydb->loadResultList();

    $data = array();
    foreach ($result as $row) {
        // تنسيق البيانات لتناسب العرض في التقويم (FullCalendar مثلاً)
        $full_name = trim($row->Fname . ' ' . $row->Lname);
        $title_display = $full_name . ' - ' . $row->Services . ' (' . $row->A_Time . ')';

        $data[] = array(
            'id'    => $row->AppoinmentID, // المعرّف
            'title' => $title_display,      // العنوان الظاهر في التقويم
            'start' => $row->A_Date . ' ' . $row->A_Time, // تاريخ ووقت البداية
            'end'   => $row->A_Date . ' ' . $row->A_Time  // لكي يظهر كنقطة (Event dot)
        );
    }

    echo json_encode($data);
}

// 🟢 دالة جديدة: جلب بيانات موعد واحد للتعديل مع debug 🟢
function doGetEvent()
{
    global $mydb;

    $id = (int)$_POST['id'];

    error_log("doGetEvent called with ID: " . $id); // debug: شوف في error.log

    $sql = "SELECT * FROM tblappointments WHERE AppoinmentID = " . $id;
    $mydb->setQuery($sql);
    $cur = $mydb->loadResultList(); // استخدم loadResultList إذا loadSingleResult غير موجود

    if ($cur && count($cur) > 0) {
        $result = $cur[0]; // أول سجل
        echo json_encode($result);
    } else {
        error_log("No record found for ID: " . $id); // debug
        echo json_encode(null);
    }
}
