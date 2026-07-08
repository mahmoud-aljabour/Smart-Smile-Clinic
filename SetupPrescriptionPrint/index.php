<?php
require_once("../include/initialize.php");
//checkAdmin();
if (!isset($_SESSION['ADMIN_USERID'])) {
	redirect(web_root . "login.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$header = $view;
$title = "Prescription Print";
$breadcrumbLabel = 'Prescription Print Setup';
switch ($view) {
	case 'list':
		$content    = 'list.php';
		break;

	case 'edit':
		$content    = 'edit.php';
		break;
	default:
		$content    = 'edit.php';
}
require_once("../theme/templates.php");
