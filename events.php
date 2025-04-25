<?php
include_once __DIR__ . '/partials/_header.php';
if (!isset($_SESSION['NPK'])) {
  header("Location: login.php");
  exit();
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Metode HTTP yang diizinkan
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

?>

<h1 class="fw-bold">PT. Kayaba Training Center Training Events</h1>
<div class="d-flex justify-content-between">
  <div>
    <a href="events.php#events_list_container" class="text-decoration-none btn btn-light"><i class="fas fa-circle-down"></i> Training Events List</a>
    <a href="events.php#event_timeline_container" class="text-decoration-none btn btn-light"><i class="fas fa-circle-down"></i> Training Timeline</a>
  </div>
  <button id="OpenModal" type="button" class="btn btn-success mt-1 d-block ms-auto" style="margin-top: -20px;"><i class="fas fa-plus"></i> Create event</button>
</div>
<!--<button id="download_event_btn" type="button" class="btn btn-success mt-1 d-block ms-auto" style="margin-top: -20px;">
  <i class="fas fa-plus"></i> Download Excel</button>
</div>
</div>
<button id="OpenUploadModal" type="button" class="btn btn-success mt-1 d-block ms-auto" style="margin-top: -20px;">
  <i class="fas fa-upload"></i> Upload Excel
</button>
</div>-->
<!-- Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createEventModalLabel">Create New Training Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="card-body pt-0 mt-2">
          <!-- training general -->
          <div class="d-flex gap-3">
            <!-- training event name select -->
            <div class="w-25">
              <label for="training_name_select" class="form-label">Training Name<span class="text-danger"> *</span></label>
              <select name="training_name" id="training_name_select" class="form-select">
                <option value="" default selected disabled>-- Select training --</option>
              </select>
              <!-- error message -->
              <small id="training_name_error" class="text-danger"></small>
              <!-- register link -->
              <small class="mt-1 d-block" style="text-align: right !important"><a href="trainings.php" class="text-decoration-none"><i class="fas fa-plus"></i> Register new training</a></small>
            </div>
            <!-- training organizer select -->
            <div class="w-25">
              <label for="organizer_select" class="form-label">Event Organizer<span class="text-danger"> *</span></label>
              <select name="training_organizer" id="organizer_select" class="form-select">
                <option value="" default selected disabled>-- Select organizer --</option>
              </select>
              <!-- error message -->
              <small id="organizer_error" class="text-danger"></small>
              <!-- register pop up button -->
              <small class="mt-1 d-block" style="text-align: right !important"><button id="registerOrganizerBtn" type="button" data-bs-toggle="modal" data-bs-target="#formModal" class="text-decoration-none py-0 border-0 bg-transparent text-primary"><i class="fas fa-plus"></i> Register new organizer</button></small>
            </div>
            <!-- training trainer select -->
            <div class="w-25">
              <label for="trainer_select" class="form-label">Event Trainer<span class="text-danger"> *</span></label>
              <select name="training_trainer" id="trainer_select" class="form-select">
                <option value="" default selected disabled>-- Select trainer --</option>
              </select>
              <!-- error message -->
              <small id="trainer_error" class="text-danger"></small>
              <!-- register pop up button -->
              <small class="mt-1 d-block" style="text-align: right !important"><button id="registerTrainerBtn" type="button" data-bs-toggle="modal" data-bs-target="#formModal" class="text-decoration-none py-0 border-0 bg-transparent text-primary"><i class="fas fa-plus"></i> Register new trainer</button></small>
            </div>
            <!-- training location select -->
            <div class="w-25">
              <label for="location_select" class="form-label">Event Location<span class="text-danger"> *</span></label>
              <select name="training_location" id="location_select" class="form-select">
                <option value="" default selected disabled>-- Select location --</option>
              </select>
              <!-- error message -->
              <small id="location_error" class="text-danger"></small>
              <!-- register pop up button -->
              <small class="mt-1 d-block" style="text-align: right !important"><button id="registerLocationBtn" type="button" data-bs-toggle="modal" data-bs-target="#formModal" class="text-decoration-none py-0 border-0 bg-transparent text-primary"><i class="fas fa-plus"></i> Register new location</button></small>
            </div>
          </div>
          <!-- hours and dates -->
          <div class="d-flex gap-3 mt-2">
            <div style="width: 25%">
              <label for="start_date_input" class="form-label">Event Start Date<span class="text-danger"> *</span></label>
              <input type="date" name="start_date" id="start_date_input" class="form-control">
              <small id="start_date_error" class="text-danger"></small>
            </div>
            <div style="width: 25%">
              <label for="end_date_input" class="form-label">Event End Date<span class="text-danger"> *</span></label>
              <input type="date" name="end_date" id="end_date_input" class="form-control">
              <small id="end_date_error" class="text-danger"></small>
            </div>
            <div style="width: 25%">
              <label for="start_time_input" class="form-label">Event Start Time<span class="text-danger"> *</span></label>
              <input type="time" name="start_time" id="start_time_input" class="form-control">
              <small id="start_time_error" class="text-danger"></small>
            </div>
            <div style="width: 25%">
              <label for="end_time_input" class="form-label">Event End Time<span class="text-danger"> *</span></label>
              <input type="time" name="end_time" id="end_time_input" class="form-control">
              <small id="end_time_error" class="text-danger"></small>
            </div>
          </div>
          <!-- durations -->
          <div id="event_div" class="d-flex gap-3 mt-3">
            <!-- duration days -->
            <div style="width: 30%">
              <label for="duration_days_input" class="form-label">Event Duration (days)<span class="text-danger"> *</span></label>
              <input type="number" name="duration_days" id="duration_days_input" class="form-control" value='0' disabled>
              <small id="duration_days_error" class="text-danger"></small>
            </div>
            <!-- duration hours -->
            <div style="width: 30%">
              <label for="duration_hours_input" class="form-label">Event Duration (hours,minutes)<span class="text-danger"> *</span></label>
              <input type="number" name="duration_hours" id="duration_hours_input" class="form-control" value='0' disabled>
              <small id="duration_hours_error" class="text-danger"></small>
            </div>
            <div style="width: 40%;">
              <label for="event_to" class="form-label">Event To Department<span class="text-danger"> *</span></label><br>
              <select name="event_to" id="event_to" class="form-select event-input" multiple="multiple"></select>
              <small id="event_to_hours_error" class="text-danger"></small>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="create_event_btn" type="button" class="btn btn-success mt-4 d-block ms-auto"><i class="fas fa-plus"></i> Create event</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadExcelModalLabel">Upload Excel File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="file" id="excel_file_input" accept=".csv" />
        <small class="text-danger" id="file_error"></small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="upload_event_btn" type="button" class="btn btn-success">
          <i class="fas fa-upload"></i> Upload Excel
        </button>
      </div>
    </div>
  </div>
</div>
<!-- events list container -->
<div id="events_list_container" class="card mt-4 py-3">
  <div class="card-header pb-0 border-0 bg-white">
    <h3 class="fw-semibold">Events List</h3>
    <!-- <p>List of all training events of PT. Kayaba Indonesia which could be enabled or disabled. Administrator could also register or unregister participants based on their sections or organizations</p> -->
    <hr>
  </div>
  <!-- loading spinner -->
  <div id="loading_spinner" class="spinner-border text-primary d-block mx-auto mb-4 my-5" role="status">
    <span class="sr-only">Loading...</span>
  </div>
  <!-- events list body container -->
  <div id="events_list_body_container" class="card-body d-none pt-2">
    <div id="filter_form" class="form gap-3 d-flex">
      <input id="search_input" type="text" name="search" placeholder="Search by training name or event id" autocomplete="off" class="form-control">
      <select name="month" id="month_select" class="form-select">
        <option value="" disabled default selected>-- Filter by month of the year --</option>
        <option value="">All</option>
      </select>
      <select name="year" id="year_select" class="form-select">
        <option value="" disabled default selected>-- Filter by month of the year --</option>
        <option value="">All</option>
      </select>
    </div>
    <!-- select list numbers shown -->
    <div id="lists_shown_container" class="mt-5">
      <label for="lists_shown_select" class="d-block form-label ms-auto text-end">Lists shown</label>
      <select name="lists_shown" id="lists_shown_select" class="form-select mt-2 w-auto d-block ms-auto">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </div>
    <!-- loading spinner -->
    <div id="loading_spinner_events" class="spinner-border text-primary d-none mx-auto mb-4 my-5" role="status">
      <span class="sr-only">Loading...</span>
    </div>

    <div class="table-responsive" style="overflow-x: auto;">
      <!-- events list table -->
      <table id="events_table" class="table mt-4 rounded-2 table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th scope="col" class="text-center">ID <i id="sort-icon-EVT_ID" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Training <i id="sort-icon-TRAINING" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Organizer <i id="sort_icon_ORGANIZER" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Start Date <i id="sort-icon-START_DATE" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">End Date <i id="sort-icon-END_DATE" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Start Time <i id="sort-icon-START_TIME" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">End Time <i id="sort-icon-END_TIME" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Status <i id="sort-icon-ACTIVATED" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody id="events_data_container">
          <!-- Event rows will be appended here -->
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-4">
      <div id="pagination_container"></div>
    </div>

  </div>
  <!-- event timeline container -->
  <div id="event_timeline_container" class="card mt-4 py-3">
    <div class="card-header pb-0 border-0 bg-white">
      <h3 class="fw-semibold">Events Timeline</h3>
      <!-- <p>List of all training events of PT. Kayaba Indonesia which could be enabled or disabled. Administrator could also register or unregister participants based on their sections or organizations</p> -->
      <hr>
    </div>
    <!-- loading spinner -->
    <!-- <div id="loading_spinner_timeline" class="spinner-border text-primary d-block mx-auto mb-4 my-5" role="status">
    <span class="sr-only">Loading...</span>
  </div> -->
    <div class="w-100 px-3 mb-2">

    </div>
    <div id="events_timeline_chart_container" style="overflow-x: auto;">
      <div id="calendar" style="height: 400px; width: 1200px; margin: auto">
      </div>
    </div>
  </div>

  <!-- formModal begin -->
  <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="formModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div id="formModalBody" class="modal-body">
        </div>
        <div class="modal-footer">
          <button id="modal_close_btn" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button id="actionBtn" type="button" class="btn btn-primary"></button>
        </div>
      </div>
    </div>
  </div>
  <!-- formModal end -->

  <!-- require paginationjs -->
  <script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

  <!-- require chartjs -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: 'prev,next,today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        dayMaxEvents: true,
        initialView: 'listMonth',
        views: {
          listWeek: {
            buttonText: 'list week'
          },
          listMonth: {
            buttonText: 'list month'
          },
          listDay: {
            buttonText: 'list day'
          }
        }
      });

      // Variabel untuk menyimpan data acara
      var events_data = [];

      // Fungsi untuk menambahkan hari
      function addDays(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
      }

      // Mengambil data dari server
      $.get(`includes/events.inc.php`).done(function(data) {
        console.log(data['events']);
        $.each(data['events'], function(index, event) {
          var startDate = new Date(event['START_DATE']);
          var endDate = new Date(event['END_DATE']);
          var currentDate = startDate;

          // Tambahkan acara untuk setiap hari dalam jangkauan tanggal mulai dan berakhir
          while (currentDate <= endDate) {
            events_data.push({
              title: `${event['TRAINING']} (${event['EVT_ID']})`, // Judul acara, bisa diganti dengan atribut lain jika sesuai
              start: currentDate.toISOString().split('T')[0] + 'T' + event['START_TIME'], // Tanggal dan waktu mulai
              end: currentDate.toISOString().split('T')[0] + 'T' + event['END_TIME'], // Tanggal dan waktu selesai
              organizer: event['ORGANIZER'] // Penyelenggara acara
            });
            currentDate = addDays(currentDate, 1);
          }
        });
        console.log(events_data);

        // Menambahkan data acara ke dalam kalender
        calendar.addEventSource(events_data);
        calendar.render();
      });
    });
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
  <script type="text/javascript" defer>
    $(document).ready(function() {
      // variables
      let search = '';
      let filterMonth = '';
      let filterYear = '';
      let trainings = [];
      let organizers = [];
      let trainers = [];
      let months = [];
      let locations = [];
      let listsShown = 10;
      let page = 1;
      let typingTimer;
      let doneTypingInterval = 1000;
      let role = '';
      let colomIndex = '';
      let direction = '';

      const month_names = {
        1: "January",
        2: "February",
        3: "March",
        4: "April",
        5: "May",
        6: "June",
        7: "July",
        8: "August",
        9: "September",
        10: "October",
        11: "November",
        12: "December"
      }
      checkRole();
      // get datas
      getRole();
      getMonths();
      getEvents();
      getCreateEventDatas();
      getDept();
      getYear();


      $('#OpenModal').click(function() {
        $('#createEventModal').modal('show');
      });
      $('#OpenUploadModal').click(function() {
        $('#uploadExcelModal').modal('show');
      });
      // listen to search filter
      $("#search_input").keyup(function() {
        setTimeout(function() {
          getEvents();
        }, 100);

        // show loading spinner and hide events list
        $("#loading_spinner_events").addClass('d-block').removeClass('d-none');
        $("#events_table").addClass('d-none');

        search = this.value;
      })

      // get role
      function getRole() {
        $.get('includes/events.inc.php?type=EACT20').done(function(a, b, xhr) {
          role = xhr.responseJSON;
        }).fail(function(xhr, a, b) {
          console.log(xhr.status);
        })
      }

      $("#upload_event_btn").click(function() {
        const fileInput = document.getElementById('excel_file_input');
        const file = fileInput.files[0];

        if (!file) {
          alert("Please select an Excel file to upload.");
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          const data = new Uint8Array(e.target.result);
          const workbook = XLSX.read(data, {
            type: 'array'
          });
          const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
          const jsonData = XLSX.utils.sheet_to_json(firstSheet, {
            header: 1
          });

          // Prepare data for sending to the server
          const eventsData = jsonData.slice(1).map(row => ({
            evt_id: row[0],
            t_id: row[1],
            start_date: row[2],
            end_date: row[3],
            duration_days: row[4],
            duration_hours: row[5],
            activated: row[6],
            start_time: row[7],
            end_time: row[8],
            loc_id: row[9],
            org_id: row[10],
            ta_id: row[11],
            evt_to: row[12] // Assuming evt_to is a comma-separated string
          }));

          console.log(JSON.stringify(eventsData)); // Log data yang akan dikirim

          // Send data to the server
          $.post(`includes/events.inc.php?type=EACT27`, {
              events: eventsData
            })
            .done(function(response) {
              swal('Success', 'Events uploaded successfully!', 'success');
            })
            .fail(function(xhr) {
              console.log(xhr.status);
              const response = xhr.responseJSON;
              if (response && response.error) {
                // Jika server mengembalikan error sebagai string
                swal('error', response.error);
              } else if (response && response.errors) {
                // Jika server mengembalikan error sebagai array
                let errorMessage = response.errors.join("\n");
                swal('error', errorMessage);
              } else {
                // Jika respons tidak valid atau tidak terduga
                swal('error', 'Terjadi kesalahan yang tidak diketahui.');
              }
            });
        };
        reader.readAsArrayBuffer(file);
      });

      $("#download_event_btn").click(function() {
        // Prepare the headers
        const headers = [
          "EVT_ID",
          "T_ID",
          "START_DATE",
          "END_DATE",
          "DURATION_DAYS",
          "DURATION_HOURS",
          "ACTIVATED",
          "START_TIME",
          "END_TIME",
          "LOC_ID",
          "ORG_ID",
          "TA_ID",
          "EVT_TO"
        ];

        const descriptions = [
          "EVT_ID = Urutkan id dari data terakhir",
          "T_ID = Gunakan kode training",
          "START_DATE = Format yyyy-mm-dd",
          "END_DATE = format yyyy-mm-dd",
          "START_TIME = Format 24 jam (Contoh: 13:00:00) ",
          "END_TIME = Format 24 jam (Contoh: 13:00:00)",
          "TA_ID = id trainers",
          "ACTIVATED = input 0",
          "LOC_ID = location idnya",
          "ORG_ID = organizatornya misal training ini dibuat oleh atau dijalankan oleh pihak kayaba",
          "EVT_TO = input all",
        ];

        const exportData = [headers];

        descriptions.forEach(desc => {
          exportData.push(["", "", "", "", "", "", "", "", "", "", "", "", "", desc]);
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(exportData);
        XLSX.utils.book_append_sheet(wb, ws, "Events");

        XLSX.writeFile(wb, 'Form_upload_events.xlsx');
      })

      // handle create event button
      $("#create_event_btn").click(function() {
        // get input datas
        const t_id = $("#training_name_select").val();
        const org_id = $("#organizer_select").val();
        const ta_id = $("#trainer_select").val();
        const loc_id = $("#location_select").val();

        const start_date = $("#start_date_input").val();
        const end_date = $("#end_date_input").val();
        const start_time = $("#start_time_input").val();
        const end_time = $("#end_time_input").val();

        const duration_hours = $("#duration_hours_input").val();
        const duration_days = $("#duration_days_input").val();
        const evt_to = $("#event_to").val();

        // create new event
        $.post(`includes/events.inc.php?type=EACT02`, {
          t_id,
          org_id,
          ta_id,
          loc_id,
          start_date,
          end_date,
          start_time,
          end_time,
          duration_hours,
          duration_days,
          evt_to
        }).done(function(a, b, xhr) {
          swal('success', 'Success!', 'Success create new Event');
        }).fail(function(xhr, a, b) {
          console.log(xhr.status);

          if (xhr.responseJSON) {
            const errors = xhr.responseJSON;
            // set errors
            $("#training_name_error").html(errors['t_id']);
            $("#organizer_error").html(errors['org_id']);
            $("#trainer_error").html(errors['ta_id']);
            $("#location_error").html(errors['loc_id']);
            $("#start_date_error").html(errors['start_date']);
            $("#end_date_error").html(errors['end_date']);
            $("#start_time_error").html(errors['start_time']);
            $("#end_time_error").html(errors['end_time']);
            $("#duration_days_error").html(errors['duration_days']);
            $("#duration_hours_error").html(errors['duration_hours']);
            $("#event_to_hours_error").html(errors['evt_to']);
          }
        })
      })

      // remove errors on value change
      $("#training_name_select").change(function() {
        const trainingId = this.value;
        $.get(`includes/events.inc.php?type=EACT26&t_id=${trainingId}`).done(function(data) {
          const durationDays = data.duration_days;
          const durationHours = data.duration_hours;

          // Ambil start date dan start time dari input
          const startDate = document.getElementById('start_date_input').value;
          const startTime = document.getElementById('start_time_input').value;

          if (!startDate || !startTime) {
            alert('Harap isi Start Date dan Start Time!');
            return;
          }

          // Hitung end date dan end time
          const endDate = new Date(startDate);
          endDate.setDate(endDate.getDate() + parseInt(durationDays));

          const endTime = new Date(`1970-01-01T${startTime}:00Z`);
          endTime.setHours(endTime.getHours() + parseInt(durationHours));

          // Format end date dan end time
          const formattedEndDate = endDate.toISOString().split('T')[0];
          const formattedEndTime = endTime.toTimeString().split(' ')[0];

          // Isi end date dan end time
          document.getElementById('end_date_input').value = formattedEndDate;
          document.getElementById('end_time_input').value = formattedEndTime;

          // Menonaktifkan input end date dan end time agar tidak bisa diubah manual
          document.getElementById('end_date_input').disabled = true;
          document.getElementById('end_time_input').disabled = true;
        }).fail(function(xhr) {
          console.log("Error: " + xhr.status);
        });
      });

      // Event listeners untuk menangani perubahan waktu dan tanggal
      $("#start_date_input").change(function() {
        // Set error untuk start date jika diperlukan
        $("#start_date_error").html("");

        // Periksa apakah start date lebih awal dari hari ini
        if ((new Date(this.value)).getTime() < (new Date()).getTime()) {
          $("#start_date_error").html("*Start date couldn't be earlier than current date");
        } else {
          // Jika valid, hitung end date dan end time
          updateEndDateAndTime();
        }
      });

      $("#start_time_input").change(function() {
        // Set error untuk start time jika diperlukan
        $("#start_time_error").html("");

        // Hitung end time setelah start time berubah
        updateEndDateAndTime();
      });

      function updateEndDateAndTime() {
        const startDate = document.getElementById('start_date_input').value;
        const startTime = document.getElementById('start_time_input').value;

        if (!startDate || !startTime) {
          return;
        }

        $.get(`includes/events.inc.php?type=EACT26&t_id=${$("#training_name_select").val()}`).done(function(data) {
          const durationDays = data.duration_days;
          const durationHours = data.duration_hours;

          // Hitung end date dan end time
          const endDate = new Date(startDate);
          endDate.setDate(endDate.getDate() + parseInt(durationDays));

          const endTime = new Date(`1970-01-01T${startTime}:00Z`);
          endTime.setHours(endTime.getHours() + parseInt(durationHours));

          // Format end date dan end time
          const formattedEndDate = endDate.toISOString().split('T')[0];
          const formattedEndTime = endTime.toTimeString().split(' ')[0];

          // Isi end date dan end time
          document.getElementById('end_date_input').value = formattedEndDate;
          document.getElementById('end_time_input').value = formattedEndTime;
        }).fail(function(xhr) {
          console.log("Error: " + xhr.status);
        });
      }

      $("#organizer_select").change(function() {
        $("#organizer_error").html('');
      })
      $("#trainer_select").change(function() {
        $("#trainer_error").html('');
      })
      $("#location_select").change(function() {
        $("#location_error").html('');
      })

      $('#event_to').on('change', function() {
        $('#event_to_hours_error').html('');
      })
      $('#event_update').on('change', function() {
          $('#event_to_update_error').html('');
        })



        // event start and end hours handler
        /
        $("#start_time_input").change(function() {
          $("#start_time_error").html("");

          // count hours
          countHours();
        });
      $("#end_time_input").change(function() {
        $("#end_time_error").html("");

        // count hours
        countHours();
      });

      // event start and end date handler
      $("#start_date_input").change(function() {
        $("#start_date_error").html("");

        // check if start day is earlier than today
        if ((new Date(this.value)).getTime() < (new Date()).getTime()) {
          $("#start_date_error").html("*Start date couldn&apos;t be earlier than current date");
        } else {
          // get days check
          getDays();
        }
      });
      $("#end_date_input").change(function() {
        $("#end_date_error").html("");

        // get days check
        getDays();
      });

      // organizer select change handler
      $("#organizer_select").change(function() {
        // set organizer variable to this.value
        const organizer = this.value;

        // get filtered trainers
        getTrainers(organizer);
      })

      // trainer select change handler
      $("#trainer_select").change(function() {
        // set trainer variable to this.value
        const trainer = this.value;

        // get organizer
        getOrganizer(trainer);
      })

      // lists shown select option change
      $("#lists_shown_select").on('change', function() {
        // get lists shown value
        listsShown = $("#lists_shown_select").val();

        // show events loading spinner
        $("#loading_spinner_events").removeClass('d-none').addClass('d-block');

        // hide table
        $("table").addClass('d-none');

        // remove previous contents
        $("tbody").html('');

        // remove months select options
        $("#month_select").html(`
        <option value="" disabled default selected>-- Filter by month of the year --</option>
        <option value="">All</option>
      `);

        // get events
        getEvents();

        setTimeout(() => {
          // remove events loading spinner
          $("#loading_spinner_events").removeClass('d-block').addClass('d-none');

          // show table
          $("table").removeClass('d-none');
        }, 500)
      })


      // filter by month
      $("#month_select").change(function() {
        // set filter month to curr month_select value
        filterMonth = this.value;

        // show events loading spinner
        $("#loading_spinner_events").removeClass('d-none').addClass('d-block');

        // hide table
        $("table").addClass('d-none');

        // remove previous contents
        $("tbody").html('');

        // get events
        getEvents();

        setTimeout(() => {
          // remove events loading spinner
          $("#loading_spinner_events").removeClass('d-block').addClass('d-none');

          // show table
          $("table").removeClass('d-none');
        }, 500)
      })

      //filter by year 
      $("#year_select").change(function() {
        // set filter month to curr month_select value
        filterYear = this.value;

        // show events loading spinner
        $("#loading_spinner_events").removeClass('d-none').addClass('d-block');

        // hide table
        $("table").addClass('d-none');

        // remove previous contents
        $("tbody").html('');

        // get events
        getEvents();

        setTimeout(() => {
          // remove events loading spinner
          $("#loading_spinner_events").removeClass('d-block').addClass('d-none');

          // show table
          $("table").removeClass('d-none');
        }, 500)
      })

      // show register organizer modal
      $("#registerOrganizerBtn").click(function() {
        // set form modal label
        $("#formModalLabel").html("Register New Organizer");

        // set form modal body
        $("#formModalBody").html(`
        <input id="modalTextInput" name="new_organizer" placeholder="Enter new organizer institution name here" class="form-control" />
        <small id="modalInputError" class="text-danger"></small>
      `);

        // set action button
        $("#actionBtn").html("<i class='fas fa-plus'></i> Register organizer");
        $("#actionBtn").removeClass('btn-primary').removeClass('btn-danger').addClass('btn-success');

        // handle form submit
        $("#actionBtn").click(function() {
          // get input datas
          let organizer = $("#modalTextInput").val();

          // register new organizer
          $.post(`includes/events.inc.php?type=EACT08`, {
            organizer
          }).done(function(a, b, xhr) {
            swal('success', 'Success!', 'Success create Organizer');
          }).fail(function(xhr, a, b) {
            console.log(xhr.status);
            if (xhr.responseJSON) {
              const errors = xhr.responseJSON;
              $("#modalInputError").html(errors['organizer']);
            }
          })
        })
      })

      // show register event trainer modal
      $("#registerTrainerBtn").click(function() {
        // set form modal label
        $("#formModalLabel").html("Register New Trainer");

        // set form modal body
        $("#formModalBody").html(`
        <input id="modalTextInput" name="new_trainer" placeholder="Enter new trainer name here" class="form-control" />
        <small id="modalInputError" class="text-danger"></small>
        <select id="modalOrganizerSelect" name="organizer" class="form-select mt-2">
          <option value="" selected disabled default>-- Select trainer organizer --</option>
        </select>
        <small id="modalOrganizerError" class="text-danger"></small>
      `);

        // get all organizers
        $.get('includes/events.inc.php?type=EACT10').done(function(a, b, xhr) {
          if (xhr.status === 204) {
            console.log(xhr.status);
            return;
          }

          let organizers = xhr.responseJSON;

          organizers.forEach(function(organizer) {
            $("#modalOrganizerSelect").append(`
            <option value="${organizer['ORG_ID']}">${organizer['ORGANIZER']}</option>
          `);
          })
        }).fail(function(xhr, a, b) {
          console.log(xhr.status);
        })

        // set action button
        $("#actionBtn").html("<i class='fas fa-plus'></i> Register trainer");
        $("#actionBtn").removeClass('btn-primary').removeClass('btn-danger').addClass('btn-success');

        // handle form submit
        $("#actionBtn").click(function() {
          // get input datas
          let trainer = $("#modalTextInput").val();
          let org_id = $("#modalOrganizerSelect").val();

          // register new organizer
          $.post(`includes/events.inc.php?type=EACT07`, {
            trainer,
            org_id
          }).done(function(a, b, xhr) {
            // reload curr page
            location.reload();
          }).fail(function(xhr, a, b) {
            console.log(xhr.status);
            if (xhr.responseJSON) {
              const errors = xhr.responseJSON;
              $("#modalInputError").html(errors['trainer']);
              $("#modalOrganizerError").html(errors['org_id']);
            }
          })
        })
      })

      // show register event location modal
      $("#registerLocationBtn").click(function() {
        // set form modal label
        $("#formModalLabel").html("Register New Location");

        // set form modal body
        $("#formModalBody").html(`
        <input id="modalTextInput" name="new_location" placeholder="Enter new location here" class="form-control" />
        <small id="modalInputError" class="text-danger"></small>
      `);

        // set action button
        $("#actionBtn").html("<i class='fas fa-plus'></i> Register location");
        $("#actionBtn").removeClass('btn-primary').removeClass('btn-danger').addClass('btn-success');

        // handle form submit
        $("#actionBtn").click(function() {
          // get input datas
          let locationName = $("#modalTextInput").val();

          // register new organizer
          $.post(`includes/events.inc.php?type=EACT09`, {
            location: locationName
          }).done(function(a, b, xhr) {
            // reload curr page
            location.reload();
          }).fail(function(xhr, a, b) {
            console.log(xhr.status);
            if (xhr.responseJSON) {
              const errors = xhr.responseJSON;
              $("#modalInputError").html(errors['location']);
            }
          })
        })
      })

      function countHours() {
        if ($("#end_time_input").val() && $("#start_time_input").val()) {
          $("#duration_hours_error").html("");

          let startTime = $("#start_time_input").val().split(':');
          let endTime = $("#end_time_input").val().split(':');

          let startHour = parseInt(startTime[0], 10);
          let startMinute = parseInt(startTime[1], 10);
          let endHour = parseInt(endTime[0], 10);
          let endMinute = parseInt(endTime[1], 10);

          let totalStartMinutes = startHour * 60 + startMinute;
          let totalEndMinutes = endHour * 60 + endMinute;

          let durationMinutes = totalEndMinutes - totalStartMinutes;

          if (durationMinutes >= 60) {
            let durationHours = Math.floor(durationMinutes / 60);
            let remainingMinutes = durationMinutes % 60;

            $("#duration_hours_input").val(durationHours + '.' + (remainingMinutes < 10 ? '0' + remainingMinutes : remainingMinutes));
            $("#duration_hours_error").html("");
          } else {
            $("#duration_hours_input").val(0);
            $("#duration_hours_error").html("*End time must be later than the start time and must have at least 1 hour difference");
          }
        }
      }


      function getDays() {
        if (!$("#start_date_error").html() && $("#start_date_input").val() && $("#end_date_input").val()) {
          $("#duration_days_error").html("");
          date1 = new Date($("#start_date_input").val());
          date2 = new Date($("#end_date_input").val());
          const milli_secs = date2.getTime() - date1.getTime();

          // Convert the milli seconds to Days 
          const days = milli_secs / (1000 * 3600 * 24) + 1;

          if (days <= 0) {
            $("#duration_days_input").val(0);
            $("#duration_days_error").html("*Start date must be earlier or the same as the end date");
          } else {
            $("#duration_days_input").val(days);
          }
        }
      }

      function getOrganizer(trainer) {
        $.get(`includes/events.inc.php?type=EACT06&trainer=${trainer}`).done(function(a, b, xhr) {
          if (xhr.responseJSON) {
            organizer = xhr.responseJSON['organizer'];

            $(".organizer-option").removeAttr('selected');
            $(`option[value='${organizer['ORG_ID']}']`).attr('selected', 'selected');
          }
        }).fail(function(xhr, a, b) {
          console.log(xhr.status, xhr.responseJSON);
        })
      }

      function getTrainers(organizer) {
        $.get(`includes/events.inc.php?type=EACT05&organizer=${organizer}`).done(function(a, b, xhr) {
          if (xhr.responseJSON) {
            trainers = xhr.responseJSON['trainers'];

            $("#trainer_select").html(`
            <option value="" default selected disabled>-- Select trainer --</option>
          `);

            trainers.forEach(function(trainer) {
              $("#trainer_select").append(`
            <option value="${trainer['TA_ID']}">${trainer['NAME']}</option>
            `);
            })
          }
        }).fail(function(xhr, a, b) {
          console.log(a, b, xhr.status, xhr.responseJSON);
        })
      }

      function checkRole() {
        $.get('includes/events.inc.php?type=EACT20').done(function(a, b, xhr) {
          role = xhr.responseJSON;
          if (role != 'RLS01') {
            window.location.href = 'eventusr.php';
          }
        }).fail(function(xhr, a, b) {
          console.log(xhr.status);
        })
      }


      $('th i').on('click', function() {
        const index = $(this).attr('id').split('-')[2];
        if (colomIndex === index) {
          if (direction === 'asc') {
            direction = 'desc';
          } else if (direction === 'desc') {
            direction = '';
          } else {
            direction = 'asc';
          }
        } else {
          colomIndex = index;
          direction = 'asc';
        }

        // Update icon class to reflect sorting direction
        $('th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort'); // Reset all icons
        if (direction === 'asc') {
          $(this).removeClass('fa-sort').addClass('fa-sort-up');
        } else if (direction === 'desc') {
          $(this).removeClass('fa-sort').addClass('fa-sort-down');
        }

        setTimeout(function() {
          getEvents();
        }, 1000);

        // show loading spinner and hide events list
        $("#loading_spinner_events").addClass('d-block').removeClass('d-none');
        $("#events_table").addClass('d-none');

      });

      function formatDate(dateString) {
        if (!dateString) return '';
        const [year, month, day] = dateString.split('-');
        return `${day}-${month}-${year}`;
      }

      function getEvents() {
        $.get(`includes/events.inc.php?search=${search}&month=${filterMonth}&year=${filterYear}&colomn=${colomIndex}&direction=${direction}`).done(function(a, b, xhr) {
          if (xhr.responseJSON) {
            // get responses
            const events = xhr.responseJSON['events'];
            // set events with pagination
            if (events) {
              // paginate table
              $("#pagination_container").pagination({
                dataSource: events,
                pageSize: listsShown,
                callback: function(data, pagination) {
                  // template method of yourself
                  let html = '';
                  data.forEach(function(event) {
                    html += `
                    <tr>
                      <!-- training event id -->
                      <td scope="row">${event['EVT_ID']}</td>
                      <!-- training name -->
                      <td style="text-align: left;">${event['TRAINING']}</td>
                      <!-- training organizer -->
                      <td>${event['ORGANIZER']}</td>
                      <!-- training start date -->
                      <td>${formatDate(event['START_DATE'])}</td> <!-- Format the start date -->
                      <!-- training end date -->
                      <td>${formatDate(event['END_DATE'])}</td> <!-- Format the end date -->
                      <!-- training start time -->
                      <td>${event['START_TIME'].slice(0, 5)}</td>
                      <!-- training end time -->
                      <td>${event['END_TIME'].slice(0, 5)}</td>
                      <!-- training enabled or disabled -->
                      <td>${event['ACTIVATED'] == 1? "<span class='text-success'>Activated</span>": "<span class='text-danger'>Disabled</span>"}</td>
                      <!-- training details dropdown button -->
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-outline-dark dropdown-toggle py-1" type="button" data-bs-toggle="dropdown">
                            Detail
                          </button>
                          <div class="dropdown-menu dropdown-menu-end">
                            <!-- view and update -->
                            ${role == 'RLS01'?`<button id="update_${event['EVT_ID']}" class="dropdown-item update-event-btn" type="button" data-bs-toggle="modal" data-bs-target="#formModal">View and update</button>`: ''}
                            <!-- approve or register participants -->
                            ${role == 'RLS01'? `<a class="dropdown-item" href="events/approve.php?evt_id=${event['EVT_ID']}">Approve participants</a>`: role == 'RLS02'? `<a class="dropdown-item" href="events/register.php?evt_id=${event['EVT_ID']}">Register participants</a>`: ''}
                            <!-- delete training event -->
                            ${role == 'RLS01'? `<button id="delete_btn_${event['EVT_ID']}" data-bs-toggle="modal" data-bs-target="#formModal" type="button" class="dropdown-item show-delete-btn">Delete</button>`: ""}
                          </div>
                        </div>
                      </td>
                    </tr>
                  `;
                  })

                  $("#events_data_container").html(html);

                  // handle delete event
                  $(".show-delete-btn").click(function() {
                    // get event id
                    const evt_id = this.id.split('_').pop();

                    // set modal label
                    $("#formModalLabel").html(`Delete event <strong>${evt_id}</strong>`)

                    // set body
                    $("#formModalBody").html(`<p>Are you sure to delete event <strong>${evt_id}</strong>? This action is <strong>irreversible</strong>. All datas related to this event <strong>will be deleted</strong></p>`);

                    // set buttons
                    $("#actionBtn").html("<i class='fas fa-trash'></i> Delete");
                    $("#actionBtn").removeClass('btn-primary').removeClass('btn-outline-primary').removeClass("btn-success").addClass('btn-danger');

                    // handle delete event
                    $("#actionBtn").click(function() {
                      $.get(`includes/events.inc.php?type=EACT04&evt_id=${evt_id}`).done(function(a, b, xhr) {
                        location.reload();
                      }).fail(function(xhr, a, b) {
                        console.log(xhr.status);
                      })
                    })
                  })

                  // handle update event
                  $(".update-event-btn").click(function() {
                    let editable = false;
                    let evtId = this.id.split('_').pop();

                    // get event information
                    $.get(`includes/events.inc.php?evt_id=${evtId}`).done(function(a, b, xhr) {
                      if (xhr.status === 204) {
                        $("#formModalLabel").html(`View and Update Event`);
                        $("#formModalBody").html("<p>No event found</p>")
                      }

                      let event = xhr.responseJSON['event'];

                      $("#formModalLabel").html(`View and Update Event <strong>${event['EVT_ID']}</strong> <div class="form-check form-switch">
                      <input id="activate_input" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" ${event['ACTIVATED'] == 1? "checked": ""}>
                      <label for="activate_input">${event['ACTIVATED'] == 1? "<small class='text-success fs-6'>Activated</small>": "<small class='text-danger fs-6'>Disabled</small>"}</label>
                    </div>`);

                      $("#formModalBody").html('');

                      let trainings = xhr.responseJSON['trainings'];
                      $("#formModalBody").append(`
                      <label for="training_select_update" class="form-label">Training name<span class="text-danger"> *</span></label>
                      <select id="training_select_update" class="form-select event-input" ${editable? "": "disabled"}>
                        <option class="training-select-option" disabled default>-- Select training --</option>
                      </select>
                    `);

                      trainings.forEach(function(training) {
                        $("#training_select_update").append(`
                        <option value="${training['T_ID']}" class="training-select-option" ${training['T_ID'] == event['T_ID']? "selected": ''}>${training['TRAINING']}</option>
                      `);
                      })

                      let organizers = xhr.responseJSON['organizers'];

                      $("#formModalBody").append(`
                      <label for="organizer_select_update" class="form-label mt-2">Training organizer<span class="text-danger"> *</span></label>
                      <select id="organizer_select_update" class="form-select event-input" ${editable? "": "disabled"}>
                        <option class="training-select-option" disabled default>-- Select organizer --</option>
                      </select>
                    `);

                      organizers.forEach(function(organizer) {
                        $("#organizer_select_update").append(`
                        <option value="${organizer['ORG_ID']}" class="training-select-option" ${organizer['ORG_ID'] == event['ORG_ID']? 'selected': ''}>${organizer['ORGANIZER']}</option>
                      `);
                      })

                      let trainers = xhr.responseJSON['trainers'];

                      $("#formModalBody").append(`
                      <label for="trainer_select_update" class="form-label mt-2">Trainer<span class="text-danger"> *</span></label>
                      <select id="trainer_select_update" class="form-select event-input" ${editable? "": "disabled"}>
                        <option class="training-select-option" disabled default>-- Select trainer --</option>
                      </select>
                    `);

                      trainers.forEach(function(trainer) {
                        $("#trainer_select_update").append(`
                        <option value="${trainer['TA_ID']}" class="training-select-option" ${trainer['TA_ID'] == event['TA_ID']? 'selected': ''}>${trainer['NAME']}</option>
                      `);
                      })

                      let locations = xhr.responseJSON['locations'];
                      $("#formModalBody").append(`
                      <label for="location_select_update" class="form-label mt-2">Training location<span class="text-danger"> *</span></label>
                      <select id="location_select_update" class="form-select event-input" ${editable? "": "disabled"}>
                        <option class="training-select-option" disabled default>-- Select location --</option>
                      </select>
                    `);

                      locations.forEach(function(location) {
                        $("#location_select_update").append(`
                        <option value="${location['LOC_ID']}" class="training-select-option" ${location['LOC_ID'] == event['LOC_ID']? 'selected': ''}>${location['LOCATION']}</option>
                      `);
                      })




                      $("#formModalBody").append(`
                      <label for="start_date_input" class="form-label mt-2">Start and end dates <span class="text-danger"> *</span></label>
                    `);

                      $("#formModalBody").append(`
                      <div class="input-group">
                        <input id="start_date_input_update" type="date" class="form-control" value="${event['START_DATE']}" disabled />
                        <input id="end_date_input_update" type="date" class="form-control" value="${event['END_DATE']}" disabled />
                      </div>
                      <div class="input-group">
                        <small id="start_date_update_error" style=" border: none;
                        box-shadow: none;
                        background-color: transparent;" class="form-control text-danger"></small>
                                            <small id="end_date_update_error" style=" border: none;
                        box-shadow: none;
                        background-color: transparent;" class="form-control text-danger"></small>
                      </div>

                    `);
                      $("#formModalBody").append(`
                        <label for="event_update" class="form-label">Event To Department<span class="text-danger"> *</span></label><br>
                        <select name="event_update" id="event_update" class="form-select event-input" multiple="multiple" disabled></select>
                        <small id="event_to_update_error" class="text-danger"></small>
                        <br>
                    `);




                      $("#formModalBody").append(`
                      <label for="start_time_input" class="form-label mt-2">Start and end times<span class="text-danger"> *</span></label>
                    `);

                      $("#formModalBody").append(`
                      <div class="input-group">
                        <input id="start_time_input_update" type="time" class="form-control" value="${event['START_TIME']}" disabled />
                        <input id="end_time_input_update" type="time" class="form-control" value="${event['END_TIME']}" disabled />
                      </div>
                      <div class="input-group">
                        <small id="start_time_update_error" style=" border: none;
                        box-shadow: none;
                        background-color: transparent;" class="form-control text-danger"></small>
                                            <small id="end_time_update_error" style=" border: none;
                        box-shadow: none;
                        background-color: transparent;" class="form-control text-danger"></small>
                      </div>
                      
                    `);
                      // Setelah Select2 diinisialisasi, lakukan AJAX request untuk memuat data
                      var selectedValues = event['EVT_TO'].split(',');
                      $.get('includes/events.inc.php?type=EACT21')
                        .done(function(response) {
                          var dept = response.map(function(department) {
                            return {
                              id: department.DPT_ID,
                              text: department.DEPARTMENT
                            };
                          });

                          // Tambahkan "All" option di awal array
                          dept.unshift({
                            id: 'all',
                            text: 'All'
                          });

                          // Update Select2 dengan opsi baru
                          $('#event_update').empty().select2({
                            data: dept,
                            placeholder: "-- Select Event To Department --",
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#formModal')
                          });



                          // Set opsi yang dipilih berdasarkan nilai dari array $ARR_evtto
                          selectedValues.forEach(function(value) {
                            $('#event_update option[value="' + value + '"]').prop('selected', true);
                          });
                          $('#event_to_update_error').html('');
                          // Perbarui tampilan Select2
                          $('#event_update').trigger('change');

                        })
                        .fail(function(xhr) {
                          console.log("Error: " + xhr.status);
                        });
                      // Event handler for Select2 selection changes
                      $('#event_update').on('select2:select', function(e) {
                        var selectedValues = $(this).val();
                        $('#event_to_update_error').html('');

                        // Check if "All" is selected
                        if (selectedValues.includes('all')) {
                          // Deselect all other options
                          $(this).val('all').trigger('change.select2');
                        }
                      });

                      // Event handler for Select2 deselection changes
                      $('#event_update').on('select2:unselect', function(e) {
                        var selectedValues = $(this).val();

                        // If "All" is deselected, do nothing. If other options are selected, ensure "All" is deselected
                        if (!selectedValues.includes('all')) {
                          $('#event_update option[value="all"]').prop('selected', false);
                          $('#event_update').trigger('change.select2');
                        }
                      });


                      $("#actionBtn").removeClass('btn-success').removeClass('btn-danger').removeClass('btn-primary').addClass('btn-outline-primary').html(`
                      <i class="fas fa-pencil"></i> Edit event
                    `);

                      $("#activate_input").click(function() {
                        // get post datas
                        const t_id = $("#training_select_update").val();
                        const org_id = $("#organizer_select_update").val();
                        const ta_id = $("#trainer_select_update").val();
                        const loc_id = $("#location_select_update").val();
                        const start_date = $("#start_date_input_update").val();
                        const end_date = $("#end_date_input_update").val();
                        const start_time = $("#start_time_input_update").val();
                        const end_time = $("#end_time_input_update").val();
                        const activated = this.checked ? 1 : 0;
                        const evt_to = $("#event_update").val();


                        $.post(`includes/events.inc.php?type=EACT03&evt_id=${evtId}`, {
                          t_id,
                          activated,
                          org_id,
                          ta_id,
                          loc_id,
                          start_date,
                          end_date,
                          start_time,
                          end_time,
                          evt_to
                        }).done(function(a, b, xhr) {
                          swal('success', 'Success!', 'Success Update Event');
                        })

                        if (activated == 1) {
                          $.post('includes/notifications.inc.php?type=2', {
                            t_id: t_id,
                            evt_id: evtId
                          }).done(function(data, textStatus, xhr) {
                            console.log('Status: ' + xhr.status); // HTTP status code
                            console.log('Response data: ', data); // Response data from server
                          }).fail(function(xhr, textStatus, errorThrown) {
                            console.log('Status: ' + xhr.status); // HTTP status code
                            console.log('Error: ' + errorThrown); // Error message
                            console.log('Response text: ' + xhr.responseText); // Response text from server
                          });

                        }

                      })

                      // edit listener
                      $("#actionBtn").click(function() {
                        $("#actionBtn").attr('id', 'updateBtn');
                        $("#updateBtn").html(`<i class="fas fa-pencil"></i> Update event`);
                        $("#updateBtn").removeClass('btn-outline-primary').addClass('btn-primary');
                        $("#modal_close_btn").html("<i class='fas fa-times'></i> Cancel edit");
                        $("#modal_close_btn").removeClass("btn-secondary").addClass("btn-danger");
                        editable = true;

                        // cancel edit listener

                        // remove disabled
                        $("#training_select_update").removeAttr('disabled');
                        $("#organizer_select_update").removeAttr('disabled');
                        $("#trainer_select_update").removeAttr('disabled');
                        $("#location_select_update").removeAttr('disabled');
                        $("#start_date_input_update").removeAttr('disabled');
                        $("#end_date_input_update").removeAttr('disabled');
                        $("#start_time_input_update").removeAttr('disabled');
                        $("#end_time_input_update").removeAttr('disabled');
                        $("#event_update").removeAttr('disabled');

                        // update listener
                        $("#updateBtn").click(function() {
                          // get post datas
                          const t_id = $("#training_select_update").val();
                          const org_id = $("#organizer_select_update").val();
                          const ta_id = $("#trainer_select_update").val();
                          const loc_id = $("#location_select_update").val();
                          const start_date = $("#start_date_input_update").val();
                          const end_date = $("#end_date_input_update").val();
                          const start_time = $("#start_time_input_update").val();
                          const end_time = $("#end_time_input_update").val();
                          const activated = document.getElementById("activate_input").checked;
                          const evt_to = $("#event_update").val();


                          $.post(`includes/events.inc.php?type=EACT03&evt_id=${evtId}`, {
                            t_id,
                            activated,
                            org_id,
                            ta_id,
                            loc_id,
                            start_date,
                            end_date,
                            start_time,
                            end_time,
                            evt_to, // Convert array to comma-separated string for the backend
                          }).done(function(a, b, xhr) {
                            swal('success', 'Success!', 'Success Update Event');
                          }).fail(function(xhr, a, b) {
                            if (xhr.responseJSON) {
                              const errors = xhr.responseJSON;
                              $("#training_name_error").html(errors['t_id']);
                              $("#organizer_error").html(errors['org_id']);
                              $("#trainer_error").html(errors['ta_id']);
                              $("#location_error").html(errors['loc_id']);
                              $("#start_date_update_error").html(errors['start_date']);
                              $("#end_date_update_error").html(errors['end_date']);
                              $("#start_time_error").html(errors['start_time']);
                              $("#end_time_error").html(errors['end_time']);
                              $("#duration_days_error").html(errors['duration_days']);
                              $("#duration_hours_error").html(errors['duration_hours']);
                              $("#event_to_update_error").html(errors['evt_to']);
                            }
                          });
                        });
                      })
                    }).fail(function(xhr, a, b) {
                      console.log(a, b, xhr.status, xhr.responseJSON);
                    })
                  })
                }
              })
            } else {
              $("#events_data_container").html(`
            <tr>
              <td colspan="9">No training events found</td>
            </tr>
            `);
            }

            // show and hide html elements
            setTimeout(() => {
              // remove loading spinner
              $("#loading_spinner").removeClass('d-block').addClass('d-none');

              // show events list container
              $("#events_list_body_container").removeClass("d-none");

              // show events table and hide spinner
              $("#loading_spinner_events").addClass('d-none').removeClass('d-block');
              $("#events_table").removeClass('d-none');
            }, 500)
          }
        }).fail(function(xhr, a, b) {
          console.log(a, b, xhr.status);
        })
      }

      function validateFields(fields) {
        let emptyFields = [];
        for (let field in fields) {
          const value = fields[field];
          if (Array.isArray(value) && value.length === 0) {
            emptyFields.push(field);
          } else if (value === "" || value === null || value === undefined) {
            emptyFields.push(field);
          }
        }
        return emptyFields;
      }


      function getMonths() {
        // append month_select dari month_names masukan semuanya
        for (const month in month_names) {
          if (month_names.hasOwnProperty(month)) {
            $("#month_select").append(`
            <option value="${month}">${month_names[month]}</option>
          `);
          }
        }
      }

      function getYear() {
        $.get(`includes/events.inc.php`).done(function(a, b, xhr) {
          if (xhr.responseJSON) {
            // set months
            const years = xhr.responseJSON['years'];
            console.log(years);
            $("#year_select").html(`
            <option value="" disabled default selected>-- Filter by year --</option>
            <option value="">All</option>
          `);
            if (years) {
              years.forEach(function(year) {
                $("#year_select").append(`
                <option value="${year['YEAR']}">
                  ${year['YEAR']}
                </option>
              `);
              })
            }
          }
        }).fail(function(xhr, a, b) {
          console.log(a, b, xhr.status, xhr.responseJSON);
        })
      }

      function getDept() {
        $.get('includes/events.inc.php?type=EACT21')
          .done(function(response) {
            var dept = response.map(function(department) {
              return {
                id: department.DPT_ID,
                text: department.DEPARTMENT
              };
            });
            // Add "All" option to the beginning of the array
            dept.unshift({
              id: 'all',
              text: 'All'
            });
            // Update Select2 with new options
            $('#event_to').empty().select2({
              dropdownParent: $('#createEventModal'),
              data: dept,
              placeholder: "-- Select Event To Department --",
              width: '100%'
            });
            $('#event_to').on('select2:select', function(e) {
              var selectedValues = $(this).val();

              // Check if "All" is selected
              if (selectedValues.includes('all')) {
                // Deselect all other options
                $(this).val('all').trigger('change.select2');
              }
            });

            // Event handler for Select2 deselection changes
            $('#event_to').on('select2:unselect', function(e) {
              var selectedValues = $(this).val();

              // If "All" is deselected, do nothing. If other options are selected, ensure "All" is deselected
              if (!selectedValues.includes('all')) {
                $('#event_to option[value="all"]').prop('selected', false);
                $('#event_to').trigger('change.select2');
              }
            });
          })
          .fail(function(xhr) {
            console.log("Error: " + xhr.status);
          });

      }

      function swal(type, title, msg) {
        Swal.fire({
          icon: type,
          title: title,
          text: msg,
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
        }).then((result) => {
          location.reload();
        });
      }

      function SwalNotifYesNo(type, title, desc, successMessage, callback) {
        Swal.fire({
          title: title,
          text: desc,
          icon: type,
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.isConfirmed) {
            if (callback && typeof callback === "function") {
              callback().then(() => {
                Swal.fire(
                  'Success!',
                  successMessage,
                  'success'
                ).then(() => {
                  location.reload(); // Reload the page after showing the error message
                });
              }).catch(() => {
                Swal.fire(
                  'Error!',
                  'Something went wrong!',
                  'error'
                ).then(() => {
                  location.reload(); // Reload the page after showing the error message
                });
              });
            }
          }
        });
      }



      function getCreateEventDatas() {
        $.get(`includes/events.inc.php`).done(function(a, b, xhr) {
          if (xhr.responseJSON) {
            // get responses
            trainings = xhr.responseJSON['trainings'];
            locations = xhr.responseJSON['locations'];
            organizers = xhr.responseJSON['organizers'];
            trainers = xhr.responseJSON['trainers'];

            // set responses to html elements
            trainings?.forEach(function(training) {
              $("#training_name_select").append(`
              <option value="${training['T_ID']}">${training['TRAINING']}</option>
            `);
            })

            organizers?.forEach(function(organizer) {
              $("#organizer_select").append(`
              <option value="${organizer['ORG_ID']}" class="organizer-option">${organizer['ORGANIZER']}</option>
            `);
            })

            trainers?.forEach(function(trainer) {
              $("#trainer_select").append(`
              <option value="${trainer['TA_ID']}">${trainer['NAME']}</option>
            `);
            })

            locations?.forEach(function(location) {
              $("#location_select").append(`
              <option value="${location['LOC_ID']}">${location['LOCATION']}</option>
            `);
            })

            // hide and show html elements
            setTimeout(() => {
              // hide create loading spinner
              $("#create_loading_spinner").removeClass('d-block').addClass('d-none');

              // show create event container
              $("#create_event_container").removeClass('d-none');
            }, 500)
          }
        }).fail(function(xhr, a, b) {
          console.log(a, b, xhr.status, xhr.responseJSON);
        })
      }
    })
  </script>

  <?php
  include_once __DIR__ . '/partials/_footer.php';
  ?>