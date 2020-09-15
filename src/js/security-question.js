///////////////
// Constants //
///////////////

const API = 'api.checklists.php';
const EMAIL_INPUT = $('input[name="reset-email"]');



//////////
// Main //
//////////
$(document).ready(function() {
  addEventListeners();
});


///////////////////////////////////////////////
// Adds event listeners to the html elements //
///////////////////////////////////////////////
function addEventListeners() {
  $('.btn-load-security-question').on('click', getSecurityQuestion);
}


function getSecurityQuestion() {
  var email = $(EMAIL_INPUT).val();

  var data = {
    function: "get-security-question",
    email: email,
  }

  $.get(API, data, function(response) {
    $('#question').text(response);
    $('#answer-section').removeClass('d-none');
  });


}