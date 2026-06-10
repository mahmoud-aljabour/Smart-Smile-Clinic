<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "admin/index.php");
}

$autonum = new Autonumber();
$res = $autonum->set_autonumber('SKU');

// Fetch the service data for editing
global $mydb;
$sku = $_GET['id']; // Assuming ID is SKU
$sql = "SELECT s.*, ag.Description as AgeGroupDesc, ag.ToothCount 
        FROM `tblservices` s 
        LEFT JOIN `tbl_age_groups` ag ON s.AgeGroupID = ag.AgeGroupID 
        WHERE s.SKU = '{$sku}'";
$mydb->setQuery($sql);
$service = $mydb->loadSingleResult();

// If no service found, redirect
if (!$service) {
  message("Service not found!", "error");
  redirect("index.php");
}

// Fetch age groups for dropdown
$sql_age = "SELECT * FROM `tbl_age_groups` ORDER BY MinAge ASC";
$mydb->setQuery($sql_age);
$age_groups = $mydb->loadResultList();
?>

<div class="center wow fadeInDown">
  <h2 class="page-header">Edit Service</h2>
</div>

<form class="form-horizontal span6  wow fadeInDown" action="controller.php?action=edit" method="POST" enctype="multipart/form-data" autocomplete="off" onsubmit="return validate_fields()">

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="SKU">Service ID:</label>

      <div class="col-md-8">
        <input class="form-control input-sm" id="SKU" name="SKU" placeholder="Service ID" type="text" value="<?php echo $service->SKU; ?>" readonly="true">
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="AgeGroupID">Age Group:</label>

      <div class="col-md-8">
        <select class="form-control input-sm" id="AgeGroupID" name="AgeGroupID" required>
          <option value="">Select Age Group</option>
          <?php foreach ($age_groups as $group): ?>
            <option value="<?php echo $group->AgeGroupID; ?>" data-toothcount="<?php echo $group->ToothCount; ?>" 
                    <?php echo ($service->AgeGroupID == $group->AgeGroupID) ? 'selected' : ''; ?>>
              <?php echo $group->Description; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="ToothNumber">Tooth Number:</label>

      <div class="col-md-8">
        <input class="form-control input-sm" id="ToothNumber" name="ToothNumber" placeholder="Tooth Number (Auto-filled)" type="number" value="<?php echo $service->ToothNumber; ?>" min="0" max="32">
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Services">Service:</label>

      <div class="col-md-8">
        <input class="form-control input-sm" id="Services" name="Services" placeholder="Service Name" type="text" value="<?php echo htmlspecialchars($service->Services); ?>" required>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Description">Description:</label>

      <div class="col-md-8">
        <textarea class="form-control input-sm" id="Description" name="Description" placeholder="Description"><?php echo htmlspecialchars($service->Description); ?></textarea>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="OriginalPrice">Price:</label>

      <div class="col-md-8">
        <input class="form-control input-sm" id="OriginalPrice" name="OriginalPrice" placeholder="Price" type="number" step="0.01" value="<?php echo $service->OriginalPrice; ?>" required>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="idno"></label>

      <div class="col-md-8">
        <button class="btn btn-primary btn-md" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Update</button>
        <a href="index.php" class="btn btn-md btn-default"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;<strong>Back</strong></a>
      </div>
    </div>
  </div>

</form>

<script type="text/javascript">
  // Auto-fill ToothNumber based on selected Age Group (on change and on load)
  $(document).ready(function() {
    function updateToothNumber() {
      var selectedOption = $('#AgeGroupID').find(':selected');
      var toothCount = selectedOption.data('toothcount');
      if (toothCount) {
        $('#ToothNumber').val(toothCount);
      } else {
        $('#ToothNumber').val('');
      }
    }

    $('#AgeGroupID').change(updateToothNumber);
    
    // Trigger on load to sync if AgeGroupID is pre-selected
    updateToothNumber();
  });
</script>