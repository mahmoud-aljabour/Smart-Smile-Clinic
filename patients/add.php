<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$msg = ''; // Initialize message variable

if (isset($_POST['save'])) {
  // Validate birth date before submitting to controller
  $birthDate = $_POST['BirthDate']; // Format: m/d/Y from datepicker
  $today = date('Y-m-d'); // Current date: 2025-10-03

  // Convert to Y-m-d for calculation
  $birthDateFormatted = DateTime::createFromFormat('m/d/Y', $birthDate);
  if ($birthDateFormatted) {
    $birthDateYmd = $birthDateFormatted->format('Y-m-d');
    $currentDate = new DateTime($today);
    $birthObj = new DateTime($birthDateYmd);
    $age = $birthObj->diff($currentDate)->y; // Age in full years

    if ($age < 1) {
      $msg = "Cannot add a patient under 1 year old. Current age: " . $age . " year(s). Please enter an earlier birth date.";
      // Stop and do not submit to controller
    } else {
      // Valid age - allow submission to controller (no redirect, let form post)
      // You can add more processing here if needed
    }
  } else {
    $msg = "Invalid birth date format. Please use mm/dd/yyyy.";
  }
}
?>

<style>
   
  .help-block {
    color: #d9534f;
    font-style: italic;
    margin-top: 5px;
    display: flex;
    align-items: center;
    font-size: 13px;
  }

  .help-block i {
    margin-right: 5px;
    color: #f0ad4e;
  }

  .alert {
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
  }

  .alert i {
    margin-right: 10px;
  }

  .shake {
    animation: shake 0.5s ease-in-out;
  }

  @keyframes shake {

    0%,
    100% {
      transform: translateX(0);
    }

    25% {
      transform: translateX(-5px);
    }

    75% {
      transform: translateX(5px);
    }
  }
</style>

<form class="form-horizontal span6" action="controller.php?action=add" method="POST" autocomplete="off" id="patientForm" onsubmit="return validateForm()">

  <div class="row">
    <div class="col-lg-12">
      <h1 class="page-header">Add New</h1>
    </div>
    <!-- /.col-lg-12 -->
  </div>

  <?php if ($msg != '') { ?>
    <div class="alert alert-danger">
      <i class="fa fa-exclamation-triangle"></i>
      <?php echo $msg; ?>
    </div>
  <?php } ?>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Fname">First Name:</label>

      <div class="col-md-8">
        <input class="form-control input-sm" id="Fname" name="Fname" placeholder="First Name" type="text" value="" autocomplete="off" required>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Mname">Middle Name:</label>

      <div class="col-md-8">
        <input class="form-control input-sm" id="Mname" name="Mname" placeholder="Middle Name" type="text" value="" autocomplete="off" required>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Lname">Last Name:</label>

      <div class="col-md-8">
        <input class="form-control input-sm" id="Lname" name="Lname" placeholder="Last Name" type="text" value="" autocomplete="off" required>
      </div>
    </div>
  </div>

  <!-- required -->

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="F_Address">Address:</label>
      <div class="col-md-8">
        <textarea class="form-control input-sm" id="F_Address" name="F_Address" placeholder="Address 1" type="text" value="" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required></textarea>

      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="Sex">Sex:</label>

      <div class="col-md-8">
        <select class="form-control input-sm" id="Sex" name="Sex">
          <option value="">Select Sex</option>
          <option>Male</option>
          <option>Female</option>
        </select>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="BirthDate">Date of Birth:</label>

      <div class="col-md-8">
        <div class="input-group date" data-provide="datepicker" data-date-format="mm/dd/yyyy">
          <input type="text" class="form-control input-sm date_picker date_inv" id="BirthDate" name="BirthDate" placeholder="mm/dd/yyyy" autocomplete="off" required value="<?php echo date_format(date_create(date('Y-m-d')), 'm/d/Y'); ?>" />
          <span class="input-group-addon"><i class="fa fa-th"></i></span>
        </div>
        <div class="help-block" data-toggle="tooltip" title="Enter a birth date that makes the patient at least 1 year old. Example: For today (October 03, 2025), use October 03, 2024 or earlier.">
          <i class="fa fa-info-circle"></i>
          Note: Birth date must be at least one year before today (age 1+).
        </div>
      </div>
    </div>
  </div>

  <!-- Commented Age field - not used -->

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="ContactNo">Contact No.:</label>

      <div class="col-md-8">
        <input
          class="form-control input-sm"
          id="ContactNo"
          name="ContactNo"
          placeholder="Contact No."
          type="number"
          oninput="javascript: if (this.value.length > 10) this.value = this.value.slice(0, 10);"
          value=""
          autocomplete="none"
          required>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-8">
      <label class="col-md-4 control-label" for="idno"></label>

      <div class="col-md-8">
        <button class="btn btn-primary btn-md" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
        <a href="index.php" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;<strong>Back</strong></a>
      </div>
    </div>
  </div>

</form>

<script>
  function validateForm() {
    var birthDate = document.getElementById('BirthDate').value; // m/d/Y format
    var contactNo = document.getElementById('ContactNo').value;

    // Validate Contact No (10 digits)
    if (contactNo.length !== 10) {
      alert('Contact number must be exactly 10 digits.');
      document.getElementById('ContactNo').classList.add('shake');
      setTimeout(() => document.getElementById('ContactNo').classList.remove('shake'), 500);
      return false;
    }

    // Validate Birth Date (age >=1)
    if (!birthDate) {
      alert('Birth date is required.');
      return false;
    }

    // Parse birth date (m/d/Y)
    var parts = birthDate.split('/');
    if (parts.length !== 3) {
      alert('Invalid birth date format. Use mm/dd/yyyy.');
      return false;
    }

    var birthYear = parseInt(parts[2]);
    var birthMonth = parseInt(parts[0]);
    var birthDay = parseInt(parts[1]);
    var today = new Date();
    var birth = new Date(birthYear, birthMonth - 1, birthDay);

    var age = today.getFullYear() - birth.getFullYear();
    var monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
      age--;
    }

    if (age < 1) {
      alert('Cannot add a patient under 1 year old. Current age: ' + age + ' year(s). Please enter an earlier birth date.');
      document.getElementById('BirthDate').classList.add('shake');
      setTimeout(() => document.getElementById('BirthDate').classList.remove('shake'), 500);
      return false;
    }

    return true; // Allow submission to controller
  }

  // Initialize tooltips if Bootstrap is loaded
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>

<!-- Your commented Google Maps script remains the same -->
<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>  
  <script type="text/javascript">
    var map = null;
    var directionsDisplay = null;
    var directionsService = null;
    function initialize() {
        
      var input = document.getElementById('S_Address');
      var searchBox = new google.maps.places.SearchBox(input); 

       var input = document.getElementById('F_Address');
      var searchBox = new google.maps.places.SearchBox(input); 
    } 
    $(document).ready(function() {
        initialize();
    });
 
  </script>   
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTanm_xZQi4_RHeCAxerOqXN96NUwrbZU&libraries=places"></script> -->