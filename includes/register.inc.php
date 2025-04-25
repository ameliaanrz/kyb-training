<?php

$npk = '';
$name = '';
$password = '';
$password_confirmation = '';
$errors = array(
  'name' => '',
  'npk' => '',
  'password' => '',
  'password_confirmation' => '',
);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // input post data
  $npk = post_data('npk');
  $name = post_data('name');
  $password = post_data('password');
  $password_confirmation = post_data('password_confirmation');

  // require classes and init registerController
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/register.class.php';
  require_once __DIR__ . '/../classes/register-contr.class.php';
  require_once __DIR__ . '/../node_modules/phpPasswordHashingLib-master/passwordLib.php';

  $register = new RegisterController($name, $npk, $password, $password_confirmation);

  // register new user
  $errors = array_merge($errors, $register->registerNewUser());

  // redirect to users admin dashboard
  if (!$errors['name'] && !$errors['npk'] && !$errors['password'] && !$errors['password_confirmation']) {
    header("Location: ../login.php?error=none");
  }
}

function post_data($field)
{
  $_POST[$field] = $_POST[$field] ? $_POST[$field] : false;
  return stripslashes(htmlspecialchars($_POST[$field]));
}
