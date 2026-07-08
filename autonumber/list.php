<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Autonumbers</h1>
    <p class="text-muted small mb-0">Manage automatic ID sequences</p>
  </div>
  <a href="index.php?view=add" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i> Add Autonumber
  </a>
</div>

<div class="content-card">
  <div class="card-body">
    <div class="table-responsive">
      <table id="dash-table" class="table table-modern table-hover table-bordered" cellspacing="0">
        <thead>
          <tr>
            <th>Current Number</th>
            <th>Key</th>
            <th width="10%" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $mydb->setQuery("SELECT * FROM `tblautonumbers` ORDER BY AUTOID ASC");
          $cur = $mydb->loadResultList();

          if (empty($cur)) {
            echo '<tr><td colspan="3" class="text-center text-muted py-4">No autonumbers found.</td></tr>';
          } else {
            foreach ($cur as $result) {
              echo '<tr>';
              echo '<td><code>' . htmlspecialchars($result->AUTOSTART . $result->AUTOEND) . '</code></td>';
              echo '<td><span class="badge text-bg-primary">' . htmlspecialchars($result->AUTOKEY) . '</span></td>';
              echo '<td class="text-center">';
              echo '<a title="Edit" href="index.php?view=edit&id=' . (int)$result->AUTOID . '" class="btn btn-sm btn-outline-secondary btn-action"><i class="bi bi-pencil"></i></a>';
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
