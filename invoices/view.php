<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "login.php");
}

unset($_SESSION['admin_gcCart']);

$invno = isset($_GET['id']) ? $_GET['id'] : "";
if ($invno == "") {
  redirect("index.php");
}

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
if ($Patients && $Patients !== 'NONE') {
  $sql = "SELECT * FROM tblpatients WHERE CONCAT(Fname, ' ', Mname, ' ', Lname)='{$Patients}'";
  $mydb->setQuery($sql);
  $patientInfo = $mydb->loadSingleResult();
}

$sql = "SELECT * FROM `tblinvoice` WHERE `InvoiceNo`='{$invno}' ORDER BY SKU ASC";
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

<style>
  @media print {
    .no-print,
    .no-print * {
      display: none !important;
    }

    .invoice-view-page {
      background: #fff !important;
      border: none !important;
      box-shadow: none !important;
      padding: 0 !important;
    }

    .invoice-view-page::before,
    .invoice-view-page::after {
      display: none !important;
    }

    .form-page-card,
    .content-card {
      background: #fff !important;
      border: 1px solid #ddd !important;
      box-shadow: none !important;
    }

    .invoice-print-header,
    .invoice-print-footer {
      display: block !important;
    }
  }

  .invoice-print-header .line-main {
    font-size: 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--dc-text);
  }

  .invoice-print-header .line-sub {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--dc-text-muted);
  }
</style>

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
      <i class="bi bi-download"></i> Export Excel
    </a>
    <button type="button" onclick="window.print()" class="btn btn-primary">
      <i class="bi bi-printer"></i> Print Invoice
    </button>
  </div>
</div>

<div id="invoice-view-area" class="form-add-page invoice-view-page">

  <div class="invoice-print-header text-center mb-4">
    <div class="line-main"><?php echo htmlspecialchars($printHeader->FirstLine ?? 'Smart Smile Clinic'); ?></div>
    <div class="line-sub"><?php echo htmlspecialchars($printHeader->SecondLine ?? ''); ?></div>
    <div class="line-sub"><?php echo htmlspecialchars($printHeader->ThirdLine ?? ''); ?></div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-6">
      <div class="form-page-card h-100">
        <div class="card-header">
          <i class="bi bi-person-vcard"></i> Patient
        </div>
        <div class="card-body">
          <?php if ($patientInfo): ?>
            <div class="patient-info-card">
              <div class="info-item full-width">
                <span class="info-label">Patient Name</span>
                <span class="info-value"><?php echo htmlspecialchars(trim($patientInfo->Fname . ' ' . $patientInfo->Mname . ' ' . $patientInfo->Lname)); ?></span>
              </div>
              <div class="info-item">
                <span class="info-label">Sex</span>
                <span class="info-value"><?php echo htmlspecialchars($patientInfo->Sex); ?></span>
              </div>
              <div class="info-item">
                <span class="info-label">Age</span>
                <span class="info-value"><?php echo htmlspecialchars($patientInfo->Age); ?></span>
              </div>
              <div class="info-item full-width">
                <span class="info-label">Address</span>
                <span class="info-value"><?php echo htmlspecialchars($patientInfo->F_Address); ?></span>
              </div>
              <div class="info-item">
                <span class="info-label">Phone</span>
                <span class="info-value"><?php echo htmlspecialchars($patientInfo->ContactNo); ?></span>
              </div>
            </div>
          <?php else: ?>
            <p class="mb-0 text-muted"><?php echo ($Patients && $Patients !== 'NONE') ? htmlspecialchars($Patients) : 'No patient assigned'; ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="form-page-card h-100">
        <div class="card-header">
          <i class="bi bi-receipt"></i> Invoice Information
        </div>
        <div class="card-body">
          <div class="patient-info-card">
            <div class="info-item">
              <span class="info-label">Invoice Number</span>
              <span class="info-value"><?php echo htmlspecialchars($invno); ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Status</span>
              <span class="info-value">
                <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
              </span>
            </div>
            <div class="info-item">
              <span class="info-label">Date Invoiced</span>
              <span class="info-value"><?php echo htmlspecialchars($DateInvoiced); ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Due Date</span>
              <span class="info-value"><?php echo htmlspecialchars($DueDate); ?></span>
            </div>
            <div class="info-item full-width">
              <span class="info-label">Payment Date</span>
              <span class="info-value"><?php echo htmlspecialchars($display_date); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card mb-4">
    <div class="card-header">
      <i class="bi bi-list-check"></i> Services
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-modern table-hover table-bordered mb-0">
          <thead>
            <tr>
              <th width="12%">Tooth Number</th>
              <th>Service</th>
              <th width="15%" class="text-end">Price (<?php echo htmlspecialchars($currency); ?>)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($invoiceItems) === 0): ?>
              <tr>
                <td colspan="3" class="text-center text-muted py-4">No services on this invoice.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($invoiceItems as $item): ?>
                <tr>
                  <td><?php echo htmlspecialchars($item->ToothNumber); ?></td>
                  <td><?php echo htmlspecialchars($item->Services); ?></td>
                  <td class="text-end"><?php echo number_format($item->Price, 2); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (count($invoiceItems) > 0): ?>
        <div class="invoice-total-box mt-4">
          <span class="total-label">Invoice Total</span>
          <span class="total-value"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($totalamount, 2); ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="invoice-print-footer text-center">
    <div class="line-sub"><?php echo htmlspecialchars($printFooter->FirstLine ?? ''); ?></div>
    <div class="line-sub"><?php echo htmlspecialchars($printFooter->SecondLine ?? ''); ?></div>
    <div class="line-sub"><?php echo htmlspecialchars($printFooter->ThirdLine ?? ''); ?></div>
  </div>

</div>
