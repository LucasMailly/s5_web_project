
// If element with id 'search' is changed by a key pressed, and not changed within 1000ms, then redirect to home with search value
$('#search').on('keyup', function() {
    clearTimeout($.data(this, 'timer'));
    var wait = setTimeout(function() {
        window.location.href = Routing.generate('app_home', {search: $('#search').val()});
    }
    , 1000);
    $(this).data('timer', wait);
});