$('#btn-update-article').on('click', function () {
    $('#btn-update-article').attr('hidden', true);
    $('#btn-back-article').attr('hidden', false);
    $('#btn-save-article').attr('hidden', false);
    $('#btn-quantity-add').attr('hidden', true);
    $('#btn-quantity-remove').attr('hidden', true);
    $('#input-title').attr('hidden', false);
    $('#input-image').attr('hidden', false);
    $('#input-price').attr('hidden', false);
    $('#input-date').attr('hidden', false);
    $('#input-category').attr('hidden', false);
    $('#input-negotiation').attr('hidden', false);
    $('#input-used').attr('hidden', false);
    $('#input-quantity').attr('hidden', false);
    $('#input-value-title').attr('hidden', true);
    $('#input-value-image').attr('hidden', true);
    $('#input-value-price').attr('hidden', true);
    $('#input-value-date').attr('hidden', true);
    $('#input-value-negotiation').attr('hidden', true);
    $('#input-value-used').attr('hidden', true);
    $('#input-value-quantity').attr('hidden', true);
    $('#input-value-category').attr('hidden', true);
});

$('#btn-back-article').on('click', function () {
    $('#btn-update-article').attr('hidden', false);
    $('#btn-back-article').attr('hidden', true);
    $('#btn-save-article').attr('hidden', true);
    $('#btn-quantity-add').attr('hidden', false);
    $('#btn-quantity-remove').attr('hidden', false);
    $('#input-title').attr('hidden', true);
    $('#input-image').attr('hidden', true);
    $('#input-price').attr('hidden', true);
    $('#input-date').attr('hidden', true);
    $('#input-category').attr('hidden', true);
    $('#input-negotiation').attr('hidden', true);
    $('#input-used').attr('hidden', true);
    $('#input-quantity').attr('hidden', true);
    $('#input-value-title').attr('hidden', false);
    $('#input-value-image').attr('hidden', false);
    $('#input-value-price').attr('hidden', false);
    $('#input-value-date').attr('hidden', false);
    $('#input-value-negotiation').attr('hidden', false);
    $('#input-value-used').attr('hidden', false);
    $('#input-value-quantity').attr('hidden', false);
    $('#input-value-category').attr('hidden', false);
});

$('#btn-save-article').on('click', function () {
    let date = $('#input-date').val();
    let dateArray = date.split('-');

    let day = dateArray[2].replace(/^0+/, '');
    let month = dateArray[1].replace(/^0+/, '');
    let year = dateArray[0];
    console.log(day);
    console.log(month);
    console.log(year);
    $('#article_dateParution_day').val(day);
    $('#article_dateParution_month').val(month);
    $('#article_dateParution_year').val(year);

    $('#form-update-article').submit();
});