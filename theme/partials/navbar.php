<?php

if (!isset($singleuser) && !empty($_SESSION['ADMIN_USERID'])) {

  $layoutUser = new User();

  $singleuser = $layoutUser->single_user((int)$_SESSION['ADMIN_USERID']);

}



$navUserName = $_SESSION['ADMIN_FULLNAME'] ?? 'User';

$navUserId = (int)($_SESSION['ADMIN_USERID'] ?? 0);

$navUserRole = $_SESSION['ADMIN_ROLE'] ?? '';

$navUserPic = $_SESSION['ADMIN_PICLOC'] ?? '';



if (!empty($singleuser)) {

  if (!empty($singleuser->FullName)) {

    $navUserName = $singleuser->FullName;

  }

  if (!empty($singleuser->UserID)) {

    $navUserId = (int)$singleuser->UserID;

  }

  if (!empty($singleuser->Role)) {

    $navUserRole = $singleuser->Role;

  }

  if (!empty($singleuser->PicLoc)) {

    $navUserPic = $singleuser->PicLoc;

  }

}



$navAvatarSrc = '';

if ($navUserPic !== '') {

  $navAvatarSrc = web_root . 'user/' . str_replace('%2F', '/', rawurlencode($navUserPic));

}



$navUserInitial = function_exists('mb_substr')

  ? strtoupper(mb_substr(trim($navUserName), 0, 1, 'UTF-8'))

  : strtoupper(substr(trim($navUserName), 0, 1));

?>

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

      <button

        type="button"

        class="user-menu-toggle dropdown-toggle"

        id="userMenuToggle"

        data-bs-toggle="dropdown"

        data-bs-auto-close="true"

        aria-expanded="false"

        aria-label="User menu"

      >

        <span class="user-avatar-wrap" aria-hidden="true">

          <?php if ($navAvatarSrc !== ''): ?>

            <img

              src="<?php echo htmlspecialchars($navAvatarSrc); ?>"

              class="user-avatar user-avatar--photo"

              alt=""

              loading="lazy"

              data-user-initial="<?php echo htmlspecialchars($navUserInitial); ?>"

            >

          <?php endif; ?>

          <span class="user-avatar user-avatar--initials<?php echo $navAvatarSrc !== '' ? ' d-none' : ''; ?>">

            <?php echo htmlspecialchars($navUserInitial); ?>

          </span>

        </span>

        <span class="user-menu-label d-none d-sm-inline"><?php echo htmlspecialchars($navUserName); ?></span>

      </button>

      <ul class="dropdown-menu dropdown-menu-end shadow user-dropdown-menu" aria-labelledby="userMenuToggle">

        <li class="dropdown-header user-dropdown-header">

          <span class="user-dropdown-name"><?php echo htmlspecialchars($navUserName); ?></span>

          <?php if ($navUserRole !== ''): ?>

            <span class="user-dropdown-role"><?php echo htmlspecialchars($navUserRole); ?></span>

          <?php endif; ?>

        </li>

        <li><hr class="dropdown-divider"></li>

        <li>

          <a class="dropdown-item" href="<?php echo web_root . 'user/index.php?view=view&id=' . $navUserId; ?>">

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


