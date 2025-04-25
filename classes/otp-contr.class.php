<?php

require_once __DIR__ . '/../classes/otp.class.php'; // Ensure this path is correct

class ReminderController extends OTP

{
  private $otp;
  private $otp_key;
  private $npk;
  private $errors = array(
    'otp' => ''
  );

  public function __construct($otp, $otp_key)
  {
    $this->otp = $otp;
    $this->otp_key = $otp_key;
   }

  public function loginOTP()
  {
    /**
     * note:
     * put the most prioritized error at bottom
     */
    //$this->badCreds();
    //$this->invalidNpk();
    //$this->CheckRoles();
    $this->unmatchingOtp();
    $this->emptyInput();

    if (!$this->errors['otp']) {
      $this->authenticateOtp($this->otp);
    }

    return $this->errors;
  }

  private function unmatchingOtp()
  {
    $err = array();
    if (strcmp($this->otp, $this->otp_key) !== 0) {
      $err['otp'] = "*OTP doesn&apos;t match";
    }

    $this->errors = array_merge($this->errors, $err);
  }

  private function emptyInput()
  {
    define("REQUIRED_INPUT_ERROR", "*This field is required");

    $result = false;
    $errs = array();

    if (empty($this->otp)) {
      $errs['otp'] = REQUIRED_INPUT_ERROR;
      $result = true;
    }

    $this->errors = array_merge($this->errors, $errs);

    return $result;
  }

  /*private function CheckRoles()
  {
    if (!$this->checkRole($this->npk)) {
      $this->errors['npk'] = '*You are not authorized to access this page';
    }
  }*/
}
