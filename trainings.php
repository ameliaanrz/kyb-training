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

<h1 class="fw-bold">PT. Kayaba Training Center Trainings</h1>
<!-- <p class="fs-5">Training administrator dashboard to manage all PT. Kayaba Indonesia training programs</p> -->
<button type="button" id="createTrainingBtn" data-bs-toggle="modal" data-bs-target="#trainingModal" class="btn btn-outline-success py-1"><i class="fa-solid fa-plus"></i> Create new training</button>
<div class="card mt-4 py-3">
  <!-- <div class="card-header pb-0 border-0 bg-white">
    <h3 class="fw-semibold">Trainings List</h3> 
    <p>List of all trainings provided by PT. Kayaba Indonesia</p>
    <hr>
  </div> -->
  <div class="card-body pt-2">
    <div id="search_filter_container">
      <label for="training_search_input" class="fw-bold">Training name or ID search</label>
      <input id="training_search_input" type="text" name="search" placeholder="Search by training name or id" class="form-control mt-2" autocomplete="off">
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
    <table id="trainings_table" class="table d-none mt-3 rounded-2 table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th scope="col" class="text-center">Kode <i id="sort-icon-T_ID" class="fas fa-sort"></i></th>
          <th scope="col" class="text-center">Training Name <i id="sort-icon-TRAINING" class="fas fa-sort"></i></th>
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
<div class="modal fade" id="trainingModal" tabindex="-1" aria-labelledby="trainingModalTitle" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="trainingModalTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="trainingModalBody" class="modal-body">
        <!-- loading spinner -->
        <div class="spinner-border text-primary d-block mx-auto my-4" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <!-- loading spinner end -->
      </div>
      <div class="modal-footer">
        <button id="trainingModalCloseBtn" type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
        <button id="trainingModalActionBtn" type="button" class="btn btn-primary">Save changes</button>
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
    getAllTrainings(listsShown, page, search, colomIndex, direction);

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

    // handle create new training btn
    $("#createTrainingBtn").click(function() {
      // set training modal title
      $("#trainingModalTitle").html("Create New Training Object");

      // set training modal body
      setTimeout(() => {
        // add training name
        $("#trainingModalBody").html("<label for='trainingNameInput' class='form-label'>Training name<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append("<input id='trainingNameInput' type='text' class='form-control' placeholder='Enter training name here' />");
        $("#trainingModalBody").append("<small id='trainingNameError' class='text-danger d-block'></small>");
        
        $("#trainingModalBody").append("<label for='trainingIdInput' class='form-label mt-2'>Training ID<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append("<input id='trainingIdInput' type='text' class='form-control' maxlength='7' placeholder='Enter training id here' />");
        $("#trainingModalBody").append("<small id='trainingIdError' class='text-danger d-block'></small>");
        // add training description
        $("#trainingModalBody").append("<label for='trainingDescriptionInput' class='form-label mt-2'>Training description<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append("<textarea id='trainingDescriptionInput' class='form-control' placeholder='Enter training description here' rows='5'></textarea>");
        $("#trainingModalBody").append("<small id='trainingDescriptionError' class='text-danger d-block'></small>");

        // add training description
        $("#trainingModalBody").append("<label for='trainingPurposeInput' class='form-label mt-2'>Training purpose<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append("<textarea id='trainingPurposeInput' class='form-control' placeholder='Enter training purpose here' rows='5'></textarea>");
        $("#trainingModalBody").append("<small id='trainingPurposeError' class='text-danger d-block'></small>");

        // add training company purposes
        $("#trainingModalBody").append("<label for='companyPurposesInput' class='form-label mt-2'>Company purposes<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append(`
        <div class='input-group'>
          <input id='companyPurposesInput' type='text' placeholder='Enter training purpose here' class='form-control' />
          <button id='companyPurposesAddBtn' class='btn btn btn-secondary'>Add</button>
        </div>
        <div id='companyPurposesContainer' class='flex flex-wrap gap-2 mt-1'></div>
        `);
        $("#trainingModalBody").append("<small id='companyPurposesError' class='text-danger d-block'></small>");

        /**
         * handle company purposes add button
         */
        // reset company purposes array
        $("#companyPurposesAddBtn").click(function() {
          // get purpose value from input
          const purpose = $("#companyPurposesInput").val();

          if (purpose) {
            // add input content to arr variable
            companyPurposes = [...companyPurposes, purpose];

            // remove content from input
            $("#companyPurposesInput").val('');

            // insert updated purposes into html container
            $("#companyPurposesContainer").append(`
              <small id='company_purpose_input_${purpose}' class='company-purpose bg-secondary text-light px-2 py-1 rounded-2 d-inline-block mt-1' style='width: fit-content !important'>${purpose} <i id='delete_${purpose}' class='company-purpose-delete fas fa-times' style='cursor: pointer !important'></i></small>
            `);

            // handle company purposes delete item
            $(".company-purpose-delete").click(function() {
              // get the purpose
              const purposeTmp = this.id.split('_').pop();

              // remove purpose from arr variable
              companyPurposes = companyPurposes.filter((item) => item != purposeTmp);

              // remove purpose from html container
              document?.getElementById(`company_purpose_input_${purposeTmp}`)?.remove();
            })
          }
        })

        // add training participant purposes
        $("#trainingModalBody").append("<label for='participantPurposesInput' class='form-label mt-2'>Participant purposes<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append(`
        <div class='input-group'>
          <input id='participantPurposesInput' type='text' placeholder='Enter participant purpose here' class='form-control' />
          <button id='participantPurposesAddBtn' class='btn btn btn-secondary'>Add</button>
        </div>
        <div id='participantPurposesContainer'></div>
        `);
        $("#trainingModalBody").append("<small id='participantPurposesError' class='text-danger d-block'></small>");

        /**
         * handle participant purposes add button
         */
        // reset participant purposes array
        participantPurposes = [];
        $("#participantPurposesAddBtn").click(function() {
          // get purpose value from input
          const purpose = $("#participantPurposesInput").val();

          if (purpose) {
            // add input content to arr variable
            participantPurposes = [...participantPurposes, purpose];

            // remove content from input
            $("#participantPurposesInput").val('');

            // insert updated purposes into html container
            $("#participantPurposesContainer").append(`
              <small id='participant_purpose_input_${purpose}' class='participant-purpose bg-secondary text-light px-2 py-1 rounded-2 d-inline-block mt-1' style='width: fit-content !important'>${purpose} <i id='delete_${purpose}' class='participant-purpose-delete fas fa-times' style='cursor: pointer !important'></i></small>
            `);

            // handle participant purposes delete item
            $(".participant-purpose-delete").click(function() {
              // get the purpose
              const purposeTmp = this.id.split('_').pop();

              // remove purpose from arr variable
              participantPurposes = participantPurposes.filter((item) => item != purposeTmp);

              // remove purpose from html container
              document?.getElementById(`participant_purpose_input_${purposeTmp}`)?.remove();
            })
          }
        })
        
        //add outline 
        $("#trainingModalBody").append("<label for='trainingOutlineInput' class='form-label mt-2'>Training Outline<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append("<textarea id='trainingOutlineInput' class='form-control' placeholder='Enter training outline here' rows='5'></textarea>");
        $("#trainingModalBody").append("<small id='trainingOutlineError' class='text-danger d-block'></small>");

        //add duration
        $("#trainingModalBody").append("<label for='trainingDurationDaysInput' class='form-label mt-2'>Training Duration (days)<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append("<input type='number' id='trainingDurationDaysInput' class='form-control' placeholder='Enter training duration here (in days)'>");
        $("#trainingModalBody").append("<small id='trainingDurationDaysError' class='text-danger d-block'></small>");

        //add duration
        $("#trainingModalBody").append("<label for='trainingDurationHoursInput' class='form-label mt-2'>Training Duration per day (hours)<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append("<input type='number' id='trainingDurationHoursInput' class='form-control' placeholder='Enter training duration per day here (in hours)'>");
        $("#trainingModalBody").append("<small id='trainingDurationHoursError' class='text-danger d-block'></small>");

        //add partipants
        $("#trainingModalBody").append("<label for='trainingParticipantInput' class='form-label mt-2'>Participant<span class='text-danger'> *</span></label>");
        $("#trainingModalBody").append("<input id='trainingParticipantInput' type='text' class='form-control' placeholder='Enter participant here' />");
        $("#trainingModalBody").append("<small id='trainingParticipantError' class='text-danger d-block'></small>");
        /**
         * handle create new training
         */
        // post data
        $("#trainingModalActionBtn").click(function() {
          // get all input datas
          const training = $("#trainingNameInput").val();
          const t_id = $("#trainingIdInput").val();
          const description = $("#trainingDescriptionInput").val();
          const purpose = $("#trainingPurposeInput").val();
          const outline = $("#trainingOutlineInput").val();
          const duration_days = $("#trainingDurationDaysInput").val();
          const duration_hours = $("#trainingDurationHoursInput").val();
          const participant = $("#trainingParticipantInput").val();
          const fields = {
            training_name: training,
            training_id: t_id,
            description: description,
            purpose: purpose,
            company_purposes:companyPurposes,
            participant_purposes:participantPurposes,
            outline:outline,
            durationDays:duration_days,
            durationHours:duration_hours,
            participant:participant
          };

          const emptyFields = validateFields(fields);
          let errors = {};

          // Clear previous error messages
          $(".error-message").html("");

          if (emptyFields.length > 0) {
            emptyFields.forEach(field => {
              errors[field] = "*This field is required";
            });

            // Display error messages
            $("#trainingNameError").html(errors['training_name']);
            $("#trainingIdError").html(errors['training_id']);
            $("#trainingDescriptionError").html(errors['description']);
            $("#trainingPurposeError").html(errors['purpose']);
            $("#companyPurposesError").html(errors['company_purposes']);
            $("#participantPurposesError").html(errors['participant_purposes']);
            $("#trainingOutlineError").html(errors['outline']);
            $("#trainingDurationDaysError").html(errors['durationDays']);
            $("#trainingDurationHoursError").html(errors['durationHours']);
            $("#trainingParticipantError").html(errors['participant']);
            return;
          }else{
            $("#trainingNameError").html("");
            $("#trainingIdError").html("");
            $("#trainingDescriptionError").html("");
            $("#trainingPurposeError").html("");
            $("#companyPurposesError").html("");
            $("#participantPurposesError").html("");
            $("#trainingOutlineError").html("");
            $("#trainingDurationDaysError").html("");
            $("#trainingDurationHoursError").html("");
            $("#trainingParticipantError").html("");
          }
          
          //memeriksa apakah ada atau tidak
          $.post(`includes/trainings.inc.php?t_id=${t_id}`, {
          }).done(function(response, status, xhr) {
            // response dapat berisi data apapun dari server, tergantung implementasi server-side
            console.log(response);
           if (xhr.status === 200) {
            // jika status 200, maka data yang dikirimkan oleh server dapat diproses
             if(response.training['status']=== '1'){
              Swal.fire({
                icon: 'warning',
                title: 'Training ID exists!',
                text: 'Training ID already exists!',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
              });
              return;
             }else if (response.training['status'] === '0'){
              $.post(`includes/trainings.inc.php?type=TACT03`, {
                training_name: training,
                training_id: t_id,
                training_description: description,
                training_purpose: purpose,
                outline:outline,
                durationDays:duration_days,
                durationHours:duration_hours,
                participant:participant,
                company_purposes: companyPurposes,
                participant_purposes: participantPurposes
                
              }).done(function(a, b, xhr) {
                // reload the page
                swal('success','Success!','Success Create Training!');
              }).fail(function(xhr, a, b) {
                console.log(xhr.status);
                if (xhr.responseJSON) {
                  // get errors
                  const errors = xhr.responseJSON;
                  $("#trainingNameError").html(errors['training_name']);
                  $("#trainingDescriptionError").html(errors['description']);
                  $("#trainingPurposeError").html(errors['purpose']);
                  $("#companyPurposesError").html(errors['company_purposes']);
                  $("#participantPurposesError").html(errors['participant_purposes']);
                  $("#trainingOutlineError").html(errors['outline']);
                  $("#trainingDurationDaysError").html(errors['durationDays']);
                  $("#trainingDurationHoursError").html(errors['durationHours']);
                  $("#trainingParticipantError").html(errors['participant']);
                }
              })
             }
            } else if(status === "nocontent") {
              //create
              $.post(`includes/trainings.inc.php?type=TACT03`, {
                training_name: training,
                training_id: t_id,
                training_description: description,
                training_purpose: purpose,
                company_purposes: companyPurposes,
                participant_purposes: participantPurposes,
                outline:outline,
                durationDays:duration_days,
                durationHours:duration_hours,
                participant:participant
              }).done(function(a, b, xhr) {
                // reload the page
                swal('success','Success!','Success Create Training!');
              }).fail(function(xhr, a, b) {
                console.log(xhr.status);
                if (xhr.responseJSON) {
                  // get errors
                  const errors = xhr.responseJSON;
                  $("#trainingNameError").html(errors['training_name']);
                  $("#trainingDescriptionError").html(errors['description']);
                  $("#trainingPurposeError").html(errors['purpose']);
                  $("#companyPurposesError").html(errors['company_purposes']);
                  $("#participantPurposesError").html(errors['participant_purposes']);
                  $("#trainingOutlineError").html(errors['outline']);
                  $("#trainingDurationDaysError").html(errors['durationDays']);
                  $("#trainingDurationHoursError").html(errors['durationHours']);
                  $("#trainingParticipantError").html(errors['participant']);
                }
              })

            }
          }).fail(function(xhr,a,b){

          })
        })
      }, 500);

      // set training modal action button
      $("#trainingModalActionBtn").html("<i class='fas fa-plus'></i> Create training");

      $("#trainingModalActionBtn").removeClass('btn-danger').removeClass('btn-primary').addClass("btn-success");
    })

    // handle training modal close btn
    $("#trainingModalCloseBtn").click(function() {
      setTimeout(() => {
        // empty training modal title
        $("#trainingModalTitle").html("...");

        // empty training modal body
        $("#trainingModalBody").html(`
          <div class="spinner-border text-primary d-block mx-auto my-4" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        `);

        // change training modal action button
        $("#trainingModalActionBtn").html("Save changes");
        $("#trainingModalActionBtn").removeClass("btn-danger").removeClass('btn-success').addClass('btn-primary');
      }, 500);
    })

    // search training trigger
    $("#training_search_input").keyup(function() {
      // show loading
      $("#loading_spinner").removeClass('d-none').addClass('d-block');

      // hide table
      $("#trainings_table").addClass('d-none');

      // clear previous typing timer
      clearTimeout(typingTimer);

      // set timeout to call searchTraining
      typingTimer = setTimeout(searchTraining, doneTypingInterval);
    })

    // lists shown select option change
    $("#lists_shown_select").on('change', function() {
      // show loading
      $("#loading_spinner").removeClass('d-none').addClass('d-block');

      // hide table
      $("#trainings_table").addClass('d-none');

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
      getAllTrainings(listsShown, page, search,colomIndex,direction);
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
        getAllTrainings(listsShown, page, search,colomIndex,direction);
      }, 1000);

      // show loading spinner and hide events list
      $("#loading_spinner_events").addClass('d-block').removeClass('d-none');
      $("#events_table").addClass('d-none');

      });


    function searchTraining() {
      // get search value
      search = $("#training_search_input").val();

      // remove previous contents
      $("tbody").html('');

      // get filtered trainings
      getAllTrainings(listsShown, page, search,colomIndex,direction);
    }

    function getAllTrainings(lists_shown = 10, page = 1, search = '',colomIndex,direction) {
      $.get(`includes/trainings.inc.php?type=TACT01&lists_shown=${lists_shown}&page=${page}&search=${search}&colomIndex=${colomIndex}&direction=${direction}`).done(function(a, b, xhr) {
        if (xhr.status === 204) {
          $("tbody").html(`<tr><td colspan="3">No trainings found</td></tr>`);
        } else {
          const trainingsCount = xhr.responseJSON['trainings_count'];
          const trainings = xhr.responseJSON['trainings'];

          if (xhr?.responseJSON) {
            $("#pagination_container").pagination({
              dataSource: trainings,
              pageSize: lists_shown,
              callback: function(data, pagination) {
                // template method of yourself
                let html = '';
                data.forEach(function(training) {
                  if (training.STATUS != 0) {
                      html += `
                          <tr>
                              <td style="width:10%;" scope="row">${training['T_ID']}</td>
                              <td style="text-align: left;">${training['TRAINING'] ? `${training['TRAINING']}` : '-'}</td>
                              <td style="width:10%;">
                                  <div class="dropdown">
                                      <button class="btn btn-outline-dark dropdown-toggle py-1" type="button" data-bs-toggle="dropdown">
                                          Detail
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-end">
                                          <button id="show_update_${training['T_ID']}" type="button" class="dropdown-item show-update-btn" data-bs-toggle="modal" data-bs-target="#trainingModal"><i class="fas fa-eye"></i> View / edit overview</button>
                                          <a class="dropdown-item" href="trainings/content.php?t_id=${training['T_ID']}"><i class="fas fa-pencil"></i> Edit training content</a>
                                          <button type="button" onclick="" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteTrainingConfirmModal${training['T_ID']}"><i class="fas fa-trash"></i> Delete</button>
                                      </div>
                                  </div>
                                  <div class="modal fade" id="deleteTrainingConfirmModal${training['T_ID']}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                          <div class="modal-content">
                                              <div class="modal-header">
                                                  <h5 class="modal-title text-left" id="exampleModalLabel">Delete Training?</h5>
                                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                              </div>
                                              <div class="modal-body">
                                                  <p style="text-align: left !important">Are you sure to delete this training? This action is irreversible! All datas associated with the training will be deleted.</p>
                                              </div>
                                              <div class="modal-footer">
                                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                  <a href="includes/trainings.inc.php?type=TACT02&t_id=${training['T_ID']}" class="btn delete-training-btn btn-danger">Delete</a>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </td>
                          </tr>
                      `;
                  }
                })
                $("tbody").html(html);

                $(".show-update-btn").click(function() {
                  // get training id
                  const trainingId = this.id.split('_').pop();

                  // get training data
                  $.get(`includes/trainings.inc.php?type=TACT01&t_id=${trainingId}`).done(function(a, b, xhr) {
                    const res = xhr.responseJSON;
                    companyPurposes = res.company_purposes ? res.company_purposes.map(item => item.BENEFIT) : [];
                    participantPurposes = res.participants_purposes ? res.participants_purposes.map(item => item.BENEFIT) : [];
                    const training = res.training;

                    // set training modal title
                    $("#trainingModalTitle").html(`Update <strong>${training['TRAINING']}</strong> Training`);

                    // set training modal body
                    setTimeout(() => {
                      // add training name
                      $("#trainingModalBody").html("<label for='trainingNameInput' class='form-label'>Training name<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`<input id='trainingNameInput' type='text' class='form-control' placeholder='Enter training name here' value="${training['TRAINING']}" disabled />`);
                      $("#trainingModalBody").append("<small id='trainingNameError' class='text-danger d-block'></small>");

                      // add training description
                      $("#trainingModalBody").append("<label for='trainingDescriptionInput' class='form-label mt-2'>Training description<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`<textarea id='trainingDescriptionInput' class='form-control' placeholder='Enter training description here' rows='5' disabled>${training['DESCRIPTION']}</textarea>`);
                      $("#trainingModalBody").append("<small id='trainingDescriptionError' class='text-danger d-block'></small>");

                      // add training description
                      $("#trainingModalBody").append("<label for='trainingPurposeInput' class='form-label mt-2'>Training purpose<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`<textarea id='trainingPurposeInput' class='form-control' placeholder='Enter training purpose here' rows='5' disabled>${training['PURPOSE']}</textarea>`);
                      $("#trainingModalBody").append("<small id='trainingPurposeError' class='text-danger d-block'></small>");

                      // add training company purposes
                      $("#trainingModalBody").append("<label for='companyPurposesInput' class='form-label mt-2'>Company purposes<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`
                      <div class='input-group'>
                        <input id='companyPurposesInput' type='text' placeholder='Enter training purpose here' class='form-control' disabled />
                        <button id='companyPurposesAddBtn' class='btn btn btn-secondary' disabled>Add</button>
                      </div>
                      <div id='companyPurposesContainer' class='flex flex-wrap gap-2 mt-1'></div>
                      `);
                      $("#trainingModalBody").append("<small id='companyPurposesError' class='text-danger d-block'></small>");

                      // insert company purposes data
                      companyPurposes.forEach(function(purpose) {
                        // insert purposes into html container
                        $("#companyPurposesContainer").append(`
                          <small id='company_purpose_input_${purpose}' class='company-purpose bg-secondary text-light px-2 py-1 rounded-2 d-inline-block mt-1' style='width: fit-content !important'>${purpose} <i id='delete_${purpose}' class='company-purpose-delete fas fa-times'></i></small>
                        `);
                      })

                      // add training participant purposes
                      $("#trainingModalBody").append("<label for='participantPurposesInput' class='form-label mt-2'>Participant purposes<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`
                      <div class='input-group'>
                        <input id='participantPurposesInput' type='text' placeholder='Enter participant purpose here' class='form-control' disabled />
                        <button id='participantPurposesAddBtn' class='btn btn btn-secondary' disabled>Add</button>
                      </div>
                      <div id='participantPurposesContainer'></div>
                      `);
                      $("#trainingModalBody").append("<small id='participantPurposesError' class='text-danger d-block'></small>");

                      // insert participant purposes data
                      participantPurposes.forEach(function(purpose) {
                        // insert purposes into html container
                        $("#participantPurposesContainer").append(`
                          <small id='participant_purpose_input_${purpose}' class='participant-purpose bg-secondary text-light px-2 py-1 rounded-2 d-inline-block mt-1' style='width: fit-content !important'>${purpose} <i id='delete_${purpose}' class='participant-purpose-delete fas fa-times'></i></small>
                        `);
                      })

                      // add training description
                      $("#trainingModalBody").append("<label for='trainingOutlineInput' class='form-label mt-2'>Training Outline<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`<textarea id='trainingOutlineInput' class='form-control' placeholder='Enter training outline here' rows='5' disabled>${training['OUTLINE']}</textarea>`);
                      $("#trainingModalBody").append("<small id='trainingOutlineError' class='text-danger d-block'></small>");

                      // add training name
                      $("#trainingModalBody").append("<label for='trainingDurationDaysInput' class='form-label mt-2'>Training duration (days)<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`<input id='trainingDurationDaysInput' type='number' class='form-control' placeholder='Enter training duration here (in days)' value="${training['DURATION_DAYS']}" disabled />`);
                      $("#trainingModalBody").append("<small id='trainingDurationDaysError' class='text-danger d-block'></small>");

                      // add training name
                      $("#trainingModalBody").append("<label for='trainingDurationHoursInput' class='form-label mt-2'>Training Duration per day (hours)<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`<input id='trainingDurationHoursInput' type='number' class='form-control' placeholder='Enter training duration per day here (in hours)' value="${training['DURATION_HOURS']}" disabled />`);
                      $("#trainingModalBody").append("<small id='trainingDurationHoursError' class='text-danger d-block'></small>");

                      // add training description
                      $("#trainingModalBody").append("<label for='trainingParticipantInput' class='form-label mt-2'>Participants<span class='text-danger'> *</span></label>");
                      $("#trainingModalBody").append(`<input id='trainingParticipantInput' type='text' class='form-control' placeholder='Enter allowed participant here' value="${training['PARTICIPANT']}" disabled />`);
                      $("#trainingModalBody").append("<small id='trainingParticipantError' class='text-danger d-block'></small>");

                      /**
                       * handle create new training
                       */
                      // post data
                      $("#trainingModalActionBtn").click(function() {
                        if ($("#trainingModalActionBtn").hasClass('btn-primary')) { 
                          // get all input datas
                          const training = $("#trainingNameInput").val();
                          const t_id = $("#trainingIdInput").val();
                          const description = $("#trainingDescriptionInput").val();
                          const purpose = $("#trainingPurposeInput").val();
                          const outline = $("#trainingOutlineInput").val();
                          const duration_days = $("#trainingDurationDaysInput").val();
                          const duration_hours = $("#trainingDurationHoursInput").val();
                          const participant = $("#trainingParticipantInput").val();
                          
                            $.post(`includes/trainings.inc.php?type=TACT04&t_id=${trainingId}`, {
                              training_name: training,
                              training_id: t_id,
                              training_description: description,
                              training_purpose: purpose,
                              company_purposes: companyPurposes,
                              participant_purposes: participantPurposes,
                              outline:outline,
                              durationDays:duration_days,
                              durationHours:duration_hours,
                              participant:participant
                            }).done(function(a, b, xhr) {
                              // reload the page
                              swal('success','Success!','Success Update Training!');
                            }).fail(function(xhr, a, b) {
                              console.log(xhr);
                              if (xhr.responseJSON) {
                                // get errors
                                const errors = xhr.responseJSON;
                                $("#trainingNameError").html(errors['training_name']);
                                $("#trainingDescriptionError").html(errors['description']);
                                $("#trainingPurposeError").html(errors['purpose']);
                                $("#companyPurposesError").html(errors['company_purposes']);
                                $("#participantPurposesError").html(errors['participant_purposes']);
                                $("#trainingOutlineError").html(errors['outline']);
                                $("#trainingDurationDaysError").html(errors['durationDays']);
                                $("#trainingDurationHoursError").html(errors['durationHours']);
                                $("#trainingParticipantError").html(errors['participant']);
                              }
                            })
                            
                        } else {
                          setTimeout(() => {
                            // reset company purposes container
                            $("#companyPurposesContainer").html('');

                            // insert company purposes data
                            companyPurposes.forEach(function(purpose) {
                              // insert purposes into html container
                              $("#companyPurposesContainer").append(`
                                <small id='company_purpose_input_${purpose}' class='company-purpose bg-secondary text-light px-2 py-1 rounded-2 d-inline-block mt-1' style='width: fit-content !important'>${purpose} <i id='delete_${purpose}' class='company-purpose-delete fas fa-times' style='cursor: pointer !important'></i></small>
                              `);

                              // handle company purposes delete item
                              $(".company-purpose-delete").click(function() {
                                // get the purpose
                                const purposeTmp = this.id.split('_').pop();

                                // remove purpose from arr variable 
                                companyPurposes = companyPurposes.filter((item) => item != purposeTmp);

                                // remove purpose from html container
                                document?.getElementById(`company_purpose_input_${purposeTmp}`)?.remove();
                              })
                            })

                            /**
                             * handle company purposes add button
                             */
                            // reset company purposes array
                            $("#companyPurposesAddBtn").click(function() {
                              // get purpose value from input
                              const purpose = $("#companyPurposesInput").val();

                              if (purpose) {
                                // add input content to arr variable
                                companyPurposes = [...companyPurposes, purpose];

                                // remove content from input
                                $("#companyPurposesInput").val('');

                                // insert updated purposes into html container
                                $("#companyPurposesContainer").append(`
                                  <small id='company_purpose_input_${purpose}' class='company-purpose bg-secondary text-light px-2 py-1 rounded-2 d-inline-block mt-1' style='width: fit-content !important'>${purpose} <i id='delete_${purpose}' class='company-purpose-delete fas fa-times' style='cursor: pointer'></i></small>
                                `);

                                // handle company purposes delete item
                                $(".company-purpose-delete").click(function() {
                                  // get the purpose
                                  const purposeTmp = this.id.split('_').pop();

                                  // remove purpose from arr variable
                                  companyPurposes = companyPurposes.filter((item) => item != purposeTmp);

                                  // remove purpose from html container
                                  document?.getElementById(`company_purpose_input_${purposeTmp}`)?.remove();
                                })
                              }
                            })

                            // reset participant purposes container
                            $("#participantPurposesContainer").html('');

                            // insert participant purposes data
                            participantPurposes.forEach(function(purpose) {
                              // insert purposes into html container
                              $("#participantPurposesContainer").append(`
                                <small id='participant_purpose_input_${purpose}' class='participant-purpose bg-secondary text-light px-2 py-1 rounded-2 d-inline-block mt-1' style='width: fit-content !important'>${purpose} <i id='delete_${purpose}' class='participant-purpose-delete fas fa-times' style='cursor: pointer !important'></i></small>
                              `);

                              // handle company purposes delete item
                              $(".participant-purpose-delete").click(function() {
                                // get the purpose
                                const purposeTmp = this.id.split('_').pop();

                                // remove purpose from arr variable
                                participantPurposes = participantPurposes.filter((item) => item != purposeTmp);

                                // remove purpose from html container
                                document?.getElementById(`participant_purpose_input_${purposeTmp}`)?.remove();
                              })
                            })

                            /**
                             * handle participant purposes add button
                             */
                            // reset participant purposes array
                            $("#participantPurposesAddBtn").click(function() {
                              // get purpose value from input
                              const purpose = $("#participantPurposesInput").val();

                              if (purpose) {
                                // add input content to arr variable
                                participantPurposes = [...participantPurposes, purpose];

                                // remove content from input
                                $("#participantPurposesInput").val('');

                                // insert updated purposes into html container
                                $("#participantPurposesContainer").append(`
                                  <small id='participant_purpose_input_${purpose}' class='participant-purpose bg-secondary text-light px-2 py-1 rounded-2 d-inline-block mt-1' style='width: fit-content !important'>${purpose} <i id='delete_${purpose}' class='participant-purpose-delete fas fa-times' style='cursor: pointer'></i></small>
                                `);

                                // handle participant purposes delete item
                                $(".participant-purpose-delete").click(function() {
                                  // get the purpose
                                  const purposeTmp = this.id.split('_').pop();

                                  // remove purpose from arr variable
                                  participantPurposes = participantPurposes.filter((item) => item != purposeTmp);

                                  // remove purpose from html container
                                  document?.getElementById(`participant_purpose_input_${purposeTmp}`)?.remove();
                                })
                              }
                            })

                            // change edit button
                            $("#trainingModalActionBtn").html("<i class='fas fa-pencil'></i> Update Training");
                            $("#trainingModalActionBtn").removeClass('btn-outline-primary').addClass('btn-primary');

                            // add classes to participant purpose
                            $(".participant-purpose-delete").addClass('cursor-pointer');
                            $(".company-purpose-delete").addClass('cursor-pointer');

                            // remove disabled
                            $("#trainingNameInput").removeAttr('disabled');
                            $("#trainingIdInput").removeAttr('disabled');
                            $("#trainingDescriptionInput").removeAttr('disabled');
                            $("#trainingPurposeInput").removeAttr('disabled');
                            $("#companyPurposesInput").removeAttr('disabled');
                            $("#participantPurposesInput").removeAttr('disabled');
                            $("#companyPurposesAddBtn").removeAttr('disabled');
                            $("#participantPurposesAddBtn").removeAttr('disabled');
                            $("#trainingOutlineInput").removeAttr('disabled');
                            $("#trainingDurationDaysInput").removeAttr('disabled');
                            $("#trainingDurationHoursInput").removeAttr('disabled');
                            $("#trainingParticipantInput").removeAttr('disabled');
                          }, 200);
                        }
                      })
                    }, 500);

                    // set training modal action button
                    $("#trainingModalActionBtn").html("<i class='fas fa-pencil'></i> Edit training");

                    $("#trainingModalActionBtn").removeClass('btn-success').removeClass("btn-primary").removeClass('btn-danger').addClass("btn-outline-primary");
                  }).fail(function(xhr, a, b) {
                    console.log(xhr.status);
                  })
                })
              }
            })
          }
        }

        setTimeout(() => {
          // show table
          $("#trainings_table").removeClass('d-none');

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