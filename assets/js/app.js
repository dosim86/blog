require('select2/dist/js/select2.min.js');

let toastr = require('toastr/toastr.js');
let Swal = require('sweetalert2/dist/sweetalert2.min.js');

global.$ = global.jQuery = $;
global.Swal = Swal;
global.notify = (function(type, message){
    if (type.toLowerCase() === 'error') {
        toastr.error(message, 'Error');
    } else if (type.toLowerCase() === 'info') {
        toastr.info(message, 'Info');
    } else {
        toastr.success(message, 'Success');
    }
});
global.appGet = function(url, data = {}){
    return $.get(url, data).fail(function(resp) {
        let data = $.parseJSON(resp.responseText);
        notify(data.type, data.message);
    });
};

$('.dropdown-toggle').dropdown();
$('.select2').select2({
    theme: 'bootstrap'
});
