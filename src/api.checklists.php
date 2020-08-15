<?php
session_start();

include('functions.php');

// create new account
if (isset($_POST['new-email'], $_POST['new-name-first'], $_POST['new-name-last'], $_POST['new-password'])) {

  // check if email is already taken
  if (doesEmailExist($_POST['new-email'])) {
    header('Location: login.php?create-account=failed&reason=email-exists');
    exit;
  } 

  // insert the new user
  $result = insertUser($_POST['new-email'], $_POST['new-password'], $_POST['new-name-first'], $_POST['new-name-last']);

  // if account creation was successful go to their home page
  if ($result->rowCount() == 1) {
    $result = getUserIdFromEmail($_POST['new-email'])->fetch(PDO::FETCH_ASSOC);
    $_SESSION['userID'] = $result['id'];
    header('Location: home.php');
    exit;
  } 

  // if not, go back to login page
  else {
    header('Location: login.php?create-account=failed&reason=unknown');
    exit;
  }
}

// user log in
else if (isset($_POST['login-email'], $_POST['login-password'])) {
  $email = $_POST['login-email'];
  $password = $_POST['login-password'];

  // if email does not exist, go back to login page
  if (!doesEmailExist($email)) {
    header('Location: login.php?login=failed&reason=email-undetected');
    exit;
  }

  // if email and password don't match, go back to login page
  else if (!isValidEmailAndPassword($email, $password)) {
    header('Location: login.php?login=failed&reason=email-password-match');
    exit;
  }

  // successful login
  else {
    // set user id session variable
    $result = getUserIdFromEmail($email)->fetch(PDO::FETCH_ASSOC);
    $_SESSION['userID'] = $result['id'];

    // go to user home page
    header('Location: home.php');
    exit;
  }
}

// create new checklist
else if (isset($_POST['new-checklist-name'])) {
  $name = $_POST['new-checklist-name'];
  $result = insertChecklist($_SESSION['userID'], $name);

  if ($result->rowCount() == 1) 
    $_SESSION['checklist-created'] = true;
  else
    $_SESSION['checklist-created'] = false;

  header('Location: home.php');
  exit;
}

// retrieve user checklists
else if (isset($_GET['function'], $_SESSION['userID']) && $_GET['function'] == 'get-checklists') {
  $userID = $_SESSION['userID'];
  $checklists = getChecklists($userID)->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($checklists);
  exit;
}

// retrive a checklist data
else if (isset($_GET['function'], $_GET['id']) && $_GET['function'] == 'get-checklist') {
  $checklistID = $_GET['id'];
  $items = getItems($checklistID)->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($items);
  exit;
}

// update an item 
else if (isset($_POST['function'], $_POST['itemID'], $_POST['content'], $_POST['completed']) && $_POST['function'] == 'update-item') {

  $itemID    = $_POST['itemID'];
  $content   = $_POST['content'];
  $completed = $_POST['completed'];
  $result = updateItem($itemID, $content, $completed);
  
  if ($result->rowCount() == 1)
    echo 'success';
  else
    echo 'failure';

  exit;

}


// add item to a checklist
else if (isset($_POST['function'], $_POST['checklistID'], $_POST['content']) && $_POST['function'] == 'add-item') {
  $checklistID = $_POST['checklistID'];
  $content = $_POST['content'];
  $result = addItem($checklistID, $content);

  // return that there was an error
  if ($result->rowCount() != 1) {
    echo 'error';
    exit;
  }

  // return the most recent added item
  $item = getLatestChecklistItem($checklistID)->fetch(PDO::FETCH_ASSOC);
  echo json_encode($item);
  exit;
}


// delete an item request
else if (isset($_POST['function'], $_POST['itemID']) && $_POST['function'] == 'delete-item') {
  $itemID = $_POST['itemID'];

  $result = deleteItem($itemID);

  if ($result->rowCount() == 1)
    echo 'success';
  else
    echo 'error';

  exit;
}

?>