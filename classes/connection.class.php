<?php

class Connection
{
  protected $pdo;
  protected $pdo2;
  protected $pdo3;
  const DB_URL = "localhost";
  const DB_PORT = "3306";
  const DB_NAME = "kayaba_training_center";
  const DB_NAME_2 = "lembur";
  const DB_NAME_3 = "isd";
  const DB_USER = "root";
  const DB_PASSWORD = "";

  public function __construct()
  {
    try {
      $dsn = "mysql:host=" . self::DB_URL . ";port=" .self::DB_PORT . ";dbname=" . self::DB_NAME;
      $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASSWORD);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $dsn2 = "mysql:host=" . self::DB_URL . ";port=" . self::DB_PORT . ";dbname=" . self::DB_NAME_2;
      $this->pdo2 = new PDO($dsn2, self::DB_USER, self::DB_PASSWORD);
      $this->pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $dsn3 = "mysql:host=" . self::DB_URL . ";port=" . self::DB_PORT . ";dbname=" . self::DB_NAME_3;
      $this->pdo3 = new PDO($dsn3, self::DB_USER, self::DB_PASSWORD);
      $this->pdo3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      print "Error: " . $e->getMessage() . '<br>';
      exit();
    }
  }

  public function getOrganizers()
  {
    $statement = $this->pdo->prepare('SELECT * FROM organizers');

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../trainings/create.php?error=stmterror");
      exit();
    }

    $organizers = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $organizers;
  }

  public function getHrd($npk)
  {
    $statement = $this->pdo2->prepare('SELECT * FROM hrd_so WHERE npk = :npk');
    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $hrd = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;
    return $hrd;
  }

  public function getCtUsers($npk)
  {
    $statement = $this->pdo2->prepare('SELECT * FROM ct_users_hash WHERE npk = :npk');
    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $user = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;
    return $user;
  }

  public function getDepartmentMatrix($dpt_id)
  {
    $statement = $this->pdo->prepare('SELECT * FROM departments WHERE DPT_ID = :dpt_id');

    $statement->bindValue('dpt_id', $dpt_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../departments/update.php?error=stmterror");
      exit();
    }

    $departments = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = '';
    return $departments;
  }

  public function getTraining($t_id)
  {
    $statement = $this->pdo->prepare('SELECT * FROM trainings WHERE T_ID = :t_id');

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../trainings/update.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = '';
    return $trainings;
  }

  public function getCompanyPurposes($t_id)
  {
    $statement = $this->pdo->prepare('SELECT CB_ID, BENEFIT FROM company_benefits WHERE T_ID = :t_id');

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../trainings/update.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $trainings;
  }

  public function getParticipantPurposes($t_id)
  {
    $statement = $this->pdo->prepare('SELECT PB_ID, BENEFIT FROM participant_benefits WHERE T_ID = :t_id');

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../trainings/update.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $trainings;
  }

  public function getMaterials($t_id)
  {
    $statement = $this->pdo->prepare('SELECT M_ID, TITLE FROM materials WHERE T_ID = :t_id');

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../trainings/update.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $trainings;
  }

  public function getLatestTrainingId()
  {
    $statement = $this->pdo->prepare("SELECT T_ID FROM trainings ORDER BY T_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = null;
    return $result;
  }

  public function getLatestCompanyBenefitsId()
  {
    $statement = $this->pdo->prepare("SELECT CB_ID FROM company_benefits ORDER BY CB_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = null;
    return $result[0];
  }

  public function getLatestParticipantBenefitsId()
  {
    $statement = $this->pdo->prepare("SELECT PB_ID FROM participant_benefits ORDER BY PB_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = null;
    return $result[0];
  }

  public function getLatestMaterialsId()
  {
    $statement = $this->pdo->prepare("SELECT M_ID FROM materials ORDER BY M_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../trainings/create.php?error=stmterror");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = null;
    return $result[0];
  }

  public function getLatestOrgId()
  {
    $statement = $this->pdo->prepare("SELECT ORG_ID FROM organizers ORDER BY ORG_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../trainings/create.php?error=stmterror");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = null;
    return $result;
  }

  public function getLatestEvtId()
  {
    $statement = $this->pdo->prepare("SELECT EVT_ID FROM events ORDER BY EVT_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = null;
    return $result[0];
  }

  public function getLatestEpId()
  {
    $statement = $this->pdo->prepare("SELECT EP_ID FROM event_participants ORDER BY EP_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = null;
    return $result[0];
  }

  public function getNoHpByNpk($npk)
  {
    $statement = $this->pdo3->prepare('SELECT no_hp FROM hp WHERE NPK = :npk');

    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $trainings;
  }

  public function getNoHpForParticipants($npks)
  {
    $no_hp_list = [];
    foreach ($npks as $npk) {
      $no_hp = $this->getNoHpByNpk($npk);
      if ($no_hp) {
        $no_hp_list[] = ['no_hp' => $no_hp];
      }
    }
    return $no_hp_list;
    return $trainings;
  }
}
