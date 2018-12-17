let toastr = require('toastr/toastr.js');

global.$ = global.jQuery = $;

$('.dropdown-toggle').dropdown();

global.notify = (function(type, message){
    if (type.toLowerCase() === 'error') {
        toastr.error(message, 'Error');
    } else if (type.toLowerCase() === 'info') {
        toastr.info(message, 'Info');
    } else {
        toastr.success(message, 'Success');
    }
});
