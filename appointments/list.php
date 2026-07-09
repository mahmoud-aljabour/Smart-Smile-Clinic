<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

date_default_timezone_set("Asia/Gaza");
global $mydb;

$todayDate = date("Y-m-d");
$todayName = date("l");
?>

<div class="appointments-page">
  <div class="page-header-bar">
    <div>
      <h1 class="h3 mb-1">Appointments Calendar</h1>
      <p class="text-muted small mb-0">Click a day to schedule, or click an appointment to edit</p>
    </div>
  </div>

  <div class="appointments-toolbar">
    <div class="appointments-date-bar">
      <i class="bi bi-calendar3"></i>
      <div class="appointments-date-text">
        <span class="appointments-date-label">Today</span>
        <span class="appointments-date-value"><?php echo $todayName . ' — ' . $todayDate; ?></span>
        <span class="appointments-date-time"><?php echo date('H:i'); ?></span>
      </div>
    </div>

    <div class="appointments-legend">
      <span class="appointments-legend-item">
        <span class="appointments-legend-dot is-today"></span>
        Today
      </span>
      <span class="appointments-legend-item">
        <span class="appointments-legend-dot is-friday"></span>
        Friday (Closed)
      </span>
      <span class="appointments-legend-item">
        <span class="appointments-legend-dot is-event"></span>
        Appointment
      </span>
    </div>
  </div>

  <div class="appointments-calendar-card">
    <div id="calendar"></div>
  </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"><i class="bi bi-calendar-plus me-2"></i>Schedule Appointment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="appointmentId" name="id">
        <input type="hidden" id="appointmentDate" name="A_Date">

        <div class="mb-3">
          <label class="form-label" for="appointmentTime">Appointment Time</label>
          <small class="text-danger d-block mb-1">Clinic hours: 8:00 AM – 4:00 PM (except Friday)</small>
          <input type="text" name="appointmentTime" id="appointmentTime" class="form-control"
                 placeholder="Example: 14:30 or 02:30 PM" required>
        </div>

        <div class="mb-3">
          <label class="form-label" for="patients">Patient</label>
          <select class="form-select" id="patients" required>
            <option value="" disabled selected>Select Patient</option>
            <?php
            $mydb->setQuery("SELECT * FROM tblpatients");
            $cur = $mydb->loadResultList();
            foreach ($cur as $result) {
              $fullName = trim($result->Fname) . '|' . trim($result->Mname) . '|' . trim($result->Lname);
              $display = trim($result->Fname . ' ' . $result->Mname . ' ' . $result->Lname);
              echo '<option value="' . htmlspecialchars($fullName) . '">' . htmlspecialchars($display) . '</option>';
            }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label" for="services">Service</label>
          <select class="form-select" id="services" name="services" required>
            <option value="" disabled selected>Select Service</option>
            <?php
            $sql = "SELECT * FROM tblservices GROUP BY Services";
            $mydb->setQuery($sql);
            $cur = $mydb->loadResultList();
            foreach ($cur as $result) {
              echo '<option value="' . htmlspecialchars($result->Services) . '">' . htmlspecialchars($result->Services) . '</option>';
            }
            ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="deleteappointment" class="btn btn-danger" style="display:none;">
          <i class="bi bi-trash"></i> Delete
        </button>
        <button type="button" id="saveappointment" class="btn btn-primary">
          <i class="bi bi-check-lg"></i> Save Changes
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
