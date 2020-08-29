const API = 'api.checklists.php';
const ANIMATION_ENTRANCE = 'animate__flipInX';
const ANIMATION_EXIT = 'animate__flipOutX';

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

  $("#checklists-open").on('click', ".btn-edit-checklist-name", function() {
    openEditChecklistModal(this);
  });

  $("#modal-edit-checklist .btn-save-checklist-name").on('click', updateChecklistName);

  $(".dropdown-sidebar .btn-sort-option").on('click', function() {
    sortSidebar(this);
  });

  $("#checklists-open").on('change', ".show-completed-items", function() {
    toggleCompletedItems(this);
  });

  $("#checklists-open").on('change', ".items-sort-select", function() {
    sortItems(this);
  });

  $("#checklists-open").on('click', '.dropdown-complete-items .dropdown-item', function() {
    toggleCompleteItems(this);
  });
}

function displayAlert(text) {
  $.toast({
    text: text,
    position: 'bottom-center',
    loader: false,
    bgColor: '#3D3D3D',
    textColor: 'white'
  });
}

function toggleSidebar() {
  $('.sidebar').toggleClass('active');
  $(".btn-toggle-sidebar").toggleClass('is-active');
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
  // html += '<button type="button" class="list-group-item list-group-item-checklist"';
  html += '<button type="button" class="list-group-item list-group-item-checklist" ';
  html += 'data-checklist-id="' + checklist.id + '"';
  html += 'data-checklist-date-created="' + checklist.date_created + '" ';
  html += '>';
  html +=  '<span class="checklist-name">' + checklist.name + '</span>';
  html += '<span class="badge badge-secondary badge-pill">' + checklist.count_items + '</span>';
  html += '</button>';
  return html;
}


// open a checklist
function openChecklist(selector) {

  if ($(selector).hasClass('active'))
    return;

  var checklistID = $(selector).attr('data-checklist-id');
  var checklistName = $(selector).find('.checklist-name').text();
  getChecklist(checklistID, checklistName);
  $(selector).addClass('active');

}


// get a checklist data
function getChecklist(checklistID, checklistName) {
  var data = {
    function: 'get-checklist',
    id: checklistID,
  }

  $.get(API, data, function(response) {
    displayChecklist(checklistID, checklistName, JSON.parse(response));
  });
}

function displayChecklist(checklistID, checklistName, items) {
  const size = items.length;

  // header
  var html = getChecklistHeaderHtml(checklistID, checklistName);

  // body
  for (var count = 0; count < size; count++) 
    html += getChecklistItemHtml(items[count]);
  
  // footer
  html += getChecklistFooterHtml();

  // add to the open checklists dom
  $("#checklists-open").append(html);
}

function getChecklistHeaderHtml(checklistID, checklistName) {
  var html = '<div class="card card-checklist animate__animated animate__faster  ' + ANIMATION_ENTRANCE + '" data-checklist-id="';
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


  /*********
  * Toolbar
  **********/
  html += '<div class="toolbar d-flex justify-content-between">';

  // show done
  html += '<div class="form-check form-check-inline">';
  html += '<input class="form-check-input show-completed-items" type="checkbox" checked>';
  html += '<label class="form-check-label">Show done</label>';
  html += '</div>';

  // sorting
  html += '<div class="d-flex align-items-center">';
  html += '<span class="mr-2"><b>Sort:</b></span>';
  html += '<select class="form-control form-control-sm items-sort-select">';
  html += '<option value="original" selected>Original</option>';
  html += '<option value="alphabetical">Alphabetical</option>';
  html += '</select>';
  html += '</div>';

  html += '</div>'; // end toolbar

  html += '<div class="items">';

  return html;
}

function getChecklistFooterHtml() {
  var html = '</div>'; // end items
  html += '</div>';
  html += '<div class="card-footer d-flex">';
  html += '<button type="button" class="btn btn-sm btn-secondary btn-edit-checklist-name">Edit name</button>';

  // mark items complete/incomplete dropdown
  html += '<div class="dropup dropdown-complete-items">';
  html += '<button class="btn btn-sm btn-secondary" type="button" data-toggle="dropdown">Mark items</button>';
  html += '<div class="dropdown-menu">';
  html += '<button class="dropdown-item" type="button" data-value="complete">Complete</button>';
  html += '<button class="dropdown-item" type="button" data-value="incomplete">Incomplete</button>';
  html += '</div>';
  html += '</div>';

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

      // item is now completed
      if ($(item).hasClass('item-completed')) {
        // check if show done checkbox is checked
        var showDoneCheckbox = $(item).closest('.card-checklist').find('.show-completed-items');

        // hide item if show done checkbox is unchecked
        if ($(showDoneCheckbox).is(':checked') == false) {
          $(item).hide();
        }
      }
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

function getOpenedChecklist(checklistID) {
  return checklist = $('.card-checklist[data-checklist-id="' + checklistID + '"]');
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
      displayAlert('Item was deleted');
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

      // display alert
      displayAlert('Item updated');
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

      displayAlert('Checklist deleted');
    }
  });
}


function openEditChecklistModal(btn) {
  var checklist          = $(btn).closest('.card-checklist');
  var checklistID        = $(checklist).attr('data-checklist-id');
  var oldChecklistName   = $(checklist).find('.card-header h4').text();
  var editChecklistModal = $("#modal-edit-checklist");

  // load the original name into the edit checklist modal name input
  $(editChecklistModal).find("input[name='edit-checklist-name']").val(oldChecklistName);

  // set the id of the modal
  $(editChecklistModal).attr('data-checklist-id', checklistID);

  // show the modal
  $('#modal-edit-checklist').modal('show');
}


function updateChecklistName() {
  var modal       = $("#modal-edit-checklist");
  var checklistID = $(modal).attr('data-checklist-id');
  var newName     = $(modal).find('input[name="edit-checklist-name"]').val();

  var data = {
    function: "update-checklist-name",
    checklistID: checklistID,
    name: newName,
  }

  $.post(API, data, function(response) {
    if (response == 'success') {
      setChecklistName(checklistID, newName);
      $(modal).modal('hide');
    }
  });

}


function setChecklistName(id, name) {
  // update sidebar name
  var sidebarChecklist = getSidebarChecklist(id);
  $(sidebarChecklist).find('.checklist-name').text(name);

  // update open checklist
  var openChecklist = getOpenedChecklist(id);
  $(openChecklist).find('.card-header h4').text(name);
}



function sortSidebar(sortOption) {

  switch ($(sortOption).attr('data-sort-value')) {
    case 'name-asc':
      sortChecklistsByNameAsc();
      break;
    case 'name-desc':
      sortChecklistsByNameDesc();
      break;
    case 'item-count-largest':
      sortChecklistsByItemCountLargest();
      break;
    case 'date-oldest':
      sortChecklsitsByDateOldest();
      break;
    case 'date-newest':
      sortChecklsitsByDateNewest();
      break;
    default:
      getChecklists();
      break;
  }
}

function sortChecklistsByNameAsc() {
  var checklists = $(".sidebar .list-group-item-checklist");

  checklists.sort(function (a, b) {
    var textA = $(a).find('.checklist-name').text().toUpperCase();
    var textB = $(b).find('.checklist-name').text().toUpperCase();
    return (textA < textB) ? -1 : 1;
  });

  $(".sidebar .list-group").html(checklists);
}

function sortChecklistsByNameDesc() {
  var checklists = $(".sidebar .list-group-item-checklist");
  
  checklists.sort(function (a, b) {
    var textA = $(a).find('.checklist-name').text().toUpperCase();
    var textB = $(b).find('.checklist-name').text().toUpperCase();
    return (textA > textB) ? -1 : 1;
  });

  $(".sidebar .list-group").html(checklists);
}


function sortChecklistsByItemCountLargest() {
  var checklists = $(".sidebar .list-group-item-checklist");
  
  checklists.sort(function (a, b) {
    var textA = parseInt($(a).find('.badge').text());
    var textB = parseInt($(b).find('.badge').text());
    return (textA > textB) ? -1 : 1;
  });

  $(".sidebar .list-group").html(checklists);
}


function sortChecklsitsByDateOldest() {
  var checklists = $(".sidebar .list-group-item-checklist");
  
  checklists.sort(function (a, b) {
    var textA = new Date($(a).attr('data-checklist-date-created'));
    var textB = new Date($(b).attr('data-checklist-date-created'));
    return (textA - textB);
  });

  $(".sidebar .list-group").html(checklists);
}

function sortChecklsitsByDateNewest() {
  var checklists = $(".sidebar .list-group-item-checklist");
  
  checklists.sort(function (a, b) {
    var textA = new Date($(a).attr('data-checklist-date-created'));
    var textB = new Date($(b).attr('data-checklist-date-created'));
    return (textB - textA);
  });

  $(".sidebar .list-group").html(checklists);
}


function toggleCompletedItems(checkbox) {
  var items = $(checkbox).closest('.card-checklist').find('.item.item-completed');

  if (checkbox.checked)
    $(items).show();
  else
    $(items).hide();
}



function sortItems(selector) {
  var checklist = $(selector).closest('.card-checklist');
  var items = $(checklist).find('.item');

  var sortedItems = null;
  if ($(selector).val() == 'alphabetical') {
    sortedItems = getSortedItemsByNameAsc(items);
  } else {
    getSortedItemsByOriginal($(checklist).attr('data-checklist-id'));
  }

  $(checklist).find('.items').html(sortedItems);
}

function getSortedItemsByNameAsc(items) {
  items.sort(function (a, b) {
    var textA = $(a).find('.item-content').text();
    var textB = $(b).find('.item-content').text();
    return (textA < textB) ? -1 : 1;
  });

  return items;
}


function getSortedItemsByOriginal(checklistID) {
  var data = {
    function: 'get-checklist',
    id: checklistID,
  }

  $.getJSON(API, data, function(items) {
    var html = '';

    for (var count = 0; count < items.length; count++) {
      html += getChecklistItemHtml(items[count]);
    }

    var openChecklist = getOpenedChecklist(checklistID);

    $(openChecklist).find('.items').html(html);
  })
}

// decide whether to mark all items complete or incomplete
function toggleCompleteItems(btn) {
  var checklistID   = $(btn).closest('.card-checklist').attr('data-checklist-id');

  if ($(btn).attr('data-value') == 'complete')
    completeAllItems(checklistID);
  else
    incompleteAllItems(checklistID);
}

// mark all items in a checklist as complete
function completeAllItems(checklistID) {
  var data = {
    checklistID: checklistID,
    function: 'complete-all-items',
  };

  $.post(API, data);
  setItemsCompleted(checklistID, true);
}


// mark all items in a checklist as incomplete
function incompleteAllItems(checklistID) {
  var data = {
    checklistID: checklistID,
    function: 'incomplete-all-items',
  };

  $.post(API, data);
  setItemsCompleted(checklistID, false);
}


// display all items as completed or incomplete
function setItemsCompleted(checklistID, response) {
  var checklist = getOpenedChecklist(checklistID);

  // check the item checkboxes and add class item-completed
  if (response == true) 
    $(checklist).find('.item').addClass('item-completed').find('.item-checkbox').prop('checked', true);
  
  // uncheck all checkboxes and remove class item-completed
  else
    $(checklist).find('.item').removeClass('item-completed').find('.item-checkbox').prop('checked', false);
}