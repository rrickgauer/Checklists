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
 * count_checklists
 *
 ******************************************************************************/
function getUser($id) {
  $stmt = '
  SELECT id,
         email,
         name_first,
         name_last,
         date_created,
         DATE_FORMAT(date_created, "%l:%i %p") AS date_created_display_time,
         DATE_FORMAT(date_created, "%c/%d/%Y") AS date_created_display_date,
         (SELECT COUNT(Checklists.id)
          FROM   Checklists
          WHERE  user_id = Users.id)           AS count_checklists
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
function insertChecklist($userID, $name, $description = null) {
  $stmt = '
  INSERT INTO Checklists (user_id, name, description, date_created, date_modified) VALUES 
    (:userID, :name, :description, NOW(), NOW())';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  // filter and bind name
  $name = filter_var($name, FILTER_SANITIZE_STRING);
  $sql->bindParam(':name', $name, PDO::PARAM_STR);

  // filter and bind description
  $description = filter_var($description, FILTER_SANITIZE_STRING);
  if ($description == '') // set description to null if it is blank
    $description = null;
  $sql->bindParam(':description', $description, PDO::PARAM_STR);

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
 * count_items
 * date_modified_items
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
         DATE_FORMAT(Checklists.date_modified, "%l:%i %p") AS date_modified_display_time,
         (SELECT COUNT(id)
          FROM   Items
          WHERE  checklist_id = Checklists.id)             AS count_items,
         (SELECT Items.date_modified
          FROM   Items
          WHERE  checklist_id = Checklists.id
          ORDER  BY Items.date_modified DESC
          LIMIT  1)                                        AS date_modified_items
  FROM   Checklists
  WHERE  user_id = :userID
  ORDER  BY date_modified_items DESC, date_created DESC';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind user id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}

/*************************************************************************
 * Return data about the checklist
 * 
 * id
 * name
 * description
 * date_created
**************************************************************************/ 
function getChecklist($checklistID) {
  $stmt = '
  SELECT Checklists.id,
         Checklists.name,
         Checklists.description,
         Checklists.date_created
  FROM   Checklists
  WHERE  id = :checklistID
  LIMIT  1';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind checklist id
  $checklistID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}

/**********************************************************************
 * Return a checklist's items
 * 
 * id
 * checklist_id
 * checklist_name
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
         Checklists.name AS checklist_name,
         Items.completed,
         Items.content,
         Items.date_created,
         Items.date_modified,
         Items.rank
  FROM   Items
         LEFT JOIN Checklists
                ON Items.checklist_id = Checklists.id
  WHERE  Items.checklist_id = :checklistID
  ORDER  BY Items.date_created DESC';

  $sql = dbConnect()->prepare($stmt);
  $checklistID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}


function updateItem($itemID, $content, $completed) {
  $stmt = '
  UPDATE Items
  SET    content = :content,
         completed = :completed,
         date_modified = now()
  WHERE  id = :itemID';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind id
  $itemID = filter_var($itemID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':itemID', $itemID, PDO::PARAM_INT);

  // filter and bind content
  $content = filter_var($content, FILTER_SANITIZE_STRING);
  $sql->bindParam(':content', $content, PDO::PARAM_STR);

  // filter and bind completed
  $completed = filter_var($completed, FILTER_SANITIZE_STRING);
  $sql->bindParam(':completed', $completed, PDO::PARAM_STR);

  $sql->execute();
  return $sql;

}


function addItem($checklistID, $content) {

  $stmt = '
  INSERT INTO Items
  (
    checklist_id,
    content,
    date_created,
    date_modified
  )
  VALUES
  (
    :checklistID,
    :content,
    NOW(),
    NOW()
  ) ';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind checklist id
  $itemID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);

  // filter and bind content
  $content = filter_var($content, FILTER_SANITIZE_STRING);
  $sql->bindParam(':content', $content, PDO::PARAM_STR);

  $sql->execute();
  return $sql;
}

/********************************************************************
 * Returns the most recent item that was added to a checklist
 * 
 * id
 * checklist_id
 * checklist_name
 * completed
 * content
 * date_created
 * date_modified
 * rank
 * 
 ********************************************************************/
function getLatestChecklistItem($checklistID) {
  $stmt = '
  SELECT Items.id,
         Items.checklist_id,
         Checklists.name AS checklist_name,
         Items.completed,
         Items.content,
         Items.date_created,
         Items.date_modified,
         Items.rank
  FROM   Items
         LEFT JOIN Checklists
                ON Items.checklist_id = Checklists.id
  WHERE  Items.checklist_id = :checklistID
  ORDER  BY Items.id DESC
  LIMIT  1';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind checklist id
  $checklistID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);
  $sql->execute();

  return $sql;
}


function deleteItem($itemID) {
  $stmt = 'DELETE from Items where id = :itemID';

  $stmt = '
  DELETE FROM Items
  WHERE  id = :itemID';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind item id
  $itemID = filter_var($itemID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':itemID', $itemID, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}


/********************************************************************
 * Returns the most recent item that was added to a checklist
 * 
 * id
 * checklist_id
 * checklist_name
 * completed
 * content
 * date_created
 * date_modified
 * rank
 * 
 ********************************************************************/
function getItem($itemID) {
  $stmt = '
  SELECT Items.id,
         Items.checklist_id,
         Checklists.name AS checklist_name,
         Items.completed,
         Items.content,
         Items.date_created,
         Items.date_modified,
         Items.rank
  FROM   Items
         LEFT JOIN Checklists
                ON Items.checklist_id = Checklists.id
  WHERE  Items.id = :itemID
  ORDER  BY Items.id DESC
  LIMIT  1';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind item id
  $itemID = filter_var($itemID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':itemID', $itemID, PDO::PARAM_INT);
  $sql->execute();

  return $sql;
}


function deleteChecklist($checklistID) {
  $stmt = '
  DELETE FROM Checklists
  WHERE  id = :checklistID';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind checklist id
  $checklistID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);
  $sql->execute();

  return $sql;
}


function updateChecklist($checklistID, $name, $description = null) {
  $stmt = '
  UPDATE Checklists
  SET    name = :name,
         description = :description
  WHERE  id = :checklistID ';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind checklist id
  $checklistID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);

  // filter and bind name
  $name = filter_var($name, FILTER_SANITIZE_STRING);
  $sql->bindParam(':name', $name, PDO::PARAM_STR);

  // filter and bind description
  $description = filter_var($description, FILTER_SANITIZE_STRING);
  if ($description == '') // set description to null if it is blank
    $description = null;
  $sql->bindParam(':description', $description, PDO::PARAM_STR);

  $sql->execute();

  return $sql;

}

function updateUserInfo($userID, $email, $firstName, $lastName) {
  $stmt = '
  UPDATE Users 
  SET    email      = :email, 
         name_first = :firstName, 
         name_last  = :lastName 
  WHERE  id = :userID';

  $sql = dbConnect()->prepare($stmt);

  // id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  // email
  $email = filter_var($email, FILTER_SANITIZE_STRING);
  $sql->bindParam(':email', $email, PDO::PARAM_STR);

  // first name
  $firstName = filter_var($firstName, FILTER_SANITIZE_STRING);
  $sql->bindParam(':firstName', $firstName, PDO::PARAM_STR);

  // last name
  $lastName = filter_var($lastName, FILTER_SANITIZE_STRING);
  $sql->bindParam(':lastName', $lastName, PDO::PARAM_STR);

  $sql->execute();
  return $sql;
}

function updateUserPassword($userID, $newPassword) {
  $stmt = '
  UPDATE Users 
  SET    password = :newPassword 
  WHERE  id = :userID';

  $sql = dbConnect()->prepare($stmt);

  // id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  // password
  $newPassword = filter_var($newPassword, FILTER_SANITIZE_STRING);
  $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
  $sql->bindParam(':newPassword', $hashedPassword, PDO::PARAM_STR);

  $sql->execute();
  return $sql;
}



// mark all items in a checklist as either complete or incomplete
function updateAllItemsComplete($checklistID, $completed = 'y') {
  $stmt = '
  UPDATE Items
  SET    completed = :completed
  WHERE  checklist_id = :checklistID';

  $sql = dbConnect()->prepare($stmt);

  // if completed is not y then set it to n
  if ($completed != 'y')
    $completed = 'n';

  // filter and bind completed
  $completed = filter_var($completed, FILTER_SANITIZE_STRING);
  $sql->bindParam(':completed', $completed, PDO::PARAM_STR);

  // filter and bind checklistID
  $checklistID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);

  $sql->execute();

  return $sql;
}


// copy the items from one checklist (source) into another checklist (destination)
function copyOverItems($sourceID, $destinationID) {
  $stmt = '
  INSERT INTO Items
  (
    checklist_id,
    content,
    completed,
    date_created,
    date_modified
  )
  SELECT :destinationID,
         content,
         completed,
         NOW(),
         NOW()
  FROM   Items
  WHERE  checklist_id = :sourceID';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind source id
  $sourceID = filter_var($sourceID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':sourceID', $sourceID, PDO::PARAM_INT);

    // filter and bind destination id
  $destinationID = filter_var($destinationID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':destinationID', $destinationID, PDO::PARAM_INT);

  $sql->execute();

  return $sql;




}

?>