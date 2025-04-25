<?php

class LoginController extends Login
{
  private $npk;
  private $password;
  private $captcha;
  private $captcha_key;
  private $errors = array(
    'npk' => '',
    'password' => '',
    'captcha' => ''
  );

  public function __construct($npk, $password, $captcha, $captcha_key)
  {
    $this->npk = $npk;
    $this->password = $password;
    $this->captcha = $captcha;
    $this->captcha_key = $captcha_key;
  }

  public function loginUser()
  {
    /**
     * note:
     * put the most prioritized error at bottom
     */
    $this->badCreds();
    $this->invalidNpk();
    //$this->CheckRoles();
    $this->unmatchingCaptcha();
    $this->emptyInput();

    if (!$this->errors['npk'] && !$this->errors['password'] && !$this->errors['captcha']) {
      $this->authenticateUser($this->npk);
    }

    return $this->errors;
  }

  private function unmatchingCaptcha()
  {
    $err = array();
    if (strcmp($this->captcha, $this->captcha_key) !== 0) {
      $err['captcha'] = "*Captcha doesn&apos;t match";
    }

    $this->errors = array_merge($this->errors, $err);
  }

  private function emptyInput()
  {
    define("REQUIRED_INPUT_ERROR", "*This field is required");

    $result = false;
    $errs = array();

    if (empty($this->npk)) {
      $errs['npk'] = REQUIRED_INPUT_ERROR;
      $result = true;
    }

    if (empty($this->password)) {
      $errs['password'] = REQUIRED_INPUT_ERROR;
      $result = true;
    }

    if (empty($this->captcha)) {
      $errs['captcha'] = REQUIRED_INPUT_ERROR;
      $result = true;
    }

    $this->errors = array_merge($this->errors, $errs);

    return $result;
  }

  private function invalidNpk()
  {
    $result = false;
    if (strlen($this->npk) !== 5) {
      $this->errors['npk'] = '*NPK must be exactly 6 or 7 characters long';
      $result = true;
    }
    return $result;
  }

  private function badCreds()
  {
    if (!$this->checkAuth($this->npk, $this->password)) {
      $this->errors['npk'] = '*Bad credentials';
    }
  }

  /*private function CheckRoles()
  {
    if (!$this->checkRole($this->npk)) {
      $this->errors['npk'] = '*You are not authorized to access this page';
    }
  }*/
}
