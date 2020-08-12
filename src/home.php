<?php 
session_start();
include('functions.php');

if (!isset($_SESSION['userID'])) {
  header('Location: login.php');
  exit;
}

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
    <h1 class="text-center mt-5">Checklists</h1>
    
  </div>

  <?php include('footer.php'); ?>
  <script src="js/home-js.js"></script>

</body>
</html>