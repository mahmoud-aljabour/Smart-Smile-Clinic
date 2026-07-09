<?php
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
  header('Content-Type: application/json');
  echo json_encode(array());
  exit;
}

$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'all';
$tooth = isset($_GET['tooth']) ? (int)$_GET['tooth'] : 0;

if (!in_array($scope, array('all', 'general', 'tooth'), true)) {
  $scope = 'all';
}

$ageGroupId = null;
if (isset($_SESSION['PatientAgeGroupID']) && $_SESSION['PatientAgeGroupID'] !== null) {
  $ageGroupId = (int)$_SESSION['PatientAgeGroupID'];
}

$services = fetch_filtered_invoice_services(array(
  'search' => $term,
  'age_group_id' => $ageGroupId,
  'scope' => $scope,
  'tooth' => $tooth
));

$suggestions = array();
foreach ($services as $service) {
  $suggestions[] = $service->Services;
}

header('Content-Type: application/json');
echo json_encode(array_values(array_unique($suggestions)));
