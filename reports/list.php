<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$dateFrom = isset($_POST['date_from']) ? $_POST['date_from'] : date('m/d/Y');
$dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : date('m/d/Y');
$submitted = isset($_POST['submit']);
$currency = $setDefault->default_currency();

$datefrom = $submitted ? date_format(date_create($dateFrom), 'Y-m-d') : '';
$dateto = $submitted ? date_format(date_create($dateTo), 'Y-m-d') : '';

$totalprice = 0;
$rows = array();

if ($submitted) {
  $sql = "SELECT i.Services, i.Price, p.InvoiceDate, p.InvoiceNo
          FROM tblinvoice i
          INNER JOIN tblpayments p ON i.InvoiceNo = p.InvoiceNo
          WHERE p.Status = 'Paid'
          AND DATE(p.InvoiceDate) >= '{$datefrom}'
          AND DATE(p.InvoiceDate) <= '{$dateto}'
          ORDER BY p.InvoiceDate DESC, i.Services ASC";
  $mydb->setQuery($sql);
  $rows = $mydb->loadResultList();
  foreach ($rows as $result) {
    $totalprice += $result->Price;
  }
}
?>

<style>
  @media print {
    .no-print,
    .no-print * {
      display: none !important;
    }
    .report-print-area {
      box-shadow: none !important;
      border: none !important;
    }
  }
</style>

<div class="page-header-bar no-print">
  <div>
    <h1 class="h3 mb-1">Sales Report</h1>
    <p class="text-muted small mb-0">View paid invoice services by date range</p>
  </div>
  <?php if ($submitted && count($rows) > 0): ?>
    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
      <i class="bi bi-printer"></i> Print Report
    </button>
  <?php endif; ?>
</div>

<form action="" method="POST" class="form-add-page no-print mb-4">
  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-funnel"></i> Filter Report
    </div>
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label" for="date_from">Date From</label>
          <div class="input-group date" data-provide="datepicker" data-date-format="mm/dd/yyyy">
            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
            <input required autocomplete="off" type="text" value="<?php echo htmlspecialchars($dateFrom); ?>" name="date_from" class="form-control date_picker" id="date_from" placeholder="mm/dd/yyyy">
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="date_to">Date To</label>
          <div class="input-group date" data-provide="datepicker" data-date-format="mm/dd/yyyy">
            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
            <input required autocomplete="off" type="text" value="<?php echo htmlspecialchars($dateTo); ?>" name="date_to" class="form-control date_picker" id="date_to" placeholder="mm/dd/yyyy">
          </div>
        </div>
        <div class="col-md-4">
          <button type="submit" name="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Generate Report
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="content-card report-print-area">
  <div class="card-body">
    <div class="report-meta text-center mb-4">
      <h2 class="h5 mb-2">Sales Report</h2>
      <p class="text-muted small mb-1">As of <?php echo date('m/d/Y'); ?></p>
      <?php if ($submitted): ?>
        <p class="mb-0">
          <span class="info-badge">
            <i class="bi bi-calendar-range"></i>
            <?php echo htmlspecialchars($dateFrom); ?> — <?php echo htmlspecialchars($dateTo); ?>
          </span>
        </p>
      <?php else: ?>
        <p class="text-muted small mb-0">Select a date range and click Generate Report</p>
      <?php endif; ?>
    </div>

    <?php if ($submitted): ?>
      <div class="table-responsive">
        <table class="table table-modern table-hover table-bordered mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Service</th>
              <th>Invoice No.</th>
              <th>Invoice Date</th>
              <th class="text-end">Price (<?php echo htmlspecialchars($currency); ?>)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($rows) === 0): ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No paid services found for this date range.</td>
              </tr>
            <?php else: ?>
              <?php $counter = 1; foreach ($rows as $result): ?>
                <tr>
                  <td><?php echo $counter++; ?></td>
                  <td><?php echo htmlspecialchars($result->Services); ?></td>
                  <td><?php echo htmlspecialchars($result->InvoiceNo); ?></td>
                  <td><?php echo date('m/d/Y', strtotime($result->InvoiceDate)); ?></td>
                  <td class="text-end"><?php echo number_format($result->Price, 2); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (count($rows) > 0): ?>
        <div class="invoice-total-box mt-4">
          <span class="total-label">Total Revenue</span>
          <span class="total-value"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totalprice, 2); ?></span>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
