
// main function
$(document).ready(function() {
  addEventListeners();
});

function addEventListeners() {
  $(".btn-toggle-sidebar").on('click', toggleSidebar);
}

function toggleSidebar() {
  $('.sidebar').toggleClass('active');
}