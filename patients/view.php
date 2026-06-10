<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
	redirect(web_root . "login.php");
}

$sql = "SELECT * FROM tblpatients WHERE PatientID=" . $_GET['id'];
$mydb->setQuery($sql);
$res = $mydb->loadSingleResult();

$Patients = $res->Fname . ' ' . $res->Mname . ' ' . $res->Lname;

$age = (int)$res->Age; 

if ($age == 0) {
	$total_teeth = 0;
} elseif ($age == 1) {
	$total_teeth = 8;
} elseif ($age == 2) {
	$total_teeth = 16;
} elseif ($age >= 3 && $age <= 5) {
	$total_teeth = 20;
} elseif ($age >= 6 && $age <= 12) {
	$total_teeth = 24;
} elseif ($age >= 13 && $age <= 16) {
	$total_teeth = 28;
} else {
	$total_teeth = 32;
}
?>
<style type="text/css">
	.table-client {
		width: 100%;
	}

	.table-client tr td {
		border-bottom: 1px solid #ddd;
		padding: 10px 0px 0px 0px;
	}
</style>
<div class="col-md-12">
	<table class="table-client">
		<tr>
			<td>Patient Name</td>
			<td> <?php echo $res->Fname . ' ' . $res->Mname . ' ' . $res->Lname; ?></td>

			<td>Sex</td>
			<td><?php echo $res->Sex; ?></td>
		</tr>
		<tr>
			<td>Age</td>
			<td><?php echo $res->Age; ?></td>

			<td>Phone #</td>
			<td><?php echo $res->ContactNo; ?></td>

		</tr>
		<tr>
			<td>Address</td>
			<td colspan="3"> <?php echo $res->F_Address; ?></td>
		</tr>
	</table>
</div>
<br />
<br />
<br />
<br />
<br />
<br />
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Patient History</h1>
	</div>
	<!-- /.col-lg-12 -->
</div>
<form action="controller.php?action=delete" Method="POST">
	<div class="table-responsive">
		<table id="dash-table" class="table table-striped table-bordered table-hover" style="font-size:12px" cellspacing="0">

			<thead>
				<tr>
					<th>Date</th>
					<th>Services</th>
					<th>Price</th>
					<th>Number of Teeth</th>
					<!-- <th>Total</th>  -->
					<!-- <th>Remarks</th> -->
					<!-- <th width="5%" align="center">Action</th> -->
				</tr>
			</thead>
			<tbody>
				<?php
				// SELECT `InvoiceID`, `InvoiceNo`, `SKU`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class` FROM `tblinvoice` WHERE 1
				$mydb->setQuery("SELECT * FROM `tblinvoice` i,`tblpayments` p WHERE i.`InvoiceNo`=p.`InvoiceNo` AND Patients='{$Patients}'");
				$cur = $mydb->loadResultList();
				foreach ($cur as $result) {
					echo '<tr>';
					// `Fullname`, `CompanyName`, `F_Address`, `S_Address`, `ContactNo`
					echo '<td>' . $result->InvoiceDate . '</td>';
					echo '<td>' . $result->Services . '</td>';
					echo '<td>' . number_format($result->Price, 2) . '</td>';
					echo '<td>' . $result->ToothNumber . '</td>';
					// echo '<td>' . $result->SubTotal.'</td>';
					// echo '<td>' . $result->Remarks . '</td>';
					// echo '<td align="center"> 
					// <a title="View" href="index.php?view=edit&id='.$result->InvoiceID.'" class="btn btn-primary btn-md  ">  <span class="fa fa-info fw-fa"> View Records</a>
					// <a title="Edit" href="index.php?view=edit&id='.$result->InvoiceID.'" class="btn btn-primary btn-md  ">  <span class="fa fa-edit fw-fa"></a>
					//      <a title="Delete" href="controller.php?action=delete&id='.$result->InvoiceID.'" class="btn btn-danger btn-md  ">  <span class="fa  fa-trash fw-fa "></a></td>';

					echo '</tr>';

					// $tooth[] =  $result->QTY;


					// 		for ($i=1; $i < 17; $i++) { 
					// 		# code...
					// 			if ($result->QTY==$i) {
					// 				# code...
					// 		echo '<a href="#1"><span style="font-size: 50px" class="icon-iconfinder_Dental_-_Tooth_-_Dentist_-_Dentistry_01_2185089"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span><span class="number" style="color:red">'.$i.'</span>	</a>';
					// 			}else{

					// 		echo '<a href="#1"><span style="font-size: 50px" class="icon-iconfinder_Dental_-_Tooth_-_Dentist_-_Dentistry_01_2185089"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span><span class="number" style="color:blue">'.$i.'</span>	</a>';
					// 			}
					// }
				}
				?>
			</tbody>

		</table>

</form>
<style type="text/css">
	.teeth-chart {
		margin-top: 30px;
	}

	.teeth-chart a {
		text-align: center;
		padding: 5px;
	}

	.number {
		font-size: 20px;
		margin-left: -30px;
		margin-top: 0px;
		position: absolute;
		font-weight: bold;
		color: red;
	}
</style>

<div class="col-md-12 teeth-chart">

	<?php if ($total_teeth > 0): ?>
		<?php
		// الصف العلوي: أول نصف من الأسنان (1 إلى نصف الكل)
		$upper_start = 1;
		$upper_end = floor($total_teeth / 2);
		for ($i = $upper_start; $i <= $upper_end; $i++) {

			$mydb->setQuery("SELECT * FROM `tblinvoice` i,`tblpayments` p 
						WHERE i.`InvoiceNo`=p.`InvoiceNo` 
						AND Patients='{$Patients}' 
						AND ToothNumber = '{$i}' 
						GROUP BY ToothNumber");
			$r = $mydb->executeQuery();
			$maxrow = $mydb->num_rows($r);

			if ($maxrow > 0) {
				$cur = $mydb->loadSingleResult();
				echo '<a href="#1"><span style="font-size: 50px" class="icon-iconfinder_Dental_-_Tooth_-_Dentist_-_Dentistry_01_2185089"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span><span class="number" style="color:red">' . $cur->ToothNumber . '</span>	</a>';
			} else {
				echo '<a href="#1"><span style="font-size: 50px" class="icon-iconfinder_Dental_-_Tooth_-_Dentist_-_Dentistry_01_2185089"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span><span class="number" style="color:blue">' . $i . '</span>	</a>';
			}
		}
		echo '<br/>';

		// الصف السفلي: النصف الثاني، من الأعلى للأسفل (للحفاظ على الترتيب الحالي، بس مقطوع عند $total_teeth)
		$lower_start = floor($total_teeth / 2) + 1;
		$lower_end = $total_teeth;
		for ($i = $lower_start; $i <= $lower_end; $i++) {

			$mydb->setQuery("SELECT * FROM `tblinvoice` i,`tblpayments` p WHERE i.`InvoiceNo`=p.`InvoiceNo` AND Patients='{$Patients}' AND ToothNumber = '{$i}' GROUP BY ToothNumber");
			$r = $mydb->executeQuery();
			$maxrow = $mydb->num_rows($r);

			if ($maxrow > 0) {
				$cur = $mydb->loadSingleResult();
				echo '<a href="#1"><span style="font-size: 50px" class="icon-iconfinder_Dental_-_Tooth_-_Dentist_-_Dentistry_01_2185089"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span><span class="number" style="color:red">' . $cur->ToothNumber . '</span>	</a>';
			} else {
				echo '<a href="#1"><span style="font-size: 50px" class="icon-iconfinder_Dental_-_Tooth_-_Dentist_-_Dentistry_01_2185089"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span><span class="number" style="color:blue">' . $i . '</span>	</a>';
			}
		}
		?>

		<br />
	<?php else: ?>
		<p style="text-align: center; font-size: 18px; color: #666; margin-top: 20px;">لا توجد أسنان للعرض (المريض مولود حديثًا).</p>
	<?php endif; ?>

</div>