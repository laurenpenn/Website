function gformDeleteUploadedFile(formId, fieldId){
    var parent = jQuery("#field_" + formId + "_" + fieldId);

    //hiding preview
    parent.find(".ginput_preview").hide();

    //displaying file upload field
    parent.find("input[type=file]").show();

    //displaying post image label
    parent.find(".ginput_post_image_file").show();

    //clearing post image meta fields
    parent.find("input[type=text]").val('');

    //removing file from uploaded meta
    var files = jQuery.secureEvalJSON(jQuery('#gform_uploaded_files_' + formId).val());
    if(files){
        files["input_" + fieldId] = null;
        jQuery('#gform_uploaded_files_' + formId).val(jQuery.toJSON(files));
    }
}