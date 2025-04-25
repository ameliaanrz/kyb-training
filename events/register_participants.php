<?php
 session_start();
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS03')) {
  header("Location: ../login.php");
}
?>

<h1 class="fs-2 fw-semibold">Register Participants of <span id="training_name_title" class="fw-bold"></span></h1>
<p>
  Administrator could register participants by themselves or based on their organizations or sections here.
</p>
<hr>
<!-- breadcrumb -->
<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb" class="d-block">
  <ol class="breadcrumb my-0">
    <li class="breadcrumb-item"><a href="eventusr.php" class="text-decoration-none">Events</a></li>
    <li class="breadcrumb-item"><a id="register_url" href="events/register.php" class="text-decoration-none">Register participants</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add participants</li>
  </ol>
</nav>
<div class="card mt-4 py-3">
  <!-- title and subtitle -->
  <div class="card-header pb-0 border-0 bg-white">
    <h3 class="fw-semibold">Employees list of PT. Kayaba Indonesia</h3>
    <p>List of all employees of PT. Kayaba Indonesia which could be registered in Training Center program</p>
    <hr>
  </div>
  <!-- loading spinner -->
  <div id='main_spinner' class="spinner-border text-primary d-block mx-auto my-4" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
  <div id="main_content" class="card-body pt-2 d-none">
    <!-- query form -->
    <form id="query_form" action="" method="GET" class="d-none">
      <input type="hidden" name="evt_id" value="<?php echo $event['EVT_ID']; ?>">
      <div class="w-100 d-flex gap-4">
        <div class="w-50">
          <label for="company_select" class="form-label">Company</label>
          <select name="company" id="company_select" class="form-select" value="">
          </select>
        </div>
        <div class="w-50">
          <label for="dept_select" class="form-label">Department</label>
          <select name="department" id="dept_select" class="form-select" value="<?php echo $department; ?>" disabled>
            <option disabled <?php echo !$department ? "selected" : ""; ?> selected value> -- Select department -- </option>
            <option value="">All</option>
            <?php foreach ($departments as $dept) : ?>
              <option value="<?php echo $dept['DPT_ID']; ?>" <?php echo $department == $dept['DPT_ID'] ? "selected" : ""; ?>><?php echo $dept['DEPARTMENT']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="w-100 d-flex gap-4 mt-3">
        <div class="w-50">
          <label for="section_select" class="form-label">Section</label>
          <select name="section" id="section_select" class="form-select" value="<?php echo $section; ?>">
            <option disabled <?php echo !$section ? "selected" : ""; ?> value> -- Select section -- </option>
            <option value="">All</option>
            <?php foreach ($sections as $sec) : ?>
              <option value="<?php echo $sec['SEC_ID']; ?>" <?php echo $section == $sec['SEC_ID'] ? "selected" : ""; ?>><?php echo $sec['SECTION']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="w-50">
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
    <form id="select_list_form" action="" method="GET" class="mt-5">
      <label for="lists_shown" class="d-block form-label ms-auto text-end">Lists shown</label>
      <select name="lists_shown" id="lists_shown" class="form-select mt-2 w-auto d-block ms-auto">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </form>
    <form id="register_participants_form" action="" method="POST" class="mt-4">
      <!-- loading spinner -->
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
            <th scope="col" class="text-center">Status <i id="sort-icon-STATUS" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center"><input id="checkall" type="checkbox" name="checkall" class="d-block mx-auto" style="width: 20px; height: 20px"></th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <div class="d-flex justify-content-end mt-4">
        <div id="pagination_container"></div>
      </div>
        <button type="button" data-bs-toggle="modal" id="regist"  class="btn btn-success d-block ms-auto mt-4"><i class="fa-solid fa-plus"></i> Register selected users</button>
    </form>
  </div>
</div>

<!-- require paginationjs -->
<script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

<!-- custom jquery script -->
<script type="text/javascript" defer>
  $(document).ready(function() {
    checkRole();
    // variables
    let isEventListenerAttached = false;
    let lists_shown = 10;
    let q_company = '';
    let q_department = '';
    let q_section = '';
    let q_subsection = '';
    let q_grade = '';
    let q_gender = '';
    let search = '';
    let colomIndex='';
    let direction='';
    let registeredUsers = [];

    // get url params
    const queryString = new URLSearchParams(window.location.search);
    const evt_id = queryString.get('evt_id');
    checkEvtStatus();
    // set url
    $("#register_url").attr('href', `events/register.php?evt_id=${evt_id}`);

    // get training title
    getTrainingTitle();

    // get queries
    getRegisteredUsers();

    setTimeout(() => getQueries(), 1000);

    setTimeout(() => getUsers(), 1000);

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

    // listen to lists shown
    $("#lists_shown").on('change', function() {
      lists_shown = this.value;
      // show table loading spinner
      $("#table_spinner").removeClass('d-none').addClass('d-block');

      // hide table
      $("table").addClass('d-none');
      setTimeout(() => getUsers(), 1000);
    })

    // listen to search input
    $("#search_input").on('keyup', function() {
      if (/^[a-zA-Z0-9]+$/.test(this.value)) {
        search = this.value;
        // show table loading spinner
        $("#table_spinner").removeClass('d-none').addClass('d-block');

        // hide table
        $("table").addClass('d-none');
        setTimeout(() => getUsers(), 1000);
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
 $("#table_spinner").removeClass('d-none').addClass('d-block');

        // hide table
        $("table").addClass('d-none');
           // show table loading spinner
        

      setTimeout(function () {
        getUsers();
       
      }, 100);
      });


    function getRegisteredUsers() {
      $.get(`includes/events.inc.php?type=EACT18&evt_id=${evt_id}`).done(function(a, b, xhr) {
        if (xhr.status != 204) {
          registerUsers = [];
          const tmp = xhr.responseJSON;
          tmp.forEach(function(item) {
            registeredUsers = [...registeredUsers, item['NPK']];
          })
        }
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }

    $("#checkall").click(function() {
      $(".form-check-input").attr('checked', this.checked);
     })


// Event listener for "Check All" checkbox
$("#checkall").change(function() {
    let isChecked = $(this).is(':checked');
    $("input[type='checkbox']").each(function() {
        $(this).prop('checked', isChecked);
    });
});


  function getUsers() {
    if (q_department === '') {
      q_department = '<?php echo $_SESSION['DPT_ID']?>';
    }
    $.get(`includes/events.inc.php?type=EACT16&c_id=${q_company}&dpt_id=${q_department}&sec_id=${q_section}&sub_sec_id=${q_subsection}&grade=${q_grade}&gender=${q_gender}&search=${search}&evt_id=${evt_id}&colomIndex=${colomIndex}&direction=${direction}`)
      .done(function(a, b, xhr) {
        if (xhr.status === 204) {
          $("tbody").html(`<tr><td colspan="7">No users found</td></tr>`);
        } else {
          const users = xhr.responseJSON;

          if (xhr?.responseJSON) {
            $("#pagination_container").pagination({
              dataSource: users,
              pageSize: lists_shown,
              callback: function(data, pagination) {
                let html = '';
                let usersNpk = [];
                let count = 0;

                data.forEach(function(item) {
                  usersNpk = [...usersNpk, item['NPK']];
                  if (registeredUsers.includes(item['NPK'])) {
                    count++;
                  }
                  html += `
                    <tr>
                      <td>${item['NPK']}</td>
                      <td>${item['NAME']}</td>
                      <td>${item['COMPANY']}</td>
                      <td>${item['DEPARTMENT']}</td>
                      <td>${item['GENDER']}</td>
                      <td>${item['GRADE']}</td>
                      <td style="color: ${item['status'] == 1 ? 'green' : 'orange'};" >
                        ${item['status'] == 1 ? 'Registered' : 'Not registered'}
                      </td>
                      <td><input id="select_${item['NPK']}" type='checkbox' data-regist='${item['status']}' class="form-check-input d-block mx-auto" style="width: 20px; height: 20px" /></td>
                    </tr>
                  `;
                });
                $("tbody").html(html);

                // Attach event listener for individual checkboxes
                $("input[type='checkbox']").change(function() {
                  let allChecked = true;
                  $("input[type='checkbox']").each(function() {
                    if (!$(this).is(':checked')) {
                      allChecked = false;
                    }
                  });
                  $("#checkall").prop('checked', allChecked);
                });
              }
            });

            if (!isEventListenerAttached) {
              $('#regist').click(function() {
                const options = document.querySelectorAll('.form-check-input');
                const ids = [];
                const status = [];

                options.forEach(function(item) {
                  if (item.checked) {
                    const id = item.id.split('_').pop();
                    ids.push(id);
                    const stat = item.getAttribute('data-regist');
                    status.push(stat);
                  }
                });

                const alrRegist = ids.filter((_, index) => status[index] == 1);

                if (alrRegist.length > 0) {
                  Swal.fire({
                    icon: 'error',
                    title: 'ERROR',
                    text: 'Some users are already registered. Please check again.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                  });
                  return;
                } else {
                  registerUser(ids, 'true');
                }
              });
              isEventListenerAttached = true;
            }

            $("#main_spinner").removeClass('d-block').addClass('d-none');
            $("#main_content").removeClass('d-none');
          }
        }

        setTimeout(() => {
          $("table").removeClass('d-none');
          $("#table_spinner").removeClass('d-block').addClass('d-none');
          $("#loading_spinner").removeClass("d-block").addClass('d-none');
        }, 500);
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      });
  }

    var dpt ='<?php echo $_SESSION['DPT_ID']?>';
    function registerUser(npk, check_type) {
      $.post(`includes/events.inc.php?type=EACT19&evt_id=${evt_id}&check_type=${check_type}`,{
        users:npk
      }).done(function(a, b, xhr) {
        $.post(`includes/notifications.inc.php?type=4`,{
          evt_id:evt_id,
          dpt_id:dpt
        })
        //swal success
        swal.fire({
          title: "Success",
          text: "User registered successfully",
          icon: "success",
          button: "OK",
        }).then(
          function() {
            location.reload();
          }
        );
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

        // set query options
        $("#company_select").html(`<option disabled value selected> -- Select company -- </option>`);
        $("#company_select").append(`<option value="">All</option>`);
        companies.forEach(function(item) {
          $("#company_select").append(`<option value="${item['C_ID']}">${item['COMPANY']}</option>`);
        });

        $("#dept_select").html(`<option disabled value selected> -- Select department -- </option>`);
        $("#dept_select").append(`<option value="">All</option>`);
        departments.forEach(function(item) {
          var selected = q_department == item['DPT_ID'] ? 'selected' : '';
          $("#dept_select").append(`<option value="${item['DPT_ID']}" ${selected}>${item['DEPARTMENT']}</option>`);
        });

        $("#section_select").html(`<option disabled value selected> -- Select section -- </option>`);
        $("#section_select").append(`<option value="">All</option>`);
        sections.forEach(function(item) {
          $("#section_select").append(`<option value="${item['SEC_ID']}">${item['SECTION']}</option>`);
        });

        $("#subsection_select").html(`<option disabled value selected> -- Select subsection -- </option>`);
        $("#subsection_select").append(`<option value="">All</option>`);
        subsections.forEach(function(item) {
          $("#subsection_select").append(`<option value="${item['SUB_SEC_ID']}">${item['SUBSECTION']}</option>`);
        });

        $("#grade_select").html(`<option disabled value selected> -- Select grade -- </option>`);
        $("#grade_select").append(`<option value="">All</option>`);
        grades.forEach(function(item) {
          $("#grade_select").append(`<option value="${item['GRADE']}">${item['GRADE']}</option>`);
        });

        $("#gender_select").html(`<option disabled value selected> -- Select gender -- </option>`);
        $("#gender_select").append(`<option value="">All</option>`);
        genders.forEach(function(item) {
          $("#gender_select").append(`<option value="${item['GENDER']}">${item['GENDER']}</option>`);
        });

        // listen to input and select options changes
        $("#company_select").on('change', function() {
          q_company = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getUsers(), 1000);
        })

        $("#dept_select").on('change', function() {
          q_department = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getUsers(), 1000);
        })

        $("#section_select").on('change', function() {
          q_section = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getUsers(), 1000);
        })

        $("#subsection_select").on('change', function() {
          q_subsection = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getUsers(), 1000);
        })

        $("#grade_select").on('change', function() {
          q_grade = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getUsers(), 1000);
        })

        $("#gender_select").on('change', function() {
          q_gender = this.value;
          // show table loading spinner
          $("#table_spinner").removeClass('d-none').addClass('d-block');

          // hide table
          $("table").addClass('d-none');
          setTimeout(() => getUsers(), 1000);
        })

        // show query options
        $("#query_form").removeClass('d-none');
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }

    function getTrainingTitle() {
      $.get(`includes/events.inc.php?type=EACT14&evt_id=${evt_id}`).done(function(a, b, xhr) {
        if (xhr.status == 204) {
          console.log("No training found");
        } else {
          const training = xhr.responseJSON;

          $("#training_name_title").html(`<strong>${training['TRAINING']}</strong>`);
        }
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }
  });
</script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>