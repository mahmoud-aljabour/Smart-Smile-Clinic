<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php
    if (!empty($documentTitle)) {
      echo htmlspecialchars($documentTitle);
    } elseif (!empty($_SESSION['ADMIN_FULLNAME'])) {
      echo htmlspecialchars($_SESSION['ADMIN_FULLNAME']);
    } else {
      echo htmlspecialchars(app_name);
    }
  ?></title>

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- DataTables BS5 -->
  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <!-- Legacy plugin styles (kept for compatibility) -->
  <link rel="stylesheet" href="<?php echo web_root; ?>fullcalendar/fullcalendar.min.css">
  <link rel="stylesheet" href="<?php echo web_root; ?>plugins/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo web_root; ?>plugins/datepicker/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="<?php echo web_root; ?>plugins/select2/select2.css">
  <link rel="stylesheet" href="<?php echo web_root; ?>plugins/teeth/style.css">
  <link rel="stylesheet" href="<?php echo web_root; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link href="<?php echo web_root; ?>dist/css/jquery.treetable.css" rel="stylesheet">
  <link href="<?php echo web_root; ?>dist/css/jquery.treetable.theme.default.css" rel="stylesheet">
  <link href="<?php echo web_root; ?>plugins/autocomplete/jquery.auto-complete.css" rel="stylesheet">

  <!-- DentalClinic theme -->
  <link rel="stylesheet" href="<?php echo web_root; ?>assets/css/theme.css">
  <link rel="stylesheet" href="<?php echo web_root; ?>assets/css/layout.css">
  <link rel="stylesheet" href="<?php echo web_root; ?>assets/css/components.css">

  <style>.table { white-space: nowrap; }</style>
</head>
<body>
