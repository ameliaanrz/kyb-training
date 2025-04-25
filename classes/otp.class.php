<?php

require_once __DIR__ . '/../node_modules/phpPasswordHashingLib/passwordLib.php';
date_default_timezone_set('Asia/Jakarta');

class OTP extends Connection

{
    public function createOTP($npk)
    {
        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);
        $sendDate = date('Y-m-d H:i:s');
        $expiredDate = date('Y-m-d H:i:s', strtotime($sendDate . ' + 5 minutes'));

        // Periksa apakah NPK sudah ada di tabel OTP
        $stmt = $this->pdo->prepare('SELECT NPK FROM otp WHERE NPK = :npk');
        $stmt->bindValue(':npk', $npk);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Jika NPK sudah ada, lakukan UPDATE
            $updateStmt = $this->pdo->prepare('UPDATE otp SET NO_OTP = :no_otp, EXPIRED_DATE = :expired_date, SEND = :send,SEND_DATE = :send_date, USE_DATE = NULL WHERE NPK = :npk');
            $updateStmt->bindValue(':no_otp', $otp);
            $updateStmt->bindValue(':expired_date', $expiredDate);
            $updateStmt->bindValue(':send', 1);
            $updateStmt->bindValue(':send_date', $sendDate);
            $updateStmt->bindValue(':npk', $npk);

            if ($updateStmt->execute()) {
                return $otp; // Return OTP yang baru di-generate
            } else {
                throw new Exception("Failed to update OTP.");
            }
        } else {
            // Jika NPK belum ada, lakukan INSERT
            $insertStmt = $this->pdo->prepare('INSERT INTO otp (NPK, NO_OTP, EXPIRED_DATE, SEND, SEND_DATE, USE_DATE) VALUES (:npk, :no_otp, :expired_date, :send, :send_date, NULL)');
            $insertStmt->bindValue(':npk', $npk);
            $insertStmt->bindValue(':no_otp', $otp);
            $insertStmt->bindValue(':expired_date', $expiredDate);
            $insertStmt->bindValue(':send', 1);
            $insertStmt->bindValue(':send_date', $sendDate);

            if ($insertStmt->execute()) {
                return $otp; // Return OTP yang baru di-generate
            } else {
                throw new Exception("Failed to create OTP.");
            }
        }
    }

    protected function authenticateOtp($otp)
    {
        $connection = new Connection();

        // Periksa apakah OTP valid dan ambil NPK terkait
        $stmt = $connection->pdo->prepare('SELECT NPK FROM otp WHERE NO_OTP = :otp');
        $stmt->bindValue(":otp", $otp);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return false; // OTP tidak ditemukan atau tidak valid
        }

        // Ambil data OTP
        $otpData = $stmt->fetch(PDO::FETCH_ASSOC);
        $npk = $otpData['NPK'];

        // Ambil detail user berdasarkan NPK
        $userStmt = $connection->pdo->prepare('SELECT NPK, NAME, RLS_ID, DPT_ID FROM users WHERE NPK = :npk');
        $userStmt->bindValue(":npk", $npk);
        $userStmt->execute();

        $fetchedUser = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$fetchedUser) {
            return false; // User tidak ditemukan
        }

        // Update USE_DATE saat OTP digunakan
        $useDate = date('Y-m-d H:i:s');
        $updateStmt = $connection->pdo->prepare('UPDATE otp SET USE_DATE = :use_date WHERE NPK = :npk AND NO_OTP = :otp');
        $updateStmt->bindValue(":use_date", $useDate);
        $updateStmt->bindValue(":npk", $npk);
        $updateStmt->bindValue(":otp", $otp);
        $updateStmt->execute();

        // Mulai sesi jika belum dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Simpan data user ke sesi
        $_SESSION['NAME'] = $fetchedUser['NAME'];
        $_SESSION['NPK'] = $fetchedUser['NPK'];
        $_SESSION['RLS_ID'] = $fetchedUser['RLS_ID'];
        $_SESSION['DPT_ID'] = $fetchedUser['DPT_ID'];

        // Hapus sesi OTP yang lama jika ada
        unset($_SESSION['pending_npk']);
        unset($_SESSION['otp']);

        return true; // Autentikasi berhasil
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
    }
}
