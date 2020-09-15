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

    <h1 class="text-center mt-5 mb-5">Reset your password</h1>

    <div class="d-flex justify-content-center">
      <div class="card card-settings">
        <div class="card-body">

          <form class="form-user-info" method="post" action="api.checklists.php">

            <!-- new password 1 -->
            <div class="form-group">
              <label for="reset-password-1">New password</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                </div>
                <input type="password" class="form-control" id="reset-password-1" name="reset-password-1" required>
              </div>
            </div>

            <!-- new password 2 -->
            <div class="form-group">
              <label for="reset-password-2">Confirm new password</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                </div>
                <input type="password" class="form-control" id="reset-password-2" name="reset-password-2" required>
              </div>
            </div>

            <input type="submit" value="Reset your password" class="btn btn-sm btn-primary">
          </form>
        </div>
      </div>
    </div>

    
  </div>


  <?php include('footer.php'); ?>
</body>
</html>