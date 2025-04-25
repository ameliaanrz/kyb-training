<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS03')) {
  header("Location: ../login.php");
}
?>

<h1 class="fs-2 fw-semibold">Register / Unregister Participants of <span class="fw-bold" id="training_name_title"></span></h1>
<p>
  Administrator could register or unregister participants based on their organizations or sections or per participants here.
</p>
<hr>
<div class="d-flex justify-content-between align-items-center">
  <!-- breadcrumb -->
  <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb" class="d-block">
    <ol class="breadcrumb my-0">
      <li class="breadcrumb-item"><a href="eventusr.php" class="text-decoration-none">Events</a></li>
      <li class="breadcrumb-item active" aria-current="page">Register participants</li>
    </ol>
  </nav>
  <a id="register_url" href="events/register_participants.php?evt_id=" class="btn btn-outline-success"><i class="fa-solid fa-plus"></i> Register participants</a>
</div>
<div class="card mt-4 py-3">
  <div class="card-header pb-0 border-0 bg-white">
    <h3 class="fw-semibold">Registered Participants List of <span class="fw-bold" id="secondary_name_title"></span> Training Event</h3>
    <p>List of all participants registered (waiting for approval or approved)<span class="fw-semibold">
    <hr>
  </div>
  <div class="card-body pt-2">
    <!-- loading spinner -->
    <div id='main_spinner' class="spinner-border text-primary d-block mx-auto my-4" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <!-- query content -->
    <form id="query_form" action="" method="GET" class="d-none">
      <input type="hidden" name="evt_id" value="<?php echo $event['EVT_ID']; ?>">
      <div class="w-100 d-flex justify-content-between gap-4">
        <div style="width: 33%">
          <label for="dept_select" class="form-label">Department</label>
          <select name="department" id="dept_select" class="form-select" value="<?php echo $department; ?>" disabled>
            <option disabled <?php echo !$department ? "selected" : ""; ?> selected value> -- Select department -- </option>
            <option value="">All</option>
            <?php foreach ($departments as $dept) : ?>
              <option value="<?php echo $dept['DPT_ID']; ?>" <?php echo $department == $dept['DPT_ID'] ? "selected" : ""; ?>><?php echo $dept['DEPARTMENT']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="width: 33%">
          <label for="section_select" class="form-label">Section</label>
          <select name="section" id="section_select" class="form-select" value="<?php echo $section; ?>">
            <option disabled <?php echo !$section ? "selected" : ""; ?> value> -- Select section -- </option>
            <option value="">All</option>
            <?php foreach ($sections as $sec) : ?>
              <option value="<?php echo $sec['SEC_ID']; ?>" <?php echo $section == $sec['SEC_ID'] ? "selected" : ""; ?>><?php echo $sec['SECTION']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="width: 33%">
          <label for="subsection_select" class="form-label">Subsection</label>
          <select name="subsection" id="subsection_select" class="form-select" value="<?php echo $subsection; ?>">
            <option disabled <?php echo !$subsection ? "selected" : ""; ?> value> -- Select subsection -- </option>
            <option value="">All</option>
            <?php foreach ($subsections as $sub) : ?>
              <option value="<?php echo $sub['SUB_SEC_ID']; ?>" <?php echo $subsection == $sub['SUB_SEC_ID'] ? "selected" : ""; ?>><?php echo $sub['SUBSECTION']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="w-100 d-flex gap-4 mt-3">
        <div class="w-50">
          <label for="approved_select" class="form-label">Approval Status</label>
          <select name="approval" id="approved_select" class="form-select" value="">
            <option selected disabled value="">-- Select approval --</option>
            <option value="">All</option>
            <option value="3">Not approved</option>
            <option value="2">Approved</option>
            <option value="1">Waiting for approval</option>
          </select>
        </div>
        <div class="w-50">
          <label for="completed_select" class="form-label">Completion Status</label>
          <select name="completion" id="completed_select" class="form-select" value="">
            <option selected disabled value="">-- Select completion --</option>
            <option value="">All</option>
            <option value="2">Completed</option>
            <option value="1">Not completed</option>
          </select>
        </div>
      </div>
      <div class="w-100 d-flex gap-4 mt-3">
        <div class="w-50">
          <label for="grade_select" class="form-label">Grade</label>
          <select name="grade" id="grade_select" class="form-select" value="<?php echo $grade; ?>">
            <option disabled <?php echo !$grade ? "selected" : ""; ?> value> -- Select grade -- </option>
            <option value="">All</option>
            <?php foreach ($grades as $grd) : ?>
              <option value="<?php echo $grd['GRADE']; ?>" <?php echo $grade === $grd['GRADE'] ? "selected" : ""; ?>><?php echo $grd['GRADE']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="w-50">
          <label for="gender_select" class="form-label">Gender</label>
          <select name="gender" id="gender_select" class="form-select" value="<?php echo $gender; ?>">
            <option disabled <?php echo !$gender ? "selected" : ""; ?> value> -- Select gender -- </option>
            <option value="">All</option>
            <?php foreach ($genders as $gdr) : ?>
              <option value="<?php echo $gdr['GENDER']; ?>" <?php echo $gender == $gdr['GENDER'] ? "selected" : ""; ?>><?php echo $gdr['GENDER']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="w-100 mt-3">
        <label for="search_input" class="form-label">Search by username or NPK</label>
        <input id="search_input" type="text" name="search" autocomplete="off" placeholder="Enter username or NPK here" class="form-control">
      </div>
    </form>
    <!-- main content -->
    <div id='main_content' class="d-none">  
      <form id="select_list_form" action="" method="GET" class="mt-5">
        <label for="lists_shown" class="d-block form-label ms-auto text-end">Lists shown</label>
        <select name="lists_shown" id="lists_shown" class="form-select mt-2 w-auto d-block ms-auto">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
        </select>
      </form>
      <form action="" method="POST">
        <!-- table loading spinner -->
        <div id='table_spinner' class="spinner-border text-primary d-none mx-auto my-4" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <table class="table mt-3 rounded-2 table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th scope="col" class="text-center">NPK <i id="sort-icon-NPK" class="fas fa-sort"></i></th>
              <th scope="col" class="text-center">Name <i id="sort-icon-NAME" class="fas fa-sort"></i></th>
              <th scope="col" class="text-center">Company <i id="sort-icon-COMPANY" class="fas fa-sort"></i></th>
              <th scope="col" class="text-center">Department <i id="sort-icon-DEPARTMENT" class="fas fa-sort"></i></th>
              <th scope="col" class="text-center">Grade <i id="sort-icon-GRADE" class="fas fa-sort"></i></th>
              <th scope="col" class="text-center">Gender <i id="sort-icon-GENDER" class="fas fa-sort"></i></th>
              <th scope="col" class="text-center">Approved <i id="sort-icon-APPROVED" class="fas fa-sort"></th>
              <th scope="col" class="text-center">Approved Dept <i id="sort-icon-APPROVED_DEPT" class="fas fa-sort"></th>
              <th scope="col" class="text-center">Completed <i id="sort-icon-COMPLETED" class="fas fa-sort"></th>
              <th><input id="checkall" type='checkbox' class="d-block mx-auto" style="width: 20px; height: 20px" /></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <div class="d-flex justify-content-end mt-4">
          <div id="pagination_container"></div>
        </div>
        <button type="button" data-bs-toggle="modal" id="unregist" data-bs-target="#deregisterModal" class="btn btn-danger d-block ms-auto mt-4"><i class="fa-solid fa-minus"></i> Unregister selected users</button>
      </form>
    </div>
  </div>
</div>
<!-- unregister approval -->
<div class="modal fade" id="deregisterModal" tabindex="-1" aria-labelledby="deregisterModalTitle" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deregisterModalTitle">Are you sure to deregister selected users?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="deregisterModalBody" class="modal-body">
        <p>The action performed is <strong>irreversible</strong>, all the progress of the current users will be deleted.</p>
      </div>
      <div class="modal-footer">
        <button id="deregisterModalCloseBtn" type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
        <button id="deregisterModalActionBtn" type="button" class="btn btn-danger">Approve deregistration</button>
      </div>
    </div>
  </div>
</div>

<!-- require paginationjs -->
<script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

<!-- custom script -->
<script type="text/javascript" defer>
  $(document).ready(function() {
    checkRole();

    // variables
    let lists_shown = 10;
    let q_company = '';
    let q_department = '<?php echo $_SESSION['DPT_ID']?>';
    let q_section = '';
    let q_subsection = '';
    let q_grade = '';
    let q_gender = '';
    let q_approved = '';
    let q_completed = '';
    let search = '';
    let colomIndex='';
    let direction='';
    let registeredUsers = [];

    // get url params
    const queryString = new URLSearchParams(window.location.search);
    const evt_id = queryString.get('evt_id');
    checkEvtStatus();
    // set links
    $("#register_url").attr('href', `events/register_participants.php?evt_id=${evt_id}`);

    // get training title
    getTrainingTitle();

    // get query values
    setTimeout(() => getQueries(), 1000);

    setTimeout(() => {
      getRegisteredUsers();
    }, 1000);

    // listen to lists_shown change
    $("#lists_shown").change(function() {
      lists_shown = this.value;

      // show table loading spinner
      $("#table_spinner").removeClass('d-none').addClass('d-block');

      // hide table
      $("table").addClass('d-none');

      setTimeout(function() {
        getRegisteredUsers();
      }, 500);
    })

    // listen to search input
    $("#search_input").on('keyup', function(e) {
      if (/^[a-zA-Z0-9]+$/.test(this.value) || e.keyCode == 8 || e.keyCode == 46) {
        search = this.value;
        // show table loading spinner
        $("#table_spinner").removeClass('d-none').addClass('d-block');

        // hide table
        $("table").addClass('d-none');
        setTimeout(() => getRegisteredUsers(), 1000);
      }
    })

    function checkRole() {
      $.get('includes/events.inc.php?type=EACT20').done(function(a, b, xhr) {
        role = xhr.responseJSON;
        if (role != 'RLS03') {
          window.location.href = 'events.php';
        }
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }

    function checkEvtStatus(){
      $.get(`includes/events.inc.php?type=EACT24&evt_id=${evt_id}`).done(function(a, b, xhr) {
        const status = xhr.responseJSON['EVENT_STATUS'];
        const activated = xhr.responseJSON['ACTIVATED'];
        if (status == 'Upcoming' && activated == 0) {
          window.location.href = 'eventusr.php';
        }else if(status == 'Complete'){
          window.location.href = 'eventusr.php';
        }else if (status == 'Running'){
          window.location.href = 'eventusr.php';
        }
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }

    function getQueries() {
      $.get(`includes/events.inc.php?type=EACT15&evt_id=${evt_id}`).done(function(a, b, xhr) {
        const companies = xhr.responseJSON['companies'];
        const departments = xhr.responseJSON['departments'];
        const sections = xhr.responseJSON['sections'];
        const subsections = xhr.responseJSON['subsections'];
        const grades = xhr.responseJSON['grades'];
        const genders = xhr.responseJSON['genders'];

        $("#dept_select").html(`<option disabled value selected> -- Select department -- </option>`);
        $("#dept_select").append(`<option value="">All</option>`);
        departments.forEach(function(item) {
          $("#dept_select").append(`<option value="${item['DPT_ID']}" ${q_department == item['DPT_ID']? "selected": ""}>${item['DEPARTMENT']}</option>`);
        });

        $("#section_select").html(`<option disabled value selected> -- Select section -- </option>`);
        $("#section_select").append(`<option value="">All</option>`);
        sections.forEach(function(item) {
          $("#section_select").append(`<option value="${item['SEC_ID']}" ${q_section == item['SEC_ID']? "selected": ""}>${item['SECTION']}</option>`);
        });

        $("#subsection_select").html(`<option disabled value selected> -- Select subsection -- </option>`);
        $("#subsection_select").append(`<option value="">All</option>`);
        subsections.forEach(function(item) {
          $("#subsection_select").append(`<option value="${item['SUB_SEC_ID']}" ${q_subsection == item['SUB_SEC_ID']? "selected": ""}>${item['SUBSECTION']}</option>`);
        });

        $("#grade_select").html(`<option disabled value selected> -- Select grade -- </option>`);
        $("#grade_select").append(`<option value="">All</option>`);
        grades.forEach(function(item) {
          $("#grade_select").append(`<option value="${item['GRADE']}" ${q_grade == item['GRADE']? "selected": ""}>${item['GRADE']}</option>`);
        });

        $("#gender_select").html(`<option disabled value selected> -- Select gender -- </option>`);
        $("#gender_select").append(`<option value="">All</option>`);
        genders.forEach(function(item) {
          $("#gender_select").append(`<option value="${item['GENDER']}" ${q_gender == item['GENDER']? "selected": ""}>${item['GENDER']}</option>`);
        });

        // listen to input and select options changes
        $("#company_select").on('change', function() {
          q_company = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getRegisteredUsers(), 1000);
        })

        $("#dept_select").on('change', function() {
          q_department = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getRegisteredUsers(), 1000);
        })

        $("#section_select").on('change', function() {
          q_section = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getRegisteredUsers(), 1000);
        })

        $("#subsection_select").on('change', function() {
          q_subsection = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getRegisteredUsers(), 1000);
        })

        $("#approved_select").change(function() {
          q_approved = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getRegisteredUsers(), 1000);
        })

        $("#completed_select").change(function() {
          q_completed = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getRegisteredUsers(), 1000);
        })

        $("#grade_select").on('change', function() {
          q_grade = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getRegisteredUsers(), 1000);
        })

        $("#gender_select").on('change', function() {
          q_gender = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getRegisteredUsers(), 1000);
        })

        // show query options
        $("#query_form").removeClass('d-none');
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

            // remove participants list container
      // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');

      setTimeout(function () {
        getRegisteredUsers();
      }, 100);
      });


    function getRegisteredUsers() {
      $.get(`includes/events.inc.php?type=EACT18&evt_id=${evt_id}&c_id=${q_company}&dpt_id=${q_department}&sec_id=${q_section}&sub_sec_id=${q_subsection}&grade=${q_grade}&gender=${q_gender}&approved=${q_approved}&completed=${q_completed}&search=${search}&colomIndex=${colomIndex}&direction=${direction}`).done(function(a, b, xhr) {
          if (xhr.status != 204) {
              const users = xhr.responseJSON;
              if (xhr?.responseJSON) {
                  $("#pagination_container").pagination({
                      dataSource: users,
                      pageSize: lists_shown,
                      callback: function(data, pavination) {
                          let html = '';

                          data.forEach(function(item) {
                              html += `
                                  <tr>
                                      <td>${item['NPK']}</td>
                                      <td>${item['NAME']}</td>
                                      <td>${item['COMPANY']}</td>
                                      <td>${item['DEPARTMENT']}</td>
                                      <td>${item['GRADE']}</td>
                                      <td>${item['GENDER']}</td>
                                      <td>${item['APPROVED'] == 0 && item['APPROVED_DEPT'] == 2 ? "<span class='text-danger'>KADept Not Approve this</span>" : item['APPROVED'] == 1 ? "<span class='text-success'>Approved</span>" :  item['APPROVED'] == 0 ? "<span class='text-warning'>Waiting for approval</span>": "<span class='text-danger'>Disapproved</span>"}</td>
                                      <td>${item['APPROVED_DEPT'] == 0 ? "<span class='text-warning'>Waiting Approval</span>" : item['APPROVED_DEPT'] == 1 ? "<span class='text-success'>Approved</span>" : "<span class='text-danger'>Not Approved</span>"}</td>
                                      <td>${item['COMPLETED'] ? "<span class='text-danger'>Not Completed</span>" : "<span class='text-success'>Completed</span>"}</td>
                                      <td><input id="select_${item['NPK']}" type='checkbox' class="form-check-input d-block mx-auto" style="width: 20px; height: 20px" /></td>
                                  </tr>
                              `;
                          });
                          $("tbody").html(html);

                          // deregister participants
                         $("#deregisterModalActionBtn").off('click').on('click', function() {
                            let deregisUsers = [];
                            let approvedUsers = [];

                            // get all checked users
                            $(".form-check-input").each(function() {
                              if (this.checked) {
                                // get id
                                const tmpId = this.id.split('_').pop();

                                // find user data by id
                                const user = users.find(user => user['NPK'] == tmpId);

                                // check if the user is approved
                                if (user && user['APPROVED'] == 1 ||user && user['APPROVED_DEPT'] == 1 ) {
                                  approvedUsers.push(tmpId);
                                } else {
                                  // add id to arr
                                  deregisUsers.push(tmpId);
                                }
                              }
                            });

                            if (approvedUsers.length > 0) {
                              swal("Cannot Deregister", "Some users are already approved and cannot be deregistered.", "error");
                            } else if (deregisUsers.length > 0) {
                              // deregister users
                              $.post(`includes/events.inc.php?type=EACT19&evt_id=${evt_id}&check_type=false`, {
                                users: deregisUsers
                              }).done(function(a, b, xhr) {
                                // reload page
                                location.reload();
                              }).fail(function(xhr, a, b) {
                                console.log(xhr.status);
                              });
                            }
                          });


                          // checkall listener
                          $("#checkall").on('change', function() {
                              if (this.checked) {
                                  $(".form-check-input").attr('checked', true);
                              } else {
                                  $(".form-check-input").removeAttr('checked');
                              }
                          });
                      }
                  });

                  // hide loading spinner
                  $("#main_spinner").removeClass('d-block').addClass('d-none');

                  // hide table loading spinner
                  $("#table_spinner").removeClass('d-block').addClass('d-none');

                  // show table
                  $("table").removeClass('d-none');

                  // show main content
                  $("#main_content").removeClass('d-none');
              }
          } else {
              // hide loading spinner
              $("#main_spinner").removeClass('d-block').addClass('d-none');
              // remove loading spinner
              $("#table_spinner").removeClass('d-block').addClass('d-none');

              // show table
              $("table").removeClass('d-none');
              // display users not found
              $("tbody").html(`<tr><td colspan="9">No users found</td></tr>`);
              // show main content
              $("#main_content").removeClass('d-none');
              $("#unregist").attr('disabled', true);
          }
      }).fail(function(xhr, a, b) {
          console.log(xhr.status);
      });
  }

function swal(title,msg,type){
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
    function getTrainingTitle() {
      $.get(`includes/events.inc.php?type=EACT14&evt_id=${evt_id}`).done(function(a, b, xhr) {
        if (xhr.status == 204) {
          console.log("No training found");
        } else {
          const training = xhr.responseJSON;

          $("#training_name_title").html(`<strong>${training['TRAINING']}</strong>`);
          $("#secondary_name_title").html(`${training['TRAINING']}`);
        }
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }
  })
</script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>