
// Main
$(document).ready(function() {
  addEventListeners();
});



function addEventListeners() {
  $('.form-edit-password input').on('keyup', function() {
    removeIsInvalidClass(this);
  });
  $('.btn-update-password').on('click', updatePassword);
}

function removeIsInvalidClass(input) {
  $(input).removeClass('is-invalid');
  $(input).closest('.form-edit-password').find('.invalid-feedback').text('');
}

function updatePassword() {
  // clear all invalid form text and colors
  $('.form-edit-password input').removeClass('is-invalid');
  $('.form-edit-password .invalid-feedback').text('');

  var currentPassword = $('#edit-password-current');
  var newPassword1    = $('#edit-password-1');
  var newPassword2    = $('#edit-password-2');

  // is current password empty
  if ($(currentPassword).val() == '') {
    $(currentPassword).closest('.form-group').find('.invalid-feedback').text('Please fill out this field');
    $(currentPassword).addClass('is-invalid');
    return;
  }

  // is new password empty
  if ($(newPassword1).val() == '') {
    $(newPassword1).closest('.form-group').find('.invalid-feedback').text('Please fill out this field');
    $(newPassword1).addClass('is-invalid');
    return;
  }

  // is confirm new password empty
  if ($(newPassword2).val() == '') {
    $(newPassword2).closest('.form-group').find('.invalid-feedback').text('Please fill out this field');
    $(newPassword2).addClass('is-invalid');
    return;
  }

  // do new passwords match
  if ($(newPassword1).val() != $(newPassword2).val()) {
    $(newPassword2).closest('.form-group').find('.invalid-feedback').text('Passwords must match');
    $('.edit-password').addClass('is-invalid');
    return;
  }

  // submit form
  document.getElementById('form-edit-password').submit();

}