<?php

session_start();
// goto login.php if session is not set
if (!isset($_SESSION['userID'])) {
  header('Location: login.php');
  exit;
}

include('functions.php');


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