<?php
require_once __DIR__ . '/../connection.class.php';

class Roles extends Connection
{
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

    protected function getAllDepartments($sec_id, $sub_sec_id, $gender, $grade, $t_id)
    {
        $connection = new Connection();
        $statement = $connection->pdo->prepare("SELECT departments.DPT_ID, departments.DEPARTMENT FROM users INNER JOIN departments ON users.DPT_ID = departments.DPT_ID WHERE (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:gender IS NULL OR users.GENDER = :gender) AND (:grade IS NULL OR users.GRADE = :grade)   GROUP BY departments.DPT_ID ORDER BY departments.DEPARTMENT");

        $statement->bindValue("sec_id", $sec_id);
        $statement->bindValue("sub_sec_id", $sub_sec_id);
        $statement->bindValue("gender", $gender);
        $statement->bindValue("grade", $grade);
        $statement->bindValue("t_id", $t_id);

        if (!$statement->execute()) {
        $statement = null;
        header("Location: ../../index.php?err=stmterror");
        exit();
        }

        $departments = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement = null;

        return $departments;
    }

    protected function getAllSections($dpt_id, $sub_sec_id, $gender, $grade, $t_id)
    {
        $connection = new Connection();
        $statement = $connection->pdo->prepare("SELECT sections.SEC_ID, sections.SECTION FROM  users INNER JOIN sections ON sections.SEC_ID = users.SEC_ID WHERE (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:gender IS NULL OR users.GENDER = :gender) AND (:grade IS NULL OR users.GRADE = :grade)  GROUP BY sections.SEC_ID ORDER BY sections.SECTION");

        $statement->bindValue('dpt_id', $dpt_id);
        $statement->bindValue('sub_sec_id', $sub_sec_id);
        $statement->bindValue('gender', $gender);
        $statement->bindValue('grade', $grade);
        $statement->bindValue('t_id', $t_id);

        if (!$statement->execute()) {
        $statement = null;
        header("Location: ../../users.php?error=stmterror");
        exit();
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement = null;

        return $result;
    }
    protected function getAllSubsections($dpt_id, $sec_id, $gender, $grade, $t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT subsections.SUB_SEC_ID, subsections.SUBSECTION FROM  users INNER JOIN subsections ON subsections.SUB_SEC_ID = users.SUB_SEC_ID WHERE (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:gender IS NULL OR users.GENDER = :gender) AND (:grade IS NULL OR users.GRADE = :grade)  GROUP BY subsections.SUB_SEC_ID ORDER BY subsections.SUBSECTION");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function updateRoles($rls_id,$npk){
    $connection = new Connection();

    $statement = $connection->pdo->prepare("UPDATE users SET RLS_ID = :rls_id WHERE npk = :npk");

    $statement->bindValue('npk', $npk);
    $statement->bindValue('rls_id', $rls_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $statement = null;
  }

     
protected function getUsers($npk, $name, $role, $department, $section, $subsection, $direction, $colom, $search)
{
    // Valid columns with their respective table prefixes
    $validColumns = array(
        'NPK' => 'users.NPK',
        'NAME' => 'users.NAME',
        'COMPANY' => 'companies.COMPANY',
        'ROLE' => 'roles.ROLE',
        'DEPARTMENT' => 'departments.DPT_ID',
        'SECTION' => 'sections.SECTION',
        'SUBSECTION' => 'subsections.SUBSECTION'
    );

    // Valid directions
    $validDirections = array('asc', 'desc');

    // Set default values
    $defaultColumn = 'users.NPK';
    $defaultDirection = 'asc';

    // Validate column and direction
    if (!array_key_exists($colom, $validColumns)) {
        $colom = $defaultColumn;
    } else {
        $colom = $validColumns[$colom];
    }

    if (!in_array(strtolower($direction), $validDirections)) {
        $direction = $defaultDirection;
    }

    $connection = new Connection();

    // Prepare the SQL statement with dynamic ORDER BY clause
    $statement = $connection->pdo->prepare("SELECT 
        users.NPK, 
        users.NAME, 
        companies.COMPANY, 
        roles.ROLE, 
        roles.RLS_ID, 
        departments.DEPARTMENT, 
        sections.SECTION, 
        subsections.SUBSECTION
    FROM 
        users 
    INNER JOIN 
        companies ON companies.C_ID = users.C_ID 
    INNER JOIN 
        roles ON roles.RLS_ID = users.RLS_ID 
    INNER JOIN 
    departments ON departments.DPT_ID = users.DPT_ID 
LEFT JOIN 
    sections ON sections.SEC_ID = users.SEC_ID 
LEFT JOIN 
    subsections ON subsections.SUB_SEC_ID = users.SUB_SEC_ID 
WHERE 
    (:npk IS NULL OR users.NPK = :npk) 
    AND (:role IS NULL OR roles.RLS_ID = :role) 
    AND (:department IS NULL OR departments.DPT_ID = :department) 
    AND (:section IS NULL OR sections.sec_id = :section ) 
    AND (:subsection IS NULL OR subsections.sub_sec_id = :subsection) 
    AND (:search IS NULL OR (users.NAME LIKE CONCAT('%', :search ,'%') OR users.NPK LIKE CONCAT('%', :search, '%')))
ORDER BY 
    $colom $direction");
   // Bind values to the statement
    $statement->bindValue('npk', $npk);
    $statement->bindValue('name', $name);
    $statement->bindValue('role', $role);
    $statement->bindValue('department', $department);
    $statement->bindValue('section', $section);
    $statement->bindValue('subsection', $subsection);
    $statement->bindValue('search', $search);

    // Execute the statement and handle errors
    if (!$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        error_log("SQL Error: " . print_r($errorInfo, true));
        $statement = null;
        header("HTTP/1.1 500 Internal Server Error");
        exit();
    }

    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $users;
}

}
?>