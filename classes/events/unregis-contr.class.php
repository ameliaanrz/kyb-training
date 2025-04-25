<?php

class UnregisterParticipantController extends Event
{
  private $evt_id;
  private $registered_users;
  private $errors = array(
    'evt_id' => '',
    'registered_users' => ''
  );

  public function __construct($evt_id, $registered_users)
  {
    $this->evt_id = $evt_id;
    $this->registered_users = $registered_users;
  }

  public function unRegisterParticipants()
  {
    $this->emptyField();

    if (empty($this->errors['t_id']) && empty($this->errors['registered_users'])) {
      $this->unRegisterSomeParticipants($this->evt_id, $this->registered_users);
    }

    return $this->errors;
  }

  private function emptyField()
  {
    $errs = array();
    define("REQUIRED_INPUT_ERROR", "*This field is required");

    if (empty($this->evt_id)) {
      $errs['t_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->registered_users)) {
      $errs['training_name'] = REQUIRED_INPUT_ERROR;
    }

    $this->errors = array_merge($errs, $this->errors);
  }
}
