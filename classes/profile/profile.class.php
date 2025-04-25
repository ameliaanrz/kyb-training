<?php

class Profile extends Connection
{
  protected function getAdminProfile($npk)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT NPK,NAME, (SELECT roles.ROLE FROM roles WHERE users.RLS_ID = roles.RLS_ID) AS ROLE, (SELECT companies.COMPANY FROM companies WHERE users.C_ID = companies.C_ID) AS COMPANY, (SELECT departments.DEPARTMENT FROM departments WHERE departments.DPT_ID = users.DPT_ID) AS DEPARTMENT, (SELECT sections.SECTION FROM sections WHERE users.SEC_ID = sections.SEC_ID) AS SECTION, (SELECT subsections.SUBSECTION FROM subsections WHERE users.SUB_SEC_ID = subsections.SUB_SEC_ID) AS SUBSECTION FROM  users WHERE NPK = :npk');

    $statement->bindValue("npk", $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../index.php?err=stmterror");
      exit();
    }

    $adminProfile = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $adminProfile;
  }

  protected function getAllDepartments()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * FROM departments");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../index.php?err=stmterror");
      exit();
    }

    $departments = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $departments;
  }

  protected function getAllRoles()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * FROM roles");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../index.php?err=stmterror");
      exit();
    }

    $roles = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $roles;
  }
}
