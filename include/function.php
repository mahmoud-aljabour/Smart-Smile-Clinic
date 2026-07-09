<?php
	function strip_zeros_from_date($marked_string="") {
		//first remove the marked zeros
		$no_zeros = str_replace('*0','',$marked_string);
		$cleaned_string = str_replace('*0','',$no_zeros);
		return $cleaned_string;
	}
	function redirect_to($location = NULL) {
		if($location != NULL){
			header("Location: {$location}");
			exit;
		}
	}
	function redirect($location=Null){
		if($location!=Null){
			echo "<script>
					window.location='{$location}'
				</script>";	
		}else{
			echo 'error location';
		}
		 
	}
	function output_message($message="") {
	
		if(!empty($message)){
		return "<p class=\"message\">{$message}</p>";
		}else{
			return "";
		}
	}
	function date_toText($datetime=""){
		$nicetime = strtotime($datetime);
		return strftime("%B %d, %Y at %I:%M %p", $nicetime);	
					
	}
	spl_autoload_register(function ($class_name) { 
		
		$class_name = strtolower($class_name);    
		$path = LIB_PATH . DS . "{$class_name}.php"; 
	
		if (file_exists($path)) {  
			require_once($path);  
		} else {
			die("The file {$class_name}.php could not be found.");
		}
		 
	});
	
	function currentpage_public(){
		$this_page = $_SERVER['SCRIPT_NAME'];  
	    $bits = explode('/',$this_page);
	    $this_page = $bits[count($bits)-1]; 
	    $this_script = $bits[0];  
		 return $bits[2];
	  
	}

	function currentpage_admin(){
		$this_page = $_SERVER['SCRIPT_NAME'];  
	    $bits = explode('/',$this_page);
	    $this_page = $bits[count($bits)-1];  
	    $this_script = $bits[0]; 
		 return $bits[4];
	  
	}
   
function curPageName() {
 return substr($_SERVER['REQUEST_URI'], 21, strrpos($_SERVER['REQUEST_URI'], '/')-24);
}

 

function currentpage(){
		$this_page = $_SERVER['SCRIPT_NAME']; // will return /path/to/file.php
	    $bits = explode('/',$this_page);
	    $this_page = $bits[count($bits)-1]; // will return file.php, with parameters if case, like file.php?id=2
	    $this_script = $bits[0]; // will return file.php, no parameters*/
		 return $bits[2];
	  
	}
	function publiccurrentpage(){
		$this_page = $_SERVER['SCRIPT_NAME']; // will return /path/to/file.php
	    $bits = explode('/',$this_page);
	    $this_page = $bits[count($bits)-1]; // will return file.php, with parameters if case, like file.php?id=2
	    $this_script = $bits[0]; // will return file.php, no parameters*/
		 return $bits[3];
	  
	}
	 // echo publiccurrentpage();
	function msgBox($msg=""){
		?>
		<script type="text/javascript">
			 alert(<?php echo $msg; ?>)
		</script>
		<?php
	}



	function resolve_invoice_service($skuOrName) {
		global $mydb;

		$input = trim((string)$skuOrName);
		if ($input === '') {
			return null;
		}

		$escaped = addslashes($input);
		$mydb->setQuery("SELECT * FROM tblservices WHERE SKU = '{$escaped}' LIMIT 1");
		$service = $mydb->loadSingleResult();
		if ($service) {
			return $service;
		}

		$mydb->setQuery("SELECT * FROM tblservices WHERE Services = '{$escaped}' LIMIT 1");
		$service = $mydb->loadSingleResult();
		if ($service) {
			return $service;
		}

		$baseName = trim(preg_replace('/\s*\(.*/', '', $input));
		if ($baseName !== '' && $baseName !== $input) {
			$escapedBase = addslashes($baseName);
			$mydb->setQuery("SELECT * FROM tblservices WHERE Services = '{$escapedBase}' LIMIT 1");
			$service = $mydb->loadSingleResult();
			if ($service) {
				return $service;
			}
		}

		return null;
	}

	function invoice_line_exists($invno, $sku) {
		global $mydb;

		$invno = addslashes($invno);
		$sku = addslashes($sku);
		$mydb->setQuery("SELECT InvoiceID FROM tblinvoice WHERE InvoiceNo = '{$invno}' AND SKU = '{$sku}' LIMIT 1");

		return (bool)$mydb->loadSingleResult();
	}

	function update_invoice_payment_totals($invno, $userId = null) {
		global $mydb;

		if ($userId === null && isset($_SESSION['ADMIN_USERID'])) {
			$userId = (int)$_SESSION['ADMIN_USERID'];
		}

		$invno = addslashes($invno);
		$mydb->setQuery("SELECT * FROM tblinvoice WHERE InvoiceNo = '{$invno}'");
		$lines = $mydb->loadResultList();

		$totalQty = 0;
		$totalAmount = 0;
		foreach ($lines as $line) {
			$totalQty += (int)$line->QTY;
			$totalAmount += (float)$line->Price;
		}

		$userId = (int)$userId;
		$mydb->setQuery("UPDATE tblpayments SET TotalQTY = '{$totalQty}', TotalAmount = '{$totalAmount}', UserID = '{$userId}' WHERE InvoiceNo = '{$invno}'");
		$mydb->executeQuery();
	}

	function deduplicate_invoice_lines($invno) {
		global $mydb;

		$invno = addslashes($invno);
		$mydb->setQuery("SELECT SKU, MIN(InvoiceID) AS keep_id, COUNT(*) AS cnt FROM tblinvoice WHERE InvoiceNo = '{$invno}' GROUP BY SKU HAVING cnt > 1");
		$duplicates = $mydb->loadResultList();

		if (empty($duplicates)) {
			return false;
		}

		foreach ($duplicates as $dup) {
			$keepId = (int)$dup->keep_id;
			$sku = addslashes($dup->SKU);
			$mydb->setQuery("DELETE FROM tblinvoice WHERE InvoiceNo = '{$invno}' AND SKU = '{$sku}' AND InvoiceID != {$keepId}");
			$mydb->executeQuery();
		}

		update_invoice_payment_totals($invno);
		return true;
	}

	function deduplicate_payment_records($invno = null) {
		global $mydb;

		$where = "Class = 'Invoice'";
		if ($invno !== null && $invno !== '') {
			$where .= " AND InvoiceNo = '" . addslashes($invno) . "'";
		}

		$mydb->setQuery("SELECT InvoiceNo, COUNT(*) AS cnt FROM tblpayments WHERE {$where} GROUP BY InvoiceNo HAVING cnt > 1");
		$duplicates = $mydb->loadResultList();

		if (empty($duplicates)) {
			return false;
		}

		foreach ($duplicates as $dup) {
			$invoiceNo = addslashes($dup->InvoiceNo);
			$mydb->setQuery("SELECT PaymentID FROM tblpayments WHERE InvoiceNo = '{$invoiceNo}' AND Class = 'Invoice' ORDER BY (TotalAmount > 0) DESC, TotalAmount DESC, PaymentID DESC LIMIT 1");
			$keep = $mydb->loadSingleResult();
			if (!$keep) {
				continue;
			}

			$keepId = (int)$keep->PaymentID;
			$mydb->setQuery("DELETE FROM tblpayments WHERE InvoiceNo = '{$invoiceNo}' AND Class = 'Invoice' AND PaymentID != {$keepId}");
			$mydb->executeQuery();
		}

		return true;
	}

	function cleanup_invoice_records($invno) {
		deduplicate_payment_records($invno);
		deduplicate_invoice_lines($invno);
	}

	function cleanup_patient_invoices($patientName) {
		global $mydb;

		$escaped = addslashes(trim((string)$patientName));
		if ($escaped === '') {
			return;
		}

		$mydb->setQuery("SELECT DISTINCT InvoiceNo FROM tblpayments WHERE Patients = '{$escaped}' AND Class = 'Invoice'");
		$invoices = $mydb->loadResultList();
		foreach ($invoices as $row) {
			cleanup_invoice_records($row->InvoiceNo);
		}
	}

	function get_patient_treatment_history($patientName) {
		global $mydb;

		$escaped = addslashes(trim((string)$patientName));
		if ($escaped === '') {
			return array();
		}

		$sql = "SELECT i.*, s.Services AS CatalogService, s.Description AS CatalogDescription,
				pay.InvoiceDate, pay.InvoiceNo
			FROM tblinvoice i
			INNER JOIN (
				SELECT InvoiceNo, MAX(InvoiceDate) AS InvoiceDate
				FROM tblpayments
				WHERE Patients = '{$escaped}' AND Class = 'Invoice' AND TotalAmount > 0
				GROUP BY InvoiceNo
			) pay ON i.InvoiceNo = pay.InvoiceNo
			INNER JOIN (
				SELECT MIN(InvoiceID) AS InvoiceID
				FROM tblinvoice
				GROUP BY InvoiceNo, SKU
			) uniq ON i.InvoiceID = uniq.InvoiceID
			LEFT JOIN tblservices s ON i.SKU = s.SKU
			ORDER BY pay.InvoiceDate DESC, i.InvoiceID ASC";

		$mydb->setQuery($sql);
		return $mydb->loadResultList();
	}

	function get_patient_treated_teeth($patientName) {
		global $mydb;

		$escaped = addslashes(trim((string)$patientName));
		if ($escaped === '') {
			return array();
		}

		$sql = "SELECT DISTINCT i.ToothNumber
			FROM tblinvoice i
			INNER JOIN (
				SELECT InvoiceNo
				FROM tblpayments
				WHERE Patients = '{$escaped}' AND Class = 'Invoice' AND TotalAmount > 0
				GROUP BY InvoiceNo
			) pay ON i.InvoiceNo = pay.InvoiceNo
			INNER JOIN (
				SELECT MIN(InvoiceID) AS InvoiceID
				FROM tblinvoice
				GROUP BY InvoiceNo, SKU
			) uniq ON i.InvoiceID = uniq.InvoiceID
			WHERE i.ToothNumber IS NOT NULL
				AND TRIM(i.ToothNumber) <> ''
				AND TRIM(i.ToothNumber) <> '0'";

		$mydb->setQuery($sql);
		return $mydb->loadResultList();
	}

	function invoice_service_label($line, $catalogService = '', $catalogDescription = '') {
		$catalogService = trim((string)$catalogService);
		if ($catalogService !== '') {
			$label = $catalogService;
			$catalogDescription = trim((string)$catalogDescription);
			if ($catalogDescription !== '') {
				$label .= ' ( ' . $catalogDescription . ' )';
			}
			return $label;
		}

		return trim((string)$line->Services);
	}

	function Add_Invoice($invno,$sku,$class){
		global $mydb;

		$UserID = $_SESSION['ADMIN_USERID'];
		$pro = resolve_invoice_service($sku);
		if (!$pro) {
			return;
		}

		$sku = $pro->SKU;
		if (invoice_line_exists($invno, $sku)) {
			update_invoice_payment_totals($invno, $UserID);
			return;
		}

		if ($pro->Description == "") {
			$desc = "";
		} else {
			$desc = ' ( ' . $pro->Description . ' )';
		}

		$toothnumber = $pro->ToothNumber;
		$product = $pro->Services . $desc;
		$price = $pro->OriginalPrice;
		$qty = 1;
		$subtotal = $pro->OriginalPrice;
		$Remarks = "";
		$class = 'Invoice';

		$inv = new Invoice();
		$inv->InvoiceNo          = $invno;
		$inv->SKU                = $sku;
		$inv->ToothNumber        = $toothnumber;
		$inv->Services           = $product;
		$inv->Price              = $price;
		$inv->QTY                = $qty;
		$inv->SubTotal           = $subtotal;
		$inv->Remarks            = $Remarks;
		$inv->UserID             = $UserID;
		$inv->Class              = $class;
		$inv->create();

		update_invoice_payment_totals($invno, $UserID);
}
function Update_Invoice($invno,$sku,$qty){
		global $mydb;

		$TotalQTY =0;
		$TotalTax  =0;
		$TotalAmount  =0;
		$stocks = 0;
		$sold = 0;
		$remaining = 0;
		$UserID = $_SESSION['ADMIN_USERID'];
		// $invno = $invno 
		$sql = "SELECT *,i.Price as invPrice FROM tblinvoice i, tblservices p WHERE i.SKU=p.SKU AND InvoiceNo='{$invno}' AND i.SKU='{$sku}'";
		$mydb->setQuery($sql); 
		$res_row = $mydb->executeQuery();
		$max_row = $mydb->num_rows($res_row);

		// echo $max_row;

		if ($max_row > 0) { 

			// geting the exact qty of the invoice



			$inv = $mydb->loadSingleResult();  
			$product=$inv;
			// $changePrice = $inv->ChangePrice;
			// $updatePrice=$inv->invPrice;   
			$OriginalPrice=$inv->OriginalPrice;   
			$Remarks=""; 


			// if ($changePrice == 0) {
				# code... 
	    //           $sql = "SELECT * FROM tblbulkpricing WHERE SKU ='{$sku}' AND '{$qty}' >= QTY ORDER BY QTY DESC";
	    //           $mydb->setQuery($sql);
	    //           $curbulk = $mydb->executeQuery($sql);
	    //           $numrow = $mydb->num_rows($curbulk);

	    //           if ($numrow > 0) { 
	    //             $bulk = $mydb->loadSingleResult(); 

	    //             $price = $bulk->Price / $bulk->QTY; 

	             

					// $subtotal=$price;

	    //           }else{  

	                $price =$OriginalPrice;
					$subtotal=$price;
	              // }
			// }else{
			// 	$price =$updatePrice;
			// 	$subtotal=$price * $qty; 
			// }



 


// updating invoice..
		 	$sql = "UPDATE tblinvoice SET Price='{$price}',QTY='{$qty}',SubTotal='{$subtotal}'
			WHERE InvoiceNo='{$invno}' AND SKU='{$sku}'";
			$mydb->setQuery($sql); 
			$mydb->executeQuery(); 


		// updating the payments
			$sql = "SELECT * FROM `tblinvoice` WHERE `InvoiceNo`='{$invno}'";
			$mydb->setQuery($sql);  
			$cur_inv = $mydb->loadResultList();

			foreach ($cur_inv as $result) {    
				$TotalQTY += $result->QTY; 
				$TotalAmount += $result->SubTotal;
			}
 
			$sql = "UPDATE `tblpayments` SET `TotalQTY`='{$TotalQTY}',  `TotalAmount`='{$TotalAmount}' , `UserID`='{$UserID}' WHERE `InvoiceNo`='{$invno}'";
			$mydb->setQuery($sql);
			$mydb->executeQuery();
			// end of payments
  
		

		}   
			
} 



function enbledisbletax($taxinfo){
	global $mydb; 

	  $sql = "SELECT * FROM tbltaxsettings LIMIT 1";
	  $mydb->setQuery($sql);
	  $taxseting = $mydb->executeQuery($sql);
	  $numrow = $mydb->num_rows($taxseting);

	  if ($numrow > 0) { 
	    $taxres = $mydb->loadSingleResult();
	    if ($taxinfo=="Quote") {
	    	if ($taxres->TaxQuote==1) {
					$enable = 1;
				}else{
					$enable = 0;
				}
	    }else{
	    	if ($taxres->TaxInvoice==1) {
					$enable = 1;
				}else{
					$enable = 0;
				}
	    }
			
	  }else{  
	    $enable = 0;
	  }

	  echo $enable; 
}

// chnaging price for the invoice

function Update_Invoice_change_price($invno,$sku,$qty,$price,$chck){
		global $mydb;

		$TotalQTY =0;
		$TotalTax  =0;
		$TotalAmount  =0;
		$stocks = 0;
		$sold = 0;
		$remaining = 0;
		$UserID = $_SESSION['ADMIN_USERID'];
		// $invno = $invno 
		$sql = "SELECT *,i.Price as invPrice FROM tblinvoice i, tblservices p WHERE i.SKU=p.SKU AND InvoiceNo='{$invno}' AND i.SKU='{$sku}'";
		$mydb->setQuery($sql); 
		$res_row = $mydb->executeQuery();
		$max_row = $mydb->num_rows($res_row);

		// echo $max_row;

		if ($max_row > 0) { 

			// geting the exact price of the invoice

			$inv = $mydb->loadSingleResult();  

			$product=$inv; 
			$subtotal=$price;
			// $taxrate=$inv->TaxRate; 
			$Remarks=""; 


			  
// updating invoice..
		 	$sql = "UPDATE tblinvoice SET Price='{$price}',QTY='{$qty}',SubTotal='{$subtotal}',TaxAmount='{$tax}',ChangePrice='{$chck}',Remarks='' WHERE InvoiceNo='{$invno}' AND SKU='{$sku}'";
			$mydb->setQuery($sql); 
			$res = $mydb->executeQuery(); 

			echo $res;


		// updating the payments
			$sql = "SELECT * FROM `tblinvoice` WHERE `InvoiceNo`='{$invno}'";
			$mydb->setQuery($sql);  
			$cur_inv = $mydb->loadResultList();

			foreach ($cur_inv as $result) {    
				$TotalQTY += $result->QTY;
				// $TotalTax += $result->TaxAmount;
				$TotalAmount += $result->SubTotal;
			}
 
			$sql = "UPDATE `tblpayments` SET `TotalQTY`='{$TotalQTY}', `TotalTax`='{$TotalTax}', `TotalAmount`='{$TotalAmount}' , `UserID`='{$UserID}' WHERE `InvoiceNo`='{$invno}'";
			$mydb->setQuery($sql);
			$mydb->executeQuery();
			// end of payments


  
		

		}   
			
}

function invoice_file_label($invno, $patientName = '') {
	$patientName = trim((string)$patientName);
	$safeName = preg_replace('/[<>:"\/\\\\|?*]/', '', $patientName);
	$safeName = preg_replace('/\s+/', ' ', $safeName);
	$safeName = str_replace(' ', '_', $safeName);

	if ($safeName === '' || strtoupper($safeName) === 'NONE') {
		return $invno;
	}

	return $invno . ' - ' . $safeName;
}

function invoice_has_line_items($invno) {
	global $mydb;
	$invno = addslashes($invno);
	$mydb->setQuery("SELECT COUNT(*) AS cnt FROM tblinvoice WHERE InvoiceNo = '{$invno}'");
	$row = $mydb->loadSingleResult();
	return $row && (int)$row->cnt > 0;
}

function delete_empty_invoice($invno) {
	global $mydb;
	$invno = addslashes(trim((string)$invno));
	if ($invno === '') {
		return false;
	}

	$mydb->setQuery("SELECT InvoiceNo, TotalAmount FROM tblpayments WHERE InvoiceNo = '{$invno}' AND Class = 'Invoice' LIMIT 1");
	$payment = $mydb->loadSingleResult();
	if (!$payment) {
		return false;
	}

	if ((float)$payment->TotalAmount > 0 && invoice_has_line_items($invno)) {
		return false;
	}

	$mydb->setQuery("DELETE FROM tblinvoice WHERE InvoiceNo = '{$invno}'");
	$mydb->executeQuery();
	$mydb->setQuery("DELETE FROM tblpayments WHERE InvoiceNo = '{$invno}'");
	$mydb->executeQuery();

	return true;
}

function cleanup_empty_invoices() {
	global $mydb;
	deduplicate_payment_records();
	$mydb->setQuery("SELECT InvoiceNo FROM tblpayments WHERE Class = 'Invoice' AND (TotalAmount IS NULL OR TotalAmount <= 0)");
	$rows = $mydb->loadResultList();
	foreach ($rows as $row) {
		delete_empty_invoice($row->InvoiceNo);
	}
}

function resolve_age_group_by_age($patientAge) {
	global $mydb;
	$patientAge = (int)$patientAge;
	if ($patientAge <= 0) {
		return null;
	}

	$mydb->setQuery("SELECT * FROM tbl_age_groups
		WHERE {$patientAge} >= MinAge
		AND ({$patientAge} <= MaxAge OR MaxAge IS NULL)
		ORDER BY MinAge DESC
		LIMIT 1");
	return $mydb->loadSingleResult();
}

function set_patient_age_group_session($patientAge) {
	$group = resolve_age_group_by_age($patientAge);
	$_SESSION['PatientAgeGroupID'] = $group ? (int)$group->AgeGroupID : null;
	$_SESSION['PatientAgeGroupLabel'] = $group ? $group->Description : null;
	$_SESSION['PatientAgeGroupMaxTeeth'] = $group ? (int)$group->ToothCount : null;
	return $group;
}

function sync_patient_age_group_from_name($patientName) {
	$patientName = trim((string)$patientName);
	if ($patientName === '' || strtoupper($patientName) === 'NONE') {
		unset($_SESSION['PatientAgeGroupID'], $_SESSION['PatientAgeGroupLabel'], $_SESSION['PatientAgeGroupMaxTeeth']);
		return null;
	}

	global $mydb;
	$escaped = addslashes($patientName);
	$mydb->setQuery("SELECT Age FROM tblpatients WHERE CONCAT(Fname, ' ', Mname, ' ', Lname) = '{$escaped}' LIMIT 1");
	$patient = $mydb->loadSingleResult();
	if (!$patient) {
		unset($_SESSION['PatientAgeGroupID'], $_SESSION['PatientAgeGroupLabel'], $_SESSION['PatientAgeGroupMaxTeeth']);
		return null;
	}

	return set_patient_age_group_session($patient->Age);
}

function get_invoice_cart_skus($invno) {
	global $mydb;
	$invno = addslashes($invno);
	$mydb->setQuery("SELECT DISTINCT SKU FROM tblinvoice WHERE InvoiceNo = '{$invno}'");
	$rows = $mydb->loadResultList();
	$skus = array();
	foreach ($rows as $row) {
		$skus[$row->SKU] = true;
	}
	return $skus;
}

function service_tooth_is_general($toothNumber) {
	$tooth = strtolower(trim((string)$toothNumber));
	return $tooth === '' || $tooth === '0' || $tooth === 'all';
}

function service_applies_to_tooth($serviceTooth, $filterTooth) {
	$filterTooth = (int)$filterTooth;
	if ($filterTooth <= 0) {
		return true;
	}
	if (service_tooth_is_general($serviceTooth)) {
		return true;
	}

	$parts = explode(',', strtolower(trim((string)$serviceTooth)));
	foreach ($parts as $part) {
		$part = trim($part);
		if ($part === '' || $part === '0' || $part === 'all') {
			continue;
		}
		if (strpos($part, '-') !== false) {
			$range = explode('-', $part);
			$start = (int)trim($range[0]);
			$end = (int)trim($range[1]);
			if ($filterTooth >= $start && $filterTooth <= $end) {
				return true;
			}
		} elseif ((int)$part === $filterTooth) {
			return true;
		}
	}

	return false;
}

function fetch_filtered_invoice_services($options = array()) {
	global $mydb;

	$search = isset($options['search']) ? trim($options['search']) : '';
	$ageGroupId = array_key_exists('age_group_id', $options) ? $options['age_group_id'] : null;
	$scope = isset($options['scope']) ? $options['scope'] : 'all';
	$tooth = isset($options['tooth']) ? (int)$options['tooth'] : 0;

	$where = array('1=1');
	if ($ageGroupId !== null) {
		$where[] = "s.AgeGroupID = '" . (int)$ageGroupId . "'";
	}
	if ($search !== '') {
		$escaped = addslashes($search);
		$where[] = "(s.SKU LIKE '%{$escaped}%' OR s.Services LIKE '%{$escaped}%' OR s.Description LIKE '%{$escaped}%')";
	}

	$sql = "SELECT s.*, ag.Description AS AgeGroupDesc, ag.ToothCount
		FROM tblservices s
		LEFT JOIN tbl_age_groups ag ON s.AgeGroupID = ag.AgeGroupID
		WHERE " . implode(' AND ', $where) . "
		ORDER BY s.Services ASC";
	$mydb->setQuery($sql);
	$services = $mydb->loadResultList();
	$filtered = array();

	foreach ($services as $service) {
		if ($scope === 'general' && !service_tooth_is_general($service->ToothNumber)) {
			continue;
		}
		if ($scope === 'tooth' && !service_applies_to_tooth($service->ToothNumber, $tooth)) {
			continue;
		}
		$filtered[] = $service;
	}

	return $filtered;
}

function render_invoice_service_picker_rows($services, $addedSkus = array(), $currency = '') {
	if (empty($services)) {
		return '<tr><td colspan="5"><div class="service-picker-empty">No services match the current filters.</div></td></tr>';
	}

	$html = '';
	foreach ($services as $result) {
		$isAdded = isset($addedSkus[$result->SKU]);
		$rowClass = 'service-picker-row' . ($isAdded ? ' is-added' : '');
		$toothLabel = service_tooth_is_general($result->ToothNumber)
			? '<span class="badge text-bg-info">All teeth</span>'
			: '<span class="badge text-bg-light text-dark border">' . htmlspecialchars($result->ToothNumber) . '</span>';

		$html .= '<tr class="' . $rowClass . '">';
		$html .= '<td class="text-center"><div class="form-check mb-0 d-flex justify-content-center">';
		$html .= '<input class="form-check-input service-picker-check" type="checkbox" name="selector[]" value="' . htmlspecialchars($result->SKU) . '"' . ($isAdded ? ' disabled checked' : '') . '>';
		$html .= '</div></td>';
		$html .= '<td><span class="fw-semibold">' . htmlspecialchars($result->Services) . '</span>';
		if ($isAdded) {
			$html .= ' <span class="badge text-bg-success ms-1">On invoice</span>';
		}
		$html .= '</td>';
		$html .= '<td class="text-muted">' . ($result->Description ? htmlspecialchars($result->Description) : '—') . '</td>';
		$html .= '<td>' . $toothLabel . '</td>';
		$html .= '<td class="text-end text-nowrap">' . number_format($result->OriginalPrice, 2);
		if ($currency !== '') {
			$html .= ' <span class="text-muted small">' . htmlspecialchars($currency) . '</span>';
		}
		$html .= '</td>';
		$html .= '</tr>';
	}

	return $html;
}
?>