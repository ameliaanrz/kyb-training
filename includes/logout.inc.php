<?php
session_start();

// Hapus semua cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Hancurkan session sepenuhnya
$_SESSION = array();
session_unset();
session_destroy();
session_write_close();

// Redirect ke halaman login
header("Location: ../login.php");
exit();
?>