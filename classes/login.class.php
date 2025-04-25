<?php

require_once __DIR__ . '/../node_modules/phpPasswordHashingLib/passwordLib.php';

class Login extends Connection
{
    protected function checkAuth($npk, $password)
    {
        $connection = new Connection();

        // Ambil password dari tabel ct_users_hash
        $statement = $connection->pdo2->prepare('SELECT * FROM ct_users_hash WHERE npk = :npk');
        $statement->bindValue("npk", $npk);

        if (!$statement->execute()) {
            header("Location: ../login.php?error=stmterror");
            exit();
        }

        if ($statement->rowCount() == 0) {
            return false;
        }

        // Verifikasi password
        $fetchedUser = $statement->fetch(PDO::FETCH_ASSOC);
        if (!password_verify($password, $fetchedUser['pwd'])) {
            return false;
        }

        // Ambil data pengguna dari tabel users
        $statement = $connection->pdo->prepare('SELECT * FROM users WHERE NPK = :npk');
        $statement->bindValue("npk", $npk);

        if (!$statement->execute()) {
            header("Location: ../login.php?error=stmterror");
            exit();
        }

        if ($statement->rowCount() == 0) {
            return false;
        }

        // Fetch data dari users
        $fetchedAdmin = $statement->fetch(PDO::FETCH_ASSOC);

        // Tentukan role 
        $dept = $fetchedUser['dept'];
        if ($dept === 'HRD') {
            $roleId = 'RLS01';
        } else {
            $statement = $connection->pdo2->prepare('SELECT * FROM hrd_so WHERE npk = :npk AND tipe = 1');
            $statement->bindValue("npk", $npk);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $roleId = 'RLS02';
            } else {
                $roleId = ['RLS03', 'RLS04'];
            }
        }

        // Jika session belum dimulai, mulai session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Set data session dari tabel users
        $_SESSION['NAME'] = $fetchedAdmin['NAME'];
        $_SESSION['NPK'] = $fetchedAdmin['NPK'];
        $_SESSION['RLS_ID'] = $roleId;
        $_SESSION['DPT_ID'] = $fetchedAdmin['DPT_ID'];

        return true;
    }


    protected function authenticateUser($npk)
    {
        $connection = new Connection();

        $statement = $connection->pdo->prepare('SELECT NPK, NAME, RLS_ID, DPT_ID FROM users WHERE NPK = :npk');
        $statement->bindValue("npk", $npk);

        if (!$statement->execute()) {
            header("HTTP/1.1 500 Internal Server Error");
            exit();
        }

        $fetchedAdmin = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$fetchedAdmin) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['NAME'] = $fetchedAdmin['NAME'];
        $_SESSION['NPK'] = $fetchedAdmin['NPK'];
        $_SESSION['RLS_ID'] = $fetchedAdmin['RLS_ID'];
        $_SESSION['DPT_ID'] = $fetchedAdmin['DPT_ID'];

        return true;
    }
}
