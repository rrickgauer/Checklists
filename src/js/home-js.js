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


  $("#checklists-open").on('click', ".item-checkbox", function() {
    toggleItemComplete(this);
  });

  $("#checklists-open").on('click', ".close-checklist", function() {
    closeChecklist(this);
  });

  $("#checklists-open").on('click', ".btn-add-item", function() {
    addItem(this);
  });

  // add item when enter key is hit
  $("#checklists-open").on('keypress', ".item-input-new", function(e) {
    if (e.keyCode == 13) {
      e.preventDefault();
      addItem(this);
    }
  });

  $("#checklists-open").on('click', ".btn-delete-item", function() {
    deleteItem(this);
  });

  $("#checklists-open").on('click', ".btn-edit-item", function() {
    editItem(this);
  });

  $("#checklists-open").on('click', ".btn-edit-item-save", function() {
    saveItemEdit(this);
  });

  $("#checklists-open").on('click', ".btn-edit-item-cancel", function() {
    cancelItemEdit(this);
  });

  $("#checklists-open").on('click', ".btn-delete-checklist", function() {
    deleteChecklist(this);
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
  html += checklist.name;
  html += '<span class="badge badge-secondary badge-pill">' + checklist.count_items + '</span>';
  html += '</button>';
  return html;
}


// open a checklist
function openChecklist(selector) {

  if ($(selector).hasClass('active'))
    return;

  var checklistID = $(selector).attr('data-checklist-id');
  getChecklist(checklistID);
  $(selector).addClass('active');

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
  var html = getChecklistHeaderHtml(checklistID, items[0].checklist_name);

  // body
  for (var count = 0; count < size; count++) 
    html += getChecklistItemHtml(items[count]);
  
  // footer
  html += getChecklistFooterHtml();

  // add to the open checklists dom
  $("#checklists-open").append(html);
}

function getChecklistHeaderHtml(checklistID, checklistName) {
  var html = '<div class="card card-checklist" data-checklist-id="';
  html += checklistID + '">';
  html += '<div class="card-header"><h4>' + checklistName + '</h4>';

  // close button
  html += '<button type="button" class="close close-checklist"><span aria-hidden="true">&times;</span></button>';

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
  html += '<button type="button" class="btn btn-sm btn-secondary btn-edit-checklist-name">Edit name</button>';
  html += '<button type="button" class="btn btn-sm btn-danger btn-delete-checklist">Delete</button>';
  html += '</div>';
  html += '</div>';

  return html;
}


function getChecklistItemHtml(item) {
  var html = '';

  if (item.completed == 'n')
    html = '<div class="item" data-item-id="';
  else
    html = '<div class="item item-completed" data-item-id="';

  html += item.id + '">';

  if (item.completed == 'n')
    html += '<div class="left"><input class="item-checkbox" type="checkbox">';
  else
    html += '<div class="left"><input class="item-checkbox" type="checkbox" checked>';
  
  html += '<span class="item-content">' + item.content + '</span></div>';
  html += '<div class="right">';
  html += '<div class="dropleft">';
  html += '<i class="bx bx-dots-horizontal-rounded" data-toggle="dropdown"></i>';
  html += '<div class="dropdown-menu">';
  html += '<button class="dropdown-item btn-edit-item" type="button">Edit</button>';
  html += '<button class="dropdown-item btn-delete-item" type="button">Delete</button>';
  html += '</div>';
  html += '</div>';
  html += '</div>';
  html += '</div>';

  return html;
}

// toggle an item's completed status
function toggleItemComplete(checkbox) {
  var item    = $(checkbox).closest('.item');
  var itemID  = $(item).attr('data-item-id');
  var content = $(item).find('.item-content').text();

  // check if checkbox is checked
  var completed = 'n';
  if ($(checkbox).is(":checked")) 
    completed = 'y';

  var data = {
    function: "update-item",
    itemID: itemID,
    content: content,
    completed: completed,
  }


  $.post(API, data, function(response) {
    if (response == 'success') {
      $(item).toggleClass('item-completed');
    }
  });

} 

// close a checklist
function closeChecklist(closeBtn) {
  var checklist = $(closeBtn).closest('.card-checklist');
  var checklistID = $(checklist).attr('data-checklist-id');
  $(checklist).remove();

  var sideBarChecklist = $('.sidebar .list-group-item-checklist[data-checklist-id="' + checklistID + '"]').removeClass('active');
}

function getSidebarChecklist(checklistID) {
  return $('.sidebar .list-group-item-checklist[data-checklist-id="' + checklistID + '"]');
}


// add an item to a checklist
function addItem(addItemBtn) {
  var checklist   = $(addItemBtn).closest(".card-checklist");
  var checklistID = $(checklist).attr('data-checklist-id');
  var content     = $(checklist).find('.item-input-new').val();

  var data = {
    function: 'add-item',
    checklistID: checklistID,
    content: content,
  }

  $.post(API, data, function(response) {
    var itemHtml = getChecklistItemHtml(JSON.parse(response));
    $(checklist).find('.items').prepend(itemHtml);
    $(checklist).find('.item-input-new').val('');
  });
}


function deleteItem(btn) {
  var item = $(btn).closest(".item");
  var itemID = $(item).attr('data-item-id');

  var data = {
    function: 'delete-item',
    itemID: itemID,
  }

  $.post(API, data, function(response) {
    if (response == 'success') {
      $(item).remove();
    }
  });
}

function editItem(btn) {
  var item = $(btn).closest(".item");
  var itemID = $(item).attr('data-item-id');
  var originalContent = $(item).find('.item-content').text();

  var html = '<div class="edit-content"><div class="input">';
  html += '<input type="text" class="form-control edit-content-input" value="' + originalContent + '"></div>';
  html += '<div class="buttons">';
  html += '<button type="button" class="btn btn-sm btn-primary btn-edit-item-save">Save</button>';
  html += '<button type="button" class="btn btn-sm btn-danger btn-edit-item-cancel">Cancel</button>';
  html += '</div></div>';
  $(item).html(html);
}


function saveItemEdit(btn) {
  var item = $(btn).closest('.item');
  var itemID = $(item).attr('data-item-id');
  var newContent = $(item).find('.edit-content-input').val();

  var completed = 'n';
  if ($(item).hasClass('item-completed'))
    completed = 'y';

  var data = {
    function: 'update-item',
    itemID: itemID,
    content: newContent,
    completed: completed,
  }

  $.post(API, data, function(response) {
    if (response == 'success') {
      var updatedItem = {
        id: itemID,
        content: newContent,
        completed: completed,
      }

      var newHtml = getChecklistItemHtml(updatedItem);
      $(item).replaceWith(newHtml);
    }
  });
}

function cancelItemEdit(btn) {
  var item = $(btn).closest('.item');
  var itemID = $(item).attr('data-item-id');

  var data = {
    function: "get-item",
    itemID: itemID,
  }

  $.get(API, data, function(response) {
    var itemHtml = getChecklistItemHtml(JSON.parse(response));
    $(item).replaceWith(itemHtml);
  });

}


function deleteChecklist(btn) {

  if (!confirm('Are you sure you want to delete this checklist?'))
    return;

  var checklist = $(btn).closest('.card-checklist');
  var checklistID = $(checklist).attr('data-checklist-id');

  var data = {
    function: 'delete-checklist',
    checklistID: checklistID,
  }

  $.post(API, data, function(response) {
    if (response == 'success') {
      $(checklist).remove();
      var sideBarChecklist = getSidebarChecklist(checklistID);
      $(sideBarChecklist).remove();
    }
  });
}


