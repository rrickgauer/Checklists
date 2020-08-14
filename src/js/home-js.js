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
  getChecklist(checklistID);

}


// get a checklist data
function getChecklist(checklistID) {
  var data = {
    function: 'get-checklist',
    id: checklistID,
  }

  $.get(API, data, function(response) {
    displayChecklist(JSON.parse(response));
  });
}

function displayChecklist(items) {
  const size = items.length;
  var checklistID = items[0].checklist_id;

  // header
  var html = getChecklistHeaderHtml(checklistID);

  // body
  for (var count = 0; count < size; count++) 
    html += getChecklistItemHtml(items[count]);
  
  // footer
  html += getChecklistFooterHtml();

  // add to the open checklists dom
  $("#checklists-open").append(html);
}

function getChecklistHeaderHtml(checklistID) {
  var html = '<div class="card card-checklist" data-checklist-id="';
  html += checklistID + '">';
  html += '<div class="card-header"><h4>Checklist_Name</h4>';
  html += '</div><div class="card-body">';
  html += '<div class="input-group input-group-new-item">';
  html += '<div class="input-group-prepend">';
  html += '<button class="btn btn-outline-secondary btn-add-item" type="button">';
  html += '<i class="bx bx-plus-circle"></i>';
  html += '</button>';
  html += '</div>';
  html += '<input type="text" class="form-control item-input-new">';
  html += '</div>';
  html += '<div class="items">';

  return html;
}

function getChecklistFooterHtml() {
  var html = '</div>'; // end items
  html += '</div>';
  html += '<div class="card-footer">';
  html += '<button type="button" class="btn btn-sm btn-secondary">Action</button>';
  html += '</div>';
  html += '</div>';

  return html;
}


function getChecklistItemHtml(item) {
  var html = '<div class="item" data-item-id="';
  html += item.id + '">';
  html += '<div class="left"><input class="item-checkbox" type="checkbox">';
  html += '<span class="item-content">' + item.content + '</span></div>';
  html += '<div class="right">';
  html += '<div class="dropleft">';
  html += '<i class="bx bx-dots-horizontal-rounded" data-toggle="dropdown"></i>';
  html += '<div class="dropdown-menu">';
  html += '<button class="dropdown-item" type="button">Action</button>';
  html += '</div>';
  html += '</div>';
  html += '</div>';
  html += '</div>';

  return html;
}




