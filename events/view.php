<?php
include_once __DIR__ . '/../partials/_header.php';
if (!isset($_SESSION['NPK']) || !isset($_SESSION['RLS_ID']) || (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] != 'RLS02')) {
  header("Location: ../login.php");
}
require_once __DIR__ . '/../includes/events.inc.php';
?>

<h1 class="fs-2 fw-semibold">Event <span class="fw-bold"><?php echo $event['EVT_ID']; ?></span> Details</h1>
<p>Training event details
</p>
<hr>
<div class="d-flex justify-content-between align-items-center">
  <a href="javascript:history.go(-1)" class="text-dark text-decoration-none"><i class="fas fa-angle-left"></i> Back</a>
  <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
    <a href="events/approve.php?evt_id=<?php echo $evt_id; ?>" class="btn btn-outline-success"><i class="fas fa-check"></i> Approve participants</a>
  <?php endif; ?>
</div>
<form action="" method="POST" class="mt-4">
  <input type="hidden" name="evt_id" value="<?php echo $event['EVT_ID']; ?>">
  <div>
    <label for="training_name" class="form-label fw-semibold">Training Name</label>
    <select name="training_name" id="training_name" disabled class="form-select mt-2">
      <option disabled <?php echo !$event['T_ID'] ? "selected" : ""; ?> value> -- Select training -- </option>
      <?php foreach ($trainings as $trn) : ?>
        <option value="<?php echo $trn['T_ID']; ?>" <?php echo $event['T_ID'] == $trn['T_ID'] ? "selected" : ""; ?>><?php echo $trn['TRAINING']; ?></option>
      <?php endforeach; ?>
    </select>
    <?php if (isset($errors['training_name'])) : ?>
      <small class="text-danger"><?php echo $errors['training_name']; ?></small>
    <?php endif; ?>
  </div>
  <div class="mt-4">
    <label class="fw-semibold">Training Event Activation Status</label>
    <div class="form-switch mt-2">
      <input type="checkbox" name="training_status" id="training_status" class="form-check-input" value="<?php echo $event['ACTIVATED']; ?>" <?php echo $event['ACTIVATED'] == 1 ? "checked" : ""; ?> disabled>
      <label id="training_status_label" for="training_status" class="form-check-label user-select-none">
        <?php echo $event['ACTIVATED'] == 1 ? '<span class="text-success fw-semibold">Active</span>' : '<span class="text-danger fw-semibold">Not Activated</span>' ?>
      </label>
      <?php if ($errors['organizer']) : ?>
        <small class="text-danger"><?php echo $errors['organizer']; ?></small>
      <?php endif; ?>
    </div>
  </div>
  <div class="mt-4">
    <label for="training_description" class="form-label fw-semibold">Training description</label>
    <textarea class="form-control" name="training_description" placeholder="Enter training description here" id="training_description" rows="7" disabled><?php echo $event['DESCRIPTION']; ?></textarea>
    <?php if ($errors['description']) : ?>
      <small class="text-danger"><?php echo $errors['description']; ?></small>
    <?php endif; ?>
  </div>
  <div class="mt-4">
    <label for="organizer" class="fw-semibold">Organizer</label>
    <select name="organizer" id="organizer" disabled class="form-select mt-2">
      <option disabled <?php echo !$event['ORG_ID'] ? "selected" : ""; ?> value> -- Select organizer institution -- </option>
      <?php foreach ($organizers as $org) : ?>
        <option value="<?php echo $org['ORG_ID']; ?>" <?php echo $event['ORG_ID'] == $org['ORG_ID'] ? "selected" : ""; ?>><?php echo $org['ORGANIZER']; ?></option>
      <?php endforeach; ?>
    </select>
    <?php if ($errors['organizer']) : ?>
      <small class="text-danger"><?php echo $errors['organizer']; ?></small>
    <?php endif; ?>
  </div>
  <div class="mt-4">
    <label for="new_organizer" class="fw-semibold form-label">New organizer</label>
    <input type="text" name="new_organizer" id="new_organizer" value="<?php echo $new_organizer; ?>" placeholder="Enter new organizer here" class="form-control" disabled>
    <small class="text-warning fw-semibold">*Only input if the organizer is not on the list</small>
    <?php if ($errors['new_organizer']) : ?>
      <small class="text-danger"><?php echo $errors['new_organizer']; ?></small>
    <?php endif; ?>
  </div>
  <div class="mt-4">
    <label for="trainer" class="fw-semibold">Trainer</label>
    <select name="trainer" id="trainer" class="form-select mt-2" disabled>
      <option disabled <?php echo !$trainer ? "selected" : ""; ?> value> -- Select trainer -- </option>
      <?php foreach ($trainers as $trn) : ?>
        <option value="<?php echo $trn['TA_ID']; ?>" <?php echo $event['TA_ID'] == $trn['TA_ID'] ? "selected" : ""; ?>><?php echo $trn['NAME']; ?></option>
      <?php endforeach; ?>
    </select>
    <?php if ($errors['trainer']) : ?>
      <small class="text-danger"><?php echo $errors['trainer']; ?></small>
    <?php endif; ?>
  </div>
  <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
    <div class="mt-4">
      <label for="new_trainer" class="fw-semibold form-label">New Trainer</label>
      <input type="text" id="new_trainer" name="new_trainer" placeholder="Enter new trainer name" class="form-control" value="<?php echo $new_trainer; ?>" disabled>
      <small class="text-warning fw-semibold">*Only input if the trainer is not on the list</small>
    </div>
  <?php endif; ?>
  <div class="mt-4">
    <label for="location" class="fw-semibold">Location</label>
    <select name="location" id="location" class="form-select mt-2" disabled>
      <option disabled <?php echo !$location ? "selected" : ""; ?> value> -- Select location -- </option>
      <?php foreach ($locations as $loc) : ?>
        <option value="<?php echo $loc['LOC_ID']; ?>" <?php echo $event['LOC_ID'] == $loc['LOC_ID'] ? "selected" : ""; ?>><?php echo $loc['LOCATION']; ?></option>
      <?php endforeach; ?>
    </select>
    <?php if ($errors['location']) : ?>
      <small class="text-danger"><?php echo $errors['location']; ?></small>
    <?php endif; ?>
  </div>
  <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
    <div class="mt-4">
      <label for="new_location" class="fw-semibold form-label">New Location</label>
      <input type="text" name="new_location" id="new_location" value="<?php echo $new_location; ?>" placeholder="Enter new location here" class="form-control" disabled>
      <small class="fw-semibold text-warning">*Only input if the location is not on the list</small>
      <?php if ($errors['new_location']) : ?>
        <small class="text-danger"><?php echo $errors['new_location']; ?></small>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="mt-4">
    <label for="start_date" class="form-label fw-semibold">Start and end dates</label>
    <div class="input-group">
      <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $event['START_DATE']; ?>" disabled>
      <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $event['END_DATE']; ?>" disabled>
    </div>
    <?php if ($errors['start_date'] || $errors['end_date']) : ?>
      <small class="text-danger"><?php echo $errors['start_date']; ?><?php ?></small>
    <?php endif; ?>
  </div>
  <div class="mt-4">
    <label for="days" class="form-label fw-semibold">Number of days</label>
    <input type="number" name="days" id="days" value="<?php echo $event['DAYS']; ?>" class="form-control" disabled>
    <small class="text-warning fw-semibold mt-1 d-inline-block">*Automatically calculated from start and finish dates</small>
  </div>
  <div class="mt-4">
    <label for="start_time" class="form-label fw-semibold">Start and end times</label>
    <div class="input-group">
      <input type="time" name="start_time" id="start_time" class="form-control" value="<?php echo $event['START_TIME']; ?>" disabled>
      <input type="time" name="end_time" id="end_time" class="form-control" value="<?php echo $event['END_TIME']; ?>" disabled>
    </div>
    <?php if ($errors['start_time'] || $errors['end_time']) : ?>
      <small class="text-danger"><?php echo $errors['start_time']; ?><?php ?></small>
    <?php endif; ?>
  </div>
  <div class="mt-4">
    <label for="duration" class="form-label fw-semibold">Duration</label>
    <input type="number" name="duration" id="duration" value="<?php echo $event['DURATION']; ?>" class="form-control" disabled>
    <small class="text-warning fw-semibold mt-1 d-inline-block">*Automatically calculated from start and end times</small>
  </div>
  <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
    <div id="updateBtnGroup" class="d-none gap-3 mt-4 justify-content-end">
      <button id="updateBtn" type="submit" class="btn btn-primary"><i class="fas fa-pencil"></i> Update event</button>
      <button id="cancelBtn" type="button" class="btn btn-dark"><i class="fas fa-times"></i> Cancel edit</button>
    </div>
  <?php endif; ?>
</form>
<?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] == 'RLS01') : ?>
  <div id="editBtnGroup" class="d-flex gap-3 mt-4 justify-content-end">
    <button id="editBtn" type="button" class="btn btn-outline-primary"><i class="fas fa-pencil"></i> Edit event</button>
    <form action="" method="POST">
      <input type="hidden" name="delete_event" value="">
      <input type="hidden" name="evt_id" value="<?php echo $evt_id; ?>">
      <button id="deleteBtn" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
    </form>
  </div>
<?php endif; ?>

<script type="text/javascript" src="public/js/events/view.script.js"></script>

<?php
include_once __DIR__ . '/../partials/_footer.php';
?>