jQuery(function ($) {
    var params = wooconnector_slider_script_params; var custom_smuploader; $('#add-slide').click(function (e) {
        e.preventDefault(); if (custom_smuploader) { custom_smuploader.open(); return; }
        custom_smuploader = wp.media.frames.file_frame = wp.media({ title: 'Save In Slider', button: { text: 'Save In Slider' }, multiple: false }); custom_smuploader.on('select', function () { var selection = custom_smuploader.state().get('selection'); selection.map(function (attachment) { attachment = attachment.toJSON(); var urlsm = attachment.url; var checkimage = urlsm.split('.').pop().toUpperCase(); if (checkimage.length < 1) { return false; } else if (checkimage != "PNG" && checkimage != "JPG" && checkimage != "GIF" && checkimage != "JPEG") { alert("invalid extension " + checkimage); return false; } else { var attid = attachment.id; $.ajax({ method: "POST", url: params.ajax_url, data: { baseurl: params.baseurl, attachment_id: attid, type: 'addslide' }, success: function (response) { $('#woo-list-slide').append(response); } }); } }); }); custom_smuploader.open(); return false;
    }); $('.symb-support-input').on('mouseover', function (e) { e.preventDefault(); $(this).parent('.support-input').children('.tooltip-support-input').fadeIn(200); })
    $(document).on('mouseout', function (event) { if (!$(event.target).closest('.symb-support-input').length && !$(event.target).closest('.tooltip-support-input').length) { if ($('.symb-support-input').is(":visible") && $('.tooltip-support-input').is(":visible")) { $('.tooltip-support-input').fadeOut(200); } } })
});