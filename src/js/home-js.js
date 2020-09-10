const API                = 'api.checklists.php';
const ANIMATION_ENTRANCE = 'animate__flipInX';
const ANIMATION_EXIT     = 'animate__flipOutX';

// main function
$(document).ready(function() {
  addEventListeners();
  getChecklists();
  enableAutosizeScript();
});

// adds the event listeners to the elements
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

  $("#checklists-open").on('keypress', ".edit-content-input", function(e) {
    if (e.keyCode == 13) {
      e.preventDefault();
      saveItemEdit(this);
    }
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

  $("#modal-edit-checklist .btn-save-checklist-name").on('click', updateChecklist);

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

  $("#checklists-open").on('click', '.btn-open-copy-modal', function() {
    openCopyModal(this);
  });

  $("#modal-copy-items .btn-copy-items").on('click', copyItems);

  // resize the description textare size when the modal is opened
  $('#modal-edit-checklist, #modal-new-checklist').on('shown.bs.modal', function (e) {
    autosize.update($('textarea.autosize'));
  });

  $("#checklists-open").on('click', '.btn-toggle-description', function() {
    toggleChecklistDescription(this);
  });

  $("#checklists-open").on('click', '.btn-open-paste-modal', function() {
    openPasteModal(this);
  });

  $("#modal-paste-items .btn-paste-items").on('click', pasteItems);
}

// implements the autosize script for the textareas
function enableAutosizeScript() {
  autosize($('textarea.autosize'));
}

// displays an alert on the screen
function displayAlert(text) {
  $.toast({
    text: text,
    position: 'bottom-center',
    loader: false,
    bgColor: '#3D3D3D',
    textColor: 'white'
  });
}

// shows/hides the sidebar
function toggleSidebar() {
  $('.sidebar').toggleClass('active');
  $(".btn-toggle-sidebar").toggleClass('is-active');
}

// get the checklists from the server
function getChecklists() {
  var data = {
    function: 'get-checklists',
  }

  // send request to the api
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
  // if checklsit is already open don't do anything
  if ($(selector).hasClass('active'))
    return;

  $("#no-open-checklists-img").hide();  // hide the initial image in the open checklists section

  var checklistID = $(selector).attr('data-checklist-id');  // get the checklist id
  getChecklist(checklistID);
  $(selector).addClass('active');
}


// get a checklist data
function getChecklist(checklistID) {
  var data = {
    function: "get-checklist-and-items",
    checklistID: checklistID,
  };

  $.get(API, data, function(response) {
    var data = JSON.parse(response);
    displayChecklist(data.checklist, data.items);
  });

}

// display a checklist
function displayChecklist(checklist, items) {
  const size = items.length;

  // header
  var html = getChecklistHeaderHtml(checklist);

  // body
  for (var count = 0; count < size; count++) 
    html += getChecklistItemHtml(items[count]);
  
  // footer
  html += getChecklistFooterHtml();

  // add to the open checklists dom
  $("#checklists-open").append(html);
}

// generates and returns a checklist header html
function getChecklistHeaderHtml(checklist) {
  var html = '<div class="card card-checklist animate__animated animate__faster  ' + ANIMATION_ENTRANCE + '" data-checklist-id="';
  html += checklist.id + '">';
  html += '<div class="card-header">';

  // name
  html += '<div class="card-header-name">';
  html += '<div class="d-flex">';
  html += '<h4>' + checklist.name + '</h4>';
  html += '<button class="btn btn-sm btn-xs btn-toggle-description" type="button"><i class="bx bx-detail"></i></button>';
  html += '</div>';
  html += '<div>';
  html += '<button type="button" class="close close-checklist float-right"><span aria-hidden="true">×</span></button>';
  html += '</div>';
  html += '</div>';

  // description

  if (checklist.description == null)
    html += '<div class="card-header-description d-none"></div>';
  else
    html += '<div class="card-header-description d-none">' + checklist.description + '</div>';

  // dates
  html += '<div class="card-header-dates">';
  html += '<span class="date-created">' + checklist.date_created_display + '</span>';                                                         // date created
  html += '<span>&nbsp;&bull;&nbsp;</span>';

  // date modified
  html += '<span class="date-modified">Updated <span class="date-modified-time">';
  if (checklist.date_modified_minutes < 60)
    html += checklist.date_modified_minutes + ' minutes ago';
  else if (checklist.date_modified_hours < 24)
    html += checklist.date_modified_hours + ' hours ago';
  else
    html += checklist.date_modified_days + ' days ago';
   html += '</span></span>'; // date modified
  
  html += '</div>'; // end dates



  // item counts
  html += '<div class="card-header-counts">';   
  html += '<span class="item-count"><span class="count">' + checklist.count_items + '</span> items &bull; </span>';               // total
  html += '<span class="item-count-complete"><span class="count">' + checklist.count_items_complete + '</span> completed &bull; </span>';  // complete
  html += '<span class="item-count-incomplete"><span class="count">' + checklist.count_items_incomplete + '</span> incomplete</span>';       // incomplete
  html += '</div>';

  // end card header
  html += '</div>';

  // card-body
  html += '<div class="card-body">';
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

// generates a checklist footer html
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

  // display copy over items modal button
  html += '<button type="button" class="btn btn-sm btn-secondary btn-open-copy-modal">Copy in items</button>';

  // button that opens modal-paste-items
  html += '<button type="button" class="btn btn-sm btn-secondary btn-open-paste-modal">Import items</button>';

  // delete checklist button
  html += '<button type="button" class="btn btn-sm btn-danger btn-delete-checklist">Delete</button>';

  


  html += '</div>';
  html += '</div>';

  return html;
}

// generates an item html
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

  // dropdown
  html += '<div class="dropleft">';
  html += '<button type="button" class="btn btn-sm btn-xs" data-toggle="dropdown">';
  html += '<i class="bx bx-dots-horizontal-rounded"></i>';
  html += '</button>';
  html += '<div class="dropdown-menu">';
  html += '<button class="dropdown-item btn-edit-item" type="button">Edit</button>';
  html += '<button class="dropdown-item btn-delete-item" type="button">Delete</button>';
  html += '</div>'; // dropdown menu

  html += '</div>'; // div.right
  html += '</div>'; // card footer
  html += '</div>'; // card

  return html;
}

////////////////////////////////////////////
// update the item counts for a checklist //
////////////////////////////////////////////
function updateChecklistDisplayData(checklistID) {
  var data = {
    function: "get-checklist",
    checklistID: checklistID,
  }

  $.getJSON(API, data, function(response) {
    // open checklist
    var openChecklist = getOpenedChecklist(checklistID);
    $(openChecklist).find('.item-count .count').text(response.count_items);
    $(openChecklist).find('.item-count-complete .count').text(response.count_items_complete);
    $(openChecklist).find('.item-count-incomplete .count').text(response.count_items_incomplete);
    $(openChecklist).find('.card-header-description').text(response.description);

    // sidebar checklist
    var sideBarChecklist = getSidebarChecklist(checklistID);
    $(sideBarChecklist).find('.badge-pill').text(response.count_items);

    // checklist name
    setChecklistName(checklistID, response.name);
  });
}

////////////////////////////////
// update the checklist name //
////////////////////////////////
function setChecklistName(id, name) {
  // update sidebar name
  var sidebarChecklist = getSidebarChecklist(id);
  $(sidebarChecklist).find('.checklist-name').text(name);

  // update open checklist
  var openChecklist = getOpenedChecklist(id);
  $(openChecklist).find('.card-header h4').text(name);
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

    if (response != 'success')
      return;

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

    var checklistID = $(checkbox).closest('.card-checklist').attr('data-checklist-id');
    updateChecklistDisplayData(checklistID);
  });
} 

// close a checklist
function closeChecklist(closeBtn) {
  var checklist = $(closeBtn).closest('.card-checklist');
  var checklistID = $(checklist).attr('data-checklist-id');
  $(checklist).remove();

  var sideBarChecklist = $('.sidebar .list-group-item-checklist[data-checklist-id="' + checklistID + '"]').removeClass('active');

  if (!isAChecklistOpen())
    $("#no-open-checklists-img").show();

}

// returns a sidebar checklist object by searching for it's id
function getSidebarChecklist(checklistID) {
  return $('.sidebar .list-group-item-checklist[data-checklist-id="' + checklistID + '"]');
}

// returns an open checklist object by searching for it's id
function getOpenedChecklist(checklistID) {
  return checklist = $('.card-checklist[data-checklist-id="' + checklistID + '"]');
}

// bool that says if there are any open checklists
function isAChecklistOpen() {
  if ($('.list-group-item-checklist.active').length == 0)
    return false;
  else
    return true;
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
    if (response == 'error') {
      displayAlert('Error. There was an issue adding the item. Please try again.')
      return;
    }

    var item = JSON.parse(response);

    // append item to the checklist html
    var itemHtml = getChecklistItemHtml(item);
    $(checklist).find('.items').prepend(itemHtml);
    $(checklist).find('.item-input-new').val('');

    updateChecklistDisplayData(checklistID);
  });
}

// delete an item
function deleteItem(btn) {
  var item = $(btn).closest(".item");
  var itemID = $(item).attr('data-item-id');
  var checklistID = $(item).closest('.card-checklist').attr('data-checklist-id');

  var data = {
    function: 'delete-item',
    itemID: itemID,
  }

  $.post(API, data, function(response) {
    if (response != 'success') {
      displayAlert('Error. There was an issue deleting the item. Please try again.')
      return;
    }

    $(item).remove();
    updateChecklistDisplayData(checklistID);
    displayAlert('Item was deleted');
  });
}

// edit an item content
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

// saves an edit to an item
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

// cancels an item edit and sets the content to its original data
function cancelItemEdit(btn) {
  var item   = $(btn).closest('.item');
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

// delete a checklist
function deleteChecklist(btn) {
  if (!confirm('Are you sure you want to delete this checklist?'))
    return;

  var checklist   = $(btn).closest('.card-checklist');
  var checklistID = $(checklist).attr('data-checklist-id');

  var data = {
    function: 'delete-checklist',
    checklistID: checklistID,
  }

  $.post(API, data, function(response) {
    if (response == 'success') {
      
      // remove the open checklist
      $(checklist).remove();      

      // remove the checklist from the sidebar
      var sideBarChecklist = getSidebarChecklist(checklistID);  
      $(sideBarChecklist).remove();

      displayAlert('Checklist deleted');
    }
  });
}

// open the edit checklist modal
function openEditChecklistModal(btn) {
  var checklist          = $(btn).closest('.card-checklist');
  var checklistID        = $(checklist).attr('data-checklist-id');
  var editChecklistModal = $("#modal-edit-checklist");
  var oldChecklistName   = $(checklist).find('.card-header h4').text();

  var data = {
    function: "get-checklist",
    checklistID: checklistID,
  };

  $.get(API, data, function(response) {
    var checklist = JSON.parse(response);

    // load the original name into the edit checklist modal name input
    $(editChecklistModal).find("input[name='edit-checklist-name']").val(checklist.name);

    // load the description
    $(editChecklistModal).find("textarea[name='edit-checklist-description']").val(checklist.description);

    // set the id of the modal
    $(editChecklistModal).attr('data-checklist-id', checklist.id);

    // show the modal
    $('#modal-edit-checklist').modal('show');
  });
}

// updates the checklist name
function updateChecklist() {
  var modal          = $("#modal-edit-checklist");
  var checklistID    = $(modal).attr('data-checklist-id');
  var newName        = $(modal).find('input[name="edit-checklist-name"]').val();
  var newDescription = $(modal).find('textarea[name="edit-checklist-description"]').val();

  var data = {
    function: "update-checklist",
    checklistID: checklistID,
    name: newName,
    description: newDescription,
  }

  $.post(API, data, function(response) {
    if (response == 'success') {
      updateChecklistDisplayData(checklistID);
      $(modal).modal('hide');
      displayAlert('Checklist updated');
    }
  });
}

// sort the side bar based on selected option
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

// sort sidebar by names asc
function sortChecklistsByNameAsc() {
  var checklists = $(".sidebar .list-group-item-checklist");

  checklists.sort(function (a, b) {
    var textA = $(a).find('.checklist-name').text().toUpperCase();
    var textB = $(b).find('.checklist-name').text().toUpperCase();
    return (textA < textB) ? -1 : 1;
  });

  $(".sidebar .list-group").html(checklists);
}

// sort sidebar by names desc
function sortChecklistsByNameDesc() {
  var checklists = $(".sidebar .list-group-item-checklist");
  
  checklists.sort(function (a, b) {
    var textA = $(a).find('.checklist-name').text().toUpperCase();
    var textB = $(b).find('.checklist-name').text().toUpperCase();
    return (textA > textB) ? -1 : 1;
  });

  $(".sidebar .list-group").html(checklists);
}

// sort sidebar by the largest item count
function sortChecklistsByItemCountLargest() {
  var checklists = $(".sidebar .list-group-item-checklist");
  
  checklists.sort(function (a, b) {
    var textA = parseInt($(a).find('.badge').text());
    var textB = parseInt($(b).find('.badge').text());
    return (textA > textB) ? -1 : 1;
  });

  $(".sidebar .list-group").html(checklists);
}

// sort checklists by oldest
function sortChecklsitsByDateOldest() {
  var checklists = $(".sidebar .list-group-item-checklist");
  
  checklists.sort(function (a, b) {
    var textA = new Date($(a).attr('data-checklist-date-created'));
    var textB = new Date($(b).attr('data-checklist-date-created'));
    return (textA - textB);
  });

  $(".sidebar .list-group").html(checklists);
}

// sort checklists by newest
function sortChecklsitsByDateNewest() {
  var checklists = $(".sidebar .list-group-item-checklist");
  
  checklists.sort(function (a, b) {
    var textA = new Date($(a).attr('data-checklist-date-created'));
    var textB = new Date($(b).attr('data-checklist-date-created'));
    return (textB - textA);
  });

  $(".sidebar .list-group").html(checklists);
}

// show/hide a checklist's completed items 
function toggleCompletedItems(checkbox) {
  var items = $(checkbox).closest('.card-checklist').find('.item.item-completed');

  if (checkbox.checked)
    $(items).show();
  else
    $(items).hide();
}

// sort the items 
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

// sort the items by names asc
function getSortedItemsByNameAsc(items) {
  items.sort(function (a, b) {
    var textA = $(a).find('.item-content').text();
    var textB = $(b).find('.item-content').text();
    return (textA < textB) ? -1 : 1;
  });

  return items;
}

// sort the items by original order
function getSortedItemsByOriginal(checklistID) {
  var data = {
    function: 'get-checklist-items',
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

// opens up the copy items modal
function openCopyModal(btn) {
  // get list of checklists and their ids
  var checklists = $('.list-group-item-checklist');
  const size = checklists.length;
  var html = '';

  // set the modal id to the open checklist that will be the destination for the items
  var checklistID = $(btn).closest('.card-checklist').attr('data-checklist-id');
  $("#modal-copy-items").attr('data-checklist-id', checklistID);

  // sort the checklists
  checklists.sort(function (a, b) {
    var nameA = $(a).find('.checklist-name').text().toUpperCase();
    var nameB = $(b).find('.checklist-name').text().toUpperCase();
    return (nameA < nameB) ? -1 : 1;
  });

  // generate the radio buttons html
  for (var count = 0; count < size; count++) {
    var checklistID = $(checklists[count]).attr('data-checklist-id');
    var checklistName = $(checklists[count]).find('.checklist-name').text();
    html += getCopyItemModalRadioHtml(checklistID, checklistName);
  }

  $('#modal-copy-items .available-checklists').html(html);
  $('#modal-copy-items').modal('show');
}

// generates the html for the copy item modal radio buttons
function getCopyItemModalRadioHtml(checklistID, checklistName) {
  var html = '<div class="form-check">';
  html += '<input class="form-check-input" type="radio" name="radio-available-checklists" value="' + checklistID + '">';
  html += '<label class="form-check-label">' + checklistName + '</label></div>';
  
  return html;
}

// copies over items into the checklist from another one
function copyItems() {
  var destinationID = $('#modal-copy-items').attr('data-checklist-id');
  var sourceID      = $('input[name="radio-available-checklists"]:checked').val();

  var data = {
    function: 'copy-items',
    sourceID: sourceID,
    destinationID: destinationID,
  }

  $.post(API, data, function(response) {
    getSortedItemsByOriginal(destinationID);
  });

  $('#modal-copy-items').modal('hide');
  displayAlert('Items copied over');
}

// toggle the display of a checklist's description
function toggleChecklistDescription(btn) {
  $(btn).closest('.card-checklist').find('.card-header-description').toggleClass('d-none');
}

// opens the paste items modal
function openPasteModal(btn) {
  var checklist   = $(btn).closest('.card-checklist');
  var checklistID = $(checklist).attr('data-checklist-id');
  var modal       = $('#modal-paste-items');
  
  // set the modal id to the checklist id
  $(modal).attr('data-checklist-id', checklistID);

  // show the modal
  $('#modal-paste-items').modal('show');
}

// add the list of items to the checklist
function pasteItems() {
  var modal       = $('#modal-paste-items');
  var checklistID = $(modal).attr('data-checklist-id');
  var newItems    = JSON.stringify($('#paste-items-input').val().split("\n"));

  var data = {
    function: "add-item-list",
    items: newItems,
    checklistID: checklistID,
  };

  $.post(API, data, function(response) {
    // dont do anything if there was an error
    if (response != 'success')
      return;

    getSortedItemsByOriginal(checklistID);  // reload the checklist items
    $('#modal-paste-items').modal('hide');  // close the modal
    $('#paste-items-input').val('');        // clear the input
    displayAlert('Items added');            // display alert
  });
}