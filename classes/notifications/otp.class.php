<?php

require_once __DIR__ . '/../connectiondbhp.class.php';


class Otp extends ConnectionDBhp
{
    /*public function getOtp($npk)
    {
        $statement = $this->pdo->prepare('SELECT no_hp FROM hp WHERE npk = :npk');
    
        $statement->bindValue(':npk', $npk);
    
        if (!$statement->execute()) {
        $statement = '';
        header("Location: ../register.php?error=stmterror");
        exit();
        }
    
        $otp = $statement->fetch(PDO::FETCH_ASSOC);
        $statement = '';
        return $otp;
    }

    public function getNo($npk)
    {
        $statement = $this->pdo->prepare('SELECT no_hp FROM hp WHERE npk = :npk');
    
        $statement->bindValue(':npk', $npk);
    
        if (!$statement->execute()) {
        $statement = '';
        header("Location: ../register.php?error=stmterror");
        exit();
        }
    
        $otp = $statement->fetch(PDO::FETCH_ASSOC);
        $statement = '';
        return $otp;
    }
    
    public function createOtp($npk, $otp)
    {
        $statement = $this->pdo->prepare('INSERT INTO hp (npk, no_hp) VALUES (:npk, :no_hp)');
    
        $statement->bindValue(':npk', $npk);
        $statement->bindValue(':no_hp', $otp);
    
        if (!$statement->execute()) {
        $statement = '';
        header("Location: ../register.php?error=stmterror");
        exit();
        }
    
        $statement = '';
    }
    
    public function deleteOtp($npk)
    {
        $statement = $this->pdo->prepare('DELETE FROM hp WHERE NPK = :npk');
    
        $statement->bindValue(':npk', $npk);
    
        if (!$statement->execute()) {
        $statement = '';
        header("Location: ../register.php?error=stmterror");
        exit();
        }
    
        $statement = '';
    }*/
}