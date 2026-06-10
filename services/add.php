<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "admin/index.php");
}

$autonum = new Autonumber();
$res = $autonum->set_autonumber('SKU');

// Fetch age groups for dropdown
global $mydb;
$sql = "SELECT * FROM `tbl_age_groups` ORDER BY MinAge ASC";
$mydb->setQuery($sql);
$age_groups = $mydb->loadResultList();
?>

<div class="center wow fadeInDown">
  <h2 class="page-header">Add New Service</h2>
</div>

<form class="form-horizontal span6  wow fadeInDown" action="controller.php?action=add" method="POST" enctype="multipart/form-data" autocomplete="off" onsubmit="return validate_fields();">

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="SKU">Service ID:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" id="SKU" name="SKU" placeholder="Service ID" type="text" value="<?php echo $res->AUTO; ?>" readonly="true">
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
            <option value="<?php echo $group->AgeGroupID; ?>" data-toothcount="<?php echo $group->ToothCount; ?>">
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
        <input class="form-control input-sm" id="ToothNumber" name="ToothNumber" placeholder="Specify tooth number(s), e.g., 0 (all), 1, 2-5" type="number" value="" required>
        <small id="toothError" class="text-danger" style="display:none; font-size: 12px;"></small>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Services">Service:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" id="Services" name="Services" placeholder="Service Name" type="text" value="" required>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Description">Description:</label>
      <div class="col-md-8">
        <textarea class="form-control input-sm" id="Description" name="Description" placeholder="Description"></textarea>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="OriginalPrice">Price:</label>
      <div class="col-md-8">
        <input class="form-control input-sm" id="OriginalPrice" name="OriginalPrice" placeholder="Price" type="number" step="0.01" required>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="idno"></label>
      <div class="col-md-8">
        <button class="btn btn-primary btn-md" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
        <a href="index.php" class="btn btn-md btn-default"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;<strong>Back</strong></a>
      </div>
    </div>
  </div>

</form>

<script type="text/javascript">
  // Function to calculate and validate (updated to handle "0" explicitly)
  function calculateToothCount(toothStr, maxTeeth) {
    toothStr = toothStr.trim(); // Extra trim for safety
    if (toothStr === '' || toothStr === null) return {
      valid: false,
      count: 0,
      error: 'Empty input'
    };

    if (toothStr.toLowerCase() === '0' || toothStr.toLowerCase() === 'all') {
      return {
        valid: true,
        count: maxTeeth,
        error: ''
      }; // "0" or "all" is valid for all teeth
    }

    let total = 0;
    let errorMsg = '';
    const parts = toothStr.split(',').map(part => part.trim().toLowerCase());

    for (let part of parts) {
      if (part === '0' || part === 'all') continue; // Skip, already handled above

      if (part.includes('-')) {
        const [startStr, endStr] = part.split('-');
        const start = parseInt(startStr);
        const end = parseInt(endStr);
        if (isNaN(start) || isNaN(end) || start < 1 || end < start || end > maxTeeth || start > maxTeeth) {
          errorMsg = `Invalid range: ${part}. Max tooth: ${maxTeeth}`;
          return {
            valid: false,
            count: 0,
            error: errorMsg
          };
        }
        total += (end - start + 1);
      } else {
        const num = parseInt(part);
        if (isNaN(num) || num < 1 || num > maxTeeth) {
          errorMsg = `Invalid tooth number: ${part}. Max: ${maxTeeth} (0 allowed for all teeth)`;
          return {
            valid: false,
            count: 0,
            error: errorMsg
          };
        }
        total += 1;
      }
    }

    if (total > maxTeeth) {
      return {
        valid: false,
        count: total,
        error: `Total exceeds max ${maxTeeth} teeth! Count: ${total}`
      };
    }

    return {
      valid: true,
      count: total,
      error: ''
    };
  }

  // Show/hide error
  function showError(msg, show) {
    const errorEl = document.getElementById('toothError');
    errorEl.textContent = msg;
    errorEl.style.display = show ? 'block' : 'none';
    document.getElementById('ToothNumber').style.borderColor = show ? 'red' : '';
  }

  // Validation on form submit
  function validateToothNumber() {
    const ageGroupSelect = document.getElementById('AgeGroupID');
    const toothInput = document.getElementById('ToothNumber');

    if (!ageGroupSelect.value) {
      alert('Please select an Age Group first!');
      ageGroupSelect.focus();
      return false;
    }

    const maxTeeth = parseInt(ageGroupSelect.options[ageGroupSelect.selectedIndex].getAttribute('data-toothcount'));
    const toothStr = toothInput.value;

    if (!toothStr || toothStr.trim() === '') {
      alert('Please specify Tooth Number!');
      toothInput.focus();
      return false;
    }

    const result = calculateToothCount(toothStr, maxTeeth);
    if (!result.valid) {
      alert('Cannot save: ' + result.error + '. Reason: Tooth number exceeds the age group limit. Stay on page to correct.');
      toothInput.focus();
      return false;
    }

    return true;
  }

  // Main validation
  function validate_fields() {
    return validateToothNumber();
  }

  // Real-time validation
  document.addEventListener('DOMContentLoaded', function() {
    const ageGroupSelect = document.getElementById('AgeGroupID');
    const toothInput = document.getElementById('ToothNumber');

    ageGroupSelect.addEventListener('change', function() {
      const maxTeeth = parseInt(this.options[this.selectedIndex].getAttribute('data-toothcount'));
      if (this.value) {
        toothInput.placeholder = `Tooth numbers 0 (all), 1-${maxTeeth}, e.g., 1, 2-${maxTeeth}`;
      } else {
        toothInput.placeholder = 'Specify tooth number(s), e.g., 1, 2-5, or All';
      }
      showError('', false);
    });

    toothInput.addEventListener('input', function() {
      const selectedOption = ageGroupSelect.options[ageGroupSelect.selectedIndex];
      if (selectedOption.value) {
        const maxTeeth = parseInt(selectedOption.getAttribute('data-toothcount'));
        const toothStr = this.value;
        const result = calculateToothCount(toothStr, maxTeeth);
        showError(result.error, !result.valid);
      } else {
        showError('Select age group first', false);
      }
    });
  });
</script>