<?php

// return a pdo database object
function dbConnect() {
  include('db-info.php');

  try {
    // connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbName",$user,$password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;

  } catch(PDOexception $e) {
      return 0;
  }
}

// prints out a bootstrap alert
function getAlert($message, $alertType = 'success') {
  return "
  <div class=\"alert alert-$alertType alert-dismissible mt-5 mb-5 fade show\" role=\"alert\">
    $message
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
      <span aria-hidden=\"true\">&times;</span>
    </button>
  </div>";
}


// create new account
function insertUser($email, $password, $firstName, $lastName) {
  $stmt = 'INSERT INTO Users (email, name_first, name_last, password, date_created) VALUES (:email, :firstName, :lastName, :password, NOW())';

  $sql = dbConnect()->prepare($stmt);

  // email
  $email = filter_var($email, FILTER_SANITIZE_STRING);
  $sql->bindParam(':email', $email, PDO::PARAM_STR);

  // first name
  $firstName = filter_var($firstName, FILTER_SANITIZE_STRING);
  $sql->bindParam(':firstName', $firstName, PDO::PARAM_STR);

  // last name
  $lastName = filter_var($lastName, FILTER_SANITIZE_STRING);
  $sql->bindParam(':lastName', $lastName, PDO::PARAM_STR);

  // password
  $password = filter_var($password, FILTER_SANITIZE_STRING);
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $sql->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

  $sql->execute();
  return $sql;
}

// return the id of a user based on their email
function getUserIdFromEmail($email) {
  $stmt = '
  SELECT id
  FROM   Users
  WHERE  email = :email
  LIMIT  1';

  $sql = dbConnect()->prepare($stmt);

  // email
  $email = filter_var($email, FILTER_SANITIZE_STRING);
  $sql->bindParam(':email', $email, PDO::PARAM_STR);

  // get result and return the id
  $sql->execute();
  return $sql;
}

// returns true if email exists
// returns false if email does not exist
function doesEmailExist($email) {
  $checkEmail = getUserIdFromEmail($email)->fetchAll(PDO::FETCH_ASSOC);
  if (count($checkEmail) == 1)
    return true;
  else
    return false;
}


// checks if email and password are a match
function isValidEmailAndPassword($email, $password) {
  $pdo = dbConnect();
  $sql = $pdo->prepare('SELECT password FROM Users WHERE email=:email LIMIT 1');

  // sanitize and bind username
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);
  $sql->bindParam(':email', $email, PDO::PARAM_STR);
  
  $sql->execute();

  // check if password matches the hashed password stored in the db
  $hash = $sql->fetch(PDO::FETCH_ASSOC);
  $hash = $hash['password'];
  return password_verify($password, $hash);
}


?>