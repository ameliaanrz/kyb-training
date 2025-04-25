<?php

class RegisterController extends Register
{
  private $name;
  private $npk;
  private $password;
  private $password_confirmation;
  private $errors = array(
    'name' => '',
    'npk' => '',
    'password' => '',
    'password_confirmation' => '',
  );

  public function __construct($name, $npk, $password, $password_confirmation)
  {
    $this->name = $name;
    $this->npk = $npk;
    $this->password = $password;
    $this->password_confirmation = $password_confirmation;
  }

  public function registerNewUser()
  {
    /**
     * note:
     * put the most prioritized error at bottom
     */
    $this->notMatchingPwd();
    $this->invalidPassword();
    $this->invalidName();
    $this->invalidNpk();
    $this->existingNpk();
    $this->emptyInput();

    if (!$this->errors['name'] && !$this->errors['npk'] && !$this->errors['password'] && !$this->errors['password_confirmation']) {
      $this->createNewUser($this->npk, $this->name, $this->password);
    }

    return $this->errors;
  }

  private function emptyInput()
  {
    define('REQUIRED_INPUT_ERROR', "*This field is required");
    $result = false;
    $errs = array();

    if (empty($this->name)) {
      $errs['name'] = REQUIRED_INPUT_ERROR;
      $result = true;
    }

    if (empty($this->npk)) {
      $errs['npk'] = REQUIRED_INPUT_ERROR;
      $result = true;
    }

    if (empty($this->password)) {
      $errs['password'] = REQUIRED_INPUT_ERROR;
      $result = true;
    }

    if (empty($this->password_confirmation)) {
      $errs['password_confirmation'] = REQUIRED_INPUT_ERROR;
      $result = true;
    }

    $this->errors = array_merge($this->errors, $errs);

    return $result;
  }

  private function existingNpk()
  {
    $result = false;
    if ($this->npkExists($this->npk)) {
      $this->errors['npk'] = "*NPK already exists";
      $result = true;
    }
    return $result;
  }

  private function invalidNpk()
  {
    $result = false;
    if (strlen($this->npk) !== 6) {
      $this->errors['npk'] = "*NPK must be exactly 6 characters long";
      $result = true;
    }
    return $result;
  }

  private function invalidName()
  {
    $result = false;
    if (strlen($this->name) < 4 || strlen($this->name) > 50) {
      $this->errors['name'] = "*Name must be at least 4 and not more than 50 characters long";
      $result = true;
    }
    return $result;
  }

  private function invalidPassword()
  {
    $result = false;
    if (strlen($this->password) < 8 || strlen($this->password) > 50) {
      $this->errors['password'] = "*Password must be at least 8 and not more than 50 characters long";
      $result = true;
    }
    return $result;
  }

  private function notMatchingPwd()
  {
    $result = false;
    if ($this->password && $this->password_confirmation && strcmp($this->password, $this->password_confirmation) !== 0) {
      $this->errors['password_confirmation'] = "*Password and password confirmation don&apos;t match";
      $result = true;
    }
    return $result;
  }
}
