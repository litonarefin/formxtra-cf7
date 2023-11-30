// Frontend Script
console.log('frontend');
jQuery(document).ready(function ($) {

    var forms = $(".wpcf7");
    var signs = [];

    forms.each(function (k, form) {

        var formId = $(this).find('input[name="_wpcf7"]').val();

        $(form).find("#formxtra-cf7-signature-pad").each(function (i, wrap) {
            var convertButton = $('.formxtra-cf7-' + formId).find("#convertButton");
            var signature_canvas = $('.formxtra-cf7-' + formId).find("#signature-canvas");
            var confirm_message = $('.formxtra-cf7-' + formId).find("#confirm_message");
            var fileInput = $('.formxtra-cf7-' + formId).find('#formxtra_cf7_img');
            fileInput.css('display', 'none');
            var clearButton = $('.formxtra-cf7-' + formId).find("#clear-button");
            var control_div = $('.formxtra-cf7-' + formId).find(".control_div");

            var data;
            var pad_bg_color = fileInput.attr('bg-color');
            var pen_color = fileInput.attr('pen-color');


            var canvas = $(wrap).find('canvas').get(0);
            var signaturePad = new SignaturePad(canvas, {
                includeBackgroundColor: true,
                backgroundColor: pad_bg_color,
                penColor: pen_color,
            });





            signs[k + '-' + i] = signaturePad;
            signs[k + '-' + i].addEventListener('endStroke', function (e) {
                data = signaturePad.toDataURL('image/png');

                control_div.css('display', 'block');
                confirm_message.text('');

            });





            canvas.style.cursor = "crosshair";

            $(convertButton).click(function (e) {
                e.preventDefault();
                confirm_message.text('Signature Confirmed');
                confirm_message.css({ 'color': '#46B450', 'font-weight': '500' });



                const image = new Image();

                image.src = data;

                image.setAttribute('class', 'Uacf7UploadedImageForSign_' + formId);

                image.style = 'display:none';

                document.body.appendChild(image);

                /** This need to be appened to get it's property: by Masum */

                const imagePreview = document.querySelector('.Uacf7UploadedImageForSign_' + formId);

                const dataUrl = imagePreview.src;
                const blob = dataURLtoBlob(dataUrl);

                const fileName = 'signature.jpg';
                const file = new File([blob], fileName);

                const fileList = new DataTransfer();
                fileList.items.add(file);

                fileInput.prop("files", fileList.files);


                function dataURLtoBlob(dataUrl) {

                    const parts = dataUrl.split(';base64,');

                    const contentType = parts[0].split(':')[1];

                    const raw = window.atob(parts[1]);

                    const rawLength = raw.length;

                    const uint8Array = new Uint8Array(rawLength);

                    for (let i = 0; i < rawLength; ++i) {
                        uint8Array[i] = raw.charCodeAt(i);
                    }
                    return new Blob([uint8Array], { type: contentType });
                }

            });


            // Clear Canvas

            clearButton.click(function (e) {
                e.preventDefault();

                fileInput.val('');
                signaturePad.clear();
                signs = [];

                confirm_message.text('Please sign first and confirm your signature before form submission');
                confirm_message.css({ 'color': '#FFB900', 'font-weight': '500' });
                control_div.css('display', 'none');
                $('.Uacf7UploadedImageForSign_' + formId).remove();

            });


            // /** Make Empty the Signature Art Board after Form Submission */

            $('.formxtra-cf7-' + formId).find('.wpcf7-submit').click(function () {
                signaturePad.clear();
                signs = [];
                confirm_message.text('');
            });

            /** Preventing file system opening */

            $('.formxtra-cf7-' + formId).find('#formxtra_cf7_img').click(function (e) {
                e.preventDefault();
            });

        });

    });

});