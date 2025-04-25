<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: /login.php");
}
?>

<h1 class="fs-2 fw-semibold"><span id="training_name_title" class="fw-bold"></span> Details</h1>
<p>Administrator could change this particular training&apos;s overview here. Administrator could also edit this particular training content or even delete the training.</p>
<hr>
<div class="d-flex justify-content-between align-items-center">
  <!-- breadcrumb -->
  <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb" class="d-block">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="trainings.php" class="text-decoration-none">Trainings</a></li>
    </ol>
  </nav>
  <a id="edit_training_url" href="trainings/materials.php?t_id=" class="text-decoration-none btn btn-outline-primary"><i class="fas fa-pencil"></i> Edit training content</a>
</div>
<!-- loading spinner -->
<div id="loading_spinner" class="spinner-border text-primary d-block mx-auto my-4" role="status">
  <span class="sr-only">Loading...</span>
</div>
<p id="no_training" class="d-none">No trainings found</p>
<div id="update_form" class="mt-4 d-none">
  <div>
    <label for="training_name_input" class="form-label fw-semibold">Training name</label>
    <input type="text" name="training_name" id="training_name_input" class="form-control" placeholder="Enter training name" disabled>
    <small id="training_name_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="training_description_input" class="form-label fw-semibold">Training description</label>
    <textarea class="form-control" name="training_description" placeholder="Enter training description here" id="training_description_input" rows="7" disabled></textarea>
    <small id="training_description_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="training_purpose_input" class="form-label fw-semibold">Training general purpose</label>
    <textarea class="form-control" name="training_purpose" placeholder="Enter training purpose here" id="training_purpose_input" rows="7" disabled></textarea>
    <small id="training_purpose_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="company_purposes_input" class="form-label fw-semibold">Training purposes for company</label>
    <div class="input-group">
      <input id="company_purposes_input" type="text" class="form-control" name="" placeholder="Add training purpose for company here" disabled>
      <button id="company_purposes_add_btn" type="button" class="btn btn-secondary add-btn" disabled>Add</button>
    </div>
    <small class="text-warning fw-semibold mt-1 d-inline-block">*Press enter or click "Add" to add more purpose</small>
    <div id="company_purposes_list" class="d-flex flex-wrap gap-2 mt-2">
    </div>
    <small id="company_purposes_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="participant_purposes_input" class="form-label fw-semibold">Training purposes for participants</label>
    <div class="input-group">
      <input id="participant_purposes_input" type="text" class="form-control" name="participant_purposes" placeholder="Add training purpose for participants here" disabled>
      <button id="participant_purposes_add_btn" type="button" class="btn btn-secondary add-btn" disabled>Add</button>
    </div>
    <small class="text-warning fw-semibold mt-1 d-inline-block">*Press enter or click "Add" to add more purpose</small>
    <div id="participants_purposes_list" class="d-flex flex-wrap gap-2 mt-2">
    </div>
    <small id="participant_purposes_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="duration_days_input" class="form-label fw-semibold">Training duration (days)</label>
    <input class="form-control" type="number" name="duration_days" placeholder="Enter training duration here (in days)" id="duration_days_input" onkeypress="return event.charCode >= 48" min="1" disabled>
    <small id="duration_days_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="duration_hours_input" class="form-label fw-semibold">Training duration per day (hours)</label>
    <input class="form-control" type="number" name="duration_hours" placeholder="Enter training duration per day here (in hours)" id="duration_hours_input" onkeypress="return event.charCode >= 48" min="1" disabled>
    <small id="duration_hours_error" class="text-danger"></small>
  </div>
  <div id="update_btn_group" class="d-none gap-3 justify-content-end mt-5">
    <button id="update_btn" type="submit" class="btn btn-primary"><i class="fas fa-pencil"></i> Update</button>
    <button id="cancel_update_btn" type="button" onclick="location.reload()" class="btn btn-dark"><i class="fas fa-times"></i> Cancel update</button>
  </div>
</div>
<div id="delete_btn_group" class="d-none gap-3 justify-content-end mt-5">
  <button id="editBtn" type="button" class="btn btn-outline-primary"><i class="fas fa-pencil"></i> Edit training</button>
  <button type="button" data-bs-toggle="modal" data-bs-target="#deleteTrainingConfirmModal" id=" deleteBtn" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
</div>

<!-- delete confirmation modal -->
<div class="modal fade" id="deleteTrainingConfirmModal" tabindex="-1" aria-labelledby="deleteTrainingConfirm" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-left" id="deleteTrainingConfirm">Delete Training?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p style="text-align: left !important">Are you sure to delete this training? This action is irreversible! All datas associated with the training will be deleted.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="includes/trainings.inc.php?type=TACT02&t_id=<?php echo $_GET['t_id']; ?>" class="btn delete-training-btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>
<!-- delete confirmation modal end -->

<script type="text/javascript" defer>
  $(document).ready(function() {
    const queryString = new URLSearchParams(window.location.search);
    const t_id = queryString.get('t_id');
    const companyPurposes = [];
    const participantsPurposes = [];

    // set url for edit training content
    $("#edit_training_url").attr('href', `trainings/materials.php?t_id=${t_id}`);

    // add purposes
    $("#company_purposes_add_btn").click(addCompanyPurpose)
    $("#participant_purposes_add_btn").click(addParticipantPurpose)

    // remove purposes error on char input
    $("#company_purposes_input").keyup(function(e) {
      if (e.which == 13) {
        addCompanyPurpose();
      } else {
        $("#company_purposes_error").html("");
      }
    })

    $("#participant_purposes_input").keyup(function(e) {
      if (e.which == 13) {
        addParticipantPurpose();
      } else {
        $("#participant_purposes_error").html("");
      }
    })

    function addCompanyPurpose() {
      const purpose = $("#company_purposes_input").val();
      if (purpose?.length >= 4 && purpose?.length <= 50) {
        companyPurposes.push(purpose);
        $("#company_purposes_input").val('');
        html = `
        <div class='px-3 py-1 bg-light'>${purpose} <i class="fas fa-times"></i></div>
        `;
        $("#company_purposes_list").append(html);
      } else {
        $("#company_purposes_error").html("Characters length must be between 4 and 50")
      }
    }

    function addParticipantPurpose() {
      const purpose = $("#participant_purposes_input").val();
      if (purpose?.length >= 4 && purpose?.length <= 50) {
        participantsPurposes.push(purpose);
        $("#participant_purposes_input").val('');
        html = `
        <div class='px-3 py-1 bg-light'>${purpose} <i class='fas fa-times'></i></div>
        `;
        $("#participants_purposes_list").append(html);
      } else {
        $("#participants_purposes_error").html("Characters length must be between 4 and 50")
      }
    }

    // edit button
    $("#editBtn").click(function() {
      // remove disabled from inputs
      $("#training_name_input").removeAttr("disabled");
      $("#training_description_input").removeAttr("disabled");
      $("#training_purpose_input").removeAttr("disabled");
      $("#participant_purposes_input").removeAttr("disabled");
      $("#company_purposes_input").removeAttr("disabled");
      $("#duration_days_input").removeAttr("disabled");
      $("#duration_hours_input").removeAttr("disabled");

      $("#company_purposes_add_btn").removeAttr("disabled");
      $("#participant_purposes_add_btn").removeAttr("disabled");

      // hide delete button group
      $("#delete_btn_group").removeClass('d-flex').addClass('d-none');

      // show update button group
      $("#update_btn_group").removeClass('d-none').addClass('d-flex');
    })

    // update training
    $("#update_btn").click(function() {
      // get post datas
      const training_name = $("#training_name_input").val();
      const training_description = $("#training_description_input").val();
      const training_purpose = $("#training_purpose_input").val();
      const duration_hours = $("#duration_hours_input").val();
      const duration_days = $("#duration_days_input").val();

      $.post(`includes/trainings.inc.php?type=TACT04&t_id=${t_id}`, {
        training_name,
        training_description,
        training_purpose,
        company_purposes: companyPurposes,
        participant_purposes: participantsPurposes,
        duration_hours,
        duration_days
      }).done(function(a, b, xhr) {
        // refresh page
        location.reload();
      }).fail(function(xhr, a, b) {
        console.log(a, b, xhr.status, xhr.responseJSON);
        // console.log(xhr.status, xhr.responseJSON);
        if (xhr.responseJSON) {
          const errors = xhr.responseJSON;

          $("#training_name_error").html(errors['training_name']);
          $("#training_description_error").html(errors['description']);
          $("#training_purpose_error").html(errors['purpose']);
          $("#company_purposes_error").html(errors['company_purposes']);
          $("#participants_purposes_error").html(errors['participant_purposes']);
          $("#duration_days_error").html(errors['duration_days']);
          $("#duration_hours_error").html(errors['duration_hours']);
        }
      })
    })

    // get training datas
    $.get(`includes/trainings.inc.php?t_id=${t_id}`).done(function(a, b, xhr) {
      // get datas
      const training = xhr.responseJSON['training'];
      const companyPurposes = xhr.responseJSON['company_purposes'];
      const participantsPurposes = xhr.responseJSON['participants_purposes'];

      // hide loading animation
      $("#loading_spinner").removeClass('d-block').addClass('d-none');

      // if no content response
      if (xhr.status === 204) {
        $("#no_training").removeClass('d-none');
        return;
      }

      // input datas
      $("#training_name_input").val(training['TRAINING']);
      $("#training_name_title").html(training['TRAINING']);
      $("#training_description_input").val(training['DESCRIPTION']);
      $("#training_purpose_input").val(training['PURPOSE']);
      $("#duration_hours_input").val(training['DURATION_HOURS']);
      $("#duration_days_input").val(training['DURATION_DAYS']);

      if (participantsPurposes?.length) {
        participantsPurposes?.forEach(function(purpose) {
          html = `
        <div class='px-3 py-1 bg-light'>${purpose['BENEFIT']} <i class='fas fa-times'></i></div>
        `;
          $("#participants_purposes_list").append(html);
        })
      }

      if (companyPurposes?.length) {
        companyPurposes.forEach(function(purpose) {
          html = `
        <div class='px-3 py-1 bg-light'>${purpose['BENEFIT']} <i class='fas fa-times'></i></div>
        `;
          $("#company_purposes_list").append(html);
        })
      }

      // show update form 
      $("#update_form").removeClass('d-none');

      // show delete button groups
      $("#delete_btn_group").removeClass('d-none').addClass('d-flex');

    }).fail(function(xhr, a, b) {
      console.log(a, b, xhr.status, xhr.responseJSON);
    })
  });
</script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>