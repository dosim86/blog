let toastr = require('toastr/toastr.js');

global.$ = global.jQuery = $;

$('.dropdown-toggle').dropdown();

global.notify = (function(type, message){
    if (type.toLowerCase() === 'error') {
        toastr.error(message, 'Error');
    } else {
        toastr.success(message, 'Success');
    }
});

