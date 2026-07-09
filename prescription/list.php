<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  message("Please login to continue.", "error");
  redirect("../index.php");
}

global $mydb;

$isAdmin = ($_SESSION['ADMIN_ROLE'] == 'Administrator' || $_SESSION['ADMIN_ROLE'] == 'admin');
$canCreate = ($_SESSION['ADMIN_ROLE'] == 'Administrator');
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Prescriptions</h1>
    <p class="text-muted small mb-0">View and manage patient medical prescriptions</p>
  </div>
  <?php if ($canCreate): ?>
    <a href="index.php?view=add_prescription" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Add Prescription
    </a>
  <?php endif; ?>
</div>

<div class="content-card">
  <div class="card-body">
    <div class="table-responsive">
      <table id="prescription-table" class="table table-modern table-hover table-bordered" cellspacing="0">
        <thead>
          <tr>
            <th>Prescription No.</th>
            <th>Patient</th>
            <th>Medicine</th>
            <th>Dosage</th>
            <th>Created</th>
            <th>Doctor</th>
            <th width="14%" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT pr.*,
                  CONCAT(p.Fname, ' ', p.Mname, ' ', p.Lname) AS patient_name,
                  u.FullName AS doctor_name,
                  u.Username AS doctor_username,
                  u.Role AS doctor_role
                  FROM prescriptions pr
                  LEFT JOIN tblpatients p ON pr.patient_id = p.PatientID
                  LEFT JOIN tblusers u ON pr.user_id = u.UserID
                  ORDER BY pr.created_at DESC, pr.id DESC";
          $mydb->setQuery($sql);
          $prescriptions = $mydb->loadResultList();

          if (empty($prescriptions)) {
            echo '<tr><td colspan="7" class="text-center text-muted py-4">No prescriptions found. Add your first prescription to get started.</td></tr>';
          } else {
            foreach ($prescriptions as $pr) {
              $prescriptionNo = !empty($pr->prescription_no) ? $pr->prescription_no : ('PRESC_' . $pr->id);
              $createdAt = $pr->created_at ? date('m/d/Y', strtotime($pr->created_at)) : '—';
              $doctorName = trim($pr->doctor_name ?? '');
              $doctorUsername = trim($pr->doctor_username ?? '');
              $doctorRole = trim($pr->doctor_role ?? '');
              $genericDoctorNames = array('doctor', 'admin', 'administrator', 'staff', 'user');

              if ($doctorName === '' || in_array(strtolower($doctorName), $genericDoctorNames, true)) {
                if ($doctorUsername !== '' && !in_array(strtolower($doctorUsername), array('admin', 'staff'), true)) {
                  $doctorName = ucwords(str_replace(array('_', '.'), ' ', $doctorUsername));
                } elseif ($doctorRole !== '') {
                  $doctorName = $doctorRole;
                } elseif ($doctorUsername !== '') {
                  $doctorName = ucfirst($doctorUsername);
                } else {
                  $doctorName = '—';
                }
              }

              echo '<tr>';
              echo '<td>' . htmlspecialchars($prescriptionNo) . '</td>';
              echo '<td>' . htmlspecialchars(trim($pr->patient_name ?? '—')) . '</td>';
              echo '<td>' . htmlspecialchars($pr->medicine_name ?? '') . '</td>';
              echo '<td>' . htmlspecialchars($pr->dosage ?? '') . '</td>';
              echo '<td>' . htmlspecialchars($createdAt) . '</td>';
              echo '<td>' . htmlspecialchars($doctorName) . '</td>';
              echo '<td class="text-center text-nowrap">';
              echo '<a href="index.php?view=view&id=' . (int)$pr->id . '" class="btn btn-sm btn-outline-primary btn-action me-1" title="View"><i class="bi bi-eye"></i></a>';

              if ($isAdmin) {
                echo '<a href="index.php?view=edit_prescription&presc_id=' . (int)$pr->id . '" class="btn btn-sm btn-outline-secondary btn-action me-1" title="Edit"><i class="bi bi-pencil"></i></a>';
                echo '<a href="controller.php?action=delete_prescription&id=' . (int)$pr->id . '" class="btn btn-sm btn-outline-danger btn-action btn-delete-prescription" title="Delete" data-prescription="' . htmlspecialchars($prescriptionNo) . '" data-patient="' . htmlspecialchars(trim($pr->patient_name ?? '')) . '" data-medicine="' . htmlspecialchars($pr->medicine_name ?? '') . '"><i class="bi bi-trash"></i></a>';
              }

              echo '</td>';
              echo '</tr>';
            }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
