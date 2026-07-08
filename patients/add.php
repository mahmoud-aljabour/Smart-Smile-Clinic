<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Add Patient</h1>
    <p class="text-muted small mb-0">Register a new patient in the clinic system</p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<form action="controller.php?action=add" method="POST" autocomplete="off" id="patientForm" class="form-add-page" novalidate>

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-person-vcard"></i> Personal Information
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label" for="Fname">First Name <span class="required">*</span></label>
          <input class="form-control" id="Fname" name="Fname" placeholder="e.g. Ahmed" type="text" autocomplete="off" required>
          <div class="invalid-feedback">First name is required.</div>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="Mname">Middle Name</label>
          <input class="form-control" id="Mname" name="Mname" placeholder="Optional" type="text" autocomplete="off">
        </div>
        <div class="col-md-4">
          <label class="form-label" for="Lname">Last Name <span class="required">*</span></label>
          <input class="form-control" id="Lname" name="Lname" placeholder="e.g. Hassan" type="text" autocomplete="off" required>
          <div class="invalid-feedback">Last name is required.</div>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="Sex">Sex <span class="required">*</span></label>
          <select class="form-select" id="Sex" name="Sex" required>
            <option value="">Select sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
          <div class="invalid-feedback">Please select sex.</div>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="BirthDate">Date of Birth <span class="required">*</span></label>
          <div class="input-group date" data-provide="datepicker" data-date-format="mm/dd/yyyy">
            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
            <input type="text" class="form-control date_picker" id="BirthDate" name="BirthDate" placeholder="mm/dd/yyyy" autocomplete="off" required>
          </div>
          <div class="invalid-feedback">Valid birth date is required (patient must be at least 1 year old).</div>
          <div class="form-hint">
            <i class="bi bi-info-circle"></i>
            <span>Patient must be at least 1 year old.</span>
          </div>
          <div class="age-preview d-none" id="agePreview">
            <i class="bi bi-hourglass-split"></i>
            <span id="agePreviewText"></span>
          </div>
        </div>
        <div class="col-md-12">
          <label class="form-label" for="F_Address">Address <span class="required">*</span></label>
          <textarea class="form-control" id="F_Address" name="F_Address" placeholder="Street, city, area..." rows="2" autocomplete="off" required></textarea>
          <div class="invalid-feedback">Address is required.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-telephone"></i> Contact Information
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="ContactNo">Contact Number <span class="required">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-phone"></i></span>
            <input class="form-control" id="ContactNo" name="ContactNo" placeholder="10-digit number" type="tel" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" autocomplete="off" required>
          </div>
          <div class="invalid-feedback">Contact number must be exactly 10 digits.</div>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" name="save" type="submit">
          <i class="bi bi-check-lg"></i> Save Patient
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
  var form = document.getElementById('patientForm');
  var birthInput = document.getElementById('BirthDate');
  var contactInput = document.getElementById('ContactNo');
  var agePreview = document.getElementById('agePreview');
  var agePreviewText = document.getElementById('agePreviewText');

  function capitalizeWords(value) {
    return value.replace(/\b\w/g, function (ch) { return ch.toUpperCase(); });
  }

  function parseBirthDate(value) {
    var parts = value.split('/');
    if (parts.length !== 3) return null;
    var month = parseInt(parts[0], 10);
    var day = parseInt(parts[1], 10);
    var year = parseInt(parts[2], 10);
    if (!month || !day || !year) return null;
    var date = new Date(year, month - 1, day);
    if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
      return null;
    }
    return date;
  }

  function calculateAge(birthDate) {
    var today = new Date();
    var age = today.getFullYear() - birthDate.getFullYear();
    var monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return age;
  }

  function updateAgePreview() {
    var birth = parseBirthDate(birthInput.value.trim());
    if (!birth) {
      agePreview.classList.add('d-none');
      return;
    }
    var age = calculateAge(birth);
    agePreview.classList.remove('d-none');
    if (age < 1) {
      agePreview.classList.add('is-invalid-age');
      agePreviewText.textContent = 'Age: ' + age + ' year(s) — must be at least 1 year old';
    } else {
      agePreview.classList.remove('is-invalid-age');
      agePreviewText.textContent = 'Calculated age: ' + age + ' year(s)';
    }
  }

  function setFieldState(field, isValid, message) {
    field.classList.toggle('is-invalid', !isValid);
    var feedback = field.closest('.col-md-4, .col-md-6, .col-md-12')?.querySelector('.invalid-feedback');
    if (feedback && message) {
      feedback.textContent = message;
    }
  }

  function validateBirthDate() {
    var value = birthInput.value.trim();
    if (!value) {
      setFieldState(birthInput, false, 'Birth date is required.');
      return false;
    }
    var birth = parseBirthDate(value);
    if (!birth) {
      setFieldState(birthInput, false, 'Use format mm/dd/yyyy.');
      return false;
    }
    var age = calculateAge(birth);
    if (age < 1) {
      setFieldState(birthInput, false, 'Patient must be at least 1 year old.');
      return false;
    }
    setFieldState(birthInput, true);
    return true;
  }

  function validateContact() {
    var value = contactInput.value.replace(/\D/g, '');
    contactInput.value = value;
    if (value.length !== 10) {
      setFieldState(contactInput, false, 'Contact number must be exactly 10 digits.');
      return false;
    }
    setFieldState(contactInput, true);
    return true;
  }

  function shakeField(field) {
    field.classList.add('form-shake');
    setTimeout(function () { field.classList.remove('form-shake'); }, 400);
  }

  ['Fname', 'Lname', 'Mname'].forEach(function (id) {
    var field = document.getElementById(id);
    field.addEventListener('blur', function () {
      field.value = capitalizeWords(field.value.trim());
    });
  });

  contactInput.addEventListener('input', function () {
    contactInput.value = contactInput.value.replace(/\D/g, '').slice(0, 10);
    if (contactInput.classList.contains('is-invalid')) {
      validateContact();
    }
  });

  birthInput.addEventListener('change', function () {
    updateAgePreview();
    if (birthInput.classList.contains('is-invalid')) {
      validateBirthDate();
    }
  });

  birthInput.addEventListener('keyup', updateAgePreview);

  form.addEventListener('submit', function (e) {
    var isValid = true;

    ['Fname', 'Lname', 'Sex', 'F_Address'].forEach(function (id) {
      var field = document.getElementById(id);
      var empty = !field.value.trim();
      field.classList.toggle('is-invalid', empty);
      if (empty) {
        isValid = false;
        shakeField(field);
      }
    });

    if (!validateBirthDate()) {
      isValid = false;
      shakeField(birthInput);
    }

    if (!validateContact()) {
      isValid = false;
      shakeField(contactInput);
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
