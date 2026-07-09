<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
  redirect(web_root . "index.php");
}

cleanup_empty_invoices();

unset($_SESSION['admin_gcCart']);
unset($_SESSION['Patients']);
unset($_SESSION['invno']);

$autonum = new Autonumber();
$res = $autonum->set_autonumber('INVOICENO');
$invno = $res->AUTO;

$currency = $setDefault->default_currency();
$isAdmin = ($_SESSION['ADMIN_ROLE'] == 'Administrator');
$canCreate = $isAdmin;
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Invoices</h1>
    <p class="text-muted small mb-0">View, create, and manage patient invoices</p>
  </div>
  <?php if ($canCreate): ?>
    <a href="index.php?view=add&invno=<?php echo urlencode($invno); ?>" id="createinvoice" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Create Invoice
    </a>
  <?php endif; ?>
</div>

<div class="content-card">
  <div class="card-body">
    <form action="controller.php?action=delete" method="POST">
      <div class="table-responsive">
        <table id="dash-tables" class="table table-modern table-hover table-bordered" cellspacing="0">
          <thead>
            <tr>
              <th>Invoice No.</th>
              <th>Patient</th>
              <th>Invoice Date</th>
              <th>Due Date</th>
              <th>Payment Date</th>
              <th class="text-end">Total (<?php echo htmlspecialchars($currency); ?>)</th>
              <th>Status</th>
              <th width="16%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($_SESSION['ADMIN_ROLE'] == 'Administrator' || $_SESSION['ADMIN_ROLE'] == 'Staff') {
              $sql = "SELECT * FROM `tblpayments` WHERE Class='Invoice' AND TotalAmount > 0 ORDER BY PaymentID DESC";
            } else {
              $sql = "SELECT * FROM `tblpayments`
                      WHERE DATE(`InvoiceDate`) = DATE(NOW())
                      AND UserID='" . $_SESSION['ADMIN_USERID'] . "'
                      AND Class='Invoice'
                      AND TotalAmount > 0
                      ORDER BY PaymentID DESC";
            }

            $mydb->setQuery($sql);
            $cur = $mydb->loadResultList();
            $hasRows = false;

            foreach ($cur as $result) {
              if ($result->Patients == 'NONE') {
                continue;
              }

              $hasRows = true;
              $patients = $result->Patients;
              $status = ($result->Status == '') ? 'Pending' : 'Paid';
              $statusClass = ($status === 'Paid') ? 'text-bg-success' : 'text-bg-warning';

              $paymentDate = $result->PaymentDate;
              if ($paymentDate == '0000-00-00 00:00:00' || empty($paymentDate) || is_null($paymentDate)) {
                $displayDate = '<span class="badge text-bg-danger">Not Paid</span>';
              } else {
                $displayDate = date('m/d/Y H:i', strtotime($paymentDate));
              }

              $invoiceDate = $result->InvoiceDate ? date('m/d/Y', strtotime($result->InvoiceDate)) : '—';
              $dueDate = $result->DateDue ? date('m/d/Y', strtotime($result->DateDue)) : '—';

              echo '<tr>';
              echo '<td>' . htmlspecialchars($result->InvoiceNo) . '</td>';
              echo '<td>' . htmlspecialchars($patients) . '</td>';
              echo '<td>' . htmlspecialchars($invoiceDate) . '</td>';
              echo '<td>' . htmlspecialchars($dueDate) . '</td>';
              echo '<td>' . $displayDate . '</td>';
              echo '<td class="text-end">' . number_format($result->TotalAmount, 2) . '</td>';
              echo '<td><span class="badge ' . $statusClass . '">' . htmlspecialchars($status) . '</span></td>';
              echo '<td class="text-center text-nowrap">';

              if ($isAdmin) {
                if ($status === 'Pending') {
                  echo '<a href="controller.php?action=payemnt&id=' . urlencode($result->InvoiceNo) . '" class="btn btn-sm btn-outline-success btn-action me-1" title="Payment"><i class="bi bi-cash-coin"></i></a>';
                  echo '<a href="index.php?view=view&id=' . urlencode($result->InvoiceNo) . '" class="btn btn-sm btn-outline-primary btn-action me-1" title="View"><i class="bi bi-eye"></i></a>';
                  echo '<a href="index.php?view=add&invno=' . urlencode($result->InvoiceNo) . '" class="btn btn-sm btn-outline-secondary btn-action me-1" title="Edit"><i class="bi bi-pencil"></i></a>';
                  echo '<a href="controller.php?action=delete&id=' . urlencode($result->InvoiceNo) . '" class="btn btn-sm btn-outline-danger btn-action btn-delete-invoice" title="Delete" data-invoice="' . htmlspecialchars($result->InvoiceNo) . '" data-patient="' . htmlspecialchars($patients) . '" data-amount="' . htmlspecialchars(number_format($result->TotalAmount, 2)) . '" data-currency="' . htmlspecialchars($currency) . '"><i class="bi bi-trash"></i></a>';
                } else {
                  echo '<a href="index.php?view=view&id=' . urlencode($result->InvoiceNo) . '" class="btn btn-sm btn-outline-primary btn-action me-1" title="View"><i class="bi bi-eye"></i></a>';
                  echo '<a href="controller.php?action=delete&id=' . urlencode($result->InvoiceNo) . '" class="btn btn-sm btn-outline-danger btn-action btn-delete-invoice" title="Delete" data-invoice="' . htmlspecialchars($result->InvoiceNo) . '" data-patient="' . htmlspecialchars($patients) . '" data-amount="' . htmlspecialchars(number_format($result->TotalAmount, 2)) . '" data-currency="' . htmlspecialchars($currency) . '"><i class="bi bi-trash"></i></a>';
                }
              } else {
                if ($status === 'Pending') {
                  echo '<a href="controller.php?action=payemnt&id=' . urlencode($result->InvoiceNo) . '" class="btn btn-sm btn-outline-success btn-action me-1" title="Payment"><i class="bi bi-cash-coin"></i></a>';
                }
                echo '<a href="index.php?view=view&id=' . urlencode($result->InvoiceNo) . '" class="btn btn-sm btn-outline-primary btn-action" title="View"><i class="bi bi-eye"></i></a>';
              }

              echo '</td>';
              echo '</tr>';
            }

            if (!$hasRows) {
              echo '<tr><td colspan="8" class="text-center text-muted py-4">No invoices found.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>
