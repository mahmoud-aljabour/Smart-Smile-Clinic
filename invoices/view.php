<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

unset($_SESSION['admin_gcCart']);

$invno = isset($_GET['id']) ? $_GET['id'] : "";
if ($invno == "") {
  redirect("index.php");
}

cleanup_invoice_records($invno);

$sql = "SELECT * FROM `tblinvoice` WHERE `InvoiceNo`='{$invno}'";
$mydb->setQuery($sql);
$inv = $mydb->loadResultList();
foreach ($inv as $result) {
  admin_addtocart($result->SKU, $result->ToothNumber, $result->Services, $result->Price, $result->QTY, $result->SubTotal, 0, 0);
}

$sql = "SELECT * FROM `tblpayments` WHERE Class='Invoice' AND InvoiceNo='{$invno}'";
$mydb->setQuery($sql);
$payment = $mydb->loadSingleResult();

if (!$payment) {
  message("Invoice not found.", "error");
  redirect("index.php");
}

$Patients = $payment->Patients;
$payment_date = $payment->PaymentDate;
$status = $payment->Status ?? 'Unpaid';
$currency = $setDefault->default_currency();

$DateInvoiced = date_format(date_create($payment->InvoiceDate), "m/d/Y");
$DueDate = date_format(date_create($payment->DateDue), "m/d/Y");

if ($payment_date == '0000-00-00 00:00:00' || empty($payment_date)) {
  $display_date = 'Not Paid';
} else {
  $display_date = date('m/d/Y H:i', strtotime($payment_date));
}

$patientInfo = null;
$patientDisplayName = ($Patients && $Patients !== 'NONE') ? $Patients : 'No patient assigned';

if ($Patients && $Patients !== 'NONE') {
  $sql = "SELECT * FROM tblpatients WHERE CONCAT(Fname, ' ', Mname, ' ', Lname)='{$Patients}'";
  $mydb->setQuery($sql);
  $patientInfo = $mydb->loadSingleResult();
  if ($patientInfo) {
    $patientDisplayName = trim($patientInfo->Fname . ' ' . $patientInfo->Mname . ' ' . $patientInfo->Lname);
  }
}

$sql = "SELECT i.*, s.Services AS CatalogService, s.Description AS CatalogDescription
  FROM tblinvoice i
  LEFT JOIN tblservices s ON i.SKU = s.SKU
  WHERE i.InvoiceNo='{$invno}'
  ORDER BY i.InvoiceID ASC";
$mydb->setQuery($sql);
$invoiceItems = $mydb->loadResultList();

$totalamount = 0;
foreach ($invoiceItems as $item) {
  $totalamount += $item->Price;
}

$sql = "SELECT * FROM `tblprintheader`";
$mydb->setQuery($sql);
$printHeader = $mydb->loadSingleResult();

$sql = "SELECT * FROM `tblprintfooter`";
$mydb->setQuery($sql);
$printFooter = $mydb->loadSingleResult();

$statusClass = ($status === 'Paid') ? 'text-bg-success' : 'text-bg-warning';
?>

<div class="page-header-bar no-print">
  <div>
    <h1 class="h3 mb-1">Invoice Details</h1>
    <p class="text-muted small mb-0"><?php echo htmlspecialchars($invno); ?></p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to List
    </a>
    <a href="export.php?id=<?php echo urlencode($invno); ?>" class="btn btn-outline-primary">
      <i class="bi bi-download"></i> Download Excel
    </a>
    <button type="button" onclick="window.print()" class="btn btn-primary">
      <i class="bi bi-printer"></i> Print Invoice
    </button>
  </div>
</div>

<div id="invoice-view-area" class="invoice-document-page invoice-view-page">

  <div class="invoice-print-header">
    <div class="line-doc"><?php echo htmlspecialchars($printHeader->FirstLine ?? 'Invoice'); ?></div>
    <div class="line-main"><?php echo htmlspecialchars($printHeader->SecondLine ?? app_name); ?></div>
    <?php if (!empty($printHeader->ThirdLine)): ?>
      <div class="line-sub"><?php echo htmlspecialchars($printHeader->ThirdLine); ?></div>
    <?php endif; ?>
  </div>

  <div class="content-card invoice-print-area">
    <div class="card-body">
      <div class="invoice-doc-head text-center mb-4">
        <div class="invoice-doc-type"><?php echo htmlspecialchars($printHeader->FirstLine ?? 'Invoice'); ?></div>
        <div class="invoice-doc-number"><?php echo htmlspecialchars($invno); ?></div>
      </div>

      <div class="invoice-info-grid mb-4">
        <div class="invoice-info-block">
          <h3 class="invoice-block-title">Bill To</h3>
          <div class="invoice-detail-list">
            <div class="invoice-detail-item full-width">
              <span class="detail-label">Patient Name</span>
              <span class="detail-value"><?php echo htmlspecialchars($patientDisplayName); ?></span>
            </div>
            <?php if ($patientInfo): ?>
              <div class="invoice-detail-item">
                <span class="detail-label">Sex</span>
                <span class="detail-value"><?php echo htmlspecialchars($patientInfo->Sex); ?></span>
              </div>
              <div class="invoice-detail-item">
                <span class="detail-label">Age</span>
                <span class="detail-value"><?php echo htmlspecialchars($patientInfo->Age); ?></span>
              </div>
              <div class="invoice-detail-item full-width">
                <span class="detail-label">Address</span>
                <span class="detail-value"><?php echo htmlspecialchars($patientInfo->F_Address); ?></span>
              </div>
              <div class="invoice-detail-item">
                <span class="detail-label">Phone</span>
                <span class="detail-value"><?php echo htmlspecialchars($patientInfo->ContactNo); ?></span>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="invoice-info-block">
          <h3 class="invoice-block-title">Invoice Details</h3>
          <div class="invoice-detail-list">
            <div class="invoice-detail-item">
              <span class="detail-label">Invoice Number</span>
              <span class="detail-value"><?php echo htmlspecialchars($invno); ?></span>
            </div>
            <div class="invoice-detail-item">
              <span class="detail-label">Status</span>
              <span class="detail-value">
                <span class="badge <?php echo $statusClass; ?> invoice-status-badge"><?php echo htmlspecialchars($status); ?></span>
              </span>
            </div>
            <div class="invoice-detail-item">
              <span class="detail-label">Date Invoiced</span>
              <span class="detail-value"><?php echo htmlspecialchars($DateInvoiced); ?></span>
            </div>
            <div class="invoice-detail-item">
              <span class="detail-label">Due Date</span>
              <span class="detail-value"><?php echo htmlspecialchars($DueDate); ?></span>
            </div>
            <div class="invoice-detail-item full-width">
              <span class="detail-label">Payment Date</span>
              <span class="detail-value"><?php echo htmlspecialchars($display_date); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="invoice-table-wrap">
        <table class="table table-modern table-bordered invoice-doc-table mb-0">
          <thead>
            <tr>
              <th class="col-tooth">Tooth Number</th>
              <th class="col-service">Service</th>
              <th class="col-price text-end">Price (<?php echo htmlspecialchars($currency); ?>)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($invoiceItems) === 0): ?>
              <tr>
                <td colspan="3" class="text-center text-muted py-4">No services on this invoice.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($invoiceItems as $item): ?>
                <?php $serviceLabel = invoice_service_label($item, $item->CatalogService ?? '', $item->CatalogDescription ?? ''); ?>
                <tr>
                  <td class="col-tooth"><?php echo htmlspecialchars($item->ToothNumber); ?></td>
                  <td class="col-service"><?php echo htmlspecialchars($serviceLabel); ?></td>
                  <td class="col-price text-end"><?php echo number_format($item->Price, 2); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
          <?php if (count($invoiceItems) > 0): ?>
            <tfoot>
              <tr class="invoice-total-row">
                <td colspan="2" class="text-end">Invoice Total</td>
                <td class="text-end"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totalamount, 2); ?></td>
              </tr>
            </tfoot>
          <?php endif; ?>
        </table>
      </div>

      <?php if (count($invoiceItems) > 0): ?>
        <div class="invoice-total-box invoice-screen-total mt-4">
          <span class="total-label">Invoice Total</span>
          <span class="total-value"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totalamount, 2); ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="invoice-print-footer">
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
