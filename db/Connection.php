<?php

class Connection
{
  private $pdo;
  public $pdo2; 
  public $pdo3;
  private static $url = 'localhost:8080';
  private static $dbname = 'kayaba_training_center';
  private static $dbname2 = 'lembur';
  private static $dbname3 = 'isd';
  private static $user = 'root';
  private static $password = '';

  public function __construct()
  {
    $dsn = "mysql:server=" . self::$url . ";dbname=" . self::$dbname;
    $this->pdo = new PDO($dsn, self::$user, self::$password);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dsn2 = "mysql:server=" . self::$url . ";dbname=" . self::$dbname2;
    $this->pdo2 = new PDO($dsn2, self::$user, self::$password);
    $this->pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dsn3 = "mysql:server=" . self::$url . ";dbname=" . self::$dbname3;
    $this->pdo3 = new PDO($dsn3, self::$user, self::$password);
    $this->pdo3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function get_users($page)
  {
    $offset = ($page - 1) * 25;
    $statement = $this->pdo->prepare("SELECT users.NPK, users.NAME, departments.COMPANY_DEPARTMENT_SECTION, trainings.TRAINING, trainings.ORG_ID FROM users INNER JOIN departments ON users.CDS_ID = departments.CDS_ID INNER JOIN trainings ON users.T_ID = trainings.T_ID ORDER BY users.NPK LIMIT 25 OFFSET $offset");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getNomor()
  {
    $statement = $this->pdo3->prepare("SELECT * FROM hp");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get_departments()
  {
    $statement = $this->pdo->prepare("SELECT * FROM departments");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get_trainings($page)
  {
    if ($page == -1) {
      $statement = $this->pdo->prepare('SELECT trainings.T_ID, trainings.TRAINING FROM trainings ORDER BY trainings.TRAINING');
    } else {
      $offset = ($page - 1) * 25;
      $statement = $this->pdo->prepare("SELECT trainings.T_ID, trainings.TRAINING, trainings.ORG_ID, trainings.START_DATE, trainings.END_DATE, trainings.DAYS FROM trainings ORDER BY trainings.T_ID LIMIT 25 OFFSET $offset");
    }
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get_user_trainings($npk)
  {
    $statement = $this->pdo->prepare("SELECT user_trainings.UT_ID, trainings.TRAINING, trainings.ORG_ID, trainings.START_DATE, trainings.END_DATE, trainings.DAYS, user_trainings.STATUS FROM user_trainings INNER JOIN trainings ON user_trainings.T_ID = trainings.T_ID WHERE user_trainings.NPK = '$npk' LIMIT 25 OFFSET 0");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get_user($id)
  {
    $statement = $this->pdo->prepare("SELECT users.NPK, users.NAME, users.CDS_ID, departments.COMPANY_DEPARTMENT_SECTION, trainings.TRAINING, trainings.ORG_ID FROM users INNER JOIN departments ON users.CDS_ID = departments.CDS_ID INNER JOIN trainings ON users.T_ID = trainings.T_ID WHERE users.NPK = '$id'");
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public function search_users($user)
  {
    $statement = $this->pdo->prepare("SELECT users.NPK, users.NAME, departments.COMPANY_DEPARTMENT_SECTION, trainings.TRAINING, trainings.ORG_ID FROM users INNER JOIN departments ON users.CDS_ID = departments.CDS_ID INNER JOIN trainings ON users.T_ID = trainings.T_ID WHERE users.NAME LIKE '%$user%' ORDER BY users.NAME LIMIT 25 OFFSET 0");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function search_trainings($training)
  {
    $statement = $this->pdo->prepare("SELECT trainings.T_ID, trainings.TRAINING, trainings.ORG_ID, trainings.START_DATE, trainings.END_DATE, trainings.DAYS FROM trainings WHERE trainings.TRAINING LIKE '%$training%' ORDER BY trainings.T_ID LIMIT 25 OFFSET 0");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get_users_page_count()
  {
    $statement = $this->pdo->prepare("SELECT * FROM users");
    $statement->execute();
    return floor($statement->rowCount() / 25);
  }

  private function create_department($id, $department)
  {
    $statement = $this->pdo->prepare("INSERT INTO departments (CDS_ID, COMPANY_DEPARTMENT_SECTION) VALUES (:id, :department)");
    $statement->bindValue("id", $id);
    $statement->bindValue("department", $department);
    return $statement->execute();
  }

  public function create_user($data)
  {
    if ($data['new_company_department_section']) {
      $this->create_department('CDS0073', $data['new_company_department_section']);
    }
    $npk = $data['npk'];
    $name = $data['first_name'] . ' ' . $data['last_name'];
    $cds_id = '';
    if ($data['new_company_department_section']) {
      $cds_id = 'CDS0073';
    } else {
      $cds_id = $data['company_department_section'];
    }
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
    $statement = $this->pdo->prepare("INSERT INTO users (NPK, NAME, T_ID, CDS_ID, PASSWORD) VALUES (:npk, :name, :t_id, :cds_id, :password)");
    $statement->bindValue('npk', $npk);
    $statement->bindValue('name', $name);
    $statement->bindValue('t_id', 'T0000');
    $statement->bindValue('cds_id', $cds_id);
    $statement->bindValue('password', $hashed_password);
    return $statement->execute();
  }
}

return new Connection();
