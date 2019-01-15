import Routing from '../routing.js';

$(document).ready(function () {
    $('.js-subscribe').on('click', function () {
        let $this = $(this);
        app.request(Routing.generate('api_user_subscribe', { username: $this.data('username') }));
    });

    $('.js-unsubscribe').on('click', function () {
        Swal({
            title: 'Are you sure?',
            // text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.value) {
                let $this = $(this);
                app.request(Routing.generate('api_user_unsubscribe', { username: $this.data('username') })).then(function(data) {
                    if (data.type === 'success') {
                        $this.closest('.unsubscribe-item').remove();
                    }
                    // Swal('Notification!', data.message, data.type);
                });
            }
        });
    });
});