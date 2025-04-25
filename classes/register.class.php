<?php
  require_once __DIR__ . '/../node_modules/phpPasswordHashingLib-master/passwordLib.php';

class Register extends Connection
{
  protected function npkExists($npk)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT * FROM users WHERE NPK = :npk');
    $statement->bindValue("npk", $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../register.php?error=stmterror");
      exit();
    }

    $rows = $statement->rowCount();
    $statement = null;
    return $rows > 0;
  }

  protected function createNewUser($npk, $name, $password)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO users (name, NPK, password) VALUES (:name, :npk, :password)');

    $statement->bindValue("name", $name);
    $statement->bindValue("npk", $npk);
    $statement->bindValue("password", password_hash($password, PASSWORD_DEFAULT));

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../register.php?error=stmterror");
      exit();
    }

    $statement = null;
  }
}
