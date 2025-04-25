<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: /login.php");
}
?>

<h1 class="fs-2 fw-semibold"><span class="fw-bold" id="name_span"></span>'s Profile Details</h1>
<p>Training participant's profile details</p>
<hr>
<!-- breadcrumb nav -->
<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb" class="d-block my-0 py-0">
  <ol class="breadcrumb my-0 py-0">
    <li class="breadcrumb-item"><a href="users.php" class="text-decoration-none">Participants</a></li>
    <li class="breadcrumb-item active" aria-current="page">Trainings details</li>
  </ol>
</nav>
<!-- loading spinner -->
<div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-4" role="status">
  <span class="sr-only">Loading...</span>
</div>
<!-- participant profile details -->
<form action="" method="POST" id="profile_details" class="mt-4 d-none">
  <div>
    <label for="npk" class="form-label fw-semibold">NPK</label>
    <input type="text" name="npk" id="npk_input" class="form-control" placeholder="Enter unique NPK" disabled>
  </div>
  <div class="mt-4">
    <label for="first_name_input" class="form-label fw-semibold">First and last name</label>
    <div class="input-group">
      <input type="text" name="first_name" id="first_name_input" class="form-control" disabled placeholder="Enter first name">
      <input type="text" name="last_name" id="last_name_input" class="form-control" disabled placeholder="Enter last name">
    </div>
  </div>
  <div class="mt-4">
    <label for="cds" class="fw-semibold form-label">Department, Section, and Subsection</label>
    <div class="input-group">
      <select name="dpt_id" id="department_select" disabled class="form-select">
      </select>
      <select name="sec_id" id="section_select" disabled class="form-select">
      </select>
      <select name="sub_sec_id" id="subsection_select" disabled class="form-select">
      </select>
    </div>
  </div>
  <div class="mt-4">
    <label for="grade" class="form-label fw-semibold">Grade and gender</label>
    <div class="input-group">
      <select name="grade" id="grade_select" disabled class="form-select">
      </select>
      <select name="gender" id="gender_select" disabled class="form-select">
      </select>
    </div>
  </div>
</form>
<section id="trainings_list" class="mt-5">
  <h3 class="fw-semibold">Training Events List</h3>
  <p>List of training events enrolled by <span id="name_span2"></span></p>
  <hr>
  <a id="print_trainings_report" href="includes/reports.inc.php?type=RACT03" class="btn btn-primary"><i class="fa-solid fa-file"></i> Print trainings report</a>
  <!-- loading spinner -->
  <div id="filter_spinner" class="spinner-border text-primary d-block mx-auto my-4" role="status">
    <span class="sr-only">Loading...</span>
  </div>
  <div id="filter_container" class="d-none">
    <form id="filter_form" action="" method="GET" class="mt-4">
      <div>
        <label for="t_id" class="form-label fw-semibold">Training and organizer</label>
        <div class="input-group">
          <select name="t_id" id="training_select" class="form-select">
          </select>
          <select name="org_id" id="organizer_select" class="form-select">
          </select>
        </div>
      </div>
      <div class="mt-4">
        <label for="start_date_input" class="form-label fw-semibold">Start and end dates</label>
        <div class="input-group">
          <input type="date" name="start_date" id="start_date_input" class="form-control" />
          <input type="date" name="end_date" id="end_date_input" class="form-control" />
        </div>
      </div>
      <div class="mt-4">
        <label for="organizer" class="form-label fw-semibold">Completion status</label>
        <div class='input-group'>
          <select name="completion" id="completion_select" class="form-select">
            <option disabled selected value> -- Select completion status -- </option>
            <option value="">All</option>
            <option value="1">Not completed</option>
            <option value="2">Completed</option>
          </select>
        </div>
      </div>
      <input id="search_input" type="text" name="search" placeholder="Search by training name or ID" autocomplete="off" class="form-control mt-5">
    </form>
    <!-- select list numbers shown -->
    <form id="select_list_form" action="" method="GET" class="mt-5">
      <select name="lists_shown" id="lists_shown" class="form-select mt-2 w-auto d-block ms-auto">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
      <label for="lists_shown" class="d-block form-label ms-auto text-end">Lists shown</label>
    </form>
  </div>
  <div id="list_spinner" class="spinner-border d-none text-primary mx-auto my-4" role="status">
    <span class="sr-only">Loading...</span>
  </div>
  <table id="trainings_list" class="table mt-3 rounded-2 table-striped table-bordered table-hover d-none">
    <thead>
      <tr>
        <th scope="col" class="text-center">ID <i id="sort-icon-EVT_ID" class="fas fa-sort"></i></th>
        <th scope="col" class="text-center">Training <i id="sort-icon-TRAINING" class="fas fa-sort"></i></th>
        <th scope="col" class="text-center">Organizer <i id="sort-icon-ORGANIZER" class="fas fa-sort"></i></th>
        <th scope="col" class="text-center">Start Time <i id="sort-icon-START_TIME" class="fas fa-sort"></i></th>
        <th scope="col" class="text-center">Finish Time <i id="sort-icon-FINISH TIME" class="fas fa-sort"></i></th>
        <th scope="col" class="text-center">Completion Status <i id="sort-icon-ACTIVATED" class="fas fa-sort"></i></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="text-center" colspan="7">No events found</td>
      </tr>
    </tbody>
  </table>

  <div class="d-flex justify-content-end mt-4">
    <div id="pagination_container"></div>
  </div>
</section>

<!-- require paginationjs -->
<script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

<!-- custom jquery script -->
<script type="text/javascript" defer>
  $(document).ready(function() {
    // get url params
    const queryString = new URLSearchParams(window.location.search);
    const npk = queryString.get('npk');

    // variables
    let lists_shown = 10;
    let t_id_filter = '';
    let org_id_filter = '';
    let start_date_filter = '';
    let end_date_filter = '';
    let approval_filter = '';
    let completion_filter = '';
    let search_filter = '';
    let colomIndex ='';
    let direction ='';

    getProfileDetails();
    getFilterDetails();
    getTrainings();

    // listen to lists shown
    $("#lists_shown").change(function() {
      lists_shown = this.value;
      getTrainings();
    })

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
        getTrainings();
      }, 1000);

      // show loading spinner and hide events list
      $("#loading_spinner").addClass('d-block').removeClass('d-none');
      $("#table").addClass('d-none');
        });

    function getTrainings() {
      // show spinner and hide events
      $("#list_spinner").removeClass('d-none').addClass('d-block');
      $("table").addClass('d-none');

      // get training events
      $.get(`includes/users.inc.php?type=TACT05&npk=${npk}&t_id=${t_id_filter}&org_id=${org_id_filter}&start_date=${start_date_filter}&end_date=${end_date_filter}&approved=${approval_filter}&completed=${completion_filter}&search=${search_filter}&colomIndex=${colomIndex}&direction=${direction}`).done(function(a, b, xhr) {

        if (xhr.status == 204) {
          $("tbody").html(`
            <tr>
              <td class="text-center" colspan="7">No events found</td>
            </tr>
          `);
        } else {
          if (xhr.responseJSON) {
            const events = xhr.responseJSON;

            $("#pagination_container").pagination({
              dataSource: events,
              pageSize: lists_shown,
              callback: function(data, pagination) {
                let html = '';

                data.forEach(function(item) {
                  const date1 = new Date();
                  const date2 = new Date(item['END_DATE']);
                  const diff = date2 - date1;
                  html += `
                        <tr>
                          <td>${item['EVT_ID']}</td>
                          <td>${item['TRAINING']}</td>
                          <td>${item['ORGANIZER']}</td>
                          <td>${item['START_DATE']}</td>
                          <td>${item['END_DATE']}</td>
                          <td>${getStatus(item['START_DATE'], item['END_DATE'])}</td>
                        </tr>
                      `;

                })

                $("tbody").html(html);
              }
            })
          }
        }

        // remove spinner and show events
        setTimeout(function() {
          $("#list_spinner").removeClass('d-block').addClass('d-none');
          $("table").removeClass('d-none');
        }, 500);
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
      .always(function() {
      // Remove spinner and show events
      $("#list_spinner").removeClass('d-block').addClass('d-none');
      $("table").removeClass('d-none');
    });
    }
    function getStatus(startDate, endDate) {
      const now = new Date();
      startDate = new Date(startDate);
      endDate = new Date(endDate);

      if (now < startDate) {
        return "<span class='text-primary'>Upcoming</span>";
      } else if (now >= startDate && now <= endDate) {
        return "<span class='text-warning'>In Progress</span>";
      } else {
        return "<span class='text-success'>Completed</span>";
      }
    }

    function changePrintHref() {
      $("#print_trainings_report").attr('href', `includes/reports.inc.php?type=RACT03&npk=${npk}&t_id=${t_id_filter}&org_id=${org_id_filter}&start_date=${start_date_filter}&end_date=${end_date_filter}&approved=${approval_filter}&completed=${completion_filter}`);
    }

    function getFilterDetails() {
      $.get(`includes/users.inc.php?type=TACT04&npk=${npk}&t_id=${t_id_filter}&org_id=${org_id_filter}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          const trainings = xhr.responseJSON['trainings'];
          const organizers = xhr.responseJSON['organizers'];

          changePrintHref();

          $("#training_select").html(`
            <option selected value>-- Select training --</option>
            <option value="">All</option>
          `);

          trainings.forEach(function(training) {
            $("#training_select").append(`
              <option value="${training['T_ID']}" ${t_id_filter == training['T_ID']? "selected": ""}>${training['TRAINING']}</option>
            `);
          })

          $("#organizer_select").html(`
            <option selected value>-- Select organizer --</option>
            <option value="">All</option>
          `);

          organizers.forEach(function(organizer) {
            $("#organizer_select").append(`
              <option value="${organizer['ORG_ID']}" ${org_id_filter == organizer['ORG_ID']? "selected": ""}>${organizer['ORGANIZER']}</option>
            `);
          })

          // change listener
          $("#training_select").change(function() {
            t_id_filter = this.value;


            // show list spinner and hide training list
            $("#list_spinner").removeClass('d-none').addClass('d-block');
            $("#training_list").addClass('d-none');

            changePrintHref();

            // re-get trainings
            getTrainings();
            getFilterDetails();
          })

          $("#organizer_select").change(function() {
            org_id_filter = this.value;

            // show list spinner and hide training list
            $("#list_spinner").removeClass('d-none').addClass('d-block');
            $("#training_list").addClass('d-none');

            changePrintHref();

            // re-get trainings
            getTrainings();
            getFilterDetails();
          })

          $("#start_date_input").change(function() {
            start_date_filter = this.value;

            setTimeout(function() {
              // show list spinner and hide training list
              $("#list_spinner").removeClass('d-none').addClass('d-block');
              $("#training_list").addClass('d-none');

              changePrintHref();

              // re-get trainings
              getTrainings();
            }, 1000);
          })

          $("#end_date_input").change(function() {
            end_date_filter = this.value;

            setTimeout(function() {
              // show list spinner and hide training list
              $("#list_spinner").removeClass('d-none').addClass('d-block');
              $("#training_list").addClass('d-none');

              changePrintHref();

              // re-get trainings
              getTrainings();
            }, 1000);
          })

          

          $("#completion_select").change(function() {
            completion_filter = this.value;

            // show list spinner and hide training list
            $("#list_spinner").removeClass('d-none').addClass('d-block');
            $("#training_list").addClass('d-none');

            changePrintHref();

            // re-get trainings
            getTrainings();
          })

          $("#search_input").keyup(function() {
            search_filter = this.value;

            setTimeout(function() {
              // show list spinner and hide training list
              $("#list_spinner").removeClass('d-none').addClass('d-block');
              $("#training_list").addClass('d-none');

              // re-get trainings
              getTrainings();
            }, 1000);
          })

          // remove spiner and show filters
          setTimeout(function() {
            $("#filter_container").removeClass('d-none');
            $("#filter_spinner").removeClass('d-block').addClass('d-none');
          }, 500);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }

    function getProfileDetails() {
      $.get(`includes/users.inc.php?type=TACT03&npk=${npk}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          const firstname = xhr.responseJSON['NAME'].split(" ")[0];
          const lastname = xhr.responseJSON['NAME'].split(' ').slice(1).join(' ');
          const dpt_id = xhr.responseJSON['DPT_ID'];
          const department = xhr.responseJSON['DEPARTMENT'];
          const sec_id = xhr.responseJSON['SEC_ID'];
          const section = xhr.responseJSON['SECTION'];
          const sub_sec_id = xhr.responseJSON['SUB_SEC_ID'];
          const subsection = xhr.responseJSON['SUBSECTION'];
          const grade = xhr.responseJSON['GRADE'];
          const gender = xhr.responseJSON['GENDER'];

          $("#name_span").html(xhr.responseJSON['NAME']);
          $("#name_span2").html(xhr.responseJSON['NAME']);

          $("#npk_input").val(npk);
          $("#first_name_input").val(firstname);
          $("#last_name_input").val(lastname);
          $("#department_select").html(`
            <option value>-- Select department --</option>
            <option value="">All</option>
            <option value="${dpt_id}" selected>${department}</option>
          `);
          $("#section_select").html(`
            <option value>-- Select section --</option>
            <option value="">All</option>
            <option value="${sec_id}" selected>${section}</option>
          `);
          $("#subsection_select").html(`
            <option value>-- Select subsection --</option>
            <option value="">All</option>
            <option value="${sub_sec_id}" selected>${subsection}</option>
          `);
          $("#grade_select").html(`
            <option value>-- Select grade --</option>
            <option value="">All</option>
            <option value="${grade}" selected>${grade}</option>
          `);
          $("#gender_select").html(`
            <option value>-- Select gender --</option>
            <option value="">All</option>
            <option value="${gender}" selected>${gender}</option>
          `);

          // show profile details
          setTimeout(function() {
            $("#loading_spinner").removeClass('d-block').addClass('d-none');
            $("#profile_details").removeClass('d-none');
          }, 500);
        }
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }
  })
</script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>