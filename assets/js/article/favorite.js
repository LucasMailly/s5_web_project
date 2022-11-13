// on click on button with class like-btn send ajax request to like or unlike article and update button class

$(document).onload(function() {
    $(document).on('click', '.like-btn', function (e) {
        e.preventDefault();

        const $this = $(this);
        const url = Routing.generate('app_article_' + $this.data('action'), {id: $this.data('id')});

        // update button class, data-action and child icon
        $this.data('action', $this.data('action') === 'add' ? 'drop' : 'add');
        $this.toggleClass('btn-danger btn-outline-danger');
        $this.find('i').toggleClass('fa-solid fa-regular');
        $this.find('svg').attr('data-prefix', $this.find('svg').data('prefix') === 'fas' ? 'far' : 'fas');


        // send ajax request, if it fails, revert button class
        $.ajax({
            url: url,
            method: 'POST'
        }).fail(function () {
            $this.data('action', $this.data('action') === 'add' ? 'drop' : 'add');
            $this.toggleClass('btn-danger btn-outline-danger');
            $this.find('i').toggleClass('fa-solid fa-regular');
        });
    });
});