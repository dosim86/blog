import Cropper from 'cropperjs/dist/cropper';
import Routing from '../routing.js';

let previewImage = document.getElementById('avatar');
let coords = document.getElementById('user_profile_crop_coords');
let reader = new FileReader();
let cropper;

previewImage.addEventListener('load', function(){
     cropper = new Cropper(previewImage, {
        aspectRatio: 1,
        movable: false,
        scalable: false,
        zoomable: false,
        maxContainerWidth: '100%',
        crop: function(e){
            let x = e.detail.x.toFixed(2),
                y = e.detail.y.toFixed(2),
                w = e.detail.width.toFixed(2),
                h = e.detail.height.toFixed(2)
            ;

            if (x < 0) x = 0;
            if (y < 0) y = 0;

            coords.value = x + ':' + y + '/' + w + ':' + h;
        }
    });
});

reader.addEventListener('load', function (e) {
    previewImage.src = reader.result;
    previewImage.style = "max-height:300px";
});

window.previewLoadedFile = function () {
    let uploadInput = document.getElementById('user_profile_uploadedFile');
    let file = uploadInput.files[0];

    if (file) {
        if (cropper) cropper.destroy();
        reader.readAsDataURL(file);
    }
};

$('.js-generate-api-key-btn').on('click', function (e) {
    e.preventDefault();
    app.request(Routing.generate('api_util_token')).then(function (data) {
        $('#user_profile_apiKey').val(data);
    });
});