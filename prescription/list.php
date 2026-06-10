<?php
// تأكد من تضمين initialize.php في مكان ما قبل هذا الكود
// إذا لم يكن موجودًا بالفعل: require_once("../include/initialize.php"); 

if (!isset($_SESSION['ADMIN_USERID'])) {
    message("Please login as a doctor.", "error");
    redirect("../index.php");
}

// ------------------------------------------------------------------
// ⭐⭐ تم حذف الكود التالي لأنه لا ينتمي لصفحة العرض ⭐⭐
// $autonum = new Autonumber();
// $res = $autonum->set_autonumber('PRESCRIPTION');
// $PRESCRIPTION = $res->AUTO;
// ------------------------------------------------------------------

global $mydb;
// 💡 ملاحظة: يجب أن تتأكد من أن كلاس Autonumber متاح للاستخدام في أي صفحة أخرى تستدعي set_autonumber.
?>

<style type="text/css">
    #stretch a>img {
        width: 100%;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <?php if ($_SESSION['ADMIN_ROLE'] == "Administrator") { ?>
            <h1 class="page-header">
                <a href="index.php?view=add_prescription" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle fw-fa"></i> Add New Prescription
                </a>
            </h1>
        <?php } ?>
    </div>
</div>

<div class="table-responsive">
    <table id="dash-tables" class="table table-striped table-bordered table-hover" style="font-size:12px" cellspacing="0">
        <thead>
            <tr>
                <th>PRESCRIPTION NO.</th>
                <th>Patient Name</th>
                <th>Medicine Name</th>
                <th>Dosage</th>
                <th>Creation Date</th>
                <th>Doctor</th>
                <th width="18%">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "
    SELECT pr.*, 
           CONCAT(p.Fname,' ' , Mname, '', p.Lname) AS patient_name,
           p.ContactNo AS patient_phone,
           FullName AS doctor_name
    FROM prescriptions pr
    LEFT JOIN tblpatients p ON pr.patient_id = p.PatientID
    JOIN tblusers u ON pr.user_id = u.UserID
    WHERE u.Username = 'admin'
    
";
            $mydb->setQuery($sql);
            $prescriptions = $mydb->loadResultList();

            if (empty($prescriptions)) {
                // ... (عرض رسالة لا يوجد بيانات) ...
                echo '<tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align:center;">No prescriptions available yet.</td>
                      </tr>';
            } else {
                $counter = 1;
                foreach ($prescriptions as $pr) {
                    echo '<tr>';
                    // ⭐⭐ التعديل الرئيسي: استخدام حقل قاعدة البيانات الفعلي
                    echo '<td>' . htmlspecialchars($pr->prescription_no ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($pr->patient_name ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($pr->medicine_name ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($pr->dosage ?? '') . '</td>';
                    echo '<td>' . ($pr->created_at ? date('d/m/Y', strtotime($pr->created_at)) : '') . '</td>';
                    echo '<td>' . htmlspecialchars($pr->doctor_name ?? '') . '</td>';
                    echo '<td>';

                    echo '<a href="index.php?view=view&id=' . $pr->id . '" class="btn btn-md btn-info" title="View"> <i class="fa fa-info"></i> View</a> ';


                    if ($_SESSION['ADMIN_ROLE'] == 'Administrator' || $_SESSION['ADMIN_ROLE'] == 'admin') {

                        echo '<a href="index.php?view=edit_prescription&presc_id=' . $pr->id . '" class="btn btn-md btn-primary">
                            <i class="fa fa-edit"></i>Edit
                            </a>';

                        // <a href="controller.php?action=delete&id=' . $result->InvoiceNo . '" class="btn btn-md btn-danger"><i class="fa fa-trash"></i> Delete</a></td>';
                        echo '<a href="controller.php?action=delete_prescription&id=' . $pr->id . '" class="btn btn-md btn-danger">
                        <i class="fa fa-trash"></i> Delete
                        </a>';
                    }

                    echo '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>