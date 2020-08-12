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
  <title>Checklists</title>
</head>
<body>
  <?php include('navbar.php'); ?>
  <div class="container">
    <h1 class="text-center mt-5">Welcome <?php echo $user['name_first']; ?></h1>


  </div>

  <?php include('footer.php'); ?>
  <script src="js/home-js.js"></script>

</body>
</html>