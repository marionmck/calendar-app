<?php

/*
 * @author Mario Nemecek
 *
*/

ini_set("session.save_path", "../sessionData/");
ini_set("session.gc_maxlifetime", "1800");
session_start();

include "../config/config.php";

$errors = validateLogin();
if($errors) {
  $_SESSION['login-error'] = $errors[0];
  header('Location: '.BASEPATH);
}
else {
  setSession("logged-in", "true");
  header('Location: '.BASEPATH);
}

function validateLogin() {
  $errors = array();

  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $dbConn = getConnection();

    $email = filter_has_var(INPUT_POST, 'email') ? $_POST['email']: null;
    $password = filter_has_var(INPUT_POST, 'psw') ? $_POST['psw']: null;

    trim($email);
    trim($password);

    $query  = "SELECT student_id, teacher_id, password FROM user WHERE email LIKE :email";
    $params = ["email" => $email];

    $stmt = $dbConn->prepare($query);
    $stmt->bindValue("email", $email);
    $stmt->execute();

    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($user) {
        $passwordHash = $user[0]['password'];
        if(!password_verify($password, $passwordHash)) {
            $errors[] = "You have entered the wrong email or password.";
        }
        else {
          $user_id = is_null($user[0]['student_id']) ? $user[0]['teacher_id'] : $user[0]['student_id'];
          setSession("user_id", $user_id);
        }
    }
    else {
        $errors[] = "Sorry, we could not find you. Please try again.";
    }
  }
  else {
    $errors[] = "Invalid Request!";
  }
  return $errors;
}
