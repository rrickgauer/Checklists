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
<body class="login-page">
  <div class="container">
    
    <div class="mt-big"></div>

    <h1 class="text-center mt-big">Checklists</h1>
    

    <?php 
    // check if user accont was deleted
    if(isset($_GET['user_deleted']) && $_GET['user_deleted'] == 1) {
      echo getAlert('Your account has been successfully deleted.'); 
    }
    ?>
    
    <div class="row">

      <div class="col-sm-12 col-md-6 mb-4">
        <img src="img/sign-in.svg" class="img-login">
      </div>
      
      <div class="col-sm-12 col-md-6">
        <ul class="nav nav-pills justify-content-center" id="project-pills-tab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#sign-up" role="tab">Sign up</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#log-in" role="tab">Log in</a>
          </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
          <div class="tab-pane fade show active" id="sign-up" role="tabpanel">
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
          </div>
        

          <div class="tab-pane fade" id="log-in" role="tabpanel">
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
        </div>

      </div>

    </div>
  </div>


<?php include('footer.php'); ?>
</body>
</html>