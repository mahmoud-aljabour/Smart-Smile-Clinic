<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
?>

<div class="page-header-bar">
  <h1 class="h3">Patients</h1>
  <a href="index.php?view=add" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i> Add Patient
  </a>
</div>

<div class="content-card">
  <div class="card-body">
    <form action="controller.php?action=delete" method="POST">
      <div class="table-responsive">
        <table id="dash-table" class="table table-modern table-hover table-bordered" cellspacing="0">
          <thead>
            <tr>
              <th>Patient ID</th>
              <th>Patient Name</th>
              <th>Address</th>
              <th>Sex</th>
              <th>Age</th>
              <th>Contact No.</th>
              <th width="15%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $mydb->setQuery("SELECT * FROM `tblpatients`");
            $cur = $mydb->loadResultList();
            foreach ($cur as $result) {
              $fullName = trim($result->Fname . ' ' . $result->Mname . ' ' . $result->Lname);
              echo '<tr>';
              echo '<td>' . htmlspecialchars($result->PatientID) . '</td>';
              echo '<td>' . htmlspecialchars($fullName) . '</td>';
              echo '<td>' . htmlspecialchars($result->F_Address) . '</td>';
              echo '<td>' . htmlspecialchars($result->Sex) . '</td>';
              echo '<td>' . htmlspecialchars($result->Age) . '</td>';
              echo '<td>' . htmlspecialchars($result->ContactNo) . '</td>';
              echo '<td class="text-center text-nowrap">';
              echo '<a title="View" href="index.php?view=view&id=' . $result->PatientID . '" class="btn btn-sm btn-outline-primary btn-action me-1"><i class="bi bi-eye"></i> View</a>';
              echo '<a title="Edit" href="index.php?view=edit&id=' . $result->PatientID . '" class="btn btn-sm btn-outline-secondary btn-action me-1"><i class="bi bi-pencil"></i></a>';
              echo '<a title="Delete" href="controller.php?action=delete&id=' . $result->PatientID . '" class="btn btn-sm btn-outline-danger btn-action"><i class="bi bi-trash"></i></a>';
              echo '</td>';
              echo '</tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>
