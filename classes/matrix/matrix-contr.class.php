<?php

require_once __DIR__ . '/matrix.class.php';

class MatrixController extends Matrix
{
  public function getDepartments($page = 1, $lists_shown = 10, $search = '',$colomIndex,$direction)
  {
    $departments = $this->getAllDepartments($page, $lists_shown, $search,$colomIndex,$direction);
    if (empty($departments)) {
      
      return false;
    }

    return $departments;
  }

  public function getDepartment($dpt_id)
  {
    $department = $this->getCurrDepartment($dpt_id);
    if (empty($department)) {
      return false;
    }

    return $department;
  }
}
