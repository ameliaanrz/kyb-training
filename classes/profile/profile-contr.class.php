<?php

class ProfileController extends Profile
{
  private $npk;

  public function __construct($npk)
  {
    $this->npk = $npk;
  }

  public function getProfileData()
  {
    if ($this->emptyData()) {
      header("Location: ../../index.php?err=emptydata");
      exit();
    }

    return $this->getAdminProfile($this->npk);
  }

  public function getDepartments()
  {
    return $this->getAllDepartments();
  }

  public function getRoles()
  {
    return $this->getAllRoles();
  }

  private function emptyData()
  {
    return empty($this->npk);
  }
}
