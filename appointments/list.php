<?php
// تضمين ملف initialize.php والتحقق من صلاحيات المسؤول
require_once("../include/initialize.php");
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/index.php");
}

date_default_timezone_set("Asia/Gaza");
global $mydb;

// ✅ متغيرات التاريخ واسم اليوم لعرضها في الأعلى
$todayDate = date("Y-m-d");
$todayName = date("l");
?>

<!-- ✅ شريط عرض التاريخ واسم اليوم -->
<div style="
    margin: 15px auto;
    padding: 10px 20px;
    background: #f0f8ff;
    border: 1px solid #cce7ff;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 600;
    color: #004080;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    max-width: 400px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
">
    <i class="fa fa-calendar" aria-hidden="true" style="font-size:20px; color:#007bff;"></i>
    <span>Today: <?php 
    echo $todayName . " - " . $todayDate . " || " . date('H:i:s');  // H:i:s للساعة بالصيغة 24 ساعة (مثل 14:30:45)
    // أو استخدم 'g:i A' للصيغة 12 ساعة مع AM/PM (مثل 2:30 PM)
?></span>
</div>

<script src="<?php echo web_root; ?>dist/js/jquery-1.11.1.min.js"></script>
<style type="text/css">
    /* ... أنماط CSS المتبقية ... */

    /* 🟢 ستايل محسن لاسم اليوم داخل كل خلية من التقويم 🟢 */
    .day-name {
        position: absolute;
        bottom: 2px;
        left: 0;
        width: 100%;
        text-align: center;
        font-size: 9px;
        font-weight: bold;
        color: #666;
        pointer-events: none;
    }

    .fc-day.friday .day-name {
        color: white !important;
    }

    .container>.calendar {
        width: 100%;
    }

    .fc-day {
        position: relative;
    }
</style>

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

    $(document).ready(function() {
        var daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        var calendar = $('#calendar').fullCalendar({
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
                if (dayOfWeek === 5) { // Friday
                    cell.css('background-color', 'lightcoral'); // أحمر فاتح
                    cell.css('color', 'black'); // نص أسود عشان يبان على الفاتح
                    cell.addClass('friday');
                    cell.attr('title', 'Friday: Not available for bookings');
                }
            },
            select: function(start, end, allDay) {
                var appointmentDate = $.fullCalendar.formatDate(start, "YYYY-MM-DD");
                var selectedDate = new Date(appointmentDate);
                var dayOfWeek = selectedDate.getDay();
                if (dayOfWeek === 5) {
                    alert('Sorry, Friday is not available for bookings. Please choose another day.');
                    return;
                }
                openModalForNew(appointmentDate);
            },
            eventClick: function(event) {
                // 🟢 التحقق الجديد: التعديل فقط للمواعيد المستقبلية 🟢
                var eventStartStr = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm:ss");
                var parts = eventStartStr.split(' ');
                var A_Date = parts[0];
                var A_Time = parts[1];
                var appointment_datetime_str = A_Date + ' ' + A_Time;

                try {
                    var now = new Date(); // التاريخ الحالي (2025-10-04)
                    var appointment_datetime = new Date(appointment_datetime_str);
                    if (appointment_datetime < now) {
                        alert('Cannot edit past appointments. Please select a future appointment.');
                        return; // لا تفتح المودال
                    }
                } catch (e) {
                    alert('Error in date or time format.');
                    return;
                }

                // إذا كان مستقبلي، استمر في جلب البيانات
                $.ajax({
                    url: 'controller.php?action=getevent',
                    type: 'POST',
                    data: {
                        id: event.id
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.AppoinmentID) {
                            $('#appointmentDate').val(data.A_Date);
                            $('#appointmentTime').val(getAmPmTime(data.A_Time));
                            $('#appointmentId').val(data.AppoinmentID);
                            $('#modalTitle').text('Edit Appointment');

                            var fullNameValue = data.Fname + '|' + (data.Mname || '') + '|' + data.Lname;
                            $('#patients option').each(function() {
                                if ($(this).val() === fullNameValue) {
                                    $(this).prop('selected', true);
                                }
                            });

                            $('#services').val(data.Services);
                            
                            // 🟢 إظهار زر الحذف في وضع التعديل 🟢
                            $('#deleteappointment').show();

                            $("#saveappointment").off('click').on('click', function() {
                                saveAppointment(true);
                            });

                            // 🟢 إضافة listener لزر الحذف 🟢
                            $("#deleteappointment").off('click').on('click', function() {
                                if (confirm('Are you sure you want to delete this appointment?')) {
                                    deleteAppointment(data.AppoinmentID);
                                    window.location = "index.php";
                                }
                            });

                            $('#myModal').modal('show');
                        } else {
                            alert('No data found for this appointment.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);
                        alert('Error fetching appointment details. Check console for more info.');
                    }
                });
            },
            editable: true,
            eventResize: function(event) {
                var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm:ss");
                var parts = start.split(' ');
                var A_Date = parts[0];
                var A_Time = parts[1];
                var id = event.id;
                updateEventTime(id, A_Date, A_Time);
            },
            eventDrop: function(event) {
                var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm:ss");
                var parts = start.split(' ');
                var A_Date = parts[0];
                var A_Time = parts[1];
                var id = event.id;
                updateEventTime(id, A_Date, A_Time);
            },
        });

        function openModalForNew(appointmentDate) {
            $('#appointmentDate').val(appointmentDate);
            $('#appointmentTime').val('');
            $('#appointmentId').val('');
            $('#modalTitle').text('Schedule New Appointment');
            $('#patients').val('');
            $('#services').val('');
            
            // 🟢 إخفاء زر الحذف في الإضافة الجديدة 🟢
            $('#deleteappointment').hide();

            $("#saveappointment").off('click').on('click', function() {
                saveAppointment(false);
            });
            $('#myModal').modal('show');
        }

        // 🟢 دالة جديدة: حذف الموعد 🟢
        function deleteAppointment(id) {
            $.ajax({
                url: 'controller.php?action=deleteevent',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response === 'success') {
                        $('#calendar').fullCalendar('refetchEvents');
                        $('#myModal').modal('hide');
                        // alert('Appointment deleted successfully!');
                    } else {
                        // alert('Error deleting appointment: ' + response);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error: ' + textStatus);
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
            var timeParts = AppointmentsTime24.split(':');
            var hour = parseInt(timeParts[0]);
            if (hour < 8 || hour > 16) {
                alert('The time must be between 8 AM and 4 PM.');
                return;
            }
            var selectedDate = new Date(appointmentDate);
            if (selectedDate.getDay() === 5) {
                alert('Sorry, Friday is not available for bookings. Please choose another day.');
                return;
            }

            var url = isUpdate ? 'controller.php?action=updateevent' : 'controller.php?action=insertevent';
            var data = {
                Fname: Fname,
                Mname: Mname,
                Lname: Lname,
                Services: Services,
                A_Date: appointmentDate,
                A_Time: AppointmentsTime24
            };
            if (isUpdate) data.id = $('#appointmentId').val();

            $.ajax({
                url: url,
                type: "POST",
                data: data,
                success: function(response) {
                    if (response.indexOf("Cannot book") > -1 || response.indexOf("Sorry") > -1 || response.indexOf("The time") > -1 || response.indexOf("Error:") > -1 || response.indexOf("booked") > -1) {
                        alert(response);
                    } else {
                        $('#calendar').fullCalendar('refetchEvents');
                        $('#myModal').modal('hide');
                        window.location = "index.php";
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("An error occurred while connecting to the server: " + textStatus);
                }
            });
        }

        function updateEventTime(id, A_Date, A_Time) {
            $.ajax({
                url: "controller.php?action=updateevent",
                type: "POST",
                data: {
                    id: id,
                    A_Date: A_Date,
                    A_Time: A_Time
                },
                success: function(response) {
                    if (response.indexOf("Cannot book") > -1 || response.indexOf("Sorry") > -1 || response.indexOf("The time") > -1 || response.indexOf("Error:") > -1 || response.indexOf("booked") > -1) {
                        alert(response);
                        $('#calendar').fullCalendar('refetchEvents');
                    } else {
                        $('#calendar').fullCalendar('refetchEvents');
                    }
                }
            });
        }
    });
</script>

<div id="calendar"></div>
<div class="clearfix"></div>

<!-- 🟢 نافذة المودال -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Schedule Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal span6">
                    <input type="hidden" id="appointmentId" name="id">
                    <input type="hidden" id="appointmentDate" name="A_Date">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Appointment Time (24-hour format or AM/PM ):</label>
                            <label style="color: red; font-size: 12px; display: block;">Clinic hours are from 8:00 AM to 4:00 PM every day except Friday.</label>
                            <input type="text" name="appointmentTime" id="appointmentTime" class="form-control"
                                placeholder="Example: 14:30 or 02:30 PM" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <select class="form-control" style="width: 100%" id="patients" required>
                                <option value="" disabled selected> Select Patient </option>
                                <?php
                                $mydb->setQuery("SELECT * FROM tblpatients");
                                $cur = $mydb->loadResultList();
                                foreach ($cur as $result) {
                                    $fullName = trim($result->Fname) . '|' . trim($result->Mname) . '|' . trim($result->Lname);
                                    $display = trim($result->Fname . ' ' . $result->Mname . ' ' . $result->Lname);
                                    echo '<option value="' . $fullName . '">' . $display . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <select class="form-control" id="services" name="services" required>
                                <option value="" disabled selected>Select Service</option>
                                <?php
                                $sql = "SELECT * FROM tblservices GROUP BY Services";
                                $mydb->setQuery($sql);
                                $cur = $mydb->loadResultList();
                                foreach ($cur as $result) {
                                    echo '<option value="' . $result->Services . '">' . $result->Services . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="deleteappointment" class="btn btn-danger" style="display:none;">
                    <i class="fa fa-trash"></i> Delete Appointment
                </button>

                <button type="button" id="saveappointment" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Changes
                </button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>