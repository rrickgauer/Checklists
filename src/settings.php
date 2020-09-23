<?php

session_start();
// goto login.php if session is not set
if (!isset($_SESSION['userID'])) {
  header('Location: login.php');
  exit;
}

include('functions.php');

$user = getUser($_SESSION['userID'])->fetch(PDO::FETCH_ASSOC);

// display approriate message when info updated
function determineUserInfoUpdateMessage() {
  if (isset($_SESSION['user-info-updated'])) {
    if ($_SESSION['user-info-updated'] == true)
      echo getAlert('Your info has been updated');
    else
      echo getAlert('There was an error updated your information. Please try again', 'danger');

    unset($_SESSION['user-info-updated']);
  }
}

// display approriate message when password is updated
function determinePasswordUpdateMessage() {
  if (isset($_SESSION['user-password-updated'])) {
    if ($_SESSION['user-password-updated'])
      echo getAlert('Your password was updated.');
    else {
      if ($_SESSION['reason'] == 'incorrect-current-password')
        echo getAlert('Incorrect current password. Your password was not updated.', 'danger');
      else
        echo getAlert('There was an error updating your password. Please try again.', 'danger');

      unset($_SESSION['reason']);
    }

    unset($_SESSION['user-password-updated']);
  }
}


?>

<!DOCTYPE html>
<html>
<head>
  <?php include('header.php'); ?>
  <title>Account Settings</title>
</head>
<body>
  <?php include('navbar.php'); ?>

  <div class="container">
    <h1 class="text-center mt-5 mb-5">Account settings</h1>

    <!-- update info form -->
    <div class="d-flex justify-content-center">
      <div class="card card-settings">
        <div class="card-header">
          <h3>Contact info</h3>
        </div>

        <div class="card-body">

          <?php determineUserInfoUpdateMessage(); ?>

          <form class="form-user-info" method="post" action="api.checklists.php">
            <!-- email -->
            <div class="form-group">
              <label for="edit-email">Email address</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                </div>
                <input type="email" class="form-control" id="edit-email" name="edit-email" value="<?php echo $user['email']; ?>" required>
              </div>
            </div>

            <!-- first name -->
            <div class="form-group">
              <label for="edit-name-first">First name</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-user'></i></span>
                </div>
                <input type="text" class="form-control" id="edit-name-first" name="edit-name-first" value="<?php echo $user['name_first']; ?>" required>
              </div>
            </div>

            <!-- last name -->
            <div class="form-group">
              <label for="edit-name-last">Last name</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-user'></i></span>
                </div>
                <input type="text" class="form-control" id="edit-name-last" name="edit-name-last" value="<?php echo $user['name_last']; ?>" required>
              </div>
            </div>
            <input type="submit" value="Update account" class="btn btn-primary">
          </form>
        </div>
      </div>
    </div>

    <!-- update password -->
    <div class="d-flex justify-content-center">
      <div class="card card-settings">
        <div class="card-header">
          <h3>Your password</h3>
        </div>

        <div class="card-body">

          <?php determinePasswordUpdateMessage(); ?>

          <form class="form-edit-password" id="form-edit-password" method="post" action="api.checklists.php">
            <!-- current password -->
            <div class="form-group">
              <label for="edit-password-current">Current password</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                </div>
                <input type="password" class="form-control" id="edit-password-current" name="edit-password-current" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <!-- new password 1 -->
            <div class="form-group">
              <label for="edit-password-current">New password</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                </div>
                <input type="password" class="form-control edit-password" id="edit-password-1" name="edit-password-1" minlength="8" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>


            <!-- new password 2 -->
            <div class="form-group">
              <label for="edit-password-current">Confirm new password</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                </div>
                <input type="password" class="form-control edit-password" id="edit-password-2" name="edit-password-2" minlength="8" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <button type="button" class="btn btn-primary btn-update-password">Update password</button>

            <!-- <input type="submit" value="Update password" class="btn btn-primary"> -->
          </form>
        </div>
        
      </div>
    </div>

    <!-- delete account -->
    <div class="d-flex justify-content-center">
      <div class="card-settings">
        <h3>Delete account</h3>
        <p>Once you delete your account, there is no going back. Please be certain.</p>
        <a class="btn btn-sm btn-danger" href="delete-account.php">Delete your account</a>
      </div>
    </div>

  </div>


<?php include('footer.php'); ?>
<script src="js/settings-js.js"></script>
</body>
</html>