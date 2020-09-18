<?php

session_start();
// goto login.php if session is not set
if (!isset($_SESSION['userID'])) {
  header('Location: logout.php');
  exit;
}

include('functions.php');


// attempt was made to delete the account
if (isset($_POST['password'])) {
  $deleteUser = true;

  $user     = getUser($_SESSION['userID'])->fetch(PDO::FETCH_ASSOC);
  $email    = $user['email'];
  $password = $_POST['password'];

  // check if email and password are a match,
  // if true, delete the account
  // if false, display error message
  if (isValidEmailAndPassword($email, $password)) {
    $result = deleteUser($_SESSION['userID']);
    header("Location: logout.php?user_deleted=1");
  } else {
    $deleteUser = false;
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <?php include('header.php'); ?>
  <title>Delete your account</title>
</head>
<body>

  <?php include('navbar.php'); ?>

  <div class="container">
    <h1 class="mt-5 mb-5 text-center">Delete account</h1>

    <div class="d-flex justify-content-center">
      <div class="card card-settings">
        <div class="card-body">

          <?php
          // display alert if user entered incorrect password
          if (isset($deleteUser)) {
            if ($deleteUser == false) {
              echo getAlert('Incorrect password. Your account was not deleted.', 'danger');
            }
          }
          ?>

          <form method="post">

            <!-- password -->
            <div class="form-group">
              <label for="new-email">Enter your password to confirm</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                </div>
                <input type="password" class="form-control" name="password" required>
              </div>
            </div>

            <!-- submit button -->
            <input type="submit" value="Delete your account" class="btn btn-danger">

          </form>
        </div>
      </div>
    </div>
  </div>

<?php include('footer.php'); ?>
</body>
</html>