<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: /login.php");
}
?>

<h1 class="fs-2 fw-semibold">Create New Training</h1>
<p>Create new training at PT. Kayaba Indonesia Training Center</p>
<hr>
<!-- breadcrumb -->
<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="trainings.php" class="text-decoration-none">Trainings</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create training</li>
  </ol>
</nav>
<!-- Create training form container -->
<div id="create_form_container" class="mt-4">
  <div>
    <label for="training_name_input" class="form-label fw-semibold">Training name</label>
    <input type="text" name="training_name" id="training_name_input" class="form-control" placeholder="Enter training name">
    <small id="training_name_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="training_description_input" class="form-label fw-semibold">Training description</label>
    <textarea class="form-control" name="training_description" placeholder="Enter training description here" id="training_description_input" rows="7"></textarea>
    <small id="training_description_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="training_purpose_input" class="form-label fw-semibold">Training general purpose</label>
    <textarea class="form-control" name="training_purpose" placeholder="Enter training purpose here" id="training_purpose_input" rows="7"><?php echo $purpose; ?></textarea>
    <small id="training_purpose_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="company_purposes_input" class="form-label fw-semibold">Training purposes for company</label>
    <div class="input-group">
      <input id="company_purposes_input" type="text" class="form-control" name="" placeholder="Add training purpose for company here" value="">
      <button id="company_purposes_add_btn" type="button" class="btn btn-secondary">Add</button>
    </div>
    <small class="text-warning fw-semibold mt-1 d-inline-block">*Click "Add" or press "Enter" to add more purpose</small>
    <div id="company_purposes_list" class="d-flex flex-wrap gap-2 mt-2"></div>
    <small id="company_purposes_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="participant_purposes_input" class="form-label fw-semibold">Training purposes for participants</label>
    <div class="input-group">
      <input id="participant_purposes_input" type="text" class="form-control" name="" placeholder="Add training purpose for participants here" value="">
      <button id="participant_purposes_add_btn" type="button" class="btn btn-secondary">Add</button>
    </div>
    <small class="text-warning fw-semibold mt-1 d-inline-block">*Click "Add" or press "Enter" to add more purpose</small>
    <div id="participant_purposes_list" class="d-flex flex-wrap gap-2 mt-2"></div>
    <small id="participant_purposes_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="duration_days_input" class="form-label fw-semibold">Training duration (days)</label>
    <input class="form-control" type="number" name="duration_days" placeholder="Enter training duration here (in days)" id="duration_days_input" value="" onkeypress="return event.charCode >= 48" min="1">
    <small id="duration_days_error" class="text-danger"></small>
  </div>
  <div class="mt-4">
    <label for="duration_hours_input" class="form-label fw-semibold">Training duration per day (hours)</label>
    <input class="form-control" type="number" name="duration_hours_input" placeholder="Enter training duration per day here (in hours)" id="duration_hours_input" value="" onkeypress="return event.charCode >= 48" min="1">
    <small id="duration_hours_error" class="text-danger"></small>
  </div>
  <button id="create_training_btn" type="button" class="btn btn-success mt-5 ms-auto d-block"><i class="fas fa-plus"></i> Create new training</button>
</div>

<script type="text/javascript" defer>
  $(document).ready(function() {
    // arrays
    const companyPurposes = [];
    const participantPurposes = [];

    // add more purposes
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
        <div class='px-3 py-1 bg-light'>${purpose} <form method=""></form></div>
        `;
        $("#company_purposes_list").append(html);
      } else {
        $("#company_purposes_error").html("Characters length must be between 4 and 50")
      }
    }

    function addParticipantPurpose() {
      const purpose = $("#participant_purposes_input").val();
      if (purpose?.length >= 4 && purpose?.length <= 50) {
        participantPurposes.push(purpose);
        $("#participant_purposes_input").val('');
        html = `
        <div class='px-3 py-1 bg-light'>${purpose} <i class='fas fa-times'></i></div>
        `;
        $("#participant_purposes_list").append(html);
      } else {
        $("#participant_purposes_error").html("Characters length must be between 4 and 50")
      }
    }

    // create new training
    $("#create_training_btn").click(function() {
      const trainingName = $("#training_name_input").val();
      const trainingDescription = $("#training_description_input").val();
      const trainingPurpose = $("#training_purpose_input").val();

      const durationHours = $("#duration_hours_input").val();
      const durationDays = $("#duration_days_input").val();

      $.post('includes/trainings.inc.php?type=TACT03', {
        training_name: trainingName,
        training_description: trainingDescription,
        training_purpose: trainingPurpose,
        company_purposes: companyPurposes,
        participant_purposes: participantPurposes,
        duration_days: durationDays,
        duration_hours: durationHours
      }).done(function(a, b, xhr) {
        window.location.href = 'trainings.php';
      }).fail(function(xhr, a, b) {
        console.log(xhr.status);
      })
    })
  });
</script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>