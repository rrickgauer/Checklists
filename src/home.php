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

  <div class="wrapper">
    <!-- checklist sidebar -->
    <div class="sidebar active">

      <h6>Your checklists</h6>

      <!-- checklists go here -->
      <div class="list-group"></div>
    </div>

    <!-- open checklists -->
    <div class="content">
      <div class="container-fluid">

        <div class="home-header mt-5 mb-5 mr-3 ml-3">
          <h1 class="text-center">Welcome <?php echo $user['name_first']; ?></h1>
          <?php displayChecklistCreated() ?>

          <button type="button" class="btn btn-secondary btn-toggle-sidebar">Toggle sidebar</button>

          <!-- new checklist modal trigger button -->
          <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-new-checklist">
            New checklist
          </button>
        </div>

        
        <div id="checklists-open">


          <div class="card card-checklist" data-checklist-id="2">
            <div class="card-header">
              <h4>Checklist_Name</h4>
            </div>
            <div class="card-body">

              <!-- input -->
              <div class="input-group input-group-new-item">
<!--                 <div class="input-group-prepend">
                  <button class="btn btn-outline-secondary btn-add-item" type="button">
                    <i class='bx bx-plus-circle'></i>
                  </button>
                </div>
                <input type="text" class="form-control item-input-new">
              </div>
              
              <div class="items">
                <div class="item" data-item-id="232">
                  <div class="left">
                    <input class="item-checkbox" type="checkbox">
                    <span class="item-content">This is the content of the item</span>
                  </div>
                  <div class="right">
                    <div class="dropleft">
                        <i class='bx bx-dots-horizontal-rounded' data-toggle="dropdown"></i>
                      <div class="dropdown-menu">
                        <button class="dropdown-item" type="button">Action</button>
                      </div>
                    </div>
                  </div>
                </div> -->
              </div>

            </div>
            <div class="card-footer">
              <button type="button" class="btn btn-sm btn-secondary">Action</button>
            </div>
          </div>



        </div>













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
                      <input type="text" class="form-control" name="new-checklist-name" required>
                    </div>
                  </div>

                  <input type="submit" class="btn btn-primary float-right" value="Save checklist">
                </form>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>


  <?php include('footer.php'); ?>
  <script src="js/home-js.js"></script>

</body>
</html>


<?php

// functions

// display whether or not checklist was created
function displayChecklistCreated() {
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
}


?>