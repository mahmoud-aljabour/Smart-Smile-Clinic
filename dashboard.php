<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

function dc_count($mydb, $sql) {
  $mydb->setQuery($sql);
  $r = $mydb->loadSingleResult();
  return $r ? $r->cnt : 0;
}

$patientCount = dc_count($mydb, "SELECT COUNT(*) AS cnt FROM tblpatients");
$invoiceCount = dc_count($mydb, "SELECT COUNT(*) AS cnt FROM tblinvoice");
$todayAppts = dc_count($mydb, "SELECT COUNT(*) AS cnt FROM tblappointments WHERE A_Date = CURDATE()");

$serviceCount = 0;
$userCount = 0;
$revenueTotal = 0;

if ($_SESSION['ADMIN_ROLE'] == "Administrator") {
  $serviceCount = dc_count($mydb, "SELECT COUNT(*) AS cnt FROM tblservices");
  $userCount = dc_count($mydb, "SELECT COUNT(*) AS cnt FROM tblusers");
}

$mydb->setQuery("SELECT COALESCE(SUM(TotalAmount), 0) AS total FROM tblpayments");
$rev = $mydb->loadSingleResult();
if ($rev) {
  $revenueTotal = $rev->total;
}
?>

<div class="mb-4">
  <p class="text-muted mb-0">Overview of your clinic at a glance</p>
</div>

<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card--primary">
      <div class="stat-card-icon"><i class="bi bi-people"></i></div>
      <div class="stat-card-category">Patients</div>
      <div class="stat-card-value"><?php echo $patientCount; ?></div>
      <a href="<?php echo web_root; ?>patients/index.php" class="stat-card-link">
        View all <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>

  <?php if ($_SESSION['ADMIN_ROLE'] == "Administrator") { ?>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card--accent">
      <div class="stat-card-icon"><i class="bi bi-heart-pulse"></i></div>
      <div class="stat-card-category">Services</div>
      <div class="stat-card-value"><?php echo $serviceCount; ?></div>
      <a href="<?php echo web_root; ?>services/index.php" class="stat-card-link">
        View all <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
  <?php } ?>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card--secondary">
      <div class="stat-card-icon"><i class="bi bi-receipt"></i></div>
      <div class="stat-card-category">Invoices</div>
      <div class="stat-card-value"><?php echo $invoiceCount; ?></div>
      <a href="<?php echo web_root; ?>invoices/" class="stat-card-link">
        View all <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card--warning">
      <div class="stat-card-icon"><i class="bi bi-calendar-check"></i></div>
      <div class="stat-card-category">Today's Appointments</div>
      <div class="stat-card-value"><?php echo $todayAppts; ?></div>
      <a href="<?php echo web_root; ?>appointments/" class="stat-card-link">
        View all <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card--accent">
      <div class="stat-card-icon"><i class="bi bi-currency-dollar"></i></div>
      <div class="stat-card-category">Total Revenue</div>
      <div class="stat-card-value"><?php echo number_format((float)$revenueTotal, 2); ?></div>
      <a href="<?php echo web_root; ?>reports/" class="stat-card-link">
        View reports <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>

  <?php if ($_SESSION['ADMIN_ROLE'] == "Administrator") { ?>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-card--primary">
      <div class="stat-card-icon"><i class="bi bi-person-gear"></i></div>
      <div class="stat-card-category">Users</div>
      <div class="stat-card-value"><?php echo $userCount; ?></div>
      <a href="<?php echo web_root; ?>user/" class="stat-card-link">
        View all <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
  <?php } ?>
</div>
