<?php

require_once __DIR__ . '/../connection.class.php';

class Matrix extends Connection
{
  protected function changeMtxId($mtx_id, $new_mtx_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("UPDATE matrix SET MTX_ID = :new_mtx_id WHERE MTX_ID = :mtx_id");

    $stmt->bindValue('mtx_id', $mtx_id);
    $stmt->bindValue('new_mtx_id', $new_mtx_id);

    if (!$stmt->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      $stmt = null;
      exit();
    }

    $stmt = null;
  }

  protected function getPrevMTXID($mtx_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT MTX_ID FROM matrix WHERE MTX_ID < :mtx_id ORDER BY MTX_ID DESC LIMIT 1");

    $stmt->bindValue('mtx_id', $mtx_id);

    if (!$stmt->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      $stmt = null;
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
    return $result['MTX_ID'];
  }

  protected function getNextMTXID($mtx_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT MTX_ID FROM matrix WHERE MTX_ID > :mtx_id ORDER BY MTX_ID ASC LIMIT 1");

    $stmt->bindValue('mtx_id', $mtx_id);

    if (!$stmt->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      $stmt = null;
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
    return $result['MTX_ID'];
  }

  protected function getDepartmentContent($mtx_id)
  {
    $connection = new Connection();

    $stmt = $connection->pdo->prepare("SELECT * FROM matrix WHERE MTX_ID = :mtx_id");

    $stmt->bindValue('mtx_id', $mtx_id);

    if (!$stmt->execute()) {
      $stmt = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

    return $result;
  }

  protected function getAllDepartments($page = 1, $lists_shown = 10, $search = '', $colomIndex = null, $direction = null)
{
    $connection = new Connection();
    $offset = ($page - 1) * $lists_shown;

    // Query base statement
    $sql = "SELECT departments.DPT_ID, departments.DEPARTMENT 
            FROM departments 
            WHERE (:search IS NULL OR departments.DEPARTMENT LIKE CONCAT('%', :search, '%') OR departments.DPT_ID LIKE CONCAT('%', :search, '%'))";

    // Add sorting if colomIndex and direction are provided
    if ($colomIndex !== null && $direction !== null) {
        // Validate and sanitize column index
        $validColumns = array('DPT_ID', 'DEPARTMENT'); // List of valid columns for sorting
        $colomIndex = in_array($colomIndex, $validColumns) ? $colomIndex : 'DPT_ID'; // Default to 'DPT_ID' if invalid
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC'; // Sanitize direction

        // Append sorting to SQL query with departments. prefix
        $sql .= " ORDER BY departments.$colomIndex $direction";
    } else {
        // Default sorting if not provided
        $sql .= " ORDER BY departments.DPT_ID";
    }

    // Prepare SQL statement
    $stmt = $connection->pdo->prepare($sql);
    $stmt->bindValue('search', $search);

    // Execute SQL statement
    if (!$stmt->execute()) {
        $stmt = null;
        header("HTTP/1.1 500 Internal Server Error");
        exit();
    }

    // Fetch results
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    return $departments;
}


  protected function getCurrDepartment($dpt_id)
  {
    $connection = new Connection();

    $stmt = $connection->pdo->prepare("SELECT * FROM departments WHERE DPT_ID = :dpt_id");

    $stmt->bindValue('dpt_id', $dpt_id);

    if (!$stmt->execute()) {
      $stmt = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $department = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

    return $department;
  }

  public function getAllDepartmentsCount()
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("SELECT COUNT(DPT_ID) FROM departments");

    if (!$stmt->execute()) {
      $stmt = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $departmentsCount = $stmt->fetch(PDO::FETCH_NUM);
    $stmt = null;

    return $departmentsCount[0];
  }

  public function getDepartmentNameById($dpt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT DEPARTMENT from DEPARTMENTS WHERE DPT_ID = :dpt_id");

    $statement->bindValue("dpt_id", $dpt_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getDepartmentById($dpt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * from DEPARTMENTS WHERE DPT_ID = :dpt_id");

    $statement->bindValue("dpt_id", $dpt_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getMaterialById($m_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT M_ID, TITLE, DESCRIPTION from materials WHERE M_ID = :m_id");

    $statement->bindValue("m_id", $m_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllDepartmentMaterials($dpt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT M_ID, TITLE, DESCRIPTION FROM materials WHERE DPT_ID = :dpt_id");

    $statement->bindValue("dpt_id", $dpt_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getAllDepartmentChapters($m_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT DCH_ID, TITLE, DESCRIPTION FROM department_chapters WHERE M_ID = :m_id");

    $statement->bindValue("m_id", $m_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getDepartmentChapterById($tch_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT DCH_ID, TITLE, DESCRIPTION FROM department_chapters WHERE DCH_ID = :tch_id");

    $statement->bindValue("tch_id", $tch_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getDepartmentMaterialById($m_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT M_ID, TITLE, DESCRIPTION FROM materials WHERE M_ID = :m_id");

    $statement->bindValue("m_id", $m_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../../matrix.php?error=stmterror");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getAllDepartmentSubchaptersContents($dpt_id, $year)
{
    $connection = new Connection();

    // Base Query
    $sql = "SELECT MTX_ID, TYPE, FILE, UPLOAD_YEAR FROM matrix WHERE dpt_id = :dpt_id";

    // Tambahkan filter berdasarkan tahun jika diberikan
    if ($year !== null) {
        $sql .= " AND UPLOAD_YEAR = :year";
    }

    $statement = $connection->pdo->prepare($sql);
    $statement->bindValue(":dpt_id", $dpt_id);

    if ($year !== null) {
        $statement->bindValue(":year", $year);
    }

    if (!$statement->execute()) {
        header("HTTP/1.1 500 Internal Server Error");
        exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


  public function getAllYears(){
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT DISTINCT UPLOAD_YEAR AS YEAR FROM matrix ORDER BY YEAR;");

    if (!$stmt->execute()) {
      $stmt = '';
      header('HTTP/1.1 500 Internal Server Error');
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  public function filterDepartments($page, $lists_shown, $search)
  {
    $offset = ((int)$page - 1) * $lists_shown;

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT departments.DPT_ID, departments.DEPARTMENT FROM departments WHERE (:search IS NULL OR (departments.DEPARTMENT LIKE CONCAT('%', :search, '%') OR departments.DPT_ID LIKE CONCAT('%', :search, '%'))) LIMIT $lists_shown OFFSET $offset");

    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../matrix.php?error=stmterror");
      exit();
    }

    $departments = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $departments;
  }

  protected function createNewSubchapter($dch_id, $sch_id, $subchapter_title)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO department_subchapters (DSCH_ID, DCH_ID, TITLE) VALUES (:sch_id, :dch_id, :subchapter_title)');

    $statement->bindValue("sch_id", $sch_id);
    $statement->bindValue("dch_id", $dch_id);
    $statement->bindValue("subchapter_title", $subchapter_title);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /matrix.php?error=stmterror");
      exit();
    }

    $statement = null;
  }

  protected function createNewCoreContent($mtx_id, $dpt_id, $file_type, $file, $upload_year)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO matrix (MTX_ID, DPT_ID, TYPE, FILE, UPLOAD_YEAR) VALUES (:mtx_id, :dpt_id, :file_type, :file, :upload_year)');

    $statement->bindValue("mtx_id", $mtx_id);
    $statement->bindValue("dpt_id", $dpt_id);
    $statement->bindValue("file_type", $file_type);
    $statement->bindValue("file", $file);
    $statement->bindValue("upload_year", $upload_year);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateMaterials($materials_update)
  {
    $connection = new Connection();
    $statement = '';
    foreach ($materials_update as $material) {
      $m_id = explode(';', $material);
      $m_id = $m_id[0];
      $title = explode(';', $material);
      $title = $title[1];

      $statement = $connection->pdo->prepare("UPDATE materials SET TITLE = :title WHERE M_ID = :m_id");

      $statement->bindValue('m_id', $m_id);
      $statement->bindValue('title', $title);

      if (!$statement->execute()) {
        $statement = '';
        header("Location: /matrix.php?error=stmterror");
        exit();
      }
    }
    $statement = '';
  }

  protected function addNewMaterials($dpt_id, $materials)
  {
    $connection = new Connection();
    $statement = '';
    foreach ($materials as $title) {
      $raw_m_id = $connection->getLatestMaterialsId();
      $m_id = empty($raw_m_id) ? "M0000" : "M" . str_pad(strval((int)substr($raw_m_id, 1) + 1), 4, "0", STR_PAD_LEFT);

      $statement = $connection->pdo->prepare("INSERT INTO materials (M_ID, TITLE, DPT_ID) VALUES (:m_id, :title, :dpt_id)");

      $statement->bindValue('m_id', $m_id);
      $statement->bindValue('title', $title);
      $statement->bindValue('dpt_id', $dpt_id);

      if (!$statement->execute()) {
        $statement = '';
        header("Location: /matrix.php?error=stmterror");
        exit();
      }
    }
    $statement = '';
  }

  protected function createNewDepartmentChapter($dpt_id, $m_id, $dch_id, $title, $description)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO department_chapters (DCH_ID, TITLE, DESCRIPTION, M_ID) VALUES (:dch_id, :title, :description, :m_id)');

    $statement->bindValue("dch_id", $dch_id);
    $statement->bindValue("m_id", $m_id);
    $statement->bindValue("title", $title);
    $statement->bindValue("description", $description);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /matrix/chapters.php?dpt_id=" . $dpt_id . "&m_id=" . $m_id . "&error=stmterror");
      exit();
    }

    $statement = null;
  }

  protected function createNewDepartmentMaterial($dpt_id, $m_id, $title, $description)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO materials (M_ID, DPT_ID, TITLE, DESCRIPTION) VALUES (:m_id, :dpt_id, :title, :description)');

    $statement->bindValue("m_id", $m_id);
    $statement->bindValue("dpt_id", $dpt_id);
    $statement->bindValue("title", $title);
    $statement->bindValue("description", $description);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrentDepartmentChapter($dch_id, $title, $description)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('UPDATE department_chapters SET TITLE = :title, DESCRIPTION = :description WHERE DCH_ID = :dch_id');

    $statement->bindValue("dch_id", $dch_id);
    $statement->bindValue("title", $title);
    $statement->bindValue("description", $description);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrentDepartmentMaterial($m_id, $title, $description)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('UPDATE materials SET TITLE = :title, DESCRIPTION = :description WHERE M_ID = :m_id');

    $statement->bindValue("m_id", $m_id);
    $statement->bindValue("title", $title);
    $statement->bindValue("description", $description);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrentDepartmentSubchapter($sch_id, $subchapter_title)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('UPDATE department_subchapters SET TITLE = :subchapter_title WHERE DSCH_ID = :sch_id');

    $statement->bindValue("sch_id", $sch_id);
    $statement->bindValue("subchapter_title", $subchapter_title);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrDepartmentContent($mtx_id, $file_type, $file, $upload_year)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('UPDATE matrix SET FILE = :file, TYPE = :file_type WHERE MTX_ID = :mtx_id');

    $statement->bindValue("mtx_id", $mtx_id);
    $statement->bindValue("file_type", $file_type);
    $statement->bindValue("file", $file);
    //$statement->bindValue("upload_year", $upload_year);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function getLatestDepartmentContentChapterId($dpt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT DCH_ID FROM department_chapters ORDER BY DCH_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getLatestDepartmentContentMaterialId($dpt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT M_ID FROM materials ORDER BY M_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Bad Request");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getCurrentLatestSubchapterId()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT DSCH_ID FROM department_subchapters ORDER BY DSCH_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getCurrentLatestCoreContentId()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT MTX_ID FROM matrix ORDER BY MTX_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

// Check if result is valid
if ($result === false) {
  return null; // or handle the error as needed
}

return $result; 
  }

  protected function getCurrentSubchapters($dch_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT DSCH_ID, DCH_ID, TITLE FROM department_subchapters WHERE DCH_ID = :dch_id ORDER BY DSCH_ID");
    $statement->bindValue('dch_id', $dch_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function deleteCurrContent($mtx_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("DELETE FROM matrix WHERE MTX_ID = :mtx_id");

    $statement->bindValue('mtx_id', $mtx_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function deleteCurrDepartment($dpt_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("UPDATE departments SET STATUS = 0 WHERE DPT_ID = :dpt_id");
    $stmt->bindValue('dpt_id', $dpt_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  protected function deleteCurrMaterial($m_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("DELETE FROM materials WHERE M_ID = :m_id");

    $stmt->bindValue('m_id', $m_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  protected function deleteCurrChapter($tch_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("DELETE FROM department_chapters WHERE DCH_ID = :tch_id");

    $stmt->bindValue('tch_id', $tch_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  protected function deleteCurrSubchapter($dsch_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("DELETE FROM department_subchapters WHERE DSCH_ID = :dsch_id");

    $stmt->bindValue('dsch_id', $dsch_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  public function contentExists($dpt_id, $upload_year)
{
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT COUNT(*) FROM matrix WHERE DPT_ID = :dpt_id AND UPLOAD_YEAR = :upload_year');
    $statement->bindValue(':dpt_id', $dpt_id);
    $statement->bindValue(':upload_year', $upload_year);
    $statement->execute();

    return $statement->fetchColumn() > 0; // Returns true if count is greater than 0
}
}
