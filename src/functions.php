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

/******************************************************************************
 * Returns a user's data
 * 
 * id
 * name_first
 * name_last
 * date_created
 * date_created_display_time
 * date_created_display_date
 ******************************************************************************/
function getUser($id) {
  $stmt = '
  SELECT id,
         email,
         name_first,
         name_last,
         date_created,
         DATE_FORMAT(date_created, "%l:%i %p") AS date_created_display_time,
         DATE_FORMAT(date_created, "%c/%d/%Y") AS date_created_display_date
  FROM   Users
  WHERE  id = :id
  LIMIT  1';

  $sql = dbConnect()->prepare($stmt);

  // sanitize and bind id
  $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':id', $id, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}

// insert new checklist
function insertChecklist($userID, $name) {
  $stmt = '
  INSERT INTO Checklists (user_id, name, date_created, date_modified) VALUES 
    (:userID, :name, NOW(), NOW())';

  $sql = dbConnect()->prepare($stmt);

  // id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  // name
  $name = filter_var($name, FILTER_SANITIZE_STRING);
  $sql->bindParam(':name', $name, PDO::PARAM_STR);

  $sql->execute();
  return $sql;
}


/*****************************************************************
 * returns all user checklists
 * 
 * id
 * name
 * date_created
 * date_modified
 * date_display_date
 * date_display_time
 * date_modified_date
 * date_modified_time
 * 
******************************************************************/
function getChecklists($userID) {
  $stmt = '
  SELECT Checklists.id,
         Checklists.name,
         Checklists.date_created,
         Checklists.date_modified,
         DATE_FORMAT(Checklists.date_created, "%c/%d/%Y")  AS date_created_display_date,
         DATE_FORMAT(Checklists.date_created, "%l:%i %p")  AS date_created_display_time,
         DATE_FORMAT(Checklists.date_modified, "%c/%d/%Y") AS date_modified_display_date,
         DATE_FORMAT(Checklists.date_modified, "%l:%i %p") AS date_modified_display_time
  FROM   Checklists
  WHERE  user_id = :userID';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind user id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}


/**********************************************************************
 * Return a checklist's items
 * 
 * id
 * checklist_id
 * completed
 * content
 * date_created
 * date_modified
 * rank
 * 
***********************************************************************/
function getItems($checklistID) {
  $stmt = '
  SELECT Items.id,
         Items.checklist_id,
         Items.completed,
         Items.content,
         Items.date_created,
         Items.date_modified,
         Items.rank
  FROM   Items
  WHERE  Items.checklist_id = :checklistID
  ORDER  BY Items.rank ASC';

  $sql = dbConnect()->prepare($stmt);
  $checklistID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}




?>