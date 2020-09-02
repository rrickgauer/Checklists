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

      <div class="split align-items-center">
        <h5 class="ml-3 mt-3 mb-3 mr-2">
          Your checklists (<?php echo $user['count_checklists']; ?>)
        </h5>

        <div class="dropleft dropdown-sidebar">
          <button class="btn btn-sm btn-xs btn-light mr-3" type="button" data-toggle="dropdown" data-display="static">Actions</button>
          <div class="dropdown-menu">
            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modal-new-checklist">New checklist</button>
            <div class="dropdown-divider"></div>
            <h6 class="dropdown-header">Sorting</h6>
            <button type="button" class="dropdown-item btn-sort-option" data-sort-value="original">Original</button>
            <button type="button" class="dropdown-item btn-sort-option" data-sort-value="name-asc">Name ascending</button>
            <button type="button" class="dropdown-item btn-sort-option" data-sort-value="name-desc">Name descending</button>
            <button type="button" class="dropdown-item btn-sort-option" data-sort-value="date-oldest">Oldest</button>
            <button type="button" class="dropdown-item btn-sort-option" data-sort-value="date-newest">Newest</button>
            <button type="button" class="dropdown-item btn-sort-option" data-sort-value="item-count-largest">Most items</button>
          </div>
        </div>  
      </div>

      <!-- checklists go here -->
      <div class="list-group"></div>
    </div>

    <!-- open checklists -->
    <div class="content">

        <div class="home-header mt-5 mr-3 ml-3">
          
          <!-- toggle sidebar -->
          <button class="hamburger hamburger--elastic is-active btn-toggle-sidebar" type="button">
            <span class="hamburger-box">
              <span class="hamburger-inner"></span>
            </span>
          </button>

          <?php displayChecklistCreated() ?>

        </div>

        <!-- open checklists -->
        <div class="checklists-wrapper">
          <div id="checklists-open"></div>
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

                  <!-- description -->
                  <div class="form-group">
                    <label>Description</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class='bx bx-detail'></i></span>
                      </div>
                      <textarea class="form-control" name="new-checklist-description" rows="1"></textarea>
                    </div>
                  </div>

                  <!-- submit button -->
                  <input type="submit" class="btn btn-primary float-right" value="Save checklist">
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- edit checklist modal -->
        <div class="modal fade" id="modal-edit-checklist" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Checklist</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <!-- edit checklist form -->
                <form class="form-edit-checklist">
                  <!-- name -->
                  <div class="form-group">
                    <label>Name</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class='bx bx-list-check'></i></span>
                      </div>
                      <input type="text" class="form-control" name="edit-checklist-name" required>
                    </div>
                  </div>
                  
                  <!-- description -->
                  <div class="form-group">
                    <label>Description</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class='bx bx-detail'></i></span>
                      </div>
                      <textarea class="form-control" name="edit-checklist-description" rows="1"></textarea>
                    </div>
                  </div>

                  <!-- submit button -->
                  <button type="button" class="btn btn-primary btn-save-checklist-name float-right">Save</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- copy over items -->
        <div class="modal fade" id="modal-copy-items" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Copy over items</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>Select the checklist that has the items you want copied over</p>

                <!-- list of radio options of available checklists -->
                <div class="available-checklists"></div>

                <button type="button" class="btn btn-primary btn-copy-items mt-3 float-right">Copy over items</button>
                
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