(function ($) {
    'use strict';
    // colors
    if (!Modernizr.inputtypes.color) {
        // No html5 field colorpicker => Calling jscolor.
        $('.bpxcftr-color').addClass('color');
    }

    $('#profile-edit-form').attr('enctype', 'multipart/form-data');
    $('#signup-form').attr('enctype', 'multipart/form-data');
    $('#your-profile').attr('enctype', 'multipart/form-data');

    // Slider.
    $('input.bpxcftr-slider').on('input', function () {
        $('#output-' + $(this).attr('id')).html($(this).val());
    });

})(jQuery);