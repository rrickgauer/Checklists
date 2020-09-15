<?php

session_start();

// goto login.php if session is not set
if (!isset($_SESSION['userID'])) {
  header('Location: login.php');
  exit;
}

include('functions.php');
$user = getUser($_SESSION['userID'])->fetch(PDO::FETCH_ASSOC);  // get user data

?>

<!DOCTYPE html>
<html>
<head>
  <?php include('header.php'); ?>
  <title>Reset your password</title>
</head>
<body>


  <div class="container">

    <h1><?php echo $user['email']; ?></h1>

    <h1 class="text-center mt-5 mb-5">Reset your password</h1>
    
  </div>


<?php include('footer.php'); ?>
</body>
</html>