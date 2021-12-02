jQuery(function($){
    $('body').on('click', '.tm_upload_image_button', function(e){
        e.preventDefault();
        aw_uploader = wp.media({
            title: 'Custom image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        }).on('select', function() {
            var attachment = aw_uploader.state().get('selection').first().toJSON();
            $('#tm_image').val(attachment.url);
            $('#tm_image_display').attr('src',attachment.url);
        })
        .open();
    });
});