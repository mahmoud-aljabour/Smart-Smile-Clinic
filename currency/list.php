<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Currency</h1>
    <p class="text-muted small mb-0">Manage clinic currency symbols</p>
  </div>
  <a href="index.php?view=add" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i> Add Currency
  </a>
</div>

<div class="content-card">
  <div class="card-body">
    <div class="table-responsive">
      <table id="dash-table" class="table table-modern table-hover table-bordered" cellspacing="0">
        <thead>
          <tr>
            <th>Currency</th>
            <th>Status</th>
            <th width="14%" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $mydb->setQuery("SELECT * FROM `tblcurrency` ORDER BY CurrencyID ASC");
          $cur = $mydb->loadResultList();

          if (empty($cur)) {
            echo '<tr><td colspan="3" class="text-center text-muted py-4">No currencies found.</td></tr>';
          } else {
            foreach ($cur as $result) {
              $isActive = (int)$result->ActiveCurrency === 1;
              echo '<tr>';
              echo '<td>' . htmlspecialchars($result->CurrencySymbol) . '</td>';
              echo '<td><span class="badge ' . ($isActive ? 'text-bg-success' : 'text-bg-secondary') . '">' . ($isActive ? 'Active' : 'Inactive') . '</span></td>';
              echo '<td class="text-center text-nowrap">';
              echo '<a title="Activate" href="controller.php?action=confirm&id=' . (int)$result->CurrencyID . '" class="btn btn-sm btn-outline-success btn-action me-1"><i class="bi bi-check-lg"></i></a>';
              echo '<a title="Edit" href="index.php?view=edit&id=' . (int)$result->CurrencyID . '" class="btn btn-sm btn-outline-secondary btn-action me-1"><i class="bi bi-pencil"></i></a>';
              echo '<a title="Delete" href="controller.php?action=delete&id=' . (int)$result->CurrencyID . '" class="btn btn-sm btn-outline-danger btn-action btn-danger"><i class="bi bi-trash"></i></a>';
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
