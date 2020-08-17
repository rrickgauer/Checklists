<?php

session_start();
// goto login.php if session is not set
if (!isset($_SESSION['userID'])) {
  header('Location: login.php');
  exit;
}

include('functions.php');

$user = getUser($_SESSION['userID'])->fetch(PDO::FETCH_ASSOC);

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

    <?php

    if (isset($_SESSION['user-info-updated'])) {
      if ($_SESSION['user-info-updated'] == true)
        echo getAlert('Your info has been updated');
      else
        echo getAlert('There was an error updated your information. Please try again', 'danger');

      unset($_SESSION['user-info-updated']);
    }

    ?>

    <!-- update info form -->
    <h3>Update basic info</h3>

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
  
    

    <?php

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


    ?>
    
    <!-- update password -->
    <h3 class="mt-5 mb-3">Update your password</h3>
    <form class="form-edit-password" method="post" action="api.checklists.php">

      <!-- current password -->
      <div class="form-group">
        <label for="edit-password-current">Current password</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
          </div>
          <input type="password" class="form-control" id="edit-password-current" name="edit-password-current" required>
        </div>
      </div>

      <!-- new password 1 -->
      <div class="form-group">
        <label for="edit-password-current">New password</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
          </div>
          <input type="password" class="form-control" id="edit-password-1" name="edit-password-1" required>
        </div>
      </div>


      <!-- new password 2 -->
      <div class="form-group">
        <label for="edit-password-current">Confirm new password</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
          </div>
          <input type="password" class="form-control" id="edit-password-2" name="edit-password-2" required>
        </div>
      </div>

      <input type="submit" value="Update password" class="btn btn-primary">

    </form>
    

    


























  </div>


<?php include('footer.php'); ?>
</body>
</html>