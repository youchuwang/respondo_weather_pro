var custom_options_ph_media_frame;

function setbackgroundimage( index ){
	if ( custom_options_ph_media_frame ) {
		custom_options_ph_media_frame.open();
		return;
	} else {
		custom_options_ph_media_frame = wp.media.frames.custom_options_ph_media_frame = wp.media({
			className: 'media-frame custom_options_ph_media',
			frame: 'select',
			multiple: false,
			title: custom_options_ph_media.title,
			library: {
				type: 'image'
			},
			button: {
				text:  custom_options_ph_media.button
			},
			displaySettings: true,
			displayUserSettings: true
		});
		custom_options_ph_media_frame.open();
	}
	
	$obj = this;

	custom_options_ph_media_frame.on('select', function(){
		var media_attachment = custom_options_ph_media_frame.state().get('selection').first().toJSON();
		jQuery('#sparkweatherbkgimage' + index).val(media_attachment.url);
		jQuery('#sparkweatherbkgimage_img' + index).attr('src' , media_attachment.url);

		custom_options_ph_media_frame = false;
	});
		
	return false;
}


function removebackgroundimage( index ){
	jQuery('#sparkweatherbkgimage' + index).val( '' );
	jQuery('#sparkweatherbkgimage_img' + index).attr('src' , '');
}