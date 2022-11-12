$('#btn-update-user').on('click', function () {
    $('#btn-update-user').attr('hidden', true);
    $('#btn-back-user').attr('hidden', false);
    $('#btn-save-user').attr('hidden', false);
    $('#input-avatar').attr('hidden', false);
    $('#input-username').prop('disabled', false);
    $('#input-email').prop('disabled', false);
    $('#input-noSIRET').prop('disabled', false);
    $('#input-name').prop('disabled', false);
    $('#input-phone').prop('disabled', false);
});

$('#btn-back-user').on('click', function () {
    $('#btn-update-user').attr('hidden', false);
    $('#btn-back-user').attr('hidden', true);
    $('#btn-save-user').attr('hidden', true);
    $('#input-avatar').attr('hidden', true);
    $('#input-username').prop('disabled', true);
    $('#input-email').prop('disabled', true);
    $('#input-noSIRET').prop('disabled', true);
    $('#input-name').prop('disabled', true);
    $('#input-phone').prop('disabled', true);
});