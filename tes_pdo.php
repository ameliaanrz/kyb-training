<?php
$dsn = "mysql:server=" . "172.16.16.253" . ";port=" ."3306" . ";dbname=" ."kayaba_training_center";
$pdo = new PDO($dsn, "nodered", "BackEnd");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>