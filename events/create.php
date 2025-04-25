<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS01')) {
  header("Location: ../login.php");
}
require_once __DIR__ . '/../includes/events.inc.php';
?>

<h1 class="fs-2 fw-semibold">Create New Event</h1>
<p>Create new training event at PT. Kayaba Indonesia Training Center</p>
<hr>
<a href="events.php" class="text-dark text-decoration-none"><i class="fas fa-angle-left"></i> Back</a>
<div class="mt-4">
  <div>
    <label for="t_id" class="form-label fw-semibold">Training name</label>
    <form id="search_form" action="" method="GET" class="mt-2">
      <select name="t_id" id="t_id" class="form-select">
        <option disabled <?php echo !$t_id ? "selected" : ""; ?> value> -- Select training name -- </option>
        <?php foreach ($trainings as $train) : ?>
          <option value="<?php echo $train['T_ID']; ?>" <?php echo $t_id == $train['T_ID'] ? "selected" : ""; ?>><?php echo $train['TRAINING']; ?></option>
        <?php endforeach; ?>
      </select>
    </form>
    <?php if ($errors['t_id']) : ?>
      <small class="text-danger"><?php echo $errors['t_id']; ?></small>
    <?php endif; ?>
  </div>
  <div class="mt-4">
    <label for="training_description" class="form-label fw-semibold">Training description</label>
    <textarea class="form-control" name="training_description" placeholder="Enter training description here" id="training_description" rows="7" disabled><?php echo $description; ?></textarea>
    <?php if ($errors['description']) : ?>
      <small class="text-danger"><?php echo $errors['description']; ?></small>
    <?php endif; ?>
  </div>
  <form id="create_event_form" action="" method="POST">
    <div class="mt-4">
      <label for="organizer" class="fw-semibold">Organizer</label>
      <select name="organizer" id="organizer" class="form-select mt-2">
        <option disabled <?php echo !$organizer ? "selected" : ""; ?> value> -- Select organizer institution -- </option>
        <?php foreach ($organizers as $org) : ?>
          <option value="<?php echo $org['ORG_ID']; ?>" <?php echo $organizer == $org['ORG_ID'] ? "selected" : ""; ?>><?php echo $org['ORGANIZER']; ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($errors['organizer']) : ?>
        <small class="text-danger"><?php echo $errors['organizer']; ?></small>
      <?php endif; ?>
    </div>
    <div class="mt-4">
      <label for="new_organizer" class="fw-semibold form-label">New organizer</label>
      <input type="text" id="new_organizer" name="new_organizer" placeholder="Enter new organizer here" class="form-control" value="<?php echo $new_organizer; ?>">
      <small class="text-warning fw-semibold">*Only input if the organizer is not on the list</small>
      <?php if ($errors['organizer']) : ?>
        <small class="text-danger"><?php echo $errors['organizer']; ?></small>
      <?php endif; ?>
    </div>
    <div class="mt-4">
      <label for="trainer" class="fw-semibold">Trainer</label>
      <select name="trainer" id="trainer" class="form-select mt-2">
        <option disabled <?php echo !$trainer ? "selected" : ""; ?> value> -- Select trainer -- </option>
        <?php foreach ($trainers as $trn) : ?>
          <option value="<?php echo $trn['TA_ID']; ?>" <?php echo $trainer == $trn['TA_ID'] ? "selected" : ""; ?>><?php echo $trn['NAME']; ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($errors['trainer']) : ?>
        <small class="text-danger"><?php echo $errors['trainer']; ?></small>
      <?php endif; ?>
    </div>
    <div class="mt-4">
      <label for="new_trainer" class="fw-semibold form-label">New Trainer</label>
      <input type="text" id="new_trainer" name="new_trainer" placeholder="Enter new trainer name" class="form-control" value="<?php echo $new_trainer; ?>">
      <small class="text-warning fw-semibold">*Only input if the trainer is not on the list</small>
    </div>
    <div class="mt-4">
      <label for="location" class="fw-semibold">Location</label>
      <select name="location" id="location" class="form-select mt-2">
        <option disabled <?php echo !$location ? "selected" : ""; ?> value> -- Select location -- </option>
        <?php foreach ($locations as $loc) : ?>
          <option value="<?php echo $loc['LOC_ID']; ?>" <?php echo $location == $loc['LOC_ID'] ? "selected" : ""; ?>><?php echo $loc['LOCATION']; ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($errors['location']) : ?>
        <small class="text-danger"><?php echo $errors['location']; ?></small>
      <?php endif; ?>
    </div>
    <div class="mt-4">
      <label for="new_location" class="fw-semibold form-label">New Location</label>
      <input type="text" name="new_location" id="new_location" value="<?php echo $new_location; ?>" placeholder="Enter new location here" class="form-control">
      <small class="fw-semibold text-warning">*Only input if the location is not on the list</small>
      <?php if ($errors['new_location']) : ?>
        <small class="text-danger"><?php echo $errors['new_location']; ?></small>
      <?php endif; ?>
    </div>
    <div class="mt-4">
      <label for="start_date" class="form-label fw-semibold">Start and end dates</label>
      <div class="input-group">
        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $start_date; ?>">
        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $end_date; ?>">
      </div>
      <?php if ($errors['start_date'] || $errors['end_date']) : ?>
        <small class="text-danger"><?php echo $errors['start_date']; ?><?php ?></small>
      <?php endif; ?>
    </div>
    <div class="mt-4">
      <label for="days" class="form-label fw-semibold">Number of days</label>
      <input type="number" name="days" id="days" value="<?php echo $days; ?>" class="form-control" disabled>
      <small class="text-warning fw-semibold mt-1 d-inline-block">*Automatically calculated from start and finish dates</small>
    </div>
    <div class="mt-4">
      <label for="start_time" class="form-label fw-semibold">Start and end times</label>
      <div class="input-group">
        <input type="time" name="start_time" id="start_time" class="form-control" value="<?php echo $start_time; ?>">
        <input type="time" name="end_time" id="end_time" class="form-control" value="<?php echo $end_time; ?>">
      </div>
      <?php if ($errors['start_time'] || $errors['end_time']) : ?>
        <small class="text-danger"><?php echo $errors['start_time']; ?><?php ?></small>
      <?php endif; ?>
    </div>
    <div class="mt-4">
      <label for="duration" class="form-label fw-semibold">Duration</label>
      <input type="number" name="duration" id="duration" value="<?php echo $duration; ?>" class="form-control" disabled>
      <small class="text-warning fw-semibold mt-1 d-inline-block">*Automatically calculated from start and end times</small>
    </div>
    <button id="submit_button" type="submit" class="btn btn-success mt-5 ms-auto d-block"><i class="fas fa-plus"></i> Create new training</button>
  </form>
</div>

<script type="text/javascript" src="public/js/events/create.script.js" defer></script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>