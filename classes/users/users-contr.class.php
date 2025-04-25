<?php

require_once __DIR__ . '/users.class.php';

class UsersController extends User
{
  public function getFilterNames($dpt_id,  $t_id, $npk, $org_id)
  {
    return $this->getAllFilterNames($dpt_id, $t_id, $npk, $org_id);
  }

  public function getEvents($npk,$dpt_id, $t_id, $org_id, $start_date, $end_date, $approved,$grade,$gender, $completed, $search, $colomIndex,$direction)
  {
    return $this->getAllEvents($npk,$dpt_id, $t_id, $org_id, $start_date, $end_date, $approved,$grade,$gender, $completed, $search, $colomIndex,$direction);
  }

  public function getPurpose($npk,$dpt_id, $t_id, $evt_id, $org_id, $description, $purpose, $start_date, $end_date, $approved, $approved_dept, $grade,$gender, $completed, $search, $colomIndex,$direction, $month, $year)
  {
    return $this->getPurposes($npk,$dpt_id, $t_id, $evt_id, $org_id, $description, $purpose, $start_date, $end_date, $approved, $approved_dept, $grade,$gender, $completed, $search, $colomIndex,$direction, $month, $year);
  }

  public function getYears()
  {
    return $this->getAllYears();
  }

  public function getMonths()
  {
    $result = $this->getAllMonths();
    return empty($result) ? false : $result;
  }

  public function getCompletions($npk)
  {
    return $this->getAllCompletions($npk);
  }

  public function getApprovals($npk)
  {
    return $this->getAllApprovals($npk);
  }

  public function getFilters()
  {
    return $this->getAllFilters();
  }

  public function getUsers($dpt_id, $sec_id, $sub_sec_id, $gender, $grade, $t_id, $search,$colomIndex,$direction)
  {
    return $this->getAllUsers($dpt_id, $sec_id, $sub_sec_id, $gender, $grade, $t_id, $search,$colomIndex,$direction);
  }

  public function getUser($npk)
  {
    return $this->getUserById($npk);
  }

  public function getDepartments($sec_id, $sub_sec_id, $gender, $grade, $t_id)
  {
    return $this->getAllDepartments($sec_id, $sub_sec_id, $gender, $grade, $t_id);
  }

  public function getFilteredDepartments($training)
  {
    return $this->getAllFilteredDepartments($training);
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
    return $this->getAllCompanies();
  }

  public function getGrades($dpt_id, $sec_id, $sub_sec_id, $gender, $t_id)
  {
    return $this->getAllGrades($dpt_id, $sec_id, $sub_sec_id, $gender, $t_id);
  }

  public function getGenders($dpt_id, $sec_id, $sub_sec_id, $grade, $t_id)
  {
    return $this->getAllGenders($dpt_id, $sec_id, $sub_sec_id, $grade, $t_id);
  }

  public function getFilteredTrainings($dpt_id)
  {
    return $this->getAllFilteredTrainings($dpt_id);
  }

  public function getTrainings($npk = null, $dpt_id = null, $sec_id = null, $sub_sec_id = null, $gender = null, $grade = null, $org_id = null)
  {
    return $this->getAllTrainings($npk, $dpt_id, $sec_id, $sub_sec_id, $gender, $grade, $org_id);
  }

  public function getTraining($t_id)
  {
    return $this->getTrainingById($t_id);
  }

  public function getDepartment($dpt_id)
  {
    return $this->getDepartmentById($dpt_id);
  }

  public function getUserOrganizers($npk, $t_id = null)
  {
    return $this->getAllOrganizers($npk, $t_id);
  }

  public function filterTrainings($page, $lists_shown, $npk, $organizer, $search, $approval, $completion)
  {
    return $this->filterAllTrainings($page, $lists_shown, $npk, $organizer, $search, $approval, $completion);
  }

  public function getTrainingEvent($page, $lists_shown, $npk)
  {
    return $this->getTrainingEventByNpk($page, $lists_shown, $npk);
  }

  public function getFilteredUsers($page, $lists_shown, $c_id, $dpt_id, $sec_id, $subsec_id, $grade, $gender, $t_id, $search)
  {
    return $this->getAllFilteredUsers($page, $lists_shown, $c_id, $dpt_id, $sec_id, $subsec_id, $grade, $gender, $t_id, $search);
  }

  public function getCompanyPurposes($t_id)
  {
    $training = $this->getAllCompanyPurposes($t_id);
    if (empty($training)) {
      return false;
    }

    return $training;
  }

  public function getParticipantsPurposes($t_id)
  {
    $training = $this->getAllParticipantspurposes($t_id);
    if (empty($training)) {
      return false;
    }

    return $training;
  }
}
