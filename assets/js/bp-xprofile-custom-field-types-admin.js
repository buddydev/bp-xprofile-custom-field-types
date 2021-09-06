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
