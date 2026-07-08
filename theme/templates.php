<?php require_once __DIR__ . '/partials/head.php'; ?>

<div class="app-wrapper">
  <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

  <div class="main-content">
    <?php require_once __DIR__ . '/partials/navbar.php'; ?>

    <main class="page-content container-fluid">
      <?php if (isset($title) && $title != 'Dashboard') { ?>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo web_root; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <?php
            if (isset($_GET['view'])) {
              echo '<li class="breadcrumb-item"><a href="index.php">' . htmlspecialchars($title) . '</a></li>';
              $crumb = isset($breadcrumbLabel) ? $breadcrumbLabel : $_GET['view'];
              echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($crumb) . '</li>';
            } else {
              echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($title) . '</li>';
            }
            ?>
          </ol>
        </nav>
      <?php } ?>

      <div id="check_msg">
        <?php if (isset($title) && $title != 'Dashboard') {
          check_message();
        } ?>
      </div>

      <?php require_once $content; ?>
    </main>

    <?php require_once __DIR__ . '/partials/footer.php'; ?>
  </div>
</div>

<?php require_once __DIR__ . '/partials/scripts.php'; ?>
<?php if (!empty($pageScript)) { require_once $pageScript; } ?>
