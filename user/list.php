<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}
if ($_SESSION['ADMIN_ROLE'] != "Administrator") {
  redirect(web_root . "index.php");
}

function userRoleBadgeClass($role)
{
  if ($role === 'Administrator' || $role === 'MainAdministrator') {
    return 'text-bg-primary';
  }
  return 'text-bg-secondary';
}

function canDeleteUser($result)
{
  if ($result->UserID == $_SESSION['ADMIN_USERID']) {
    return false;
  }
  if ($result->Role == 'MainAdministrator' || $result->Role == 'Administrator') {
    return false;
  }
  return true;
}
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Users</h1>
    <p class="text-muted small mb-0">Manage clinic accounts and roles</p>
  </div>
  <a href="index.php?view=add" class="btn btn-primary">
    <i class="bi bi-person-plus me-1"></i> Add User
  </a>
</div>

<div class="content-card">
  <div class="card-body">
    <div class="table-responsive">
      <table id="dash-table" class="table table-modern table-hover table-bordered" cellspacing="0">
        <thead>
          <tr>
            <th>Account ID</th>
            <th>Account Name</th>
            <th>Username</th>
            <th>Role</th>
            <th width="12%" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $mydb->setQuery("SELECT * FROM `tblusers` ORDER BY UserID ASC");
          $cur = $mydb->loadResultList();

          if (empty($cur)) {
            echo '<tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>';
          } else {
            foreach ($cur as $result) {
              $canDelete = canDeleteUser($result);
              echo '<tr>';
              echo '<td>' . htmlspecialchars($result->UserID) . '</td>';
              echo '<td>' . htmlspecialchars($result->FullName) . '</td>';
              echo '<td>' . htmlspecialchars($result->Username) . '</td>';
              echo '<td><span class="badge ' . userRoleBadgeClass($result->Role) . '">' . htmlspecialchars($result->Role) . '</span></td>';
              echo '<td class="text-center text-nowrap">';
              echo '<a title="Edit" href="index.php?view=edit&id=' . (int)$result->UserID . '" class="btn btn-sm btn-outline-secondary btn-action me-1"><i class="bi bi-pencil"></i></a>';
              if ($canDelete) {
                echo '<a title="Delete" href="controller.php?action=delete&id=' . (int)$result->UserID . '" class="btn btn-sm btn-outline-danger btn-action btn-danger"><i class="bi bi-trash"></i></a>';
              } else {
                echo '<button type="button" class="btn btn-sm btn-outline-secondary btn-action" disabled title="Cannot delete this account"><i class="bi bi-trash"></i></button>';
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
