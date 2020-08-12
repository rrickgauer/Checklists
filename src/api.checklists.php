<?php
session_start();
include('functions.php');


// create new account
if (isset($_POST['new-email'], $_POST['new-name-first'], $_POST['new-name-last'], $_POST['new-password'])) {
  // check if email is already taken
  $checkEmail = getUserIdFromEmail($_POST['new-email']);
  if ($checkEmail == null) {
    header('Location: login.php?create-account=failed&reason=email-exists');
    exit;
  }

  // insert the new user
  $result = insertUser($_POST['new-email'], $_POST['new-password'], $_POST['new-name-first'], $_POST['new-name-last']);

  // if account creation was successful go to their home page
  if ($result->rowCount() == 1) {
    $_SESSION['userID'] = getUserIdFromEmail($_POST['new-email']);
    header('Location: home.php');
    exit;
  } 

  // if not, go back to login page
  else {
    header('Location: login.php?create-account=failed&reason=unknown');
    exit;
  }
}







?>