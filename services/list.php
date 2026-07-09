<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$currency = $setDefault->default_currency();
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Services</h1>
    <p class="text-muted small mb-0">Manage dental services, pricing, and tooth mapping</p>
  </div>
  <a href="index.php?view=add" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i> Add Service
  </a>
</div>

<div class="content-card">
  <div class="card-body">
    <form action="controller.php?action=delete" method="POST">
      <div class="table-responsive">
        <table id="dash-table" class="table table-modern table-hover table-bordered" cellspacing="0">
          <thead>
            <tr>
              <th>Service ID</th>
              <th>Service Name</th>
              <th>Age Group</th>
              <th>Tooth Number</th>
              <th>Description</th>
              <th class="text-end">Price (<?php echo htmlspecialchars($currency); ?>)</th>
              <th width="14%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "SELECT s.*, ag.Description AS AgeGroupDesc
                    FROM `tblservices` s
                    LEFT JOIN `tbl_age_groups` ag ON s.AgeGroupID = ag.AgeGroupID
                    ORDER BY s.SKU DESC";
            $mydb->setQuery($sql);
            $cur = $mydb->loadResultList();

            if (empty($cur)) {
              echo '<tr><td colspan="7" class="text-center text-muted py-4">No services found. Add your first service to get started.</td></tr>';
            } else {
              foreach ($cur as $result) {
                $ageGroup = $result->AgeGroupDesc ? $result->AgeGroupDesc : 'Not Specified';
                echo '<tr>';
                echo '<td>' . htmlspecialchars($result->SKU) . '</td>';
                echo '<td>' . htmlspecialchars($result->Services) . '</td>';
                echo '<td>' . htmlspecialchars($ageGroup) . '</td>';
                echo '<td>' . htmlspecialchars($result->ToothNumber) . '</td>';
                echo '<td>' . htmlspecialchars($result->Description) . '</td>';
                echo '<td class="text-end">' . number_format($result->OriginalPrice, 2) . '</td>';
                echo '<td class="text-center text-nowrap">';
                echo '<a title="View" href="index.php?view=view&id=' . urlencode($result->SKU) . '" class="btn btn-sm btn-outline-primary btn-action me-1"><i class="bi bi-eye"></i></a>';
                echo '<a title="Edit" href="index.php?view=edit&id=' . urlencode($result->SKU) . '" class="btn btn-sm btn-outline-secondary btn-action me-1"><i class="bi bi-pencil"></i></a>';
                echo '<a title="Delete" href="controller.php?action=delete&id=' . urlencode($result->SKU) . '" class="btn btn-sm btn-outline-danger btn-action"><i class="bi bi-trash"></i></a>';
                echo '</td>';
                echo '</tr>';
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>
