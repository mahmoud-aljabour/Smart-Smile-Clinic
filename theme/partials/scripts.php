<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo web_root; ?>assets/js/theme.js"></script>

<script src="<?php echo web_root; ?>fullcalendar/lib/moment.min.js"></script>
<script src="<?php echo web_root; ?>fullcalendar/fullcalendar.min.js"></script>

<script src="<?php echo web_root; ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo web_root; ?>plugins/datepicker/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="<?php echo web_root; ?>plugins/datepicker/locales/bootstrap-datetimepicker.uk.js" charset="UTF-8"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script src="<?php echo web_root; ?>plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo web_root; ?>plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo web_root; ?>plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="<?php echo web_root; ?>plugins/select2/select2.full.min.js"></script>
<script src="<?php echo web_root; ?>plugins/autocomplete/jquery.auto-complete.min.js"></script>

<script>
  function hideMsg() {
    $("#check_msg").hide();
  }
  setTimeout(function() { hideMsg(); }, 3000);
  setTimeout(function() { window.location = "<?php echo web_root; ?>logout.php"; }, 1800000);

  $(".addbulk").click(function() {
    var sku = $(this).data("id");
    $.ajax({
      type: "POST",
      url: "modalproduct.php",
      dataType: "text",
      data: { id: sku },
      success: function(data) { $("#addbulkmodal").html(data); }
    });
  });

  $(function() {
    $(".btn-danger").click(function() {
      return confirm("Are you sure you want to delete this?");
    });
  });

  $(function() {
    $(".btn-trans").click(function() {
      return confirm("Are you sure you want to cancel this?");
    });
  });

  $(function() {
    $.ajax({
      type: "POST",
      url: "loaddata.php",
      dataType: "text",
      success: function(data) {
        $('#searchclient').show().html(data);
      }
    });
  });

  $(function() {
    $.ajax({
      type: "POST",
      url: "loadcart.php",
      dataType: "text",
      success: function(data) {
        $('#loadcart').show().html(data);
      }
    });
  });

  $(function() { $('.select2').select2(); });

  $(function() {
    if ($("#dash-table").length) {
      $("#dash-table").DataTable({ pageLength: 50 });
    }
    if ($('#dash-table2').length) {
      $('#dash-table2').DataTable({
        paging: true, lengthChange: false, searching: false,
        ordering: true, info: true, autoWidth: false
      });
    }
  });

  $(function() {
    if ($("#dash-tables").length) {
      $("#dash-tables").DataTable({
        order: [[2, "desc"]],
        pageLength: 50
      });
    }
  });

  $('input[data-mask]').each(function() {
    var input = $(this);
    input.setMask(input.data('mask'));
  });

  if ($('#appointmentTime').length) {
    $('#appointmentTime').inputmask({
      mask: "h:s t\\m", placeholder: "hh:mm xm", alias: "datetime", hourFormat: "12"
    });
  }

  if ($('#DueDate').length) {
    $('#DueDate').inputmask({ mask: "2/1/y", placeholder: "mm/dd/yyyy", alias: "date", hourFormat: "24" });
  }

  if ($('#DateInvoiced').length) {
    $('#DateInvoiced').inputmask({ mask: "2/1/y", placeholder: "mm/dd/yyyy", alias: "date", hourFormat: "24" });
  }

  if ($('.date_picker').length) {
    $('.date_picker').datetimepicker({
      format: 'mm/dd/yyyy', startDate: '01/01/1950', language: 'en',
      weekStart: 1, todayBtn: 1, autoclose: 1, todayHighlight: 1,
      startView: 2, minView: 2, forceParse: 0
    });
  }

  $("#addtoinvoice").on("click", function(e) {
    var inv = document.getElementById('InvoiceNo').value;
    var product = document.getElementById("SKU").value;
    $.ajax({
      type: "POST", url: "loadcart.php", dataType: "text",
      data: { SKU: product, invno: inv },
      beforeSend: function() {
        $("#loading-client").show();
        $("#invoicing-body").hide();
      },
      success: function(data) {
        $("#loading-client").hide();
        $("#invoicing-body").show();
        $('#loadcart').show().html(data);
        $("#SKU").val('').focus();
      }
    });
    e.preventDefault();
  });
</script>

<?php
$products = [];
$sql = "Select * From tblservices";
$mydb->setQuery($sql);
$cur = $mydb->loadResultList();
foreach ($cur as $result) {
  $products[] = $result->Services;
}
?>

<script>
  var pro = <?php echo json_encode($products); ?>;

  if ($("#SKU").length) {
    $("#SKU").autoComplete({
      minChars: 1,
      source: function(term, suggest) {
        term = term.toLowerCase();
        var matches = [];
        for (var i = 0; i < pro.length; i++)
          if (~pro[i].toLowerCase().indexOf(term)) matches.push(pro[i]);
        suggest(matches);
      }
    });
  }

  <?php
  $data_products = [];
  $sql = "Select * From tblservices ";
  $mydb->setQuery($sql);
  $cur = $mydb->loadResultList();
  foreach ($cur as $result) {
    $data_products[] = $result->Services;
  }
  ?>
  var availableTags = <?php echo json_encode($data_products); ?>;

  if ($("#findProducts").length) {
    $("#findProducts").autoComplete({
      minChars: 0,
      source: function(term, suggest) {
        term = term.toLowerCase();
        var matches = [];
        for (var i = 0; i < availableTags.length; i++)
          if (~availableTags[i].toLowerCase().indexOf(term)) matches.push(availableTags[i]);
        suggest(matches);
      }
    });

    $("#findProducts").on("keyup change", function() {
      var searchvalue = $(this).val();
      $.ajax({
        type: "POST", url: "loaddashboard.php", dataType: "text",
        data: { search_data: searchvalue },
        success: function(data) { $("#loaddashboard").html(data); }
      });
    });
  }

  function validate_fields() {
    var unit = $("#Unit").val();
    var supplierid = $("#SuplierID").val();
    if (unit == "None") { alert("Please choose a unit for the Product."); return false; }
    if (supplierid == "None") { alert("Please choose a supplier for the Product."); return false; }
  }

  $(".date_inv").on("change", function() {
    var invdate = document.getElementById('DateInvoiced').value;
    var duedate = document.getElementById('DueDate').value;
    var invno = document.getElementById('InvoiceNo').value;
    $.ajax({
      type: "POST", url: "controller.php?action=updatedate", dataType: "text",
      data: 'invdate=' + invdate + '&duedate=' + duedate + '&invno=' + invno
    });
  });

  $(".editinv").click(function() {
    $("#modal-body #invno").val($(this).data("id"));
  });

  $(function() {
    $("#createinvoice").click(function() {
      return confirm("Are you sure you want to create a new Invoice?");
    });
    $("#createquote").click(function() {
      return confirm("Are you sure you want to create a new Quote?");
    });
  });

  function IsNumeric(input) {
    return /^-{0,1}\d*\.{0,1}\d+$/.test(input);
  }

  $("#bulk_form").submit(function() {
    var qty = $("#QTY").val();
    var price = $("#Price").val();
    if (qty == 0 || qty == 1 || qty == "") {
      alert("Please put the right quantity.");
      $("#QTY").focus();
      return false;
    } else if (price == 0 || price == "" || !IsNumeric(price)) {
      alert("Please put the right amount.");
      $("#Price").focus();
      return false;
    }
    return true;
  });
</script>
</body>
</html>
