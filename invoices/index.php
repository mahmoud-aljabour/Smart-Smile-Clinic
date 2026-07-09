<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
	redirect(web_root . "index.php");
}



$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title = "Invoices";
$header = $view;
$documentTitle = null;
$viewLabels = array(
	'list' => 'Invoices',
	'add' => 'Create Invoice',
	'edit' => 'Edit Invoice',
	'view' => 'Invoice Details'
);
$breadcrumbLabel = isset($viewLabels[$view]) ? $viewLabels[$view] : ucfirst($view);
switch ($view) {
	case 'list':
		$content    = 'list.php';
		break;

	case 'add':
		$content    = 'add.php';
		$pageScript = __DIR__ . '/add-scripts.php';
		break;

	case 'edit':
		$content    = 'edit.php';
		break;
	case 'view':
		$content    = 'view.php';
		if (!empty($_GET['id'])) {
			$invnoTitle = $_GET['id'];
			$mydb->setQuery("SELECT Patients FROM tblpayments WHERE Class='Invoice' AND InvoiceNo='{$invnoTitle}'");
			$payTitle = $mydb->loadSingleResult();
			if ($payTitle) {
				$documentTitle = invoice_file_label($invnoTitle, $payTitle->Patients ?? '');
			}
		}
		break;

	default:
		$content    = 'list.php';
}
require_once("../theme/templates.php");
