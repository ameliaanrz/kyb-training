<?php
include_once __DIR__ . '/partials/_header.php';
if (!isset($_SESSION['NPK'])) {
  header("Location: login.php");
  exit();
}
?>


<h1 class="fw-bold">PT. Kayaba Training Center Training Events</h1>
<div class="d-flex justify-content-between">
  <div>
    <a href="eventusr.php#eventupcoming" class="text-decoration-none btn btn-light"><i class="fas fa-circle-down"></i> Training Events Upcoming</a>
    <a href="eventusr.php#event_content_alr" class="text-decoration-none btn btn-light"><i class="fas fa-circle-down"></i> Training Events Completed</a>
    <a href="eventusr.php#event_timeline_container" class="text-decoration-none btn btn-light"><i class="fas fa-circle-down"></i> Training Timeline</a>

  </div>
</div>
<!-- <p class="fs-5">Training events administrator dashboard to manage all PT. Kayaba Indonesia training events</p> -->
<main id="main" class="container">

  <section id="slowonganect-" class="col-12">
    <div class="m-4">
      <p class="h4 text-center fw-semibold">EVENTS <span class="events-running">RUNNING</span></p>
    </div>
    <div id="filter_form" class="form gap-3 d-flex">
      <input id="search_input" type="text" name="search" placeholder="Search by training name or event id" autocomplete="off" class="form-control">
    </div>
    <div id="events_list_body_container" class="card-body d-none pt-2">
      <br>
      <div id="eventupcoming"></div>
      <div id="event_content">
        <div class="row mb-lg-3 mb-md-3 event-card-container" id="events_data_containers">
        </div>
      </div>
    </div>
    <!-- loading spinner -->
    <div id="loading_spinner_events" style="display: flex;align-items: center;justify-content: center;" class="spinner-border text-primary mx-auto mb-4 my-5" role="status">
      <span class="sr-only">Loading...</span>
    </div>

    <br>

    </div>
    <div class="m-4">
      <p class="h4 text-center fw-semibold">EVENTS <span class="events-upcoming">UPCOMING</span></p>
    </div>
    <div id="filter_form" class="form gap-3 d-flex">
      <input id="search_input_UP" type="text" name="search" placeholder="Search by training name or event id" autocomplete="off" class="form-control">
    </div>
    <div id="events_list_body_container_UP" class="card-body d-none pt-2">
      <br>
      <div id="event_content_alr"></div>
      <div id="event_content_UP">
        <div class="row mb-lg-3 mb-md-3 event-card-container" id="events_data_containers_UP">
        </div>
      </div>
    </div>

    <!-- loading spinner -->
    <div id="loading_spinner_events_UP" style="display: flex;align-items: center;justify-content: center;" class="spinner-border text-primary mx-auto mb-4 my-5" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <br>

    <div class="m-4">
      <p class="h4 text-center fw-semibold">EVENTS <span class="events-already">COMPLETED</span></p>
    </div>
    <div id="filter_form" class="form gap-3 d-flex">
      <input id="search_input_ALR" type="text" name="search" placeholder="Search by training name or event id" autocomplete="off" class="form-control">
    </div>
    <div id="events_list_body_container_ALR" class="card-body d-none pt-2">
      <br>
      <div id="event_content_ALR">
        <div class="row mb-lg-3 mb-md-3 event-card-container" id="events_data_containers_ALR">
        </div>
      </div>
    </div>

    <!-- loading spinner -->
    <div id="loading_spinner_events_ALR" style="display: flex;align-items: center;justify-content: center;" class="spinner-border text-primary mx-auto mb-4 my-5" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <br>

  </section>

</main><!-- End #main -->

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
    <div id="calendar" style="height: 400px; width: 1200px;  margin: auto">
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
<script type="text/javascript" src="node_modules/fullcalendar/dist/index.global.min.js"></script>
<!-- <script>
    var myCarousel = document.getElementById('carouselExample');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 3000, // Set the interval for automatic sliding (in milliseconds)
        wrap: true // Set to false to disable continuous loop
    });
</script> -->
<script type="text/javascript" defer>
  $(document).ready(function() {
    checkRole();
    // Get events
    let search = '';
    let search_UP = '';
    let search_ALR = '';
    let filterMonth = new Date().getMonth() + 1; // getMonth() returns month from 0-11
    let year = new Date().getFullYear();

    getEvents1();
    getEventsUpcoming();
    getEventAlrDone();

    function checkRole() {
      $.get('includes/events.inc.php?type=EACT20').done(function(a, b, xhr) {
        role = xhr.responseJSON;
        <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS02') : ?>
          if (role != 'RLS02') {
            window.location.href = 'eventusr.php';
          }
        <?php endif; ?>
        <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS03') : ?>
          if (role != 'RLS03') {
            window.location.href = 'eventusr.php';
          }
        <?php endif; ?>
        <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS04') : ?>
          if (role != 'RLS04') {
            window.location.href = 'eventusr.php';
          }
        <?php endif; ?>
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }

    // Listen to search filter
    $("#search_input").keyup(function() {
      setTimeout(function() {
        getEvents1();
      }, 1000);
      // Show loading spinner and hide events list
      $("#loading_spinner_events").addClass('d-block').removeClass('d-none');
      $("#event_content").addClass('d-none');
      search = this.value;
    });
    $("#search_input_UP").keyup(function() {
      setTimeout(function() {
        getEventsUpcoming();
      }, 1000);
      // Show loading spinner and hide events list
      $("#loading_spinner_events_UP").addClass('d-block').removeClass('d-none');
      $("#event_content_UP").addClass('d-none');
      search_UP = this.value;
    });
    $("#search_input_ALR").keyup(function() {
      setTimeout(function() {
        getEventAlrDone();
      }, 1000);
      // Show loading spinner and hide events list
      $("#loading_spinner_events_ALR").addClass('d-block').removeClass('d-none');
      $("#event_content_ALR").addClass('d-none');
      search_ALR = this.value;
    });

    var sessionDPT_ID = "<?php echo $_SESSION['DPT_ID']; ?>";
    var role = "<?php echo $_SESSION['RLS_ID']; ?>";

    function getMonthName(monthIndex) {
      var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
      return months[monthIndex];
    }

    // Fungsi untuk mengonversi nama hari dari angka ke nama hari dalam bahasa Indonesia
    function getDayName(dayIndex) {
      var days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
      return days[dayIndex];
    }

    function getEvents1() {
      // Show loading spinner and hide events list
      $.get(`includes/events.inc.php?search=${search}&month=${filterMonth}&year=${year}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          // Get responses
          const events = xhr.responseJSON['events'] || [];
          let runningEvents = '';
          let upcomingEvents = '';
          let doneEvents = '';
          const now = new Date();
          
          if (Array.isArray(events)) {
            events.forEach(function(data) {
              var startdate = new Date(data['START_DATE']);
              var enddate = new Date(data['END_DATE']);

              var dayname = getDayName(startdate.getDay());
              var date = startdate.getDate();
              var monthname = getMonthName(startdate.getMonth());
              var tahun = startdate.getFullYear();

              var dayname2 = getDayName(enddate.getDay());
              var date2 = enddate.getDate();
              var monthname2 = getMonthName(enddate.getMonth());
              var tahun2 = enddate.getFullYear();

              var isPast = startdate <= now;
              var isRunning = startdate <= now && now <= enddate;
              var isActive = now <= enddate && data['ACTIVATED'] != 0;
              var between = startdate < now && now < enddate;
              var href = (data['ACTIVATED'] != 1 && isPast) ? `>CLOSED</a>` : `href="events/register.php?evt_id=${data['EVT_ID']}">CLOSED</a>`;
              var href2 = (data['ACTIVATED'] != 1 && isPast) ? `>ACTIVE</a>` : `href="events/register.php?evt_id=${data['EVT_ID']}">Register Participant</a>`;
              var desc;
              if (isRunning) {
                desc = `<a class="btn-event-non btn-danger" ${href}`;
              } else if (isActive) {
                desc = `<a class="btn-event-non btn-danger" ${href2}`;
              } else {
                desc = ``;
              }

              var btn;
              if (role === 'RLS02') {
                if (data['ACTIVATED'] != 0 && isActive) {
                  btn = `<a class="btn-event-non btn-success" href="events/approve.php?evt_id=${data['EVT_ID']}">Approval</a>`;
                } else if (between) {
                  btn = '';
                } else {
                  btn = '';
                }
              } else if (role === 'RLS03') {
                btn = desc;
              } else {
                btn = '';
              }

              let eventHTML = `
              <div class="event-card">
                <div class="card-title">${data['TRAINING']}</div>
                <p class="card-text">${data['PURPOSE'].substring(0, 100)}...</p>
                <div>
                  <a class="btn-event btn-danger" href="events/trainingcontent.php?t_id=${data['T_ID']}&evt_id=${data['EVT_ID']}">View More</a>
                  ${btn}
                </div>
              </div>
            `;

              now.setHours(0, 0, 0, 0);

              if (isRunning || between || isActive) {
                runningEvents += eventHTML;
              }
            })
          };

          if (runningEvents) {
            $("#events_data_containers").html(runningEvents);
          } else {
            $("#events_data_containers").html(`<div class="col-12 text-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <p class="text-center">No events found</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
          }

          setTimeout(() => {
            $("#loading_spinner").removeClass('d-block').addClass('d-none');
            $("#events_list_body_container").removeClass("d-none");
            $("#loading_spinner_events").addClass('d-none').removeClass('d-block');
            $("#event_content").removeClass('d-none');
          }, 500);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      });
    }

    function getEventsUpcoming() {
      // Show loading spinner and hide events list
      $.get(`includes/events.inc.php?search=${search_UP}&month=${filterMonth}&year=${year}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          // Get responses
          const events = xhr.responseJSON['events'] || [];
          let runningEvents = '';
          let upcomingEvents = '';
          let doneEvents = '';
          const now = new Date();

          if (Array.isArray(events)) {
            events.forEach(function(data) {
              var startdate = new Date(data['START_DATE']);
              var enddate = new Date(data['END_DATE']);

              var dayname = getDayName(startdate.getDay());
              var date = startdate.getDate();
              var monthname = getMonthName(startdate.getMonth());
              var tahun = startdate.getFullYear();

              var dayname2 = getDayName(enddate.getDay());
              var date2 = enddate.getDate();
              var monthname2 = getMonthName(enddate.getMonth());
              var tahun2 = enddate.getFullYear();

              var isPast = startdate <= now;
              var isRunning = startdate > now;
              var between = startdate > now && now < enddate;
              var href = (data['ACTIVATED'] != 0 && isPast) ? `` : `href="events/register.php?evt_id=${data['EVT_ID']}"`;
              var desc;
              if (startdate <= now) {
                desc = ``;
              } else if (data['ACTIVATED'] != 1) {
                desc = ``;
              } else if (between) {
                desc = '';
              } else {
                desc = `<a class="btn-event-non btn-danger" ${href}>Register Participant</a>`;

              }
              var btn;
              if (role === 'RLS02') {
                if (data['ACTIVATED'] != 0 && isRunning) {
                  btn = `<a class="btn-event-non btn-success" href="events/approve.php?evt_id=${data['EVT_ID']}">Approval</a>`;
                } else if (between) {
                  btn = '';
                } else {
                  btn = '';
                }
              } else if (role === 'RLS03') {
                btn = desc;
              } else {
                btn = '';
              }

              let eventHTML = `
              <div class="event-card">
                <div class="card-title">${data['TRAINING']}</div>
                <p class="card-text">${data['PURPOSE'].substring(0, 100)}${data['PURPOSE'].length > 100 ? '...' : ''}</p>
                <div>
                  <a class="btn-event btn-danger" href="events/trainingcontent.php?t_id=${data['T_ID']}&evt_id=${data['EVT_ID']}">View More</a>
                  ${btn}
                </div>
              </div>
            `;
              now.setHours(0, 0, 0, 0);
              if (startdate >= now && data['ACTIVATED'] != 1) {
                upcomingEvents += eventHTML;
              }
            })
          };

          if (upcomingEvents) {
            $("#events_data_containers_UP").html(upcomingEvents);
          } else {
            $("#events_data_containers_UP").html(`<div class="col-12 text-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <p class="text-center">No events found</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
          }



          setTimeout(() => {
            $("#loading_spinner_UP").removeClass('d-block').addClass('d-none');
            $("#events_list_body_container_UP").removeClass("d-none");
            $("#loading_spinner_events_UP").addClass('d-none').removeClass('d-block');
            $("#event_content_UP").removeClass('d-none');
          }, 500);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      });
    }

    function getEventAlrDone() {
      // Show loading spinner and hide events list
      $.get(`includes/events.inc.php?search=${search_ALR}&month=${filterMonth}&year=${year}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          // Get responses
          const events = xhr.responseJSON['events'] || [];
          let runningEvents = '';
          let upcomingEvents = '';
          let doneEvents = '';
          const now = new Date();

          if (Array.isArray(events)) {
            events.forEach(function(data) {
              var startdate = new Date(data['START_DATE']);
              var enddate = new Date(data['END_DATE']);

              var dayname = getDayName(startdate.getDay());
              var date = startdate.getDate();
              var monthname = getMonthName(startdate.getMonth());
              var tahun = startdate.getFullYear();

              var dayname2 = getDayName(enddate.getDay());
              var date2 = enddate.getDate();
              var monthname2 = getMonthName(enddate.getMonth());
              var tahun2 = enddate.getFullYear();

              var isPast = startdate <= now;
              var isRunning = startdate > now;
              var between = startdate > now && now < enddate;
              var href = (data['ACTIVATED'] != 0 && isPast) ? `` : `href="events/register.php?evt_id=${data['EVT_ID']}"`;
              var desc;
              if (startdate <= now) {
                desc = ``;
              } else if (data['ACTIVATED'] != 1) {
                desc = ``;
              } else if (between) {
                desc = '';
              } else {
                desc = `<a class="btn-event-non btn-danger" ${href}>Register Participant</a>`;

              }
              var btn;
              if (role === 'RLS02') {
                if (data['ACTIVATED'] != 0 && isRunning) {
                  btn = `<a class="btn-event-non btn-success" href="events/approve.php?evt_id=${data['EVT_ID']}">Approval</a>`;
                } else if (between) {
                  btn = '';
                } else {
                  btn = '';
                }
              } else if (role === 'RLS03') {
                btn = desc;
              } else {
                btn = '';
              }

              let eventHTML = `
              <div class="event-card">
                <div class="card-title">${data['TRAINING']}</div>
                <p class="card-text">${data['PURPOSE'].substring(0, 100)}...</p>
                <div>
                  <a class="btn-event btn-danger" href="events/trainingcontent.php?t_id=${data['T_ID']}&evt_id=${data['EVT_ID']}">View More</a>
                  ${btn}
                </div>
              </div>
            `;
              now.setHours(0, 0, 0, 0);
              if (enddate <= now) {
                doneEvents += eventHTML;
              }
            })
          };

          if (doneEvents) {
            $("#events_data_containers_ALR").html(doneEvents);
          } else {
            $("#events_data_containers_ALR").html(`
                <div class="col-12 text-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <p class="text-center">No events found</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);

          }



          setTimeout(() => {
            $("#loading_spinner_ALR").removeClass('d-block').addClass('d-none');
            $("#events_list_body_container_ALR").removeClass("d-none");
            $("#loading_spinner_events_ALR").addClass('d-none').removeClass('d-block');
            $("#event_content_ALR").removeClass('d-none');
          }, 500);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      });
    }
  });
</script>

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

    // Variable to store event data
    var events_data = [];

    // Function to add days
    function addDays(date, days) {
      var result = new Date(date);
      result.setDate(result.getDate() + days);
      return result;
    }

    var Dates = new Date();
    var years = Dates.getFullYear();

    // Get data from server
    $.get(`includes/events.inc.php?year=${years}`).done(function(data) {
      $.each(data['events'], function(index, event) {
        var startDate = new Date(event['START_DATE']);
        var endDate = new Date(event['END_DATE']);
        var currentDate = startDate;

        // Add event for each day within the date range
        while (currentDate <= endDate) {
          events_data.push({
            title: `${event['TRAINING']} (${event['EVT_ID']})`, // Event title, can be changed to another attribute if needed
            start: currentDate.toISOString().split('T')[0] + 'T' + event['START_TIME'], // Start date and time
            end: currentDate.toISOString().split('T')[0] + 'T' + event['END_TIME'], // End date and time
            organizer: event['ORGANIZER'] // Event organizer
          });
          currentDate = addDays(currentDate, 1);
        }
      });

      // Add event data to the calendar
      calendar.addEventSource(events_data);
      calendar.render();
    });
  });
</script>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>