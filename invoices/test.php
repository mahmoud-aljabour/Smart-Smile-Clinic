if ($_SESSION['ADMIN_ROLE']=='Administrator') {

if ($status=='Pending') {
echo '<td>
    <a href="controller.php?action=payemnt&id='. $result->InvoiceNo.'" class="btn btn-md btn-success"><i class="fa fa-money"></i> Payment</a>
    <a href="index.php?view=view&id='. $result->InvoiceNo.'" class="btn btn-md btn-info"><i class="fa fa-info"></i> View</a>
    <a href="index.php?view=add&invno='. $result->InvoiceNo.'" class="btn btn-md btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <a href="controller.php?action=delete&id='. $result->InvoiceNo.'" class="btn btn-md btn-danger"><i class="fa fa-trash"></i> Delete</a>
</td>';
}else{
echo '<td>
    <a href="index.php?view=view&id='. $result->InvoiceNo.'" class="btn btn-md btn-info"><i class="fa fa-info"></i> View</a>
    <a href="index.php?view=add&invno='. $result->InvoiceNo.'" class="btn btn-md btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <a href="controller.php?action=delete&id='. $result->InvoiceNo.'" class="btn btn-md btn-danger"><i class="fa fa-trash"></i> Delete</a>
</td>';
}

}else{
if ($status=='Pending') {

echo '<td>
    <a href="index.php?view=view&id='. $result->InvoiceNo.'" class="btn btn-md btn-info"><i class="fa fa-info"></i> View</a> 
</td>';
}else{
echo '<td>
    <a href="index.php?view=view&id='. $result->InvoiceNo.'" class="btn btn-md btn-info"><i class="fa fa-info"></i> View</a>';
    }
    }

    echo '</tr>';
    }
    ?>