jQuery( document).ready(function($) {

    var fieldWithOptions = ['checkbox_acceptance', 'select_custom_post_type', 'multiselect_custom_post_type',
        'select_custom_taxonomy', 'multiselect_custom_taxonomy',
        'decimal_number', 'number_minmax', 'slider','token', 'fromto', 'country', 'web', 'tags'];

    var $selectBox = $('#select2-box');

    //on initial run for edit field page
    var fieldType = $('#fieldtype').val();
    var lastFieldType = fieldType;
    toggleSelectSettingsBox(fieldType);

    if ($.inArray(fieldType, fieldWithOptions) !== -1) {
        $('#' + fieldType).show();
    }

    // on field type selection change.
    $(document).on('change', '#fieldtype', function () {

        var fieldType = $(this).val();
        if ($.inArray(lastFieldType, fieldWithOptions) !== -1) {
            $('#' + lastFieldType).hide();
        }

        lastFieldType = fieldType;

        toggleSelectSettingsBox(fieldType);

        if ($.inArray(fieldType, fieldWithOptions) === -1) {
            return;
        }

        $('.bp-options-box').hide();
        $('#' + fieldType).show();

    });

    // Clicking into the custom date format field should select the Custom radio button.
    var $birthdate_format_custom_value = jQuery( '#bpxcftr-birthdate-date-format-custom-value' );
    var $birthdate_format_custom = jQuery( '#bpxcftr-birthdate-date-format-custom' );
    var $birthdate_format_sample = jQuery( '#bpxcftr-birthdate-date-format-custom-sample' );
    $birthdate_format_custom_value.on( 'focus', function() {
        $birthdate_format_custom.prop( 'checked', 'checked' );
    } );

    // Validate custom date field.
    var $birthdate_format_spinner = jQuery( '#bpxcftr-birthdate-date-format-custom-spinner' );
    $birthdate_format_custom_value.on( 'change', function( e ) {
        $birthdate_format_spinner.addClass( 'is-active' );
        jQuery.post( ajaxurl, {
                action: 'date_format',
                date: e.target.value
            },
            function( response ) {
                $birthdate_format_spinner.removeClass( 'is-active' );
                $birthdate_format_sample.html( response );
            } );
    } );

    /**
     * Toggle Select2 box based on preference.
     * @param selectedType
     */
    function toggleSelectSettingsBox( selectedType ) {

        if ( $.inArray( selectedType, BPXprofileCFTRAdmin.selectableTypes ) !== -1 ) {
            $selectBox.show();
        } else {
            $selectBox.hide();
        }

    }
});
