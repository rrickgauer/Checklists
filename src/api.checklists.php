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
  $name        = $_POST['new-checklist-name'];
  $description = $_POST['new-checklist-description'];
  $result      = insertChecklist($_SESSION['userID'], $name, $description);

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

// get the data of a checklist
else if (isset($_GET['function'], $_GET['checklistID']) && $_GET['function'] == 'get-checklist') {
  $checklistID = $_GET['checklistID'];
  $checklist = getChecklist($checklistID)->fetch(PDO::FETCH_ASSOC);
  echo json_encode($checklist);
  exit;
}

// get the checklist data and its items
else if (isset($_GET['function'], $_GET['checklistID']) && $_GET['function'] == 'get-checklist-and-items') {
  $checklistID = $_GET['checklistID'];
  $checklist['checklist'] = getChecklist($checklistID)->fetch(PDO::FETCH_ASSOC);
  $checklist['items'] = getItems($checklistID)->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($checklist);
  exit;
}

// retrive the items in a checklist
else if (isset($_GET['function'], $_GET['id']) && $_GET['function'] == 'get-checklist-items') {
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

// get an item based on its id
else if (isset($_GET['function'], $_GET['itemID']) && $_GET['function'] == 'get-item') {
  $itemID = $_GET['itemID'];
  $item = getItem($itemID)->fetch(PDO::FETCH_ASSOC);
  echo json_encode($item);
  exit;
}


// delete a checklist
else if (isset($_POST['function'], $_POST['checklistID']) && $_POST['function'] == 'delete-checklist') {
  $checklistID = $_POST['checklistID'];
  $result = deleteChecklist($checklistID);

  if ($result->rowCount() == 1)
    echo 'success';
  else 
    echo 'error';

  exit;
}

// update checklist data
else if (isset($_POST['function'], $_POST['checklistID'], $_POST['name']) && $_POST['function'] == 'update-checklist') {
  $checklistID = $_POST['checklistID'];
  $name        = $_POST['name'];
  $description = $_POST['description'];

  $result = updateChecklist($checklistID, $name, $description);

  if ($result->rowCount() == 1)
    echo 'success';
  else
    echo 'error';

  exit;
}


// update user info
else if (isset($_SESSION['userID'], $_POST['edit-email'], $_POST['edit-name-first'], $_POST['edit-name-last'])) {
  $userID    = $_SESSION['userID'];
  $email     = $_POST['edit-email'];
  $firstName = $_POST['edit-name-first'];
  $lastName  = $_POST['edit-name-last'];

  $result = updateUserInfo($userID, $email, $firstName, $lastName);

  if ($result->rowCount() == 1)
    $_SESSION['user-info-updated'] = true;
  else {
    $_SESSION['user-info-updated'] = false;
    $_SESSION['reason'] = 'unknown';
  }

  header('Location: settings.php');
  exit;
}

else if (isset($_SESSION['userID'], $_POST['edit-password-current'], $_POST['edit-password-1'])) {
  $userID          = $_SESSION['userID'];
  $currentPassword = $_POST['edit-password-current'];
  $newPassword     = $_POST['edit-password-1'];

  // check if user entered correct current password
  $user = getUser($userID)->fetch(PDO::FETCH_ASSOC);
  $email = $user['email'];

  if (!isValidEmailAndPassword($email, $currentPassword)) {
    $_SESSION['user-password-updated'] = false;
    $_SESSION['reason'] = 'incorrect-current-password';
    header('Location: settings.php');
    exit;
  }


  $result = updateUserPassword($userID, $newPassword);

  if ($result->rowCount() == 1) {
    $_SESSION['user-password-updated'] = true;
  } else {
    $_SESSION['user-password-updated'] = false;
    $_SESSION['reason'] = 'unknown';
  }


  header('Location: settings.php');
  exit;
}


// mark all items complete
else if (isset($_POST['function'], $_POST['checklistID']) && $_POST['function'] == 'complete-all-items') {
  $checklistID = $_POST['checklistID'];
  $result = updateAllItemsComplete($checklistID);
  exit;
}

// mark all items incomplete
else if (isset($_POST['function'], $_POST['checklistID']) && $_POST['function'] == 'incomplete-all-items') {
  $checklistID = $_POST['checklistID'];
  $result = updateAllItemsComplete($checklistID, 'n');
  exit;
}


// copy over checklist items into another checklist
else if (isset($_POST['function'], $_POST['sourceID'], $_POST['destinationID']) && $_POST['function'] == 'copy-items') {
  $destinationID = $_POST['destinationID'];
  $sourceID = $_POST['sourceID'];

  $result = copyOverItems($sourceID, $destinationID);

  if ($result->rowCount() >= 0)
    echo 'success';
  else
    echo 'error';
  exit;
}


?>