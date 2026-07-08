<?php
require_once("../include/initialize.php");
//checkAdmin();
if (!isset($_SESSION['ADMIN_USERID'])) {
	redirect(web_root . "login.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$header = $view;
$title = "Patients";
$viewLabels = array(
	'list' => 'Patients',
	'add' => 'Add Patient',
	'edit' => 'Edit Patient',
	'view' => 'Patient Details'
);
$breadcrumbLabel = isset($viewLabels[$view]) ? $viewLabels[$view] : ucfirst($view);
switch ($view) {
	case 'list':
		$content    = 'list.php';
		break;

	case 'add':
		$content    = 'add.php';
		break;

	case 'edit':
		$content    = 'edit.php';
		break;
	case 'view':
		$content    = 'view.php';
		break;

	default:
		$content    = 'list.php';
}
require_once("../theme/templates.php");
