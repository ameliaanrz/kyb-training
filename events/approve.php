<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID'])) {
  header("Location: ../login.php");
}
?>

<h1 class="fs-2 fw-semibold">Approve / Disapprove Participants of <span id="main_title_id" class="fw-bold"></span> Training Event</h1>
<p>
  Administrator could approve or disapprove participants based on their organizations or sections or per participants.
</p>
<hr>
<div class="d-flex justify-content-between align-items-center">
  <!-- breadcrumb -->
  <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb" class="d-block">
    <ol class="breadcrumb py-0 my-0">
      <li class="breadcrumb-item"><a href="events.php" class="text-decoration-none">Events</a></li>
      <li class="breadcrumb-item active" aria-current="page">Approve participants</li>
    </ol>
  </nav>
  <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
    <div class="d-flex">
      <a id="printReport" class="btn btn-primary me-2"><i class="fas fa-file"></i> Print attendance form</a>
      <a id="sendReminder" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Reminder</a>
    </div>

  <?php endif; ?>
</div>
<!-- main -->
<div class="card mt-4 py-3">
  <div class="card-header pb-0 border-0 bg-white">
    <h3 class="fw-semibold">Participants Approval List of <span id="secondary_id_title" class="fw-bold"></span> Training Event</h3>
    <p>List of all participants registered (Waiting for approval, approved, and not approved)</p>
    <hr>
  </div>
  <!-- loading spinner -->
  <div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-5" role="status">
    <span class="sr-only">Loading...</span>
  </div>
  <!-- main content container -->
  <div id="main_content_container" class="card-body pt-2 d-none">
    <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
      <div id="query_container" class="d-none">
        <div class="w-100 d-flex gap-4">
          <div class="w-50">
            <label for="department_select" class="form-label">Department</label>
            <select name="department" id="department_select" class="form-select query-form">
            </select>
          </div>
          <div class="w-50">
            <label for="approval_select" class="form-label">Approval status</label>
            <select name="approval" id="approval_select" class="form-select query-form">
            </select>
          </div>
        </div>
        <div class="w-100 d-flex gap-4 mt-4">
          <div class="w-50">
            <label for="grade_select" class="form-label">Grade</label>
            <select name="grade" id="grade_select" class="form-select query-form">
            </select>
          </div>
          <div class="w-50">
            <label for="gender_select" class="form-label">Gender</label>
            <select name="gender" id="gender_select" class="form-select query-form">
            </select>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS02') : ?>
      <div class="w-50" style="display:none;">
        <label for="department_select_kadept" class="form-label">Department</label>
        <select name="department_dept" id="department_select_kadept" class="form-select query-form">
        </select>
      </div>
    <?php endif; ?>

    <input id="search_input" type="text" name="search" placeholder="Search by name or NPK" class="form-control mt-5">
    <div id="select_list_container" class="mt-5">
      <label for="lists_shown_select" class="d-block form-label ms-auto text-end">Lists shown</label>
      <select name="lists_shown" id="lists_shown_select" class="form-select mt-2 w-auto d-block ms-auto">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </div>
    <!-- loading spinner -->
    <div id="loading_spinner_secondary" class="spinner-border text-primary d-none mx-auto my-5" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div id="table_container">
      <small><span class="fw-bold text-danger">*WARNING</span>: The check all checkbox button could only check the list one page at a time</small>
      <table class="table mt-3 rounded-2 table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th scope="col" class="text-center">NPK <i id="sort-icon-NPK" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Name <i id="sort-icon-NAME" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Department <i id="sort-icon-DEPARTMENT" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Grade <i id="sort-icon-GRADE" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Gender <i id="sort-icon-GENDER" class="fas fa-sort"></i></th>
            <th scope="col" class="text-center">Approval Status <i id="sort-icon-APPROVED" class="fas fa-sort"></th>
            <th scope="col" class="text-center">Approval KaDept Status <i id="sort-icon-APPROVED_DEPT" class="fas fa-sort"></th>
            <th scope="col" class="text-center"><input id="checkall" type="checkbox" name="checkall" class="form-check-input d-block mx-auto" style="width: 20px; height: 20px"></th>
          </tr>
        </thead>
        <tbody id="table_body">
          <div id="loading_spinner_table" class="spinner-border text-primary d-none mx-auto my-5" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </tbody>
      </table>
      <div class="d-flex gap-3 mt-4 justify-content-end">
        <button id="approveBtn" type="button" class="btn approve-btn btn-success py-1 px-3"><i class="fas fa-check"></i> Approve</button>
        <button id="disapproveBtn" type="button" class="btn disapprove-btn btn-danger py-1 px-3"><i class="fas fa-times"></i> Disapprove</button>
      </div>
    </div>

    <div class="d-flex justify-content-end mt-5">
      <div id="pagination_container"></div>
    </div>
  </div>
</div>

<!-- require paginationjs -->
<script type="text/javascript" src="node_modules/paginationjs/dist/pagination.min.js"></script>

<script type="text/javascript" defer>
  $(document).ready(function() {

    // check role first
    checkRole();
    // variables
    const queryString = new URLSearchParams(window.location.search);
    const evt_id = queryString.get('evt_id');
    console.log("Event ID from URL: ", evt_id); // Debugging

    let lists_shown = 10;
    let approval = '';
    let sec_id = '';
    let sub_sec_id = '';
    let grade = '';
    let gender = '';
    let search = '';
    let colomIndex = '';
    let direction = '';
    let dpt_id = '';
    // set main title
    getTrainingTitle();

    // get query data
    getQueryData();

    // get events
    getParticipants();




    // query form handler
    $(".query-form").change(function() {
      // show secondary loading
      $("#loading_spinner_secondary").removeClass('d-none').addClass('d-block');

      // hide table body
      $("#table_body").addClass('d-none');

      setTimeout(queryParticipants, 500);
    })

    $("#search_input").keyup(function() {
      search = $(this).val();
      // show secondary loading
      $("#loading_spinner_secondary").removeClass('d-none').addClass('d-block');

      // hide table body
      $("#table_body").addClass('d-none');

      setTimeout(queryParticipants, 1000);
    })

    function checkRole() {
      $.get('includes/events.inc.php?type=EACT20').done(function(a, b, xhr) {
        role = xhr.responseJSON;
        if (role === 'RLS03') {
          window.location.href = 'eventusr.php';
        }
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }


    function queryParticipants() {
      // get participants
      getParticipants();
      // hide secondary loading
      $("#loading_spinner_secondary").removeClass('d-block').addClass('d-none');

      // show table body
      $("#table_body").removeClass('d-none');
    }

    function getTrainingTitle() {
      $.get(`includes/events.inc.php?type=EACT14&evt_id=${evt_id}`).done(function(a, b, xhr) {
        if (xhr.status == 204) {
          console.log("No training found");
        } else {
          const training = xhr.responseJSON;

          $("#main_title_id").html(`<strong>${training['TRAINING']}</strong>`);
          $("#secondary_id_title").html(`${training['TRAINING']}`);

        }
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    }
    // lists shown select handler
    $("#lists_shown_select").change(function() {
      lists_shown = this.value;

      // hide table container
      $("#loading_spinner_secondary").removeClass('d-none').addClass('d-block');
      $("#table_container").addClass('d-none');

      getParticipants();

      // show table container
      setTimeout(() => {
        $("#loading_spinner_secondary").removeClass('d-block').addClass('d-none');
        $("#table_container").removeClass('d-none');
      }, 500)
    })



    //select approval
    $("#approval_select").change(function() {
      // show secondary loading
      approval = this.value;

      $("#loading_spinner_secondary").removeClass('d-none').addClass('d-block');

      // hide table body
      $("#table_body").addClass('d-none');

      setTimeout(queryParticipants, 500);
    })

    //select grade
    $("#grade_select").change(function() {
      // show secondary loading
      grade = this.value;

      $("#loading_spinner_secondary").removeClass('d-none').addClass('d-block');

      // hide table body
      $("#table_body").addClass('d-none');

      setTimeout(queryParticipants, 500);
    })

    //select gender
    $("#gender_select").change(function() {
      // show secondary
      gender = this.value;
      $("#loading_spinner_secondary").removeClass('d-none').addClass('d-block');

      // hide table body
      $("#table_body").addClass('d-none');

      setTimeout(queryParticipants, 500);
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

      getParticipants();

      setTimeout(function() {
        // remove secondary loading spinner
        $("#loading_spinner").removeClass("d-none").addClass('d-block');

        // show main content container
        $("#main_content_container").addClass('d-none');
      }, 100);
    });

    // get events function
    function getParticipants() {
      // set query values
      <?php if ($_SESSION['RLS_ID'] === 'RLS01') { ?>
        dpt_id = $("#department_select").val();
      <?php } else { ?>
        dpt_id = '<?php echo $_SESSION['DPT_ID'] ?>';
      <?php } ?>

      $('#sendReminder').click(function(event) {
        event.preventDefault(); // Mencegah halaman reload

        // Ambil hanya checkbox yang diceklis
        const options = document.querySelectorAll('.form-check-input:checked');
        const ids = [];

        options.forEach(function(item) {
          const id = item.id.split('_').pop();
          ids.push(id);
        });

        // Jika tidak ada yang dipilih, tampilkan alert
        /*if (ids.length === 0) {
          Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Pilih setidaknya satu peserta untuk dikirim reminder.',
          });
          return;
        }*/

        // Kirim data ke server
        $.post(`includes/notifications.inc.php?type=13&evt_id=${evt_id}`, {
            ids: JSON.stringify(ids)
          },
          function(response) {
            try {
              let data = JSON.parse(response);
              if (data.message) {
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: 'Reminders successfully sent.',
                });
              } else if (data.errors) {
                Swal.fire({
                  icon: 'error',
                  title: 'Gagal!',
                  text: "Gagal menyimpan reminder:\n" + data.errors.join("\n"),
                });
              }
            } catch (e) {
              Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: response,
              });
            }
          }
        ).fail(function(xhr) {
          Swal.fire({
            icon: 'error',
            title: 'Gagal Mengirim!',
            text: 'No matching phone numbers found.',
          });
        });
      });


      $.get(`includes/events.inc.php?type=EACT12&evt_id=${evt_id}&dpt_id=${dpt_id}&sec_id=${sec_id}&sub_sec_id=${sub_sec_id}&approval=${approval}&gender=${gender}&grade=${grade}&search=${search}&colomIndex=${colomIndex}&direction=${direction}`).done(function(a, b, xhr) {
        if (xhr.status === 204) {
          $('#printReport').click(function() {
            Swal.fire({
              icon: 'error',
              title: 'ERROR',
              text: 'No participants found',
              confirmButtonColor: '#d33',
              confirmButtonText: 'OK'
            });
          });
          $("#table_body").html(`
            <tr>
              <td colspan='8'>No participants found</td>
            </tr>
          `);


          setTimeout(() => {
            // remove secondary loading spinner
            $("#loading_spinner_secondary").removeClass("d-block").addClass('d-none');

            // show main content container
            $("#main_content_container").removeClass('d-none');
          }, 500);

          return;
        }

        if (xhr.responseJSON) {
          const participants = xhr.responseJSON;
          $("#pagination_container").pagination({
            dataSource: participants,
            pageSize: lists_shown,
            callback: function(data, pagination) {
              // template method of yourself
              let html = '';
              let allNotApproved = true;
              data.forEach(function(participant) {
                if (participant['APPROVED'] != 0 || participant['APPROVED_DEPT'] != 0) {
                  allNotApproved = false; // If any item is approved, set the flag to false
                }
                html += `
                <tr>
                  <td scope="row">${participant['NPK']}</td>
                  <td>${participant['NAME'] ? `${participant['NAME']}` : '-'}</td>
                  <td>${participant['GENDER'] ? `${participant['GENDER']}` : '-'}</td>
                  <td>${participant['GRADE'] ? `${participant['GRADE']}` : '-'}</td>
                  <td>${participant['DEPARTMENT'] ? `${participant['DEPARTMENT']}` : '-'}</td>
                  <td>${participant['APPROVED'] == 0 && participant['APPROVED_DEPT'] == 2 ? "<span class='text-danger'>KADept Not Approve this</span>" : participant['APPROVED'] == 1 ? "<span class='text-success'>Approved</span>" :  participant['APPROVED'] == 0 ? "<span class='text-warning'>Waiting for approval</span>": "<span class='text-danger'>Disapproved</span>"}</td>
                  <td>${participant['APPROVED_DEPT'] == 0 ? "<span class='text-warning'>Waiting for approval</span>" : participant['APPROVED_DEPT'] == 1 ? "<span class='text-success'>Approved</span>" : "<span class='text-danger'>Disapproved</span>"}</td>
                  <td>
                    <input id="select_${participant['EP_ID']}" data-approved='${participant['APPROVED']}' data-appdept='${participant['APPROVED_DEPT']}' type='checkbox' class='form-check-input p-2' /> 
                  </td>
                </tr>
              `;
              })

              // show main content container
              $("tbody").html(html);

              // Unbind previous event listeners before binding new ones
              $("#checkall").off('click');
              $('#printReport').off('click');
              $("#approveBtn").off('click');
              $("#disapproveBtn").off('click');

              // checkall listener
              $("#checkall").click(function() {
                $(".form-check-input").attr('checked', this.checked);
              });

              $('#printReport').click(function() {
                if (allNotApproved) {
                  Swal.fire({
                    icon: 'error',
                    title: 'ERROR',
                    text: 'Cannot print attendance because no one has been approved',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                  }).then((result) => {
                    location.reload();
                  });
                } else {
                  $(this).attr('href', `includes/reports.inc.php?type=RACT04&for=print&evt_id=${evt_id || ''}&dpt_id=${dpt_id || ''}&sec_id=${sec_id || ''}&sub_sec_id=${sub_sec_id || ''}&approval=1&gender=${gender || ''}&grade=${grade || ''}&search=${search || ''}`);
                }
              });



              // add select listener
              $("#approveBtn").click(function() {
                const options = document.querySelectorAll('.form-check-input');
                const ids = [];
                const statusapproveddept = []; // Assuming these values come from somewhere
                const statusapproved = []; // Assuming these values come from somewhere
                let type = '';

                options.forEach(function(item) {
                  if (item.checked) {
                    const id = item.id.split('_').pop();
                    ids.push(id);

                    // Assuming you have a way to get statusapproveddept and statusapproved for each id
                    const deptStatus = item.getAttribute('data-appdept'); // Example
                    const approvalStatus = item.getAttribute('data-approved'); // Example

                    statusapproveddept.push(deptStatus);
                    statusapproved.push(approvalStatus);
                  }
                });

                if (ids.length === 0) {
                  Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Select at least one participant to approve.',
                  });
                  return;
                }

                <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
                  const notApprovedDept = ids.filter((_, index) => statusapproveddept[index] == 2 || statusapproveddept[index] == 0);
                  const alreadyApproved = ids.filter((_, index) => statusapproved[index] == 1);

                  if (notApprovedDept.length > 0) {
                    Swal.fire({
                      icon: 'error',
                      title: 'ERROR',
                      text: 'there is something that has not been approved by the Kadept, check again',
                      confirmButtonColor: '#d33',
                      confirmButtonText: 'OK'
                    });
                    return;
                  }

                  if (alreadyApproved.length > 0) {
                    Swal.fire({
                      icon: 'error',
                      title: 'ERROR',
                      text: 'Anything that has been approved, check again',
                      confirmButtonColor: '#d33',
                      confirmButtonText: 'OK'
                    });
                    return;
                  }
                  $.post(`includes/events.inc.php?type=EACT13`, {
                    ids,
                    approval: 1
                  }).done(function(a, b, xhr) {
                    $.post(`includes/notifications.inc.php?type=5`, {
                      evt_id
                    }).done(function(a, b, xhr) {
                      Swal.fire({
                        icon: 'success',
                        title: 'SUCCESS',
                        text: 'Participants have been approved',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                      }).then((result) => {
                        location.reload();
                      });
                      console.log(a, b, xhr.status, xhr.responseJSON);
                    }).fail(function(xhr, a, b) {
                      console.log(a, b, xhr.status, xhr.responseJSON);
                    });
                  }).fail(function(xhr, a, b) {
                    console.log(a, b, xhr.status, xhr.responseJSON);
                  });
                <?php endif; ?>

                <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS02') : ?>
                  const notApproved = ids.filter((_, index) => statusapproveddept[index] == 1);
                  if (notApproved.length > 0) {
                    Swal.fire({
                      icon: 'error',
                      title: 'ERROR',
                      text: 'Anything that has been approved, check again',
                      confirmButtonColor: '#d33',
                      confirmButtonText: 'OK'
                    });
                    return;
                  }

                  $.post(`includes/events.inc.php?type=EACT22`, {
                    ids,
                    approval: 1
                  }).done(function(a, b, xhr) {
                    $.post(`includes/notifications.inc.php?type=8`, {
                      evt_id
                    }).done(function(a, b, xhr) {
                      Swal.fire({
                        icon: 'success',
                        title: 'SUCCESS',
                        text: 'Participants have been approved',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                      }).then((result) => {
                        location.reload();
                      });
                      console.log(a, b, xhr.status, xhr.responseJSON);
                    }).fail(function(xhr, a, b) {
                      console.log(a, b, xhr.status, xhr.responseJSON);
                    });
                  }).fail(function(xhr, a, b) {
                    console.log(a, b, xhr.status, xhr.responseJSON);
                  });
                <?php endif; ?>
              });

              $("#disapproveBtn").click(function() {
                const options = document.querySelectorAll('.form-check-input');
                const ids = [];
                const statusapproveddept = [];
                const statusapproved = [];

                let hasApproved = false;
                let hasApprovedDept = false;

                options.forEach(function(item) {
                  if (item.checked) {
                    const id = item.id.split('_').pop();
                    ids.push(id);

                    const deptStatus = item.getAttribute('data-appdept'); // Example
                    const approvalStatus = item.getAttribute('data-approved'); // Example

                    statusapproveddept.push(deptStatus);
                    statusapproved.push(approvalStatus);

                    if (approvalStatus == 1) {
                      hasApproved = true;
                    } else if (deptStatus == 0) {
                      hasApprovedDept = true;
                    }
                  }
                });

                <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
                  if (hasApprovedDept) {
                    //swal error
                    Swal.fire({
                      icon: 'error',
                      title: 'ERROR',
                      text: 'Anything thats not have approve kaDept, check again',
                      confirmButtonColor: '#d33',
                      confirmButtonText: 'OK'
                    });
                    return; // Stop further execution
                  }
                <?php endif; ?>

                <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS02') : ?>
                  if (hasApproved) {
                    //swal error
                    Swal.fire({
                      icon: 'error',
                      title: 'ERROR',
                      text: 'Anything that has been approved, check again',
                      confirmButtonColor: '#d33',
                      confirmButtonText: 'OK'
                    });
                    return; // Stop further execution
                  }
                <?php endif; ?>

                <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
                  type = 'EACT13';
                <?php endif; ?>

                <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS02') : ?>
                  type = 'EACT22';
                <?php endif; ?>

                Swal.fire({
                  title: 'Confirmation!',
                  text: `Are you sure to Disapprove?`,
                  showCancelButton: true,
                  confirmButtonText: 'Ya',
                  cancelButtonText: 'Batal',
                }).then((result) => {
                  if (result.isConfirmed) {
                    $.post(`includes/events.inc.php?type=${type}`, {
                      ids: ids,
                      approval: 2,
                    }).done(function(a, b, xhr) {
                      $.post(`includes/notifications.inc.php?type=11`, {
                        evt_id
                      }).done(function(a, b, xhr) {
                        Swal.fire({
                          icon: 'success',
                          title: 'SUCCESS',
                          text: 'Participants have been disapproved',
                          confirmButtonColor: '#28a745',
                          confirmButtonText: 'OK'
                        }).then((result) => {
                          location.reload(); // Reload halaman jika berhasil
                        });
                      }).fail(function(xhr, a, b) {
                        console.log(a, b, xhr.status, xhr.responseJSON); // Log error jika gagal
                      });
                      location.reload(); // Reload halaman jika berhasil
                    }).fail(function(xhr, a, b) {
                      console.log(a, b, xhr.status, xhr.responseJSON); // Log error jika gagal
                    });
                  }
                });
              });
            }
          });
        }


        setTimeout(() => {
          // remove secondary loading spinner
          $("#loading_spinner_secondary").removeClass("d-block").addClass('d-none');

          // show main content container
          $("#main_content_container").removeClass('d-none');
          $("#loading_spinner").removeClass("d-block").addClass('d-none');

        }, 500);
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
      })
    }


    // get departments, sections, subsections, grades, genders, approval, and completion status
    function getQueryData() {
      $.get(`includes/events.inc.php?type=EACT11&evt_id=${evt_id}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          // get response data
          const departments = xhr.responseJSON['departments'];
          const sections = xhr.responseJSON['sections'];
          const subsections = xhr.responseJSON['subsections'];
          const grades = xhr.responseJSON['grades'];
          const genders = xhr.responseJSON['genders'];
          const approvals = xhr.responseJSON['approvals'];
          const completions = xhr.responseJSON['completions'];

          // set departments
          $("#department_select").append(`
            <option class="department-option" disabled selected>-- Select department --</option>
            <option class="department-option" value="">All</option>
          `);

          departments.forEach(function(department) {
            $("#department_select").append(`
              <option class="department-option" value="${department['DPT_ID']}">${department['DEPARTMENT']}</option>
            `);
          })

          // // set sections
          // $("#section_select").append(`
          //   <option class="section-option" disabled selected>-- Select section --</option>
          //   <option class="section-option" value="">All</option>
          // `);

          // sections.forEach(function(section) {
          //   $("#section_select").append(`
          //     <option class="section-option" value="${section['SEC_ID']}">${section['SECTION']}</option>
          //   `);
          // })

          // // set subsections
          // $("#subsection_select").append(`
          //   <option class="subsection-option" disabled selected>-- Select subsection --</option>
          //   <option class="subsection-option" value="">All</option>
          // `);

          // subsections.forEach(function(subsection) {
          //   $("#subsection_select").append(`
          //     <option class="subsection-option" value="${subsection['SUB_SEC_ID']}">${subsection['SUBSECTION']}</option>
          //   `);
          // })

          // set grades
          $("#grade_select").append(`
            <option class="grade-option" disabled selected>-- Select grade --</option>
            <option class="grade-option" value="">All</option>
          `);

          grades.forEach(function(grade) {
            $("#grade_select").append(`
              <option class="grade-option" value="${grade['GRADE']}">${grade['GRADE']}</option>
            `);
          })

          // set genders
          $("#gender_select").append(`
            <option class="gender-option" disabled selected>-- Select gender --</option>
            <option class="gender-option" value="">All</option>
          `);

          genders.forEach(function(gender) {
            $("#gender_select").append(`
              <option class="gender-option" value="${gender['GENDER']}">${gender['GENDER']}</option>
            `);
          })

          // set genders
          $("#approval_select").append(`
            <option class="approval-option" disabled selected>-- Select approval --</option>
            <option class="approval-option" value="">All</option>
          `);

          approvals.forEach(function(approval) {
            $("#approval_select").append(`
              <option class="approval-option" value="${approval['APPROVED']}">${approval['APPROVED'] == 0? "<span class='text-warning'>Waiting for approval</span>": approval['APPROVED'] == 1? "<span class='text-success'>Approved</span>": approval['APPROVED'] == 2? "<span class='text-danger'>Not approved</span>": ""}</option>
            `);
          })

          // set completions
          $("#completion_select").append(`
            <option class="completion-option" disabled selected>-- Select completion --</option>
            <option class="completion-option" value="">All</option>
          `);

          completions.forEach(function(completion) {
            $("#completion_select").append(`
              <option class="completion-option" value="${completion['COMPLETED']}">${completion['COMPLETED'] == 0? "Not completed": "Completed"}</option>
            `);
          })

          setTimeout(() => {

            // remove loading spinner
            $("#loading_spinner").removeClass('d-block').addClass('d-none');

            // show query container
            $("#query_container").removeClass('d-none');
          }, 500)
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