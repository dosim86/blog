$(document).ready(function () {
    $('.js-subscribe').on('click', function (e) {
        e.preventDefault();

        let $this = $(this);
        app.request($this.attr('href'));
    });

    $('.js-unsubscribe').on('click', function (e) {
        e.preventDefault();

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
                app.request($this.attr('href')).then(function(data) {
                    if (data.type === 'success') {
                        $this.closest('.unsubscribe-item').remove();
                    }
                    // Swal('Notification!', data.message, data.type);
                });
            }
        });
    });
});