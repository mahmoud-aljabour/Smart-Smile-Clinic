<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

$sql = "SELECT * FROM `tblprintheader`";
$mydb->setQuery($sql);
$printHeader = $mydb->loadSingleResult();

$sql = "SELECT * FROM `tblprintfooter`";
$mydb->setQuery($sql);
$printFooter = $mydb->loadSingleResult();

$dateFrom = isset($_POST['date_from']) ? $_POST['date_from'] : date('m/d/Y');
$dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : date('m/d/Y');
$submitted = isset($_POST['submit']);
$currency = $setDefault->default_currency();

$datefrom = $submitted ? date_format(date_create($dateFrom), 'Y-m-d') : '';
$dateto = $submitted ? date_format(date_create($dateTo), 'Y-m-d') : '';

$totalprice = 0;
$rows = array();

if ($submitted) {
  $sql = "SELECT i.Services, i.Price, pay.InvoiceDate, pay.InvoiceNo,
          s.Services AS CatalogService, s.Description AS CatalogDescription
          FROM tblinvoice i
          INNER JOIN (
            SELECT InvoiceNo, MAX(InvoiceDate) AS InvoiceDate
            FROM tblpayments
            WHERE Status = 'Paid' AND Class = 'Invoice'
            GROUP BY InvoiceNo
          ) pay ON i.InvoiceNo = pay.InvoiceNo
          INNER JOIN (
            SELECT MIN(InvoiceID) AS InvoiceID
            FROM tblinvoice
            GROUP BY InvoiceNo, SKU
          ) uniq ON i.InvoiceID = uniq.InvoiceID
          LEFT JOIN tblservices s ON i.SKU = s.SKU
          WHERE DATE(pay.InvoiceDate) >= '{$datefrom}'
          AND DATE(pay.InvoiceDate) <= '{$dateto}'
          ORDER BY pay.InvoiceDate DESC, i.InvoiceID ASC";
  $mydb->setQuery($sql);
  $rows = $mydb->loadResultList();
  foreach ($rows as $result) {
    $totalprice += $result->Price;
  }
}
?>


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

<div class="sales-report-page">
  <div class="report-print-header">
    <div class="line-main"><?php echo htmlspecialchars($printHeader->SecondLine ?? app_name); ?></div>
    <?php if (!empty($printHeader->ThirdLine)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($printHeader->ThirdLine); ?></div>
    <?php endif; ?>
  </div>

  <div class="content-card report-print-area">
    <div class="card-body">
      <div class="report-meta text-center mb-4">
        <h2 class="report-title mb-2">Sales Report</h2>
        <p class="text-muted small mb-1 report-generated-on">As of <?php echo date('m/d/Y'); ?></p>
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
        <div class="report-print-summary">
          <div class="summary-item">
            <span class="summary-label">Report Period</span>
            <span class="summary-value"><?php echo htmlspecialchars($dateFrom); ?> — <?php echo htmlspecialchars($dateTo); ?></span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Total Records</span>
            <span class="summary-value"><?php echo number_format(count($rows)); ?></span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Generated On</span>
            <span class="summary-value"><?php echo date('m/d/Y g:i A'); ?></span>
          </div>
        </div>

        <div class="table-responsive report-table-wrap">
          <table class="table table-modern table-hover table-bordered report-table mb-0">
            <thead>
              <tr>
                <th class="col-num">#</th>
                <th class="col-service">Service</th>
                <th class="col-invoice">Invoice No.</th>
                <th class="col-date">Invoice Date</th>
                <th class="col-price text-end">Price (<?php echo htmlspecialchars($currency); ?>)</th>
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
                    <td class="col-num"><?php echo $counter++; ?></td>
                    <?php $serviceLabel = invoice_service_label($result, $result->CatalogService ?? '', $result->CatalogDescription ?? ''); ?>
                    <td class="col-service"><?php echo htmlspecialchars($serviceLabel); ?></td>
                    <td class="col-invoice"><?php echo htmlspecialchars($result->InvoiceNo); ?></td>
                    <td class="col-date"><?php echo date('m/d/Y', strtotime($result->InvoiceDate)); ?></td>
                    <td class="col-price text-end"><?php echo number_format($result->Price, 2); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
            <?php if (count($rows) > 0): ?>
              <tfoot>
                <tr class="report-total-row">
                  <td colspan="4" class="text-end">Total Revenue</td>
                  <td class="text-end"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totalprice, 2); ?></td>
                </tr>
              </tfoot>
            <?php endif; ?>
          </table>
        </div>

        <div class="invoice-total-box report-screen-total mt-4">
          <span class="total-label">Total Revenue</span>
          <span class="total-value"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totalprice, 2); ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="report-print-footer">
    <?php if (!empty($printFooter->FirstLine)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($printFooter->FirstLine); ?></div>
    <?php endif; ?>
    <?php if (!empty($printFooter->SecondLine)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($printFooter->SecondLine); ?></div>
    <?php endif; ?>
    <?php if (!empty($printFooter->ThirdLine)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($printFooter->ThirdLine); ?></div>
    <?php endif; ?>
  </div>
</div>
