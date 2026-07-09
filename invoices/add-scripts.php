<script>
$(function() {
  var discardUrl = 'controller.php?action=discarddraft&invno=<?php echo urlencode($invno); ?>';
  var serviceSearchTimer;

  function invoiceHasServices() {
    return $('#loadcart tbody tr').filter(function() {
      return $(this).find('td').length > 1;
    }).length > 0;
  }

  function initPatientSelect() {
    if (!$.fn.select2 || !$('#Patients').length) {
      return;
    }
    if ($('#Patients').hasClass('select2-hidden-accessible')) {
      $('#Patients').select2('destroy');
    }
    $('#Patients').select2({ width: '100%' });
  }

  function getPatientAgeMeta() {
    var card = $('[data-patient-selected]').first();
    if (!card.length) {
      return { label: '', maxTeeth: 0 };
    }
    return {
      label: String(card.data('age-group-label') || ''),
      maxTeeth: parseInt(card.data('age-group-max-teeth'), 10) || 0
    };
  }

  function updateServicePickerBadge() {
    var $badge = $('#modalproducts .service-picker-age-badge');
    if (!$badge.length) {
      return;
    }

    var meta = getPatientAgeMeta();
    if (!meta.label) {
      $badge.addClass('d-none').empty();
      return;
    }

    var $span = $('<span></span>');
    $span.append('Patient age group: ').append($('<strong></strong>').text(meta.label));
    if (meta.maxTeeth > 0) {
      $span.append($('<span class="text-muted"></span>').text(' (' + meta.maxTeeth + ' teeth)'));
    }

    $badge.removeClass('d-none').empty()
      .append('<i class="bi bi-funnel"></i>')
      .append($span);
  }

  function getServicePickerMaxTeeth() {
    var modalMax = parseInt($('#modalproducts').data('max-teeth'), 10) || 0;
    var patientMax = getPatientAgeMeta().maxTeeth;
    return patientMax > 0 ? patientMax : modalMax;
  }

  function bindServicePickerRows() {
    var selectAll = document.getElementById('selectAllServices');
    if (!selectAll) {
      return;
    }

    var checks = document.querySelectorAll('.service-picker-check:not(:disabled)');
    selectAll.checked = false;
    selectAll.indeterminate = false;

    selectAll.onchange = function() {
      checks.forEach(function(cb) { cb.checked = selectAll.checked; });
      selectAll.indeterminate = false;
    };

    checks.forEach(function(cb) {
      cb.onchange = function() {
        var enabledChecks = document.querySelectorAll('.service-picker-check:not(:disabled)');
        var checkedCount = document.querySelectorAll('.service-picker-check:not(:disabled):checked').length;
        selectAll.checked = checkedCount > 0 && checkedCount === enabledChecks.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < enabledChecks.length;
      };
    });
  }

  function toggleServiceToothField() {
    var scope = $('#serviceScope').val();
    $('#serviceToothWrap').toggle(scope === 'tooth');
  }

  function loadServicePickerRows() {
    var $modal = $('#modalproducts');
    if (!$modal.length) {
      return;
    }

    var scope = $('#serviceScope').val() || 'all';
    var tooth = $('#serviceTooth').val() || '';
    var search = $('#findProducts').val() || '';
    var invno = $('#invno').val() || $modal.data('invno') || '';
    var maxTeeth = getServicePickerMaxTeeth();

    if (scope === 'tooth') {
      var toothNum = parseInt(tooth, 10);
      if (!toothNum || toothNum < 1 || (maxTeeth > 0 && toothNum > maxTeeth)) {
        $('#loaddashboard').html(
          '<tr><td colspan="5"><div class="service-picker-empty">Enter a valid tooth number between 1 and ' +
          maxTeeth + '.</div></td></tr>'
        );
        bindServicePickerRows();
        return;
      }
    }

    $('#loaddashboard').html(
      '<tr><td colspan="5"><div class="service-picker-empty">Loading services...</div></td></tr>'
    );

    $.ajax({
      type: 'POST',
      url: 'lisofproducts.php',
      dataType: 'text',
      data: {
        search_data: search,
        scope: scope,
        tooth: tooth,
        invno: invno
      },
      success: function(data) {
        $('#loaddashboard').html(data);
        bindServicePickerRows();
      },
      error: function() {
        $('#loaddashboard').html(
          '<tr><td colspan="5"><div class="service-picker-empty">Could not load services. Please try again.</div></td></tr>'
        );
      }
    });
  }

  window.refreshInvoiceServicePicker = function() {
    var maxTeeth = getServicePickerMaxTeeth();
    if (maxTeeth > 0) {
      $('#serviceTooth').attr('max', maxTeeth);
    }
    updateServicePickerBadge();
    if ($('#modalproducts').hasClass('show')) {
      loadServicePickerRows();
    }
  };

  function onPatientPanelUpdated() {
    initPatientSelect();
    if (typeof window.refreshInvoiceServicePicker === 'function') {
      window.refreshInvoiceServicePicker();
    }
  }

  $('#invoiceBackBtn, #invoiceCancelBtn').on('click', function(e) {
    if (!invoiceHasServices()) {
      e.preventDefault();
      window.location.href = discardUrl;
    }
  });

  window.addEventListener('beforeunload', function() {
    if (!invoiceHasServices()) {
      navigator.sendBeacon(discardUrl + '&ajax=1');
    }
  });

  $('#browseServicesBtn').on('click', function(e) {
    if (!$('[data-patient-selected]').length) {
      e.preventDefault();
      e.stopPropagation();
      alert('Please select a patient first. Services are filtered by the patient age group.');
    }
  });

  $('#modalproducts').on('show.bs.modal', function(e) {
    if (!$('[data-patient-selected]').length) {
      e.preventDefault();
      alert('Please select a patient first. Services are filtered by the patient age group.');
      return;
    }

    updateServicePickerBadge();
    $('#serviceScope').val('all');
    $('#serviceTooth').val('');
    $('#findProducts').val('');
    toggleServiceToothField();
    loadServicePickerRows();
  });

  $(document).on('change', '#serviceScope', function() {
    toggleServiceToothField();
    loadServicePickerRows();
  });

  $(document).on('input change', '#serviceTooth', function() {
    if ($('#serviceScope').val() === 'tooth') {
      clearTimeout(serviceSearchTimer);
      serviceSearchTimer = setTimeout(loadServicePickerRows, 250);
    }
  });

  $(document).on('keyup', '#findProducts', function() {
    clearTimeout(serviceSearchTimer);
    serviceSearchTimer = setTimeout(loadServicePickerRows, 250);
  });

  $(document).on('change', '#Patients', function() {
    var patients = $(this).val();
    var invno = $('#InvoiceNo').val();
    if (!patients || patients === 'None') {
      return;
    }

    $.ajax({
      type: 'POST',
      url: 'loaddata.php',
      dataType: 'text',
      data: { Patients: patients, invno: invno },
      beforeSend: function() {
        $('#loading-client').show();
        $('#invoicing-body').hide();
      },
      success: function(data) {
        $('#loading-client').hide();
        $('#invoicing-body').show();
        $('#searchclient').show().html(data);
        onPatientPanelUpdated();
      }
    });
  });

  $(document).on('click', '#closeClient', function() {
    var invno = $('#InvoiceNo').val();
    $.ajax({
      type: 'POST',
      url: 'loaddata.php',
      dataType: 'text',
      data: { ClosedClientSession: 'closed', invno: invno },
      beforeSend: function() {
        $('#loading-client').show();
        $('#invoicing-body').hide();
      },
      success: function(data) {
        $('#loading-client').hide();
        $('#invoicing-body').show();
        $('#searchclient').show().html(data);
        onPatientPanelUpdated();
      }
    });
  });

  $(document).on('click', '#client_modal', function() {
    $('#my_form').find('input[type=text], input[type=password], input[type=number], input[type=email], textarea').val('');
    $('#successmsg').html('');
  });

  $('#my_form').on('submit', function(event) {
    event.preventDefault();
    $.ajax({
      type: 'POST',
      url: '<?php echo web_root; ?>patients/controller.php?action=add&modal=true',
      data: $('#my_form').serialize(),
      success: function(data) {
        $('#successmsg').html(data);
        $.ajax({
          type: 'POST',
          url: 'loaddata.php',
          dataType: 'text',
          success: function(panelData) {
            $('#searchclient').hide().fadeIn(function() {
              $(this).html(panelData);
              onPatientPanelUpdated();
            });
          }
        });
      }
    });
  });

  $(document).on('invoice:patientPanelUpdated', onPatientPanelUpdated);

  toggleServiceToothField();
  bindServicePickerRows();
  onPatientPanelUpdated();
});
</script>
