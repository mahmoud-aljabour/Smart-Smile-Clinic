<?php
$settingsActive = in_array(currentpage(), ['user', 'suplier', 'taxrate', 'discountrate', 'currency', 'SetupPrescriptionPrint', 'settings', 'taxsettings', 'autonumber']);
?>
<aside id="sidebar" class="sidebar">
  <a href="<?php echo web_root; ?>" class="sidebar-brand">
    <img src="<?php echo web_root; ?>dist/img/logo02.svg" alt="Logo">
    <span class="sidebar-brand-text">Pearl Dental Clinic</span>
  </a>

  <nav class="sidebar-nav nav flex-column">
    <a class="nav-link <?php echo (currentpage() == 'index.php') ? 'active' : ''; ?>" href="<?php echo web_root; ?>">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </a>
    <a class="nav-link <?php echo (currentpage() == 'patients') ? 'active' : ''; ?>" href="<?php echo web_root; ?>patients/">
      <i class="bi bi-people"></i>
      <span>Patients</span>
    </a>
    <a class="nav-link <?php echo (currentpage() == 'appointments') ? 'active' : ''; ?>" href="<?php echo web_root; ?>appointments/">
      <i class="bi bi-calendar-check"></i>
      <span>Appointments</span>
    </a>

    <?php if ($_SESSION['ADMIN_ROLE'] == "Administrator") { ?>
      <a class="nav-link <?php echo (currentpage() == 'services') ? 'active' : ''; ?>" href="<?php echo web_root; ?>services/">
        <i class="bi bi-heart-pulse"></i>
        <span>Services</span>
      </a>
    <?php } ?>

    <a class="nav-link <?php echo (currentpage() == 'invoices') ? 'active' : ''; ?>" href="<?php echo web_root; ?>invoices/">
      <i class="bi bi-receipt"></i>
      <span>Invoices</span>
    </a>
    <a class="nav-link <?php echo (currentpage() == 'prescription') ? 'active' : ''; ?>" href="<?php echo web_root; ?>prescription/">
      <i class="bi bi-capsule"></i>
      <span>Prescriptions</span>
    </a>

    <?php if ($_SESSION['ADMIN_ROLE'] == "Administrator") { ?>
      <a class="nav-link <?php echo (currentpage() == 'reports') ? 'active' : ''; ?>" href="<?php echo web_root; ?>reports/">
        <i class="bi bi-bar-chart-line"></i>
        <span>Sales Reports</span>
      </a>

      <a class="nav-link <?php echo $settingsActive ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#settingsSubmenu" role="button" aria-expanded="<?php echo $settingsActive ? 'true' : 'false'; ?>">
        <i class="bi bi-gear"></i>
        <span>Settings</span>
        <i class="bi bi-chevron-down chevron ms-auto"></i>
      </a>
      <div class="collapse sidebar-submenu <?php echo $settingsActive ? 'show' : ''; ?>" id="settingsSubmenu">
        <a class="nav-link <?php echo (currentpage() == 'user') ? 'active' : ''; ?>" href="<?php echo web_root; ?>user/">
          <span>Manage Users</span>
        </a>
        <a class="nav-link <?php echo (currentpage() == 'currency') ? 'active' : ''; ?>" href="<?php echo web_root; ?>currency/">
          <span>Currency</span>
        </a>
        <a class="nav-link <?php echo (currentpage() == 'SetupPrescriptionPrint') ? 'active' : ''; ?>" href="<?php echo web_root; ?>SetupPrescriptionPrint/">
          <span>Prescription Print</span>
        </a>
        <a class="nav-link <?php echo (currentpage() == 'settings') ? 'active' : ''; ?>" href="<?php echo web_root; ?>settings/">
          <span>Invoice Print</span>
        </a>
        <a class="nav-link <?php echo (currentpage() == 'autonumber') ? 'active' : ''; ?>" href="<?php echo web_root; ?>autonumber/">
          <span>Autonumbers</span>
        </a>
      </div>
    <?php } ?>
  </nav>
</aside>
<div id="sidebarBackdrop" class="sidebar-backdrop"></div>
