<?php
require_once("../include/initialize.php");
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

    case 'addbulk':
        addBulk();
        break;

    case 'deletebulk':
        deleteBulk();
        break;
}

function doInsert()
{
    global $mydb;
    if (isset($_POST['save'])) {

        // Clean input: Capitalize first letter for service name and description
        $Services_clean = ucwords(strtolower(trim($_POST['Services'])));
        $Description_clean = ucwords(strtolower(trim($_POST['Description'])));

        $ageGroupID = (int)$_POST['AgeGroupID'];
        $ToothNumber = trim($_POST['ToothNumber']); // Use manual input directly

        // Server-side: Get maxTeeth for age group
        $sql_age = "SELECT ToothCount FROM `tbl_age_groups` WHERE `AgeGroupID` = {$ageGroupID}";
        $mydb->setQuery($sql_age);
        $ageData = $mydb->loadSingleResult();
        $maxTeeth = ($ageData) ? (int)$ageData->ToothCount : 0;

        // Server-side validation for ToothNumber count (updated to allow 0)
        $result = calculateToothCountServer($ToothNumber, $maxTeeth);
        if (!$result['valid']) {
            message("Cannot save: " . $result['error'], "error");
            redirect("index.php?view=add");
            die(); // Stop execution here!
        }

        $sql = "SELECT * FROM `tblservices` WHERE `SKU`='" . $_POST['SKU'] . "'";
        $mydb->setQuery($sql);
        $res = $mydb->executeQuery();
        $maxrow = $mydb->num_rows($res);

        if ($maxrow > 0) {

            echo '<script>alert("SKU already exists! It will automatically assign a new and unique SKU to avoid duplication of Services.")</script>';

            $sql = "SELECT SKU FROM `tblservices`";
            $mydb->setQuery($sql);
            $pro = $mydb->loadResultList();
            foreach ($pro as $row) {
                $str[] = (int) filter_var($row->SKU, FILTER_SANITIZE_NUMBER_INT);
            }

            $incvalue = json_encode(max($str)) + 1;

            $sql = "UPDATE `tblautonumbers` SET `AUTOEND`='{$incvalue}' WHERE `AUTOKEY`='SKU'";
            $mydb->setQuery($sql);
            $res = $mydb->executeQuery();

            $autonum = new Autonumber();
            $res = $autonum->set_autonumber('SKU');

            $serviceid = $res->AUTO;
        } else {
            $serviceid = $_POST['SKU']; 
        }

        // Check for duplicate service including AgeGroupID and ToothNumber
        $sql = "SELECT * FROM `tblservices` WHERE `ToothNumber`='{$ToothNumber}' AND `Services`='{$Services_clean}' AND `AgeGroupID`='{$ageGroupID}'";
        $mydb->setQuery($sql);
        $res = $mydb->executeQuery();
        $maxrow = $mydb->num_rows($res);

        if ($maxrow > 0) {
            echo '<script>alert("Service already exists for this age group and tooth number!")</script>';
            redirect("index.php?view=add");
            die();
        } else {

            $service = new Services();
            $service->SKU 				= $serviceid;
            $service->ToothNumber 		= $ToothNumber; // Use manual input
            $service->Services 			= $Services_clean;
            $service->Description		= $Description_clean;
            $service->AgeGroupID		= $ageGroupID;
            $service->OriginalPrice		= $_POST['OriginalPrice'];
            $service->create();

            $autonum = new Autonumber();
            $autonum->auto_update('SKU');

            message("New Service created successfully!", "success");
            redirect("index.php");
        }
    }
}

function doEdit()
{
    global $mydb;
    if (isset($_POST['save'])) {

        // Clean input: Capitalize first letter for service name and description
        $Services_clean = ucwords(strtolower(trim($_POST['Services'])));
        $Description_clean = ucwords(strtolower(trim($_POST['Description'])));

        $ageGroupID = (int)$_POST['AgeGroupID'];
        $ToothNumber = trim($_POST['ToothNumber']); // Use manual input

        // Server-side validation for edit (updated to allow 0)
        $sql_age = "SELECT ToothCount FROM `tbl_age_groups` WHERE `AgeGroupID` = {$ageGroupID}";
        $mydb->setQuery($sql_age);
        $ageData = $mydb->loadSingleResult();
        $maxTeeth = ($ageData) ? (int)$ageData->ToothCount : 0;

        $result = calculateToothCountServer($ToothNumber, $maxTeeth);
        if (!$result['valid']) {
            message("Cannot save: " . $result['error'], "error");
            redirect("index.php?view=edit&id=" . $_POST['SKU']);
            die(); // Stop execution
        }

        // Check for duplicate service including AgeGroupID and ToothNumber (exclude current SKU)
        $sql = "SELECT * FROM `tblservices` WHERE `ToothNumber`='{$ToothNumber}' AND `Services`='{$Services_clean}' AND `AgeGroupID`='{$ageGroupID}' AND `SKU` != '" . $_POST['SKU'] . "'";
        $mydb->setQuery($sql);
        $res = $mydb->executeQuery();
        $maxrow = $mydb->num_rows($res);

        if ($maxrow > 0) {
            message("Service already exists for this age group and tooth number!", "error");
            redirect("index.php?view=edit&id=" . $_POST['SKU']);
            die();
        } else {

            $service = new Services();
            $service->ToothNumber 		= $ToothNumber; // Use manual input
            $service->Services 			= $Services_clean;
            $service->Description		= $Description_clean;
            $service->AgeGroupID		= $ageGroupID;
            $service->OriginalPrice		= $_POST['OriginalPrice'];
            $service->update($_POST['SKU']);

            message("Service has been updated successfully!", "success");
            redirect("index.php");
        }
    }
}

function doDelete()
{
    global $mydb;

    $id = $_GET['id'];

    $service = new Services();
    $service->delete($id);

    message("Service deleted successfully!", "success");
    redirect('index.php');
}

function addBulk()
{
    global $mydb;
    global $setDefault;

    $sku = $_POST['SKU'];

    $sql = "INSERT INTO `tblbulkpricing` (`SKU`, `QTY`, `Price`, `ModifiedDate`) 
            Values ('" . $_POST['SKU'] . "','" . $_POST['QTY'] . "','" . $_POST['Price'] . "',Now())";
    $mydb->setQuery($sql);
    $mydb->executeQuery();

    if (isset($_GET['modal'])) {
        $discounted_price = 0;

        $sql = "SELECT * FROM `tblbulkpricing` B,tblservices P WHERE B.SKU=P.SKU AND P.SKU='{$sku}' ORDER BY QTY ASC";
        $mydb->setQuery($sql);
        $cur = $mydb->loadResultList();

        foreach ($cur as $result) {

            $discounted_price = $result->Price / $result->QTY;
            echo '<tr>';
            echo '<td align="center">    
                        <a title="Remove" href="#"  data-id="' . $result->BulkID . '"  class="btn btn-danger btn-xs del  ">
                        <span class="fa fa-trash-o fw-fa"></span></a> 
                     </td>';
            echo '<td>' . $result->QTY . '</td>';
            echo '<td> ' . $setDefault->default_currency() . ' ' . number_format($result->Price, 2) . '</td>';
            echo '<td> ' . $setDefault->default_currency() . ' ' . number_format($discounted_price, 2) . '</td>';
            echo '<td>' . $result->Unit . '</td>';

            echo '</tr>';
        }
    } else {

        message("Bulk price created successfully.", "success");
        redirect('index.php?view=bulk&id=' . $_POST['SKU']);
    }
}

function deleteBulk()
{
    global $mydb;
    global $setDefault;
    $id = 0;

    if (isset($_GET['modal'])) {
        $id = $_POST['id'];

        $sql = "SELECT * FROM `tblbulkpricing` WHERE BulkID=" . $id;
        $mydb->setQuery($sql);
        $res = $mydb->loadSingleResult();

        $sku = $res->SKU;
    } else {
        $id = $_GET['id'];
    }

    $sql = "DELETE FROM `tblbulkpricing` WHERE BulkID=" . $id;
    $mydb->setQuery($sql);
    $mydb->executeQuery();

    if (isset($_GET['modal'])) {
        $discounted_price = 0;

        $sql = "SELECT * FROM `tblbulkpricing` B,tblservices P WHERE B.SKU=P.SKU AND P.SKU='{$sku}' ORDER BY QTY ASC";
        $mydb->setQuery($sql);
        $cur = $mydb->loadResultList();

        foreach ($cur as $result) {

            $discounted_price = $result->Price / $result->QTY;
            echo '<tr>';
            echo '<td align="center">    
                        <a title="Remove" href="#"  data-id="' . $result->BulkID . '"  class="btn btn-danger btn-xs del  ">
                        <span class="fa fa-trash-o fw-fa"></span></a> 
                     </td>';
            echo '<td>' . $result->QTY . '</td>';
            echo '<td> ' . $setDefault->default_currency() . ' ' . number_format($result->Price, 2) . '</td>';
            echo '<td> ' . $setDefault->default_currency() . ' ' . number_format($discounted_price, 2) . '</td>';
            echo '<td>' . $result->Unit . '</td>';

            echo '</tr>';
        }
    } else {
        message("Bulk price deleted successfully.", "info");
        redirect('index.php?view=bulk&id=' . $_GET['SKU']);
    }
}

// Helper function for server-side tooth count (updated to allow 0)
function calculateToothCountServer($toothStr, $maxTeeth) {
    $toothStr = trim($toothStr);
    // Do not use empty() — in PHP empty('0') is true, but 0 means "all teeth"
    if ($toothStr === '') {
        return ['valid' => false, 'error' => 'Tooth number is required.'];
    }

    if (strtolower($toothStr) === '0' || strtolower($toothStr) === 'all') {
      return ['valid' => true, 'error' => '']; // "0" or "all" is valid for all teeth
    }
    
    $total = 0;
    $error = '';
    $parts = explode(',', $toothStr);
    foreach ($parts as $part) {
        $part = trim(strtolower($part));
        if ($part === '0' || $part === 'all') continue; // Skip, already handled
        
        if (strpos($part, '-') !== false) {
            $range = explode('-', $part);
            $start = (int)trim($range[0]);
            $end = (int)trim($range[1]);
            if ($start < 1 || $end < $start || $end > $maxTeeth || $start > $maxTeeth) {
                $error = "Invalid range {$part}. Max tooth: {$maxTeeth}";
                return ['valid' => false, 'error' => $error];
            }
            $total += ($end - $start + 1);
        } else {
            $num = (int)$part;
            if (is_nan($num) || ($num < 1 && $num !== 0) || $num > $maxTeeth) { // Allow 0
                $error = "Invalid tooth number {$part}. Max: {$maxTeeth} (0 allowed for all teeth)";
                return ['valid' => false, 'error' => $error];
            }
            if ($num > 0) $total += 1; // Only add if >0
        }
    }
    
    if ($total > $maxTeeth) {
        return ['valid' => false, 'error' => "Total exceeds max {$maxTeeth} teeth"];
    }
    
    return ['valid' => true, 'error' => ''];
}
?>

<script type="text/javascript">
    $(".del").click(function() {

        var id = $(this).data("id");

        $.ajax({
            type: "POST",
            url: "controller.php?action=deletebulk&modal=true",
            dataType: "text",
            data: {
                id: id
            },
            success: function(data) {
                $("#loadtable").html(data);
                $("#QTY").val("");
                $("#Price").val("");
                $("#QTY").focus();
            }
        });
    });
</script>