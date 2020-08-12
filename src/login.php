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
    

    <?php
      // error when creating account
      if (isset($_GET['create-account']) && $_GET['create-account'] == 'failed') {
        $reason = $_GET['reason'];

        // email already exists.
        if ($reason == 'email-exists')
          echo getAlert('Error. Email is already taken. Please try again.', 'danger');
        // unknown error
        else if ($reason == 'unknown')
          echo getAlert('There was an error creating your account. Please try again.', 'danger');
      }
    ?>

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

    <?php
      // error when loggin in
      if (isset($_GET['login']) && $_GET['login'] == 'failed') {
        $reason = $_GET['reason'];

        // email does not exist.
        if ($reason == 'email-undetected')
          echo getAlert('Email does not exist..', 'danger');
        // email and password do not match
        else if ($reason == 'email-password-match')
          echo getAlert('Email and password do not match.', 'danger');
      }
    ?>

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