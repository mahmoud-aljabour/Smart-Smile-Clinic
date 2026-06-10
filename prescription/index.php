<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Prescriptions";
$header = $view;

// Check if an ID is provided for viewing a specific prescription
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($view) {
    
    case 'prescriptions':
        // if ($_SESSION['ADMIN_ROLE'] != 'Administrator') {
        //     message("You do not have permission to access this page.", "error");
        //     redirect("index.php");
        // }
        $content = 'list.php';
        $title = "Prescriptions";
        break;

    case 'view':

        $content = 'view_prescription.php';
        $title = "View Prescription";
        break;

    case 'add_prescription': 
        $content = 'add_prescription.php';
        $title = "Add Prescription";
        break;

    case 'edit_prescription':
        
        $content = 'edit_prescription.php';
        $title = "Edit Prescription";
        break;

    // =============== Default ===============
    default:
        $content = 'list.php';
        $title = "Prescriptions";
}

require_once("../theme/templates.php");
