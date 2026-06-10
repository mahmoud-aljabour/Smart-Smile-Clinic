<?php
require_once("../include/initialize.php");

// فحص الجلسة
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "login.php");
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
    
    default:
        break;
}

// تضمين الـ view
// if ($action == 'edit_prescription_print') {
//     include_once "edit.php";
// }

function doEdit()
{
    global $mydb;

    if (isset($_POST['save'])) {

        // هرب القيم
        $HFirstLine = str_replace("'", "&#39;", $_POST['HFirstLine']);
        $HSecondLine = str_replace("'", "&#39;", $_POST['HSecondLine']);
        $HThirdLine = str_replace("'", "&#39;", $_POST['HThirdLine']);
        $FFirstLine = str_replace("'", "&#39;", $_POST['FFirstLine']);
        $FSecondLine = str_replace("'", "&#39;", $_POST['FSecondLine']);
        $FThirdLine = str_replace("'", "&#39;", $_POST['FThirdLine']);

        // التحقق من السجل
        $sql = "SELECT * FROM `tplprintprescriptions` LIMIT 1";
        $mydb->setQuery($sql);
        $cur = $mydb->executeQuery();
        $maxrow = $mydb->num_rows($cur);

        if ($maxrow > 0) {
            $sql = "UPDATE `tplprintprescriptions` SET 
                    `header1`='" . $HFirstLine . "', 
                    `header2`='" . $HSecondLine . "', 
                    `header3`='" . $HThirdLine . "', 
                    `footer1`='" . $FFirstLine . "', 
                    `footer2`='" . $FSecondLine . "', 
                    `footer3`='" . $FThirdLine . "' 
                    LIMIT 1";
            $mydb->setQuery($sql);
            $mydb->executeQuery();
        } else {
            $sql = "INSERT INTO `tplprintprescriptions` (`header1`, `header2`, `header3`, `footer1`, `footer2`, `footer3`) 
                    VALUES ('" . $HFirstLine . "', '" . $HSecondLine . "', '" . $HThirdLine . "', 
                            '" . $FFirstLine . "', '" . $FSecondLine . "', '" . $FThirdLine . "')";
            $mydb->setQuery($sql);
            $mydb->executeQuery();
        }

        message("Settings has been updated!", "success");

        // redirect إلى الـ form عشان يعرض التحديث
        redirect(web_root . "SetupPrescriptionPrint/");
    }
}
?>