<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Prescriptions";
$header = $view;
$viewLabels = array(
    '' => 'Prescriptions',
    'list' => 'Prescriptions',
    'prescriptions' => 'Prescriptions',
    'add_prescription' => 'Add Prescription',
    'edit_prescription' => 'Edit Prescription',
    'view' => 'View Prescription'
);
$breadcrumbLabel = isset($viewLabels[$view]) ? $viewLabels[$view] : ucfirst(str_replace('_', ' ', $view));

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
        if (!empty($_GET['id'])) {
            $prescIdTitle = (int)$_GET['id'];
            $mydb->setQuery("SELECT pr.prescription_no, pr.id,
                CONCAT(p.Fname, ' ', p.Mname, ' ', p.Lname) AS patient_name
                FROM prescriptions pr
                JOIN tblpatients p ON pr.patient_id = p.PatientID
                WHERE pr.id = '{$prescIdTitle}'");
            $prescTitle = $mydb->loadSingleResult();
            if ($prescTitle) {
                $prescNo = !empty($prescTitle->prescription_no)
                    ? $prescTitle->prescription_no
                    : ('PRESC_' . $prescTitle->id);
                $documentTitle = invoice_file_label($prescNo, trim($prescTitle->patient_name));
            }
        }
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
