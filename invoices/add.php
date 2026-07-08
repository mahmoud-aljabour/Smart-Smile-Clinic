<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
	redirect(web_root . "index.php");
}
unset($_SESSION['Patients']);
unset($_SESSION['invno']);
unset($_SESSION['admin_gcCart']);

$invno = isset($_GET['invno']) ? $_GET['invno'] : "";
$_SESSION['invno'] = $invno;

if ($invno == "") {
	redirect("index.php");
}

$TotalQTY = 0;
$TotalTax  = 0;
$TotalAmount  = 0;

$sql = "SELECT * FROM tblpayments WHERE InvoiceNo='{$invno}'";
$mydb->setQuery($sql);
$res = $mydb->executeQuery();
$maxrow = $mydb->num_rows($res);

if ($maxrow == 0) {
	$UserID = $_SESSION['ADMIN_USERID'];
	$sql = "INSERT INTO tblpayments (InvoiceDate,DateDue,InvoiceNo, TotalQTY, TotalAmount, Patients, UserID,Class)  VALUES(NOW(),NOW(),'{$invno}','{$TotalQTY}','{$TotalAmount}','NONE','{$UserID}','Invoice')";
	$mydb->setQuery($sql);
	$mydb->executeQuery();

	$autonum = new Autonumber();
	$autonum->auto_update('INVOICENO');
}

$sql = "SELECT * FROM tblpayments WHERE InvoiceNo='{$invno}'";
$mydb->setQuery($sql);
$res = $mydb->loadSingleResult();

if ($res->Patients != "") {
	$_SESSION['Patients'] = $res->Patients;
}

if (empty($_SESSION['admin_gcCart'])) {
	$sql = "SELECT * FROM tblinvoice i,tblservices p WHERE i.SKU=p.SKU AND InvoiceNo='{$invno}'";
	$mydb->setQuery($sql);
	$row = $mydb->executeQuery();
	$max = $mydb->num_rows($row);

	if ($max > 0) {
		$row = $mydb->loadResultList();
		foreach ($row as $result) {
			admin_addtocart($result->SKU, $result->ToothNumber, $result->Services, $result->Price, $result->QTY, $result->SubTotal, 0, 0);
		}
	}
}

$currency = $setDefault->default_currency();
?>

<div class="page-header-bar">
  <div>
    <h1 class="h3 mb-1">Create Invoice</h1>
    <p class="text-muted small mb-0">Add patient, services, and preview before printing</p>
  </div>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
  </a>
</div>

<div id="loading-client">
  <p class="mb-2"><img src="<?php echo web_root; ?>dist/img/loading2.gif" alt="Loading"></p>
  <p class="text-muted mb-0">Please wait...</p>
</div>

<div id="invoicing-body" class="form-add-page">

  <div class="row g-4 mb-4">
    <div class="col-lg-6">
      <div class="form-page-card h-100">
        <div class="card-header">
          <i class="bi bi-person-vcard"></i> Patient
        </div>
        <div class="card-body">
          <div id="searchclient"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="form-page-card h-100">
        <div class="card-header">
          <i class="bi bi-receipt"></i> Invoice Details
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="InvoiceNo">Invoice Number</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="text" name="InvoiceNo" id="InvoiceNo" value="<?php echo htmlspecialchars($invno); ?>" readonly class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="DateInvoiced">Invoice Date</label>
              <div class="input-group date" id="datepicker-container">
                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                <input type="text" class="form-control date_inv" id="DateInvoiced" name="DateInvoiced" value="<?php echo date_format(date_create($res->InvoiceDate), 'm/d/Y'); ?>" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="DueDate">Due Date <span class="required">*</span></label>
              <div class="input-group date" data-provide="datepicker" data-date-format="mm/dd/yyyy">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="text" class="form-control date_picker date_inv" id="DueDate" name="DueDate" placeholder="mm/dd/yyyy" autocomplete="off" required value="<?php echo date_format(date_create($res->DateDue), 'm/d/Y'); ?>">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-page-card">
    <div class="card-header">
      <i class="bi bi-list-check"></i> Services
    </div>
    <div class="card-body">
      <div class="invoice-service-toolbar">
        <div class="toolbar-field">
          <label class="form-label" for="SKU">Find Services</label>
          <input type="text" name="SKU" id="SKU" class="form-control" placeholder="Type service name..." autocomplete="off" required>
        </div>
        <div class="toolbar-actions">
          <a href="#" class="btn btn-primary" id="addtoinvoice" name="addinvoice">
            <i class="bi bi-plus-circle"></i> Add to Invoice
          </a>
          <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalproducts">
            <i class="bi bi-grid"></i> Browse Services
          </a>
        </div>
      </div>

      <div id="loadcart"></div>
    </div>
  </div>

  <div class="form-actions mt-4">
    <a href="printinvoice.php?id=<?php echo urlencode($invno); ?>" class="btn btn-primary btn-lg">
      <i class="bi bi-printer"></i> Preview Print
    </a>
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="bi bi-x-lg"></i> Cancel
    </a>
  </div>

</div>

<?php
include("addModal.php");
include("modalsearchproduct.php");
?>

<script type="text/javascript">
  $(function() {
    $('.select2').select2({
      width: '100%'
    });
  });
</script>
