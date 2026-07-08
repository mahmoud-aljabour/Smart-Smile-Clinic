<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$autonum = new Autonumber();
$res = $autonum->set_autonumber('SKU');

global $mydb;
$sql = "SELECT * FROM `tbl_age_groups` ORDER BY MinAge ASC";
$mydb->setQuery($sql);
$age_groups = $mydb->loadResultList();

$currency = $setDefault->default_currency();
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Add Service</h1>
    <p class="text-muted small mb-0">Define a dental service with age group and tooth mapping</p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=add" method="POST" enctype="multipart/form-data" autocomplete="off" id="serviceForm" class="form-add-page" novalidate>

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-clipboard2-pulse"></i> Service Details
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label" for="SKU">Service ID</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-hash"></i></span>
            <input class="form-control" id="SKU" name="SKU" type="text" value="<?php echo htmlspecialchars($res->AUTO); ?>" readonly>
          </div>
          <div class="form-hint">
            <i class="bi bi-info-circle"></i>
            <span>Auto-generated unique identifier</span>
          </div>
        </div>
        <div class="col-md-8">
          <label class="form-label" for="Services">Service Name <span class="required">*</span></label>
          <input class="form-control" id="Services" name="Services" placeholder="e.g. Root Canal Treatment" type="text" required>
          <div class="invalid-feedback">Service name is required.</div>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="Description">Description</label>
          <textarea class="form-control" id="Description" name="Description" placeholder="Brief description of the service (optional)" rows="2"></textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-emoji-smile"></i> Dental Configuration
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="AgeGroupID">Age Group <span class="required">*</span></label>
          <select class="form-select" id="AgeGroupID" name="AgeGroupID" required>
            <option value="">Select age group</option>
            <?php foreach ($age_groups as $group): ?>
              <option value="<?php echo (int)$group->AgeGroupID; ?>" data-toothcount="<?php echo (int)$group->ToothCount; ?>">
                <?php echo htmlspecialchars($group->Description); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Please select an age group.</div>
          <div class="info-badge d-none" id="ageGroupBadge">
            <i class="bi bi-grid-3x3-gap"></i>
            <span id="ageGroupBadgeText"></span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="ToothNumber">Tooth Number(s) <span class="required">*</span></label>
          <input class="form-control" id="ToothNumber" name="ToothNumber" placeholder="e.g. 0 (all), 1, 2-5" type="text" required>
          <div class="invalid-feedback" id="toothFeedback">Please specify valid tooth number(s).</div>
          <div class="form-hint">
            <i class="bi bi-info-circle"></i>
            <span>Use <strong>0</strong> or <strong>all</strong> for every tooth, or comma-separated values like <strong>1, 3, 5-8</strong></span>
          </div>
          <div class="tooth-preview d-none" id="toothPreview">
            <i class="bi bi-check-circle"></i>
            <span id="toothPreviewText"></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-cash-coin"></i> Pricing
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label" for="OriginalPrice">Price <span class="required">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><?php echo htmlspecialchars($currency); ?></span>
            <input class="form-control" id="OriginalPrice" name="OriginalPrice" placeholder="0.00" type="number" step="0.01" min="0.01" required>
          </div>
          <div class="invalid-feedback">Enter a valid price greater than zero.</div>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit">
          <i class="bi bi-check-lg"></i> Save Service
        </button>
        <a href="index.php" class="btn btn-outline-secondary">
          <i class="bi bi-x-lg"></i> Cancel
        </a>
      </div>
    </div>
  </div>

</form>

<script>
(function () {
  var form = document.getElementById('serviceForm');
  var ageGroupSelect = document.getElementById('AgeGroupID');
  var toothInput = document.getElementById('ToothNumber');
  var priceInput = document.getElementById('OriginalPrice');
  var ageGroupBadge = document.getElementById('ageGroupBadge');
  var ageGroupBadgeText = document.getElementById('ageGroupBadgeText');
  var toothPreview = document.getElementById('toothPreview');
  var toothPreviewText = document.getElementById('toothPreviewText');
  var toothFeedback = document.getElementById('toothFeedback');

  function calculateToothCount(toothStr, maxTeeth) {
    toothStr = toothStr.trim();
    if (!toothStr) {
      return { valid: false, count: 0, error: 'Tooth number is required.' };
    }
    if (toothStr.toLowerCase() === '0' || toothStr.toLowerCase() === 'all') {
      return { valid: true, count: maxTeeth, error: '' };
    }

    var total = 0;
    var parts = toothStr.split(',');

    for (var i = 0; i < parts.length; i++) {
      var part = parts[i].trim().toLowerCase();
      if (!part || part === '0' || part === 'all') continue;

      if (part.indexOf('-') !== -1) {
        var range = part.split('-');
        var start = parseInt(range[0], 10);
        var end = parseInt(range[1], 10);
        if (isNaN(start) || isNaN(end) || start < 1 || end < start || end > maxTeeth || start > maxTeeth) {
          return { valid: false, count: 0, error: 'Invalid range: ' + part + '. Max tooth: ' + maxTeeth };
        }
        total += (end - start + 1);
      } else {
        var num = parseInt(part, 10);
        if (isNaN(num) || num < 1 || num > maxTeeth) {
          return { valid: false, count: 0, error: 'Invalid tooth: ' + part + '. Max: ' + maxTeeth };
        }
        total += 1;
      }
    }

    if (total > maxTeeth) {
      return { valid: false, count: total, error: 'Total exceeds max ' + maxTeeth + ' teeth.' };
    }
    if (total === 0) {
      return { valid: false, count: 0, error: 'Enter at least one valid tooth number.' };
    }

    return { valid: true, count: total, error: '' };
  }

  function getMaxTeeth() {
    if (!ageGroupSelect.value) return 0;
    return parseInt(ageGroupSelect.options[ageGroupSelect.selectedIndex].getAttribute('data-toothcount'), 10) || 0;
  }

  function updateAgeGroupBadge() {
    var maxTeeth = getMaxTeeth();
    if (!ageGroupSelect.value) {
      ageGroupBadge.classList.add('d-none');
      toothInput.placeholder = 'Select age group first';
      return;
    }
    ageGroupBadge.classList.remove('d-none');
    ageGroupBadgeText.textContent = maxTeeth + ' teeth available for this age group';
    toothInput.placeholder = '0 (all), 1-' + maxTeeth + ', e.g. 1, 3, 5-' + maxTeeth;
  }

  function setToothState(isValid, message) {
    toothInput.classList.toggle('is-invalid', !isValid);
    if (message) toothFeedback.textContent = message;

    if (!toothInput.value.trim() || !ageGroupSelect.value) {
      toothPreview.classList.add('d-none');
      return;
    }

    var result = calculateToothCount(toothInput.value, getMaxTeeth());
    if (result.valid) {
      toothPreview.classList.remove('d-none', 'is-invalid-tooth');
      toothPreviewText.textContent = result.count === getMaxTeeth()
        ? 'All teeth selected (' + result.count + ')'
        : result.count + ' tooth/teeth selected';
    } else {
      toothPreview.classList.remove('d-none');
      toothPreview.classList.add('is-invalid-tooth');
      toothPreviewText.textContent = result.error;
    }
  }

  function validateToothNumber() {
    if (!ageGroupSelect.value) {
      ageGroupSelect.classList.add('is-invalid');
      return false;
    }
    ageGroupSelect.classList.remove('is-invalid');

    var result = calculateToothCount(toothInput.value, getMaxTeeth());
    setToothState(result.valid, result.error || 'Please specify valid tooth number(s).');
    return result.valid;
  }

  function validatePrice() {
    var price = parseFloat(priceInput.value);
    var isValid = !isNaN(price) && price > 0;
    priceInput.classList.toggle('is-invalid', !isValid);
    return isValid;
  }

  function shakeField(field) {
    field.classList.add('form-shake');
    setTimeout(function () { field.classList.remove('form-shake'); }, 400);
  }

  ageGroupSelect.addEventListener('change', function () {
    updateAgeGroupBadge();
    ageGroupSelect.classList.remove('is-invalid');
    if (toothInput.value.trim()) {
      setToothState(validateToothNumber(), toothFeedback.textContent);
    }
  });

  toothInput.addEventListener('input', function () {
    if (!ageGroupSelect.value) {
      ageGroupSelect.classList.add('is-invalid');
      setToothState(false, 'Select age group first.');
      return;
    }
    var result = calculateToothCount(toothInput.value, getMaxTeeth());
    setToothState(result.valid, result.error || 'Please specify valid tooth number(s).');
  });

  priceInput.addEventListener('input', function () {
    if (priceInput.classList.contains('is-invalid')) {
      validatePrice();
    }
  });

  document.getElementById('Services').addEventListener('blur', function () {
    this.value = this.value.replace(/\b\w/g, function (ch) { return ch.toUpperCase(); });
  });

  form.addEventListener('submit', function (e) {
    var isValid = true;
    var servicesField = document.getElementById('Services');

    if (!servicesField.value.trim()) {
      servicesField.classList.add('is-invalid');
      isValid = false;
      shakeField(servicesField);
    }

    if (!validateToothNumber()) {
      isValid = false;
      shakeField(toothInput);
    }

    if (!validatePrice()) {
      isValid = false;
      shakeField(priceInput);
    }

    if (!isValid) {
      e.preventDefault();
      var firstInvalid = form.querySelector('.is-invalid');
      if (firstInvalid) {
        firstInvalid.focus();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  });

  form.querySelectorAll('.form-control, .form-select').forEach(function (field) {
    field.addEventListener('input', function () {
      if (field.classList.contains('is-invalid') && field.value.trim()) {
        field.classList.remove('is-invalid');
      }
    });
    field.addEventListener('change', function () {
      if (field.classList.contains('is-invalid') && field.value.trim()) {
        field.classList.remove('is-invalid');
      }
    });
  });
})();
</script>
