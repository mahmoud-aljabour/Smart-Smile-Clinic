<nav class="top-navbar">
  <button type="button" class="btn-icon d-lg-none" id="sidebarToggle" aria-label="Toggle menu">
    <i class="bi bi-list"></i>
  </button>
  <button type="button" class="btn-icon d-none d-lg-inline-flex" id="sidebarCollapse" aria-label="Collapse sidebar">
    <i class="bi bi-layout-sidebar"></i>
  </button>

  <h1 class="page-title"><?php echo isset($title) ? htmlspecialchars($title) : 'Dashboard'; ?></h1>

  <div class="top-navbar-actions">
    <button type="button" class="btn-icon" id="themeToggle" aria-label="Toggle dark mode">
      <i class="bi bi-moon-fill"></i>
    </button>

    <div class="dropdown user-dropdown">
      <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="<?php echo web_root . 'user/' . $singleuser->PicLoc; ?>" class="user-avatar" alt="User">
        <span class="hidden-xs"><?php echo htmlspecialchars($singleuser->FullName); ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li>
          <a class="dropdown-item" href="<?php echo web_root . 'user/index.php?view=view&id=' . $singleuser->UserID; ?>">
            <i class="bi bi-person me-2"></i> Profile
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item text-danger" href="<?php echo web_root; ?>logout.php">
            <i class="bi bi-box-arrow-right me-2"></i> Sign out
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Photo upload modal -->
<div class="modal fade" id="menuModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Photo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo web_root; ?>user/controller.php?action=photos" enctype="multipart/form-data" method="post">
        <div class="modal-body">
          <input class="mealid" type="hidden" name="mealid" id="mealid" value="">
          <input name="MAX_FILE_SIZE" type="hidden" value="1000000">
          <input id="photo" name="photo" type="file" class="form-control">
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
          <button class="btn btn-primary" name="savephoto" type="submit">Upload Photo</button>
        </div>
      </form>
    </div>
  </div>
</div>
