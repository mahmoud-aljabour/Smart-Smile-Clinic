<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Suppliers</h1>
    <p class="text-muted small mb-0">Manage supplier list</p>
  </div>
  <a href="index.php?view=add" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i> Add Supplier
  </a>
</div>

<div class="content-card">
  <div class="card-body">
    <div class="table-responsive">
      <table id="dash-table" class="table table-modern table-hover table-bordered" cellspacing="0">
        <thead>
          <tr>
            <th>Supplier</th>
            <th width="12%" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $mydb->setQuery("SELECT * FROM `tblsuplier` ORDER BY Suplier ASC");
          $cur = $mydb->loadResultList();

          if (empty($cur)) {
            echo '<tr><td colspan="2" class="text-center text-muted py-4">No suppliers found.</td></tr>';
          } else {
            foreach ($cur as $result) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($result->Suplier) . '</td>';
              echo '<td class="text-center text-nowrap">';
              echo '<a title="Edit" href="index.php?view=edit&id=' . (int)$result->SuplierID . '" class="btn btn-sm btn-outline-secondary btn-action me-1"><i class="bi bi-pencil"></i></a>';
              echo '<a title="Delete" href="controller.php?action=delete&id=' . (int)$result->SuplierID . '" class="btn btn-sm btn-outline-danger btn-action btn-danger"><i class="bi bi-trash"></i></a>';
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
