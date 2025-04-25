<?php
include_once __DIR__ . '/partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: login.php");
}
?>

<h1 class="fw-bold">PT. Kayaba Training Center Set Access Roles</h1>
<!-- <p class="fs-5">Participants administrator dashboard to manage all PT. Kayaba Indonesia training participants</p> -->
<a href="roles.php#participants_list_container" class="text-decoration-none btn btn-light"><i
    class="fas fa-circle-down"></i> Users List</a>
<div class="card mt-4 py-3">
  <!-- <div class="card-header pb-0 border-0 bg-white">
    <h3 class="fw-semibold">Participants List</h3>
    <p>List of all participants participating in PT. Kayaba Indonesia trainings</p>
    <hr>
  </div> -->
  <div class="card-body pt-2">
    <!-- loading spinner -->
    <div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-4" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <!-- query form -->
    <div id="query_form_container" class="card-body pt-0 mt-2 d-none">
      <form id="query_form" action="" method="GET">
        <div class="d-flex gap-3">
          <div style="width: 50%">
            <label for="dept_select">Department</label>
            <select name="department" id="dept_select" class="form-select">
            </select>
          </div>
          <div style="width: 50%">
            <label for="role_select">Roles</label>
            <select name="role" id="role_select" class="form-select">
            </select>
          </div>
        </div>
        <div class="d-flex gap-3 mt-4">
          <div style="width: 50%">
            <label for="section_select">Section</label>
            <select name="section" id="section_select" class="form-select">
            </select>
          </div>
          <div style="width: 50%">
            <label for="subsec_select">Subsection</label>
            <select name="subsection" id="subsec_select" class="form-select">
            </select>
          </div>
        </div>
        <div class="mt-4">
          <label for="search_user" class="form-label">Search by NPK or Users name</label>
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

      <table class="table mt-2 table-responsive table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th scope="col" class="text-center">NPK <i id="sort-icon-NPK" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Name <i id="sort-icon-NAME" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Company <i id="sort-icon-COMPANY" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Department <i id="sort-icon-DEPARTMENT" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Section <i id="sort-icon-SECTION" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Subsection <i id="sort-icon-SUBSECTION" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Role <i id="sort-icon-ROLE" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Edit</i></th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>

      <!-- pagination links -->
      <div class="d-flex justify-content-end mt-4">
        <div id="pagination_container"></div>
      </div>

    </div>
  </div>
</div>



<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User Roles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <!-- Add your form fields here -->
                    <div class="mb-3">
                        <label for="userNPK" class="form-label">NPK</label>
                        <input type="text" class="form-control" id="userNPK" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="userName" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label">Role</label>
                        <select class="form-select" id="userRole">
                            <option value="">--Select Roles--</option>
                            <option value="RLS01">Dashboard Administrator(HR Roles)</option>
                            <option value="RLS02">Department Administrator(Kadept Roles)</option>
                            <option value="RLS03">Department PIC(User Roles)</option>
                            <option value="RLS04">Training Participant(Cant Access Page)</option>
                        </select>
                    <!-- Add more fields as necessary -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="UpdateBtn" class="btn btn-primary">Save changes</button>
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
    checkRole();
    let listsShown ='';
    let dpt_id = '';
    let sec_id = '';
    let sub_sec_id = '';
    let search = '';
    let colomIndex ='';
    let direction ='';
    let npk = '';
    let name = '';
    let company = '';
    let role = '';

    getFilters();
    getParticipants();
    changeListShown();

    $("#lists_shown").change(function() {
        changeListShown();
    });

    function checkRole() {
      $.get('includes/events.inc.php?type=EACT20').done(function(a, b, xhr) {
        roles = xhr.responseJSON;
        if(roles != 'RLS01'){
          window.location.href = 'index.php';
        }

      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }

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

    function getFilters() {
      $.get(`includes/roles.inc.php?type=3&dpt_id=${dpt_id}&sec_id=${sec_id}&sub_sec_id=${sub_sec_id}&role=${role}`).done(function (a, b, xhr) {
        if (xhr?.responseJSON) {
          const departments = xhr.responseJSON['departments'];
          const sections = xhr.responseJSON['sections'];
          const subsections = xhr.responseJSON['subsections'];
            const roles = xhr.responseJSON['roles'];

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
          $("#role_select").html(`
            <option selected disabled value>-- Select role --</option>
            <option value="">All</option>
          `);

            roles.forEach(function (role) {
                $("#role_select").append(`
                <option value="${role['RLS_ID']}" ${role['RLS_ID'] == role ? "selected" : ""}>${role['ROLE']}</option>
                `);
            });


          $("#dept_select").change(function () {
            dpt_id = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');


            
            getParticipants();
            getFilters();
          });

          $("#section_select").change(function () {
            sec_id = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

            

            getParticipants();
            getFilters();
          });

          $("#subsec_select").change(function () {
            sub_sec_id = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

            

            getParticipants();
            getFilters();
          });

          $("#role_select").change(function (){
            role = this.value;

            // remove loading spinner
            $("#participants_list_spinner").removeClass('d-none').addClass('d-block');

            // show participants list container
            $("#participants_list_container").addClass('d-none');

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
      $.get(`includes/roles.inc.php?npk=${npk}&name=${name}&role=${role}&dpt_id=${dpt_id}&sec_id=${sec_id}&sub_sec_id=${sub_sec_id}&search=${search}&colomIndex=${colomIndex}&direction=${direction}`).done(function (a, b, xhr) {
        if (xhr.status == 204 || xhr.status == 404 ) {
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
                          <td  class="text-left">${user['NPK']}</td>
                          <td style="text-align: left;">${user['NAME']}</td>
                          <td>${user['COMPANY']}</td>
                          <td style="text-align: left;" >${user['DEPARTMENT']}</td>
                          <td>${user['SECTION'] != null ? user['SECTION'] : '-'}</td>
                          <td>${user['SUBSECTION'] != null ? user['SUBSECTION'] : '-'}</td>
                          <td style="text-align: left;">${user['ROLE'] != null ? user['ROLE'] : '-'}</td>
                          <td>
                            <button class="btn btn-primary edit-user-btn" data-npk="${user['NPK']}" data-name="${user['NAME']}" data-role="${user['RLS_ID']}"><i class="fas fa-pencil"></i></button>
                        </td>
                      </tr>
                      `;
                  });
                      $("tbody").html(html);
                      // Add event listener to the Edit Roles buttons
                        $('.edit-user-btn').on('click', function() {
                            // Populate the modal with user data
                            let npk = $(this).data('npk');
                            let name = $(this).data('name');
                            let role = $(this).data('role');
                            $('#userNPK').val(npk);
                            $('#userName').val(name);
                            $('#userRole').val(role);
                            
                            // Show the modal
                            $('#editUserModal').modal('show');
                        });

                        $('#UpdateBtn').click(function(){
                            let npk = $('#userNPK').val();
                            let role = $('#userRole').val();
                            //swal yes no
                            field = [role];
                            if (field.includes('')) {
                                swal.fire({
                                    title: "Warning",
                                    text: "Please fill all fields",
                                    icon: "warning",
                                    button: "OK",
                                });
                                return;
                            }
                            swal.fire({
                                title: "Are you sure?",
                                text: "You are about to update user role",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Yes",
                                cancelButtonText: "No",
                                dangerMode: true,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: 'includes/roles.inc.php?type=2',
                                        type: 'POST',
                                        data: {
                                            type: '4',
                                            npk: npk,
                                            role: role
                                        },
                                        success: function(data) {
                                            swal.fire({
                                                title: "Success",
                                                text: "Success update roles!",
                                                icon: "success",
                                                button: "OK",
                                            }).then((result)=>{
                                                if(result.isConfirmed){
                                                    location.reload();
                                                }
                                            });
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Request failed:', status, error);
                                        }
                                    });
                                }
                            });

                        });

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
  })
</script>

<?php
include_once __DIR__ . '/partials/_footer.php';
?>