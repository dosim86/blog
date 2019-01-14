let toastr = require('toastr/toastr.js');
let Swal = require('sweetalert2/dist/sweetalert2.min.js');

let notify = (function(type, message){
    if (message) {
        if (type.toLowerCase() === 'error') {
            toastr.error(message, 'Error');
        } else if (type.toLowerCase() === 'info') {
            toastr.info(message, 'Info');
        } else {
            toastr.success(message, 'Success');
        }
    }
});

global.$ = global.jQuery = $;
global.Swal = Swal;
global.app = {
    csrfToken: (function(){
        return $('meta[name=csrf_token]').attr('content');
    }()),

    request: function(url, data = {}, type = 'get'){
        data.token = this.csrfToken;

        let preSuccess = function(resp) {
            if (!resp.nodisplay) {
                notify(resp.type, resp.message);
            }
            return resp;
        };

        let fail = function(resp) {
            let data = $.parseJSON(resp.responseText);
            notify(data.type, data.message);
        };

        return type.toLowerCase() === 'post'
            ? $.post(url, data).then(preSuccess).fail(fail)
            : $.get(url, data).then(preSuccess).fail(fail)
        ;
    },
};
