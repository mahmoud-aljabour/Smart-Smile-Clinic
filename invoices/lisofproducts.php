<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$search_value = isset($_POST['search_data']) ? trim($_POST['search_data']) : '';
$scope = isset($_POST['scope']) ? trim($_POST['scope']) : 'all';
$tooth = isset($_POST['tooth']) ? (int)$_POST['tooth'] : 0;
$invno = isset($_POST['invno']) ? trim($_POST['invno']) : '';
$currency = $setDefault->default_currency();

if (!in_array($scope, array('all', 'general', 'tooth'), true)) {
  $scope = 'all';
}

$ageGroupId = null;
if (isset($_SESSION['PatientAgeGroupID']) && $_SESSION['PatientAgeGroupID'] !== null) {
  $ageGroupId = (int)$_SESSION['PatientAgeGroupID'];
}

$services = fetch_filtered_invoice_services(array(
  'search' => $search_value,
  'age_group_id' => $ageGroupId,
  'scope' => $scope,
  'tooth' => $tooth
));

$addedSkus = $invno !== '' ? get_invoice_cart_skus($invno) : array();
echo render_invoice_service_picker_rows($services, $addedSkus, $currency);
