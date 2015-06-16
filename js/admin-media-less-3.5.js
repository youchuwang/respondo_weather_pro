$geo_upload_media_obj = '';
function send_to_editor(html) {
    imgurl = jQuery('img',html).attr('src');
	
    jQuery('input', jQuery($geo_upload_media_obj).parent()).val(imgurl);
    jQuery('input', jQuery($geo_upload_media_obj).parent()).removeClass('required');
    jQuery('.image-region img', jQuery($geo_upload_media_obj).parent()).remove();
    jQuery('div.delete-img', jQuery($geo_upload_media_obj).parent()).removeClass('hide');
    jQuery('div.image-region', jQuery($geo_upload_media_obj).parent()).append('<img src="'+imgurl+'" width="260"/>');
    tb_remove();
}

function setbackgroundimage( index ) {
	$geo_upload_media_obj = this;
}

jQuery(document).ready(function () {
    jQuery('.upload_file_grp .file-upload').live('click', function(){
        $geo_upload_media_obj = this;
    });
});

function removebackgroundimage( index ){
	jQuery('#sparkweatherbkgimage' + index).val( '' );
	jQuery('#sparkweatherbkgimage_img' + index).attr('src' , '');
}