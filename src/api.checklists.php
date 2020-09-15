<?php
session_start();

include('functions.php');

/*********************************************************
 * Create a new account
 * 
 * post
 * 
 * new-email
 * new-name-first
 * new-name-last
 * new-password
 * new-security-question
 * new-security-question-answer
***********************************************************/
if (isset($_POST['new-email'], $_POST['new-name-first'], $_POST['new-name-last'], $_POST['new-password'], $_POST['new-security-question'], $_POST['new-security-question-answer'])) {

  $email                  = $_POST['new-email'];
  $firstName              = $_POST['new-name-first'];
  $lastName               = $_POST['new-name-last'];
  $password               = $_POST['new-password'];
  $securityQuestionID     = $_POST['new-security-question'];
  $securityQuestionAnswer = $_POST['new-security-question-answer'];

  // check if email is already taken
  if (doesEmailExist($_POST['new-email'])) {
    header('Location: login.php?create-account=failed&reason=email-exists');
    exit;
  } 

  // insert the user
  $result = insertUser($email, $password, $firstName, $lastName, $securityQuestionID, $securityQuestionAnswer);

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

/*********************************************************
 * Log in a user
 * 
 * post
 * 
 * login-email
 * login-password
***********************************************************/
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

/*********************************************************
 * Create a new checklist
 * 
 * post
 *
 * function = insert-checklist
 * 
 * new-checklist-name
 * new-checklist-description
***********************************************************/
else if (isset($_POST['name'], $_POST['function'], $_SESSION['userID']) && $_POST['function'] == 'insert-checklist') {
  $name        = $_POST['name'];
  $description = $_POST['description'];
  $result      = insertChecklist($_SESSION['userID'], $name, $description);

  if ($result->rowCount() != 1) {
    echo 'error';
    exit;
  }

  // get the id of the newest checklist
  $checklistID = getNewestChecklistID($_SESSION['userID'])->fetch(PDO::FETCH_ASSOC);

  // get the checklist data
  $checklist = getChecklist($checklistID['id'])->fetch(PDO::FETCH_ASSOC);

  echo json_encode($checklist);
  exit;
}

/*********************************************************
 * Get all of the user's checklists
 * 
 * get
 * 
 * function = get-checklists
 * 
 * session (userID)
***********************************************************/
else if (isset($_GET['function'], $_SESSION['userID']) && $_GET['function'] == 'get-checklists') {
  $userID = $_SESSION['userID'];
  $checklists = getChecklists($userID)->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($checklists);
  exit;
}

/*********************************************************
 * get checklist data
 * 
 * get
 * 
 * function = get-checklist 
 * 
 * checklistID
 ***********************************************************/
else if (isset($_GET['function'], $_GET['checklistID']) && $_GET['function'] == 'get-checklist') {
  $checklistID = $_GET['checklistID'];
  $checklist = getChecklist($checklistID)->fetch(PDO::FETCH_ASSOC);
  echo json_encode($checklist);
  exit;
}

/*********************************************************
 * get a checklist data and its items
 * 
 * get
 * 
 * function = get-checklist-and-items
 * 
 * checklistID
 ***********************************************************/
else if (isset($_GET['function'], $_GET['checklistID']) && $_GET['function'] == 'get-checklist-and-items') {
  $checklistID = $_GET['checklistID'];
  $checklist['checklist'] = getChecklist($checklistID)->fetch(PDO::FETCH_ASSOC);
  $checklist['items'] = getItems($checklistID)->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($checklist);
  exit;
}

/*********************************************************
 * return all the items in a checklist
 * 
 * get
 * 
 * function = get-checklist-items
 * 
 * checklistID
 ***********************************************************/
else if (isset($_GET['function'], $_GET['id']) && $_GET['function'] == 'get-checklist-items') {
  $checklistID = $_GET['id'];
  $items = getItems($checklistID)->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($items);
  exit;
}

/*********************************************************
 * Update an item
 * 
 * post
 * 
 * function = update-item
 * 
 * itemID
 * content
 * completed
 ***********************************************************/
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


/*********************************************************
 * Add item to a checklist
 * 
 * post
 * 
 * function = add-item
 * 
 * checklistID
 * content
 ***********************************************************/
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


/*********************************************************
 * Delete an item
 * 
 * post
 * 
 * function = delete-item
 * 
 * itemID
 ***********************************************************/
else if (isset($_POST['function'], $_POST['itemID']) && $_POST['function'] == 'delete-item') {
  $itemID = $_POST['itemID'];

  $result = deleteItem($itemID);

  if ($result->rowCount() == 1)
    echo 'success';
  else
    echo 'error';

  exit;
}

/*********************************************************
 * Get the data for an item
 * 
 * get
 * 
 * function = get-item
 * 
 * itemID
 ***********************************************************/
else if (isset($_GET['function'], $_GET['itemID']) && $_GET['function'] == 'get-item') {
  $itemID = $_GET['itemID'];
  $item = getItem($itemID)->fetch(PDO::FETCH_ASSOC);
  echo json_encode($item);
  exit;
}


/*********************************************************
 * Delete a checklist
 * 
 * post
 * 
 * function = delete-checklist
 * 
 * checklistID
 ***********************************************************/
else if (isset($_POST['function'], $_POST['checklistID']) && $_POST['function'] == 'delete-checklist') {
  $checklistID = $_POST['checklistID'];
  $result = deleteChecklist($checklistID);

  if ($result->rowCount() == 1)
    echo 'success';
  else 
    echo 'error';

  exit;
}

/*********************************************************
 * Update checklist data
 * 
 * post
 * 
 * function = update-checklist
 * 
 * checklistID
 * name
 * description (optional)
 ***********************************************************/
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


/*********************************************************
 * Update user data
 * 
 * post
 * 
 * edit-email
 * edit-name-first
 * edit-name-last
 ***********************************************************/
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

/*********************************************************
 * Update user password
 * 
 * post
 * 
 * edit-password-current   (old password)
 * edit-password-1         (new password)
 ***********************************************************/
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


/*********************************************************
 * Mark all the items in the checklist complete
 * 
 * post
 *
 * function = complete-all-items
 * 
 * checklistID
 ***********************************************************/
else if (isset($_POST['function'], $_POST['checklistID']) && $_POST['function'] == 'complete-all-items') {
  $checklistID = $_POST['checklistID'];
  $result = updateAllItemsComplete($checklistID);
  exit;
}

/*********************************************************
 * Mark all the items in the checklist incomplete
 * 
 * post
 *
 * function = incomplete-all-items
 * 
 * checklistID
 ***********************************************************/
else if (isset($_POST['function'], $_POST['checklistID']) && $_POST['function'] == 'incomplete-all-items') {
  $checklistID = $_POST['checklistID'];
  $result = updateAllItemsComplete($checklistID, 'n');
  exit;
}


/*********************************************************
 * Copy over the items in one checklist into another one
 * 
 * post
 *
 * function = copy-items
 * 
 * sourceID         (id of the source checklist)
 * destinationID    (id of the destination checklist)
 ***********************************************************/
else if (isset($_POST['function'], $_POST['sourceID'], $_POST['destinationID']) && $_POST['function'] == 'copy-items') {
  $destinationID = $_POST['destinationID'];
  $sourceID      = $_POST['sourceID'];

  $result = copyOverItems($sourceID, $destinationID);

  if ($result->rowCount() >= 0)
    echo 'success';
  else
    echo 'error';
  exit;
}

/*********************************************************
 * Add a list of items to a checklist
 * 
 * post
 * 
 * function = add-item-list
 * 
 * checklistID
 * items (array of items)
***********************************************************/
else if (isset($_POST['function'], $_POST['checklistID'], $_POST['items']) && $_POST['function'] == 'add-item-list') {
  $checklistID = $_POST['checklistID'];
  $newItems    = json_decode($_POST['items']);
  $result      = insertItemList($checklistID, $newItems);

  // return an error if the rowcount does not match the number of items to be inserted
  if ($result->rowCount() == count($newItems)) 
    echo 'success';
   else
    echo 'error';

  exit;
}

/*********************************************************
 * Add a list of items to a checklist
 * 
 * post
 * 
 * function = delete-completed-items
 * 
 * checklistID
***********************************************************/
else if (isset($_POST['function'], $_POST['checklistID']) && $_POST['function'] == 'delete-completed-items') {
  $checklistID = $_POST['checklistID'];
  $result = deleteCompletedItems($checklistID, 'y');

  if ($result->rowCount() >= 0)
    echo 'success';
  else
    echo 'error';

  exit;
}


?>