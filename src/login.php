<?php

session_start();

// go to home page if user has already logged on
if (isset($_COOKIE['userID'])) {
  $_SESSION['userID'] = $_COOKIE['userID'];
  header('Location: home.php');
  exit;
}


include('functions.php');

function printSecurityQuestions() {
  $securityQuestions = getSecurityQuestions();
  
  $html = '';
  while ($question = $securityQuestions->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<option value = "' . $question['id'] . '">' . $question['question'] . '</option>';
  }

  echo $html;
}

// determine what error message to display when creating account
function createAccountAttemptMessage() {
  if (isset($_GET['create-account']) && $_GET['create-account'] == 'failed') {
    $reason = $_GET['reason'];

    // email already exists.
    if ($reason == 'email-exists')
      echo getAlert('Error. Email is already taken. Please try again.', 'danger');
    // unknown error
    else if ($reason == 'unknown')
      echo getAlert('There was an error creating your account. Please try again.', 'danger');
  }
}

// determine what error message to display when loggin in
function displayLoginErrorMessage() {
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
}

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

          <!-- create new account form -->
          <div class="tab-pane fade show active" id="sign-up" role="tabpanel">

            <?php createAccountAttemptMessage(); ?>

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
                  <input type="password" class="form-control" name="new-password" minlength="8" required>
                </div>
              </div>

              <!-- security question -->
              <div class="form-row">
                <!-- dropdown -->
                <div class="form-group col-md-6">
                  <label>Security question</label>                  
                  <select name="new-security-question" class="form-control" required>
                    <?php printSecurityQuestions(); ?>
                  </select>
                </div>

                <!-- answer -->
                <div class="form-group col-md-6">
                  <label>Answer</label>
                  <input type="text" class="form-control" name="new-security-question-answer" required>
                </div>
              </div>

              <input type="submit" class="btn btn-primary" value="Create account">
            </form>
          </div>
        
          <!-- login form -->
          <div class="tab-pane fade" id="log-in" role="tabpanel">

            <?php displayLoginErrorMessage() ?>
            
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

              <div class="d-flex justify-content-between align-items-baseline">
                <input type="submit" class="btn btn-primary" value="Log in">
                <a href="security-question.php" title="I forgot my password">I forgot my password</a>
              </div>

              
            </form>
          </div>
        </div>

      </div>

    </div>
  </div>


<?php include('footer.php'); ?>
</body>
</html>