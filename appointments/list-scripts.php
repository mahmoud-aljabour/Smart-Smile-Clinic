<script type="text/javascript">
  function getTwentyFourHourTime(amPmString) {
    var d = new Date("1/1/2025 " + amPmString);
    var hours = d.getHours();
    var minutes = d.getMinutes();
    return (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes + ':00';
  }

  function getAmPmTime(time24) {
    var parts = time24.split(':');
    var hours = parseInt(parts[0]);
    var minutes = parts[1];
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    return hours + ':' + minutes + ' ' + ampm;
  }

  function showAppointmentModal() {
    var el = document.getElementById('myModal');
    bootstrap.Modal.getOrCreateInstance(el).show();
  }

  function hideAppointmentModal() {
    var el = document.getElementById('myModal');
    var instance = bootstrap.Modal.getInstance(el);
    if (instance) instance.hide();
  }

  $(document).ready(function() {
    if (!$('#calendar').length || typeof $.fn.fullCalendar !== 'function') {
      return;
    }

    var daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    $('#calendar').fullCalendar({
      eventOrder: "eventOrder",
      editable: true,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      events: 'controller.php?action=loadevent',
      selectHelper: true,
      selectable: true,
      selectConstraint: {
        start: $.fullCalendar.moment().subtract(1, 'days'),
        end: $.fullCalendar.moment().startOf('month').add(1, 'month')
      },
      dayRender: function(date, cell) {
        var dayOfWeek = date.day();
        var dayName = daysOfWeek[dayOfWeek];
        if (cell.find('.day-name').length === 0) {
          cell.append('<span class="day-name">' + dayName + '</span>');
        }
        if (dayOfWeek === 5) {
          cell.css('background-color', 'lightcoral');
          cell.css('color', 'black');
          cell.addClass('friday');
          cell.attr('title', 'Friday: Not available for bookings');
        }
      },
      select: function(start) {
        var appointmentDate = $.fullCalendar.formatDate(start, "YYYY-MM-DD");
        var selectedDate = new Date(appointmentDate);
        if (selectedDate.getDay() === 5) {
          alert('Sorry, Friday is not available for bookings. Please choose another day.');
          return;
        }
        openModalForNew(appointmentDate);
      },
      eventClick: function(event) {
        var eventStartStr = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm:ss");
        var parts = eventStartStr.split(' ');
        var appointment_datetime = parts[0] + ' ' + parts[1];

        try {
          var now = new Date();
          var appointment_datetime_obj = new Date(appointment_datetime);
          if (appointment_datetime_obj < now) {
            alert('Cannot edit past appointments. Please select a future appointment.');
            return;
          }
        } catch (e) {
          alert('Error in date or time format.');
          return;
        }

        $.ajax({
          url: 'controller.php?action=getevent',
          type: 'POST',
          data: { id: event.id },
          dataType: 'json',
          success: function(data) {
            if (data && data.AppoinmentID) {
              $('#appointmentDate').val(data.A_Date);
              $('#appointmentTime').val(getAmPmTime(data.A_Time));
              $('#appointmentId').val(data.AppoinmentID);
              $('#modalTitle').text('Edit Appointment');

              var fullNameValue = data.Fname + '|' + (data.Mname || '') + '|' + data.Lname;
              $('#patients option').each(function() {
                $(this).prop('selected', $(this).val() === fullNameValue);
              });

              $('#services').val(data.Services);
              $('#deleteappointment').show();

              $("#saveappointment").off('click').on('click', function() {
                saveAppointment(true);
              });

              $("#deleteappointment").off('click').on('click', function() {
                if (confirm('Are you sure you want to delete this appointment?')) {
                  deleteAppointment(data.AppoinmentID);
                  window.location = "index.php";
                }
              });

              showAppointmentModal();
            } else {
              alert('No data found for this appointment.');
            }
          },
          error: function(jqXHR, textStatus) {
            console.error('AJAX Error:', textStatus);
            alert('Error fetching appointment details.');
          }
        });
      },
      eventResize: function(event) {
        var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm:ss");
        var parts = start.split(' ');
        updateEventTime(event.id, parts[0], parts[1]);
      },
      eventDrop: function(event) {
        var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm:ss");
        var parts = start.split(' ');
        updateEventTime(event.id, parts[0], parts[1]);
      }
    });

    function openModalForNew(appointmentDate) {
      $('#appointmentDate').val(appointmentDate);
      $('#appointmentTime').val('');
      $('#appointmentId').val('');
      $('#modalTitle').text('Schedule New Appointment');
      $('#patients').val('');
      $('#services').val('');
      $('#deleteappointment').hide();

      $("#saveappointment").off('click').on('click', function() {
        saveAppointment(false);
      });
      showAppointmentModal();
    }

    function deleteAppointment(id) {
      $.ajax({
        url: 'controller.php?action=deleteevent',
        type: 'POST',
        data: { id: id },
        success: function(response) {
          if (response === 'success') {
            $('#calendar').fullCalendar('refetchEvents');
            hideAppointmentModal();
          }
        }
      });
    }

    function saveAppointment(isUpdate) {
      var patientsData = $('#patients').val().split('|');
      if (!patientsData || !patientsData[0] || !$('#services').val() || !$("#appointmentTime").val()) {
        alert("Please select the patient, service, and enter the time.");
        return;
      }

      var Fname = patientsData[0];
      var Mname = patientsData[1] || '';
      var Lname = patientsData[2] || '';
      var Services = $('#services').val();
      var appointmentDate = $('#appointmentDate').val();
      var AppointmentsTime24 = getTwentyFourHourTime($("#appointmentTime").val());
      var hour = parseInt(AppointmentsTime24.split(':')[0]);
      if (hour < 8 || hour > 16) {
        alert('The time must be between 8 AM and 4 PM.');
        return;
      }
      if (new Date(appointmentDate).getDay() === 5) {
        alert('Sorry, Friday is not available for bookings. Please choose another day.');
        return;
      }

      var url = isUpdate ? 'controller.php?action=updateevent' : 'controller.php?action=insertevent';
      var data = {
        Fname: Fname, Mname: Mname, Lname: Lname,
        Services: Services, A_Date: appointmentDate, A_Time: AppointmentsTime24
      };
      if (isUpdate) data.id = $('#appointmentId').val();

      $.ajax({
        url: url, type: "POST", data: data,
        success: function(response) {
          if (response.indexOf("Cannot book") > -1 || response.indexOf("Sorry") > -1 ||
              response.indexOf("The time") > -1 || response.indexOf("Error:") > -1 ||
              response.indexOf("booked") > -1) {
            alert(response);
          } else {
            $('#calendar').fullCalendar('refetchEvents');
            hideAppointmentModal();
            window.location = "index.php";
          }
        },
        error: function(jqXHR, textStatus) {
          alert("An error occurred: " + textStatus);
        }
      });
    }

    function updateEventTime(id, A_Date, A_Time) {
      $.ajax({
        url: "controller.php?action=updateevent",
        type: "POST",
        data: { id: id, A_Date: A_Date, A_Time: A_Time },
        success: function(response) {
          if (response.indexOf("Cannot book") > -1 || response.indexOf("Sorry") > -1 ||
              response.indexOf("The time") > -1 || response.indexOf("Error:") > -1 ||
              response.indexOf("booked") > -1) {
            alert(response);
          }
          $('#calendar').fullCalendar('refetchEvents');
        }
      });
    }
  });
</script>
