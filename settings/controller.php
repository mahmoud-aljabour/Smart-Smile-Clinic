<?php
require_once("../include/initialize.php");
// if (!isset($_SESSION['ADMIN_USERID'])){
//    redirect(web_root."admin/index.php");
//   }

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add':
		doInsert();
		break;

	case 'edit':
		doEdit();
		break;

	case 'edit_prescription_print':
		doEditPrescriptionPrint();
		break;

	case 'delete':
		doDelete();
		break;
}

function doEdit()
{
	global $mydb;
	if (isset($_POST['save'])) {
		$sql = "SELECT * FROM `tblprintheader` WHERE 1";
		$mydb->setQuery($sql);
		$cur = $mydb->executeQuery();
		$maxrow = $mydb->num_rows($cur);

		if ($maxrow > 0) {
			$sql = "UPDATE `tblprintheader` SET `FirstLine`='" . str_replace("'", "&#39;",  $_POST['HFirstLine']) . "', `SecondLine`='" . str_replace("'", "&#39;",  $_POST['HSecondLine']) . "', `ThirdLine`='" . str_replace("'", "&#39;",  $_POST['HThirdLine']) . "'";
			$mydb->setQuery($sql);
			$mydb->executeQuery();
		} else {
			$sql = "INSERT INTO `tblprintheader` (`FirstLine`, `SecondLine`,`ThirdLine`) 
						VALUES ('" . str_replace("'", "&#39;",  $_POST['HFirstLine']) . "','" . str_replace("'", "&#39;",  $_POST['HSecondLine']) . "','" . str_replace("'", "&#39;",  $_POST['HThirdLine']) . "')";
			$mydb->setQuery($sql);
			$mydb->executeQuery();
		}

		$sql = "SELECT * FROM `tblprintfooter` WHERE 1";
		$mydb->setQuery($sql);
		$cur = $mydb->executeQuery();
		$maxrow = $mydb->num_rows($cur);

		if ($maxrow > 0) {
			$sql = "UPDATE `tblprintfooter` SET `FirstLine`='" . str_replace("'", "&#39;",  $_POST['FFirstLine']) . "', `SecondLine`='" . str_replace("'", "&#39;",  $_POST['FSecondLine']) . "', `ThirdLine`='" . str_replace("'", "&#39;",  $_POST['FThirdLine']) . "'";
			$mydb->setQuery($sql);
			$mydb->executeQuery();
		} else {
			$sql = "INSERT INTO `tblprintfooter` (`FirstLine`, `SecondLine`,`ThirdLine`) 
						VALUES ('" . str_replace("'", "&#39;",  $_POST['FFirstLine']) . "','" . str_replace("'", "&#39;",  $_POST['FSecondLine']) . "','" . str_replace("'", "&#39;",  $_POST['FThirdLine']) . "')";
			$mydb->setQuery($sql);
			$mydb->executeQuery();
		}

		message("Settings has been updated!", "success");
		redirect("index.php");
	}
}

function doEditPrescriptionPrint()
{
	global $mydb;
	if (isset($_POST['save'])) {
		$sql = "SELECT * FROM `tplprintprescriptions` WHERE 1";
		$mydb->setQuery($sql);
		$cur = $mydb->executeQuery();
		$maxrow = $mydb->num_rows($cur);

		if ($maxrow > 0) {
			$sql = "UPDATE `tplprintprescriptions` SET 
					`header1`='" . str_replace("'", "&#39;", $_POST['HFirstLine']) . "', 
					`header2`='" . str_replace("'", "&#39;", $_POST['HSecondLine']) . "', 
					`header3`='" . str_replace("'", "&#39;", $_POST['HThirdLine']) . "', 
					`footer1`='" . str_replace("'", "&#39;", $_POST['FFirstLine']) . "', 
					`footer2`='" . str_replace("'", "&#39;", $_POST['FSecondLine']) . "', 
					`footer3`='" . str_replace("'", "&#39;", $_POST['FThirdLine']) . "'";
			$mydb->setQuery($sql);
			$mydb->executeQuery();
		} else {
			$sql = "INSERT INTO `tplprintprescriptions` (`header1`, `header2`, `header3`, `footer1`, `footer2`, `footer3`) 
					VALUES ('" . str_replace("'", "&#39;", $_POST['HFirstLine']) . "', 
							'" . str_replace("'", "&#39;", $_POST['HSecondLine']) . "', 
							'" . str_replace("'", "&#39;", $_POST['HThirdLine']) . "', 
							'" . str_replace("'", "&#39;", $_POST['FFirstLine']) . "', 
							'" . str_replace("'", "&#39;", $_POST['FSecondLine']) . "', 
							'" . str_replace("'", "&#39;", $_POST['FThirdLine']) . "')";
			$mydb->setQuery($sql);
			$mydb->executeQuery();
		}

		message("Prescription print settings have been updated!", "success");
		redirect("index.php?view=edit_prescription_print");
	}
}
?>