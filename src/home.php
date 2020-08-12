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
  <title>Checklists</title>
</head>
<body>
  <?php include('navbar.php'); ?>
  <div class="container">
    <h1 class="text-center mt-5">Welcome <?php echo $user['name_first']; ?></h1>

    <?php
      // display alert if user attempted to create a checklist
      if (isset($_SESSION['checklist-created'])) {
        $created = $_SESSION['checklist-created'];
        if ($created == true)
          echo getAlert('Checklist created successfully.');
        else
          echo getAlert('Error when attempting to create checklist.', 'danger');

        // clear out session variable
        unset($_SESSION['checklist-created']);
      }
    ?>

    <!-- new checklist modal trigger button -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-new-checklist">
      New checklist
    </button>

    <!-- new checklist modal -->
    <div class="modal fade" id="modal-new-checklist" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">New Checklist</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- new checklist form -->
            <form class="form-new-checklist" method="post" action="api.checklists.php">
              <!-- name -->
              <div class="form-group">
                <label>Name</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class='bx bx-list-check'></i></span>
                  </div>
                  <input type="text" class="form-control" name="new-checklist-name" required4>
                </div>
              </div>

              <input type="submit" class="btn btn-primary float-right" value="Save checklist">
            </form>
          </div>
        </div>
      </div>
    </div>


















  </div>

  <?php include('footer.php'); ?>
  <script src="js/home-js.js"></script>

</body>
</html>