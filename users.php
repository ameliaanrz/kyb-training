<?php
include_once __DIR__ . '/partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: login.php");
}

$lists_shown = 10;
$currPage = 1;

if (isset($_GET['lists_shown']) && in_array($_GET['lists_shown'], [10, 25, 50])) {
    $lists_shown = (int)$_GET['lists_shown'];
}
?>
<h1 class="fw-bold">PT. Kayaba Training Center Participants</h1>

<a href="users.php#participants_list_container" class="text-decoration-none btn btn-light"><i
    class="fas fa-circle-down"></i> Participants List</a>
<div class="card mt-4 py-3">
  <div class="card-body pt-2">
    <!-- loading spinner -->
    <div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-4" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <!-- query form -->
    <div id="query_form_container" class="card-body pt-0 mt-2 d-none">
      <form id="query_form" action="" method="GET">
        <div class="d-flex gap-3">
          <div style="width: 33%">
            <label for="dept_select">Department</label>
            <select name="department" id="dept_select" class="form-select">
            </select>
          </div>
          <div style="width: 33%">
            <label for="section_select">Section</label>
            <select name="section" id="section_select" class="form-select">
            </select>
          </div>
          <div style="width: 33%">
            <label for="subsec_select">Subsection</label>
            <select name="subsection" id="subsec_select" class="form-select">
            </select>
          </div>
        </div>
        <div class="d-flex gap-3 mt-4">
          <div style="width: 50%">
            <label for="grade_select">Grade</label>
            <select name="grade" id="grade_select" class="form-select mt-2">
            </select>
          </div>
          <div style="width: 50%">
            <label for="gender_select">Gender</label>
            <select name="gender" id="gender_select" class="form-select mt-2">
            </select>
          </div>
        </div>
        <div class="mt-4">
          <label for="training_select">Training</label>
          <select name="training" id="training_select" class="form-select mt-2">
          </select>
        </div>
        <div class="mt-4">
          <label for="search_user" class="form-label">Search by NPK or participant name</label>
          <input type="text" name="search_user" id="search_user" placeholder="Enter NPK or participant name"
            class="form-control">
        </div>
      </form>
    </div>
    <!-- participants list spinner -->
    <div id="participants_list_spinner" class="spinner-border text-primary d-none mx-auto my-4" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <!-- participants list -->
    <div id="participants_list_container" class="d-none">
      <!-- select list numbers shown -->
      <form id="select_list_form" action="" method="GET" class="mt-2">
        <input type="hidden" name="page" value="<?php echo $currPage; ?>">
        <label for="lists_shown" class="d-block form-label ms-auto text-end">Lists shown</label>
        <select name="lists_shown" id="lists_shown" class="form-select mt-2 w-auto d-block ms-auto">
          <option value="10" <?php echo $lists_shown == 10 ? "selected" : ""; ?>>10</option>
          <option value="25" <?php echo $lists_shown == 25 ? "selected" : ""; ?>>25</option>
          <option value="50" <?php echo $lists_shown == 50 ? "selected" : ""; ?>>50</option>
        </select>
      </form>

      <div class="table-responsive" style="overflow-x: auto;">
      <table class="table mt-2 table-responsive table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th scope="col" class="text-center">NPK <i id="sort-icon-NPK" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Name <i id="sort-icon-NAME" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Company <i id="sort-icon-COMPANY" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Department <i id="sort-icon-DEPARTMENT" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Section <i id="sort-icon-SECTION" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Subsection <i id="sort-icon-SUBSECTION" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Grade <i id="sort-icon-GRADE" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Gender <i id="sort-icon-GENDER" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Training</i></th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      </div>


      <!-- pagination links -->
      <div class="d-flex justify-content-end mt-4">
        <div id="pagination_container"></div>
      </div>

      <!-- print report button -->
      <div class="d-flex justify-content-end mt-4">
        <a href="includes/reports.inc.php?type=RACT02" id="print_participants_report" class="btn btn-primary w-25"><i
            class="fa-solid fa-file"></i> Print participants report</a>
      </div>
    </div>
  </div>
</div>

<!-- require paginationjs -->
<script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

<!-- custom jquery script -->
<script type="text/javascript" defer>
  $(document).ready(function () {
    // variables
    let listsShown ='';
    let dpt_id = '';
    let sec_id = '';
    let sub_sec_id = '';
    let q_grade = '';
    let q_gender = '';
    let t_id = '';
    let search = '';
    let colomIndex ='';
    let direction ='';

    getFilters();
    getParticipants();
    changeListShown();

    $("#lists_shown").change(function() {
        changeListShown();
    });

    function changeListShown() {
        listsShown = $("#lists_shown").val();
        // remove participants list container
      $("#participants_list_container").addClass('d-none');

      // show participants list spinner
      $("#participants_list_spinner").removeClass('d-none').addClass('d-block');
        setTimeout(function () {
        getParticipants();
      }, 1000);
    }

    // search filter
    $("#search_user").keyup(function () {
      search = this.value;

      // remove participants list container
      $("#participants_list_container").addClass('d-none');

      // show participants list spinner
      $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

      setTimeout(function () {
        getParticipants();
      }, 1000);
    });

    function setPrintReportHtml() {
      $("#print_participants_report").attr('href', `includes/reports.inc.php?type=RACT02&dpt_id=${dpt_id}&sec_id=${sec_id}&sub_sec_id=${sub_sec_id}&t_id=${t_id}&grade=${q_grade}&gender=${q_gender}&search=${search}`);
    }
    function getFilters() {
      $.get(`includes/users.inc.php?type=TACT02&dpt_id=${dpt_id}&sec_id=${sec_id}&sub_sec_id=${sub_sec_id}&grade=${q_grade}&gender=${q_gender}&t_id=${t_id}`).done(function (a, b, xhr) {
        if (xhr?.responseJSON) {
          const departments = xhr.responseJSON['departments'];
          const sections = xhr.responseJSON['sections'];
          const subsections = xhr.responseJSON['subsections'];
          const genders = xhr.responseJSON['genders'];
          const grades = xhr.responseJSON['grades'];
          const trainings = xhr.responseJSON['trainings'];

          // set filters
          $("#dept_select").html(`
            <option selected disabled value>-- Select department --</option>
            <option value="">All</option>
          `);

          departments.forEach(function (dept) {
            $("#dept_select").append(`
              <option value="${dept['DPT_ID']}" ${dept['DPT_ID'] == dpt_id ? "selected" : ""}>${dept['DEPARTMENT']}</option>
            `);
          });

          $("#section_select").html(`
            <option selected disabled value>-- Select section --</option>
            <option value="">All</option>
          `);

          sections.forEach(function (sec) {
            $("#section_select").append(`
              <option value="${sec['SEC_ID']}" ${sec['SEC_ID'] == sec_id ? "selected" : ""}>${sec['SECTION']}</option>
            `);
          });

          $("#subsec_select").html(`
            <option selected disabled value>-- Select subsection --</option>
            <option value="">All</option>
          `);

          subsections.forEach(function (subsec) {
            $("#subsec_select").append(`
              <option value="${subsec['SUB_SEC_ID']}" ${subsec['SUB_SEC_ID'] == sub_sec_id ? "selected" : ""}>${subsec['SUBSECTION']}</option>
            `);
          });

          $("#grade_select").html(`
            <option selected disabled value>-- Select grade --</option>
            <option value="">All</option>
          `);

          grades.forEach(function (grade) {
            $("#grade_select").append(`
              <option value="${grade['GRADE']}" ${grade['GRADE'] == q_grade ? "selected" : ""}>${grade['GRADE']}</option>
            `);
          });

          $("#gender_select").html(`
            <option selected disabled value>-- Select gender --</option>
            <option value="">All</option>
          `);

          genders.forEach(function (gender) {
            $("#gender_select").append(`
              <option value="${gender['GENDER']}" ${gender['GENDER'] == q_gender ? "selected" : ""}>${gender['GENDER']}</option>
            `);
          });

          $("#training_select").html(`
            <option selected disabled value>-- Select training --</option>
            <option value="">All</option>
          `);

          trainings.forEach(function (training) {
            $("#training_select").append(`
              <option value="${training['T_ID']}" ${training['T_ID'] == t_id ? "selected" : ""}>${training['TRAINING']}</option>
            `);
          });

          $("#dept_select").change(function () {
            dpt_id = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

            setPrintReportHtml();

            getParticipants();
            getFilters();
          });

          $("#section_select").change(function () {
            sec_id = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

            setPrintReportHtml();

            getParticipants();
            getFilters();
          });

          $("#subsec_select").change(function () {
            sub_sec_id = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

            setPrintReportHtml();

            getParticipants();
            getFilters();
          });

          $("#grade_select").change(function () {
            q_grade = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

            setPrintReportHtml();

            getParticipants();
            getFilters();
          });

          $("#gender_select").change(function () {
            q_gender = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

            setPrintReportHtml();

            getParticipants();
            getFilters();
          });

          $("#training_select").change(function () {
            t_id = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

            setPrintReportHtml();

            getParticipants();
            getFilters();
          });

          setTimeout(function () {
            // show filters
            $("#query_form_container").removeClass('d-none');
          }, 500);
        }else if(xhr.status == 204){
          // no users found
          $("#pagination_container").pagination({
            dataSource: [],
            pageSize: listsShown,
            callback: function (data, pagination) {
              $("tbody").html(`
                <tr class="bg-white">
                  <td colspan="9" class="text-center">No users found</td>
                </tr>
              `);
            }
          });
        }
      }).fail(function (xhr, a, b) {
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

            // remove participants list container
      $("#participants_list_container").addClass('d-none');

      // show participants list spinner
      $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

      setTimeout(function () {
        getParticipants();
      }, 100);
      });

    function getParticipants() {
      $.get(`includes/users.inc.php?dpt_id=${dpt_id}&sec_id=${sec_id}&sub_sec_id=${sub_sec_id}&grade=${q_grade}&gender=${q_gender}&t_id=${t_id}&search=${search}&colomIndex=${colomIndex}&direction=${direction}`).done(function (a, b, xhr) {
        if (xhr.status == 204) {
          // no users found
          $("#pagination_container").pagination({
            dataSource: [],
            pageSize: listsShown,
            callback: function (data, pagination) {
              $("tbody").html(`
                <tr class="bg-white">
                  <td colspan="9" class="text-center">No users found</td>
                </tr>
              `);
            }
          });
        } else {
          // show participants
          console.log(xhr.responseJSON);
          if (xhr?.responseJSON) {
              // Create the Set to track displayed NPKs
          let displayedNPKs = new Set();

          // Filter the data source to only include unique NPKs
          let filteredData = xhr.responseJSON.filter(user => {
              if (!displayedNPKs.has(user['NPK'])) {
                  displayedNPKs.add(user['NPK']);
                  return true;
              }
              return false;
          });

          $("#pagination_container").pagination({
              dataSource: filteredData,
              pageSize: listsShown,
              callback: function (data, pagination) {
                  let html = '';

                  data.forEach(function (user) {
                      html += `
                      <tr>
                          <td>${user['NPK']}</td>
                          <td>${user['NAME']}</td>
                          <td>${user['COMPANY']}</td>
                          <td>${user['DEPARTMENT']}</td>
                          <td>${user['SECTION'] != null ? user['SECTION'] : '-'}</td>
                          <td>${user['SUBSECTION'] != null ? user['SUBSECTION'] : '-'}</td>
                          <td>${user['GRADE']}</td>
                          <td>${user['GENDER']}</td>
                          <td>
                              <div class="dropdown">
                                  <button class="btn btn-outline-dark dropdown-toggle py-1" type="button" data-bs-toggle="dropdown">
                                      Detail
                                  </button>
                                  <div class="dropdown-menu dropdown-menu-end">
                                      <a href="users/details.php?npk=${user['NPK']}" class="dropdown-item show-update-btn"><i class="fas fa-eye"></i> Training details</a>
                                  </div>
                              </div>
                          </td>
                      </tr>
                      `;
                  });
                      $("tbody").html(html);
                  }
              });
          }

        }

        setTimeout(function () {
          // remove loading spinner
          $("#loading_spinner").removeClass('d-block').addClass('d-none');

          // remove participants list spinner
          $("#participants_list_spinner").removeClass('d-block').addClass('d-none');

          // show participants list container
          $("#participants_list_container").removeClass('d-none');
        }, 500);
      }).fail(function (xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }

  })
</script>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>