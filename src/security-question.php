<?php include('functions.php'); ?>

<!DOCTYPE html>
<html>
<head>
  <?php include('header.php'); ?>
  <title>Security Question</title>
</head>
<body>


  <div class="container">

    <h1 class="text-center mt-5 mb-5">Reset your password</h1>

    <div class="d-flex justify-content-center">
      <div class="card card-settings">
        <div class="card-body">

          <form id="question-form" method="post" action="api.checklists.php">
            <div class="form-group">
              <label>Email</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                </div>
                <input type="email" class="form-control" name="reset-email">
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary btn-load-security-question">Get security question</button>

            <div id="answer-section" class="d-none mt-5">
              <!-- answer -->
              <div class="form-group">
                <label id="question"></label>
                <input type="text" class="form-control" name="reset-answer" required>
              </div>

              <input type="submit" class="btn btn-sm btn-primary" value="Submit answer">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>  


  <?php include('footer.php'); ?>
  <script src="js/security-question.js"></script>
</body>
</html>