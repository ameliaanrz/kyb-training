<?php

require_once __DIR__ . '/trainings.class.php';

class CreateTrainingController extends Trainings
{
  private $t_id;
  private $training_name;
  private $description;
  private $purpose;
  private $company_purposes;
  private $participant_purposes;
  private $outline;
  private $duration_days;
  private $duration_hours;
  private $participant;

  private $errors = array(
    't_id' => '',
    'training_name' => '',
    'description' => '',
    'purpose' => '',
    'company_purposes' => '',
    'participant_purposes' => '',
    'outline' => '',
    'duration_days' => '',
    'duration_hours' => '',
    'participant' => ''
);
  public function __construct($t_id, $training_name, $description, $purpose, $company_purposes, $participant_purposes, $outline, $duration_days, $duration_hours, $participant)
  {
    $this->t_id = $t_id;
    $this->training_name = $training_name;
    $this->description = $description;
    $this->purpose = $purpose;
    $this->company_purposes = $company_purposes;
    $this->participant_purposes = $participant_purposes;
    $this->outline = $outline;
    $this->duration_days = $duration_days;
    $this->duration_hours = $duration_hours;
    $this->participant = $participant;
  }

  public function createTraining()
  {
    // error checks
    $this->notLongEnough();
    $this->emptyField();

    if (empty($this->errors['t_id']) && empty($this->errors['training_name']) && empty($this->errors['description']) && empty($this->errors['purpose']) && empty($this->errors['company_purposes']) && empty($this->errors['participant_purposes']) && empty($this->errors['outline'])&& empty($this->errors['duration_days'])&& empty($this->errors['duration_hours'])&& empty($this->errors['participant'])) {
      $this->createNewTraining($this->t_id, $this->training_name, $this->description, $this->purpose, $this->company_purposes, $this->participant_purposes, $this->outline, $this->duration_days, $this->duration_hours, $this->participant);
  }

    return $this->errors;
  }

  public function getAllOrganizers()
  {
    return $this->getOrganizers();
  }

  private function emptyField()
  {
    $errs = array();
    define("REQUIRED_INPUT_ERROR", "*This field is required");

    if (empty($this->t_id)) {
      $errs['t_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->training_name)) {
      $errs['training_name'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->description)) {
      $errs['description'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->purpose)) {
      $errs['purpose'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->company_purposes)) {
      $errs['company_purposes'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->participant_purposes)) {
      $errs['participant_purposes'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->outline)) {
      $errs['outline'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->duration_days)) {
      $errs['duration_days'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->duration_hours)) {
      $errs['duration_hours'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->participant)) {
      $errs['participant'] = REQUIRED_INPUT_ERROR;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function notLongEnough()
  {
    $errs = array();
    define("STRLEN_ERROR_50", "*Characters length must be between 4 and 50");
    define("STRLEN_ERROR_255", "*Characters length must be between 4 and 255");
    define("STRLEN_ERROR_500", "*Characters length must be between 4 and 500");
    define("STRLEN_ERROR_1000", "*Characters length must be between 4 and 1000");

    if (strlen($this->training_name) < 2 || strlen($this->training_name) > 255) {
      $errs['training_name'] = "*Characters length must be between 2 and 255";
    }
    if (!empty($this->company_purposes)) {
      foreach ($this->company_purposes as $pur) {
        if (strlen($pur) < 4 || strlen($pur) > 500) {
          $errs['company_purposes'] = STRLEN_ERROR_500;
          break;
        }
      }
    }
    if (!empty($this->participant_purposes)) {
      foreach ($this->participant_purposes as $pur) {
        if (strlen($pur) < 4 || strlen($pur) > 500) {
          $errs['participant_purposes'] = STRLEN_ERROR_500;
          break;
        }
      }
    }
    if (strlen($this->description) < 4 || strlen($this->description) > 1000) {
      $errs['description'] = STRLEN_ERROR_1000;
    }
    if (strlen($this->purpose) < 4 || strlen($this->purpose) > 1000) {
      $errs['purpose'] = STRLEN_ERROR_1000;
    }
    if (strlen($this->outline) < 4 || strlen($this->outline) > 1000) {
      $errs['outline'] = STRLEN_ERROR_1000;
    }
    if (strlen($this->participant) < 4 || strlen($this->participant) > 1000) {
      $errs['participant'] = STRLEN_ERROR_1000;
    }
    $this->errors = array_merge($this->errors, $errs);
  }
}
