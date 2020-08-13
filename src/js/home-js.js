const API = 'api.checklists.php';

// main function
$(document).ready(function() {
  addEventListeners();
  getChecklists();
});

function addEventListeners() {
  $(".btn-toggle-sidebar").on('click', toggleSidebar);

  $(".sidebar").on('click', '.list-group-item-checklist', function() {
    openChecklist(this);
  }); 
}

function toggleSidebar() {
  $('.sidebar').toggleClass('active');
}


// get the checklists from the server
function getChecklists() {
  var data = {
    function: 'get-checklists',
  }

  $.get(API, data, function(response) {
    displayChecklists(JSON.parse(response));
  });
}

// display the user checklists
function displayChecklists(checklists) {
  const size = checklists.length;
  var html = '';

  // load html
  for (var count = 0; count < size; count++) 
    html += getChecklistSidebarHtml(checklists[count]);
  

  // display html
  $(".sidebar .list-group").html(html);
}

// generates and returns the sidebar checklist html
function getChecklistSidebarHtml(checklist) {
  var html = '';
  html += '<button type="button" class="list-group-item list-group-item-checklist" data-checklist-id="' + checklist.id + '">';
  html += checklist.name + '</button>';

  return html;
}


// open a checklist
function openChecklist(selector) {

  var checklistID = $(selector).attr('data-checklist-id');

}


// get a checklist data
function getChecklist(checklistID) {
  var data = {
    function: 'get-checklist',
    id: checklistID,
  }

  $.get(API, data, function(response)) {
    console.log(response);
  }
}




