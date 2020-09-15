<?php

include('functions.php');

?>

<!DOCTYPE html>
<html>
<head>
  <?php include('header.php'); ?>
  <title>Security Question</title>
</head>
<body>


  <div class="container">

    <h1 class="text-center mt-5 mb-5">Reset your password</h1>

    <!-- email -->
    <div class="form-group">
      <label>Email</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class='bx bx-envelope'></i></span>
        </div>
        <input type="email" class="form-control" name="reset-email">
      </div>
    </div>


    <div id="question-section" class="mt-5">
      <p id="question">questrion goes here</p>
      <input type="text" class="form-control" name="reset-answer">
    </div>    
  </div>  


  <?php include('footer.php'); ?>
  <script src="js/security-question.js"></script>
</body>
</html>