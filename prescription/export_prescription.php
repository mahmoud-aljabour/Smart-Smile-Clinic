<?php
require_once("../include/initialize.php");

$presc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($presc_id <= 0) {
  die("معرف الروشته غير صالح.");
}

// جلب بيانات الروشته + المريض + الطبيب
$sql = "
    SELECT pr.*, 
           CONCAT(p.Fname, ' ', p.Lname) AS patient_name,
           p.ContactNo AS patient_phone,
           'Doctor' AS doctor_name  -- ← تم التصحيح هنا
    FROM prescriptions pr
    JOIN tblpatients p ON pr.patient_id = p.PatientID
    WHERE pr.id = {$presc_id}
";
$mydb->setQuery($sql);
$prescription = $mydb->loadSingleResult();

if (!$prescription) {
  die("الروشته غير موجودة.");
}

// إعداد التصدير إلى Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=' . date('Y-m-d') . '-Prescription-' . $presc_id . '.xls');
?>

<style type="text/css">
  body {
    font-family: Arial, sans-serif;
    direction: rtl;
  }

  .header {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
  }

  .section {
    margin: 15px 0;
  }

  .label {
    font-weight: bold;
    display: inline-block;
    width: 150px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
  }

  th,
  td {
    border: 1px solid #000;
    padding: 8px;
    text-align: right;
  }
</style>

<div class="header">روشته طبية</div>

<div class="section">
  <div><span class="label">اسم المريض:</span> <?php echo htmlspecialchars($prescription->patient_name); ?></div>
  <div><span class="label">رقم الهاتف:</span> <?php echo htmlspecialchars($prescription->patient_phone); ?></div>
  <div><span class="label">تاريخ الإصدار:</span> <?php echo date('d/m/Y', strtotime($prescription->created_at)); ?></div>
</div>

<div class="section">
  <table>
    <thead>
      <tr>
        <th>اسم الدواء</th>
        <th>الجرعة</th>
        <th>توقيت الاستخدام</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo htmlspecialchars($prescription->medicine_name); ?></td>
        <td><?php echo htmlspecialchars($prescription->dosage); ?></td>
        <td><?php echo htmlspecialchars($prescription->timing ?: 'غير محدد'); ?></td>
      </tr>
    </tbody>
  </table>
</div>

<?php if (!empty($prescription->medical_advice)): ?>
  <div class="section">
    <div class="label">نصائح طبية:</div>
    <div><?php echo nl2br(htmlspecialchars($prescription->medical_advice)); ?></div>
  </div>
<?php endif; ?>

<div class="section">
  <div><span class="label">الطبيب:</span> <?php echo htmlspecialchars($prescription->doctor_name); ?></div>
</div>