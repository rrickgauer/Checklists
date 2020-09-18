<?php

//////////////////////////////////////////////////
// This file clears all session and cookie data //
//                                              //
// then sends user to login page                //
//////////////////////////////////////////////////


// clear cookie
setcookie('userID', '', time() - 3600, "/");

// clear session data
session_start();
session_destroy();
$_SESSION = array();

// check if user deleted their account
if (isset($_GET['user_deleted']) && $_GET['user_deleted'] == 1)
  header('Location: login.php?user_deleted=1');
else
  header('Location: login.php');

exit;
?>