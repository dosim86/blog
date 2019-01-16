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
    request: function(url, data = {}, method = 'get'){
        return $.ajax({
                url: url,
                data: data,
                method: method.toLowerCase(),
                headers: {
                    'X-AUTH-TOKEN': user.apiKey
                }
            })
            .then(function(resp) {
                if (!resp.nodisplay) {
                    notify(resp.type, resp.message);
                }
                return resp;
            })
            .fail(function(resp) {
                let data = $.parseJSON(resp.responseText);
                notify(data.type, data.message);
            })
        ;
    },
};
