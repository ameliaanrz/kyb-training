<?php

class ConnectionDBhp
{
  protected $pdo;
  const DB_URL = "localhost";
  const DB_PORT = "3306";
  const DB_NAME = "isd";
  const DB_USER = "root";
  const DB_PASSWORD = "";

  public function __construct()
  {
    try {
      $dsn = "mysql:host=" . self::DB_URL . ";port=" .self::DB_PORT . ";dbname=" . self::DB_NAME;
      $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASSWORD);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      print "Error: " . $e->getMessage() . '<br>';
      exit();
    }
  }
  
  public function getNoHpByNpk($npk)
  {
    try {
      $stmt = $this->pdo->prepare("SELECT no_hp FROM hp WHERE NPK = :npk");
      $stmt->bindValue(':npk', $npk);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? $result['no_hp'] : null;
    } catch (PDOException $e) {
      error_log("Error fetching no_hp: " . $e->getMessage());
      return null;
    }
  }

  // Method to fetch no_hp for multiple NPKs
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
  }
}
?>