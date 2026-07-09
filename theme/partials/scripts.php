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
    $(".btn-trans").click(function() {
      return confirm("Are you sure you want to cancel this?");
    });
  });

  $(function() {
    if ($('#searchclient').length) {
      $.ajax({
        type: "POST",
        url: "loaddata.php",
        dataType: "text",
        success: function(data) {
          $('#searchclient').show().html(data);
          $(document).trigger('invoice:patientPanelUpdated');
        }
      });
    }
  });

  $(function() {
    if ($('#loadcart').length) {
      $.ajax({
        type: "POST",
        url: "loadcart.php",
        dataType: "text",
        success: function(data) {
          $('#loadcart').show().html(data);
        }
      });
    }
  });

  $(function() { $('.select2').select2(); });

  function initModernDataTable(selector, options) {
    if (!$(selector).length || $.fn.DataTable.isDataTable(selector)) {
      return;
    }

    var defaults = {
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      language: {
        search: '',
        searchPlaceholder: 'Search records...',
        lengthMenu: 'Show _MENU_',
        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
        infoEmpty: 'No entries to show',
        infoFiltered: '(filtered from _MAX_ total)',
        zeroRecords: 'No matching records found',
        paginate: {
          first: '<i class="bi bi-chevron-double-left"></i>',
          last: '<i class="bi bi-chevron-double-right"></i>',
          next: '<i class="bi bi-chevron-right"></i>',
          previous: '<i class="bi bi-chevron-left"></i>'
        }
      },
      dom: '<"dt-toolbar row g-2 align-items-center"<"col-12 col-md-6"l><"col-12 col-md-6"f>>rt<"row g-2 align-items-center mt-3"<"col-12 col-md-6"i><"col-12 col-md-6"p>>',
      responsive: true,
      autoWidth: false
    };

    $(selector).DataTable($.extend(true, {}, defaults, options || {}));
  }

  $(function() {
    initModernDataTable('#dash-table');
    initModernDataTable('#dash-tables', { order: [[2, 'desc']], pageLength: 50 });
    initModernDataTable('#prescription-table', { order: [[4, 'desc']], pageLength: 25 });

    if ($('#dash-table2').length && !$.fn.DataTable.isDataTable('#dash-table2')) {
      $('#dash-table2').DataTable({
        paging: true,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: true,
        autoWidth: false
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
$invoiceSuggestUrl = '';
if (isset($title) && $title === 'Invoices' && isset($view) && in_array($view, array('add', 'edit'), true)) {
  $invoiceSuggestUrl = web_root . 'invoices/servicesuggest.php';
}
?>

<script>
  if ($("#SKU").length) {
    <?php if ($invoiceSuggestUrl !== ''): ?>
    $("#SKU").autoComplete({
      minChars: 1,
      source: function(term, suggest) {
        $.getJSON("<?php echo $invoiceSuggestUrl; ?>", { term: term, scope: 'all' }, function(data) {
          suggest(data || []);
        });
      }
    });
    <?php else: ?>
    <?php
    $products = [];
    $sql = "Select * From tblservices";
    $mydb->setQuery($sql);
    $cur = $mydb->loadResultList();
    foreach ($cur as $result) {
      $products[] = $result->Services;
    }
    ?>
    var pro = <?php echo json_encode($products); ?>;
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
    <?php endif; ?>
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
