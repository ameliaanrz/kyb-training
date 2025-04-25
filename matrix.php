<?php
include_once __DIR__ . '/partials/_header.php';
// only allow HRD admin
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: /login.php");
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Metode HTTP yang diizinkan
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

?>

<h1 class="fw-bold">PT. Kayaba Training Center Matrix</h1>
<!-- <p class="fs-5">Training administrator dashboard to manage all PT. Kayaba Indonesia training programs</p> -->
<div class="card mt-4 py-3">
  <!-- <div class="card-header pb-0 border-0 bg-white">
    <h3 class="fw-semibold">Trainings List</h3> 
    <p>List of all trainings provided by PT. Kayaba Indonesia</p>
    <hr>
  </div> -->
  <div class="card-body pt-2">
    <div id="search_filter_container">
      <label for="department_search_input" class="fw-bold">Department name or ID search</label>
      <input id="department_search_input" type="text" name="search" placeholder="Search by department name or id" class="form-control mt-2" autocomplete="off">
    </div>
    <!-- select list numbers shown -->
    <div id="lists_shown_container" class="mt-3">
      <select name="lists_shown" id="lists_shown_select" class="form-select mt-2 w-auto d-block ms-auto">
        <option value="10" default selected>10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
      <label for="lists_shown" class="d-block form-label ms-auto text-end">Lists shown</label>
    </div>
    <!-- loading spinner -->
    <div id="loading_spinner" class="spinner-border text-primary d-block mx-auto mb-4" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <!-- trainings list table -->
    <table id="departments_table" class="table d-none mt-3 rounded-2 table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th scope="col" class="text-center">Kode <i id="sort-icon-DPT_ID" class="fas fa-sort"></i></th>
          <th scope="col" class="text-center">Department Name <i id="sort-icon-DEPARTMENT" class="fas fa-sort"></i></th>
          <th scope="col" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>

    <div class="d-flex justify-content-end mt-4">
      <div id="pagination_container"></div>
    </div>
  </div>
</div>
<!-- create, update, and delete training modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="trainingModalTitle" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="departmentModalTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="departmentModalBody" class="modal-body">
        <!-- loading spinner -->
        <div class="spinner-border text-primary d-block mx-auto my-4" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <!-- loading spinner end -->
      </div>
      <div class="modal-footer">
        <button id="departmentModalCloseBtn" type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
        <button id="departmentModalActionBtn" type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<!-- modal end -->

<!-- require paginationjs -->
<script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

<script type="text/javascript" defer>
  $(document).ready(function() {
    // variables
    let search = '';
    let listsShown = 10;
    let page = 1;
    let typingTimer;
    let doneTypingInterval = 1000;
    let companyPurposes = [];
    let participantPurposes = [];
    let colomIndex='';
    let direction='';

    // get all trainings
    getAllDepartments(listsShown, page, search, colomIndex, direction);

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

    // search training trigger
    $("#department_search_input").keyup(function() {
      // show loading
      $("#loading_spinner").removeClass('d-none').addClass('d-block');

      // hide table
      $("#departments_table").addClass('d-none');

      // clear previous typing timer
      clearTimeout(typingTimer);

      // set timeout to call searchTraining
      typingTimer = setTimeout(searchDepartment, doneTypingInterval);
    })

    // lists shown select option change
    $("#lists_shown_select").on('change', function() {
      // show loading
      $("#loading_spinner").removeClass('d-none').addClass('d-block');

      // hide table
      $("#departments_table").addClass('d-none');

      // clear previous typing timer
      clearTimeout(typingTimer);

      // set timeout to call changeListsShown
      typingTimer = setTimeout(changeListsShown, doneTypingInterval);
    })

    function changeListsShown() {
      // get lists shown value
      listsShown = $("#lists_shown_select").val();

      // remove previous contents
      $("tbody").html('');

      // get filtered trainings
      getAllDepartments(listsShown, page, search,colomIndex,direction);
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
        getAllDepartments(listsShown, page, search,colomIndex,direction);
      }, 1000);

      // show loading spinner and hide events list
      $("#loading_spinner_events").addClass('d-block').removeClass('d-none');
      $("#events_table").addClass('d-none');

      });


    function searchDepartment() {
      // get search value
      search = $("#department_search_input").val();

      // remove previous contents
      $("tbody").html('');

      // get filtered trainings
      getAllDepartments(listsShown, page, search,colomIndex,direction);
    }

    function getAllDepartments(lists_shown = 10, page = 1, search = '',colomIndex,direction) {
      $.get(`includes/matrix.inc.php?type=MACT01&lists_shown=${lists_shown}&page=${page}&search=${search}&colomIndex=${colomIndex}&direction=${direction}`).done(function(a, b, xhr) {
        if (xhr.status === 204) {
          $("tbody").html(`<tr><td colspan="3">No departments found</td></tr>`);
        } else {
          const departmentsCount = xhr.responseJSON['departments_count'];
          const departments = xhr.responseJSON['departments'];

          if (xhr?.responseJSON) {
            $("#pagination_container").pagination({
              dataSource: departments,
              pageSize: lists_shown,
              callback: function(data, pagination) {
                // template method of yourself
                let html = '';
                data.forEach(function(department) {
                  if (department.STATUS != 0) {
                      html += `
                          <tr>
                              <td style="width:10%;" scope="row">${department['DPT_ID']}</td>
                              <td style="text-align: left;">${department['DEPARTMENT'] ? `${department['DEPARTMENT']}` : '-'}</td>
                              <td style="width:10%;">
                                  <div class="dropdown">
                                      <button class="btn btn-outline-dark dropdown-toggle py-1" type="button" data-bs-toggle="dropdown">
                                          Detail
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-end">
                                          <a class="dropdown-item" href="matrix/content.php?dpt_id=${department['DPT_ID']}"><i class="fas fa-eye"></i> View Document</a>
                                      </div>
                                  </div>
                              </td>
                          </tr>
                      `;
                  }
                })
                $("tbody").html(html);
              }
            })
          }
        }

        setTimeout(() => {
          // show table
          $("#departments_table").removeClass('d-none');

          // hide loading spinner
          $("#loading_spinner").removeClass("d-block").addClass('d-none');
        }, 500)
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      }).always(function() {
      // Remove spinner and show events
      $("#loading_spinner").removeClass('d-block').addClass('d-none');
      $("table").removeClass('d-none');
    });
    }

    function swal(type,title,msg){
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
              );
            });
          }
        }
      });
    }
  })
</script>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>