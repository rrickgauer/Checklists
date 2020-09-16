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

/////////////////////////////////////////////////////
// Return all the security questions and their IDs //
/////////////////////////////////////////////////////
function getSecurityQuestions() {
  $stmt = '
  SELECT id,
         question
  FROM   Security_Questions
  ORDER  BY question ASC';

  $sql = dbConnect()->prepare($stmt);
  $sql->execute();

  return $sql;
}


/*****************************************************
 * USERS
******************************************************/ 

// create new user
function insertUser($email, $password, $firstName, $lastName, $securityQuestionID, $securityQuestionAnswer) {
  $stmt = '
  INSERT INTO Users (
    email,
    name_first,
    name_last,
    password,
    date_created,
    security_question_id,
    security_question_answer
  )

  VALUES (
    :email,
    :firstName,
    :lastName,
    :password,
    NOW(),
    :securityQuestionID,
    :securityQuestionAnswer
  )';

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

  // security question id
  $securityQuestionID = filter_var($securityQuestionID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':securityQuestionID', $securityQuestionID, PDO::PARAM_INT);

  // security question answer
  $securityQuestionAnswer = filter_var($securityQuestionAnswer, FILTER_SANITIZE_STRING);
  $sql->bindParam(':securityQuestionAnswer', $securityQuestionAnswer, PDO::PARAM_STR);

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
// returns true if valid, false if not valid
function isValidEmailAndPassword($email, $password) {
  $stmt = '
  SELECT password
  FROM   Users
  WHERE  email = :email
  LIMIT  1';

  $sql = dbConnect()->prepare($stmt);

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
 * security_question_answer
 * date_created_display_time
 * date_created_display_date
 * count_checklists
 ******************************************************************************/
function getUser($id) {
  $stmt = '
  SELECT id,
         email,
         name_first,
         name_last,
         date_created,
         security_question_answer,
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

// delete the user from the database
function deleteUser($userID) {
  $stmt = '
  DELETE FROM Users
  WHERE  id = :userID';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  $sql->execute();

  return $sql;
}

// updates a user's information
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

////////////////////////////
// update a user password //
////////////////////////////
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


/////////////////////////////////////
// Get the users security question //
/////////////////////////////////////
function getUserSecurityQuestion($userID) {
  $stmt = 'SELECT Security_Questions.question FROM Security_Questions where id = (select Users.security_question_id FROM Users where id = :userID) LIMIT 1';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  $sql->execute();

  return $sql;

}


/*****************************************************
 * CHECKLISTS
******************************************************/ 

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
 * date_created_display
 * date_modified_minutes
 * date_modified_hours
 * date_modified_days
 * count_items
 * count_items_complete
 * count_items_incomplete
**************************************************************************/ 
function getChecklist($checklistID) {
  $stmt = '
  SELECT Checklists.id,
         Checklists.name,
         Checklists.description,
         Checklists.date_created,
         DATE_FORMAT(Checklists.date_created, "%c/%d/%Y") AS date_created_display,
         (SELECT ABS(TIMESTAMPDIFF(minute, NOW(), Items.date_modified))
          FROM   Items
          WHERE  Items.checklist_id = Checklists.id
          ORDER  BY Items.date_modified DESC
          LIMIT  1)                                       AS date_modified_minutes,
         (SELECT ABS(TIMESTAMPDIFF(hour, NOW(), Items.date_modified))
          FROM   Items
          WHERE  Items.checklist_id = Checklists.id
          ORDER  BY Items.date_modified DESC
          LIMIT  1)                                       AS date_modified_hours,
         (SELECT ABS(TIMESTAMPDIFF(day, NOW(), Items.date_modified))
          FROM   Items
          WHERE  Items.checklist_id = Checklists.id
          ORDER  BY Items.date_modified DESC
          LIMIT  1)                                       AS date_modified_days,
         (SELECT COUNT(Items.id)
          FROM   Items
          WHERE  Items.checklist_id = Checklists.id)      AS count_items,
         (SELECT COUNT(Items.id)
          FROM   Items
          WHERE  Items.checklist_id = Checklists.id AND
                 Items.completed = "y")                   AS count_items_complete,
         (SELECT COUNT(Items.id)
          FROM   Items
          WHERE  Items.checklist_id = Checklists.id AND
                 Items.completed = "n")                   AS count_items_incomplete
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


/*************************************************************************
 * Return the id of a the newest checklist a user has created
 * 
 * id
**************************************************************************/ 
function getNewestChecklistID($userID) {
  $stmt = '
  SELECT Checklists.id
  FROM   Checklists
  WHERE  user_id = :userID
  ORDER  BY date_created DESC
  LIMIT  1';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind user id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}


// delete a checklist
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

// updates a checklst name and description
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

///////////////////////////////////////////////////////////////////
// Delete all of a users's checklists that have no items in them //
///////////////////////////////////////////////////////////////////
function deleteEmptyChecklists($userID) {
  $stmt = '
  DELETE FROM Checklists
  WHERE  id IN
         (SELECT *
          FROM   (SELECT id
                  FROM   Checklists
                  WHERE  user_id = :userID AND
                         0 IN
                         (SELECT Count(id)
                          FROM   Items
                          WHERE  checklist_id = Checklists.id)) AS x)';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind user id
  $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':userID', $userID, PDO::PARAM_INT);

  $sql->execute();
  return $sql;
}






/*****************************************************
 * ITEMS
******************************************************/ 

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

// updates an item's content and completed status
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

// insert an item into a checklist
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

// deletes an item
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


////////////////////////////////////////////////////////////////////////////////
// Deletes all items in a checklist marked as either complete or incomplete //
////////////////////////////////////////////////////////////////////////////////
function deleteCompletedItems($checklistID, $completed = 'y') {
  $stmt = '
  DELETE FROM Items
  WHERE  checklist_id = :checklistID AND
         completed = :completed';

  $sql = dbConnect()->prepare($stmt);

  // filter and bind checklistID
  $checklistID = filter_var($checklistID, FILTER_SANITIZE_NUMBER_INT);
  $sql->bindParam(':checklistID', $checklistID, PDO::PARAM_INT);

  // filter and bind completed
  $completed = filter_var($completed, FILTER_SANITIZE_STRING);
  $sql->bindParam(':completed', $completed, PDO::PARAM_STR);

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

// insert a list of items into a checklist
function insertItemList($checklistID, $items) {
  $stmt = 'INSERT INTO Items (checklist_id, content, date_created, date_modified) VALUES ';
  $stmt  = $stmt . getInsertItemListSqlStatement($checklistID, $items);

  $sql = dbConnect()->prepare($stmt);
  $sql->execute();

  return $sql;
}

// generate the insert statement for insertItemList()
function getInsertItemListSqlStatement($checklistID, $items) {
  $stmt     = '';
  $numItems = count($items);
  $count    = 0;

  // generate the string
  while ($count < $numItems) {
    $content  = filter_var($items[$count], FILTER_SANITIZE_STRING);
    $stmt = $stmt . "($checklistID, \"$content\", NOW(), NOW()),";
    $count++;
  }

  // remove the trailing comma from the string
  $stmt = substr(trim($stmt), 0, -1);

  return $stmt;
}





?>