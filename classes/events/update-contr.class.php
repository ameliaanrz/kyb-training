<?php

class UpdateEventController extends Event
{
  private $evt_id;
  private $start_date;
  private $end_date;
  private $days;
  private $start_time;
  private $end_time;
  private $duration;
  private $activation;
  private $training_name;
  private $organizer;
  private $new_organizer;
  private $trainer;
  private $new_trainer;
  private $location;
  private $new_location;
  private $errors = array(
    'evt_id' => '',
    'start_date' => '',
    'end_date' => '',
    'days' => '',
    'start_time' => '',
    'end_time' => '',
    'duration' => '',
    'training_name' => '',
    'organizer' => '',
    'new_organizer' => '',
    'trainer' => '',
    'new_trainer' => '',
    'location' => '',
    'new_location' => ''
  );

  public function __construct($evt_id, $training_name, $organizer, $new_organizer, $trainer, $new_trainer, $location, $new_location, $start_date, $end_date, $days, $start_time, $end_time, $duration, $activation)
  {
    $this->evt_id = $evt_id;
    $this->start_date = $start_date;
    $this->end_date = $end_date;
    $this->days = $days;
    $this->start_time = $start_time;
    $this->end_time = $end_time;
    $this->duration = $duration;
    $this->activation = $activation;
    $this->training_name = $training_name;
    $this->organizer = $organizer;
    $this->new_organizer = $new_organizer;
    $this->trainer = $trainer;
    $this->new_trainer = $new_trainer;
    $this->location = $location;
    $this->new_location = $new_location;
  }

  public function updateEvent()
  {
    $this->invalidTimes();
    $this->notLongEnough();
    $this->emptyField();

    if (empty($this->errors['evt_id']) && empty($this->errors['training_name']) && empty($this->errors['organizer']) && empty($this->errors['new_organizer']) && empty($this->errors['trainer']) && empty($this->errors['new_trainer']) && empty($this->errors['location']) && empty($this->errors['new_location']) && empty($this->errors['start_date']) && empty($this->errors['end_date']) && empty($this->errors['days']) && empty($this->errors['start_time']) && empty($this->errors['end_time']) && empty($this->errors['duration'])) {
      $this->updateCurrEvent($this->evt_id, $this->training_name, $this->organizer, $this->new_organizer, $this->trainer, $this->new_trainer, $this->location, $this->new_location, $this->start_date, $this->end_date, $this->days, $this->start_time, $this->end_time, $this->duration, $this->activation);
    }

    return $this->errors;
  }

  private function notLongEnough()
  {
    $errs = array();
    define("STRLEN_ERROR_50", "*Characters length must be between 4 and 50");

    if (!empty($this->new_organizer) && (strlen($this->new_organizer) < 4 || strlen($this->new_organizer) > 50)) {
      $errs['new_organizer'] = STRLEN_ERROR_50;
    }

    if (!empty($this->new_location) && (strlen($this->new_location) < 4 || strlen($this->new_location) > 50)) {
      $errs['new_location'] = STRLEN_ERROR_50;
    }

    if (!empty($this->new_trainer) && (strlen($this->new_trainer) < 4 || strlen($this->new_trainer) > 50)) {
      $errs['new_trainer'] = STRLEN_ERROR_50;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function emptyField()
  {
    $errs = array();
    define("REQUIRED_INPUT_ERROR", "*This field is required");

    if (empty($this->evt_id)) {
      $errs['evt_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->training_name)) {
      $errs['training_name'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->start_date)) {
      $errs['start_date'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->end_date)) {
      $errs['end_date'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->days)) {
      $errs['days'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->start_time)) {
      $errs['start_time'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->end_time)) {
      $errs['end_time'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->duration)) {
      $errs['duration'] = REQUIRED_INPUT_ERROR;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function invalidTimes()
  {
    $errs = array();
    if ($this->end_date <= $this->start_date || $this->days <= 0) {
      $errs['start_date'] = '*End date couldn\'t be smaller than or equals to start date';
    }
    if ($this->end_time <= $this->start_time || $this->duration <= 0) {
      $errs['start_time'] = '*Finish time couldn\'t be smaller than or equals to start time';
    }
    $this->errors = array_merge($this->errors, $errs);
  }
}
