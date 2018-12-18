let toastr = require('toastr/toastr.js');
let Swal = require('sweetalert2/dist/sweetalert2.min.js');

$('.dropdown-toggle').dropdown();

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
