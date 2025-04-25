<?php

require_once __DIR__ . '/trainings.class.php';

class TrainingsUpdateController extends Trainings
{
  private $t_id;
  private $training_name;
  private $description;
  private $purpose;
  private $outline;
  private $duration_days;
  private $duration_hours;
  private $participant;
  private $company_purposes = array();
  private $participant_purposes = array();
  private $errors = array();

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

  public function updateTraining()
  {
    // error checks
    $this->notLongEnough();
    $this->emptyField();

    if (empty($this->errors)) {
      // update training
      $this->updateCurrentTraining($this->t_id, $this->training_name, $this->description, $this->purpose, $this->outline, $this->duration_days, $this->duration_hours, $this->participant);

      // remove previous benefits
      $this->removeAllPurposes($this->t_id);

      // create new benefits
      if (!empty($this->participant_purposes)) {
        $this->createParticipantBenefits($this->t_id, $this->participant_purposes);
      }

      if (!empty($this->company_purposes)) {
        $this->createCompanyBenefits($this->t_id, $this->company_purposes);
      }
    }

    return $this->errors;
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
    if(empty($this->participant_purposes)){
      $errs['participant_purposes'] = REQUIRED_INPUT_ERROR;
    }
    if(empty($this->company_purposes)){
      $errs['company_purposes'] = REQUIRED_INPUT_ERROR;
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
    define("STRLEN_ERROR_255", "*Characters length must be between 2 and 255");
    define("STRLEN_ERROR_500", "*Characters length must be between 4 and 500");
    define("STRLEN_ERROR_1000", "*Characters length must be between 4 and 1000");

    if (strlen($this->training_name) < 2 || strlen($this->training_name) > 255) {
      $errs['training_name'] = STRLEN_ERROR_255;
    }
    if (strlen($this->description) < 4 || strlen($this->description) > 1000) {
      $errs['description'] =  STRLEN_ERROR_1000;
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
    if (isset($this->company_purposes) && !empty($this->company_purposes) && is_array($this->company_purposes)) {
      foreach ($this->company_purposes as $purpose) {
        if (strlen($purpose) < 4 || strlen($purpose) > 500) {
          $errors['company_purposes'] = STRLEN_ERROR_500;
          break;
        }
      }
    }
    if (isset($this->company_purposes) && !empty($this->participant_purposes) && is_array($this->participant_purposes)) {
      foreach ($this->participant_purposes as $purpose) {
        if (strlen($purpose) < 4 || strlen($purpose) > 500) {
          $errors['participant_purposes'] = STRLEN_ERROR_500;
          break;
        }
      }
    }

    $this->errors = array_merge($this->errors, $errs);
  }
}
