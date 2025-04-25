<?php
require_once __DIR__ . '/roles-class.php';

class RolesController extends Roles
{
    public function getRoles()
    {
        return $this->getAllRoles();
    }

    public function getAllUsers($npk,$name,$role,$department,$section,$subsection,$direction,$colom,$search)
    {
        return $this->getUsers($npk,$name,$role,$department,$section,$subsection,$direction,$colom,$search);
    }

  public function getDepartments($sec_id, $sub_sec_id, $gender, $grade, $t_id)
  {
    return $this->getAllDepartments($sec_id, $sub_sec_id, $gender, $grade, $t_id);
  }

   public function getSections($dpt_id, $sub_sec_id, $gender, $grade, $t_id)
  {
    return $this->getAllSections($dpt_id, $sub_sec_id, $gender, $grade, $t_id);
  }

  public function getSubsections($dpt_id, $sec_id, $gender, $grade, $t_id)
  {
    return $this->getAllSubsections($dpt_id, $sec_id, $gender, $grade, $t_id);
  }

  public function getCompanies()
  {
    //return $this->getAllCompanies();
  }

  public function updateRoleUsers($rls_id,$npk){
    return $this->updateRoles($rls_id,$npk);
  }

}