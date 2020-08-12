<?php

// clear session data
session_start();
session_destroy();
$_SESSION = array();

include('functions.php');
?>
<!DOCTYPE html>
<html>
<head>
  <?php include('header.php'); ?>
  <title>Login</title>
</head>
<body>
  <div class="container">

    <h1 class="text-center mt-5 mb-5">Login to checklists</h1>
    
    <!-- create new account form -->
    <h4>Create account</h4>
    <form class="form-create-account" method="post" action="api.checklists.php">
      <!-- email -->
      <div class="form-group">
        <label>Email</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-envelope'></i></span>
          </div>
          <input type="email" class="form-control" name="new-email" required>
        </div>
      </div>

      <!-- first name -->
      <div class="form-group">
        <label>First name</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-user'></i></span>
          </div>
          <input type="text" class="form-control" name="new-name-first" required>
        </div>
      </div>

      <!-- last name -->
      <div class="form-group">
        <label>Last name</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-user'></i></span>
          </div>
          <input type="text" class="form-control" name="new-name-last" required>
        </div>
      </div>

      <!-- password -->
      <div class="form-group">
        <label>Password</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
          </div>
          <input type="password" class="form-control" name="new-password" required>
        </div>
      </div>

      <input type="submit" class="btn btn-primary" value="Create account">
    </form>


    <!-- login form -->
    <h4 class="mt-5">Login to your account</h4>
    <form class="form-login" method="post" action="api.checklists.php">
      <!-- email -->
      <div class="form-group">
        <label>Email</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-envelope'></i></span>
          </div>
          <input type="email" class="form-control" name="login-email" required>
        </div>
      </div>

      <!-- password -->
      <div class="form-group">
        <label>Password</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
          </div>
          <input type="password" class="form-control" name="login-password" required>
        </div>
      </div>

      <input type="submit" class="btn btn-primary" value="Log in">
    </form>
  </div>


<?php include('footer.php'); ?>
</body>
</html>