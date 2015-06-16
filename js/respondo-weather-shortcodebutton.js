(function(){
	tinymce.create('tinymce.plugins.respondo_weather_sls', {
		createControl : function(id, controlManager) {
			if (id == 'respondo_weather_sls_button') {
				// creates the button
				var button = controlManager.createButton('respondo_weather_sls_button', {
					title : 'Respondo Weather',
					image : '../wp-content/plugins/respondo-weather-pro/images/Respondo-Weather.png',
					onclick : function() {
						var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
						W = W - 50;
						H = H - 150;
						tb_show( 'Respondo Weather Shortcode', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=respondo-shortcode-dialog' );
						 // window.send_to_editor('[respondo-weather cityname="" unit="F"]');
						 
						 jQuery('#colorselectflatmode').ColorPicker({
							flat: true,
							onSubmit: function(hsb, hex, rgb, el) {
								jQuery(el).val(hex);
								jQuery(el).ColorPickerHide();
							},
							onBeforeShow : function () {
								jQuery(this).ColorPickerSetColor(jQuery('#backgroundimagecolor').value);
							},
							onChange: function (hsb, hex, rgb) {
								jQuery('#backgroundimagecolor').val('#' + hex);
							}
						});

						 // jQuery('#backgroundimagecolor').ColorPicker({
							// onSubmit: function(hsb, hex, rgb, el) {
								// jQuery(el).val(hex);
								// jQuery(el).ColorPickerHide();
							// },
							// onBeforeShow : function () {
								// jQuery(this).ColorPickerSetColor(this.value);
							// },
							// onChange: function (hsb, hex, rgb) {
								// jQuery('#backgroundimagecolor').val('#' + hex);
							// }
						// })
						// .bind('keyup', function(){
							// jQuery(this).ColorPickerSetColor(this.value);
						// });
					}
				});
				return button;
			}
			return null;
		}
	});

	tinymce.PluginManager.add('respondo_weather_sls', tinymce.plugins.respondo_weather_sls);
	
	jQuery("#close-weather-shortcode-dialog").click(function(){
		tb_remove();
	});
	
	jQuery("#insert-weather-shortcode").click(function(){
		var $weather_widget_width = jQuery("#weather_widget_width").val();
		var $weather_widget_time = jQuery("#weather_widget_time").val();
	
		var $weather_float = jQuery("#weather_float").val();

		var $weather_city = jQuery("#weather_city").val();
		var $showing_city_name = jQuery("#showingcity")[0].checked;
		
		if( $showing_city_name == true ){
			$showing_city_name = 'true';
		} else {
			$showing_city_name = 'false';
		}
		
		var $f_temper_unit = jQuery("#f_temper_unit").attr('checked');
		var $c_temper_unit = jQuery("#c_temper_unit").val();		

		var $temper_unit = 'C';
		if( $f_temper_unit == 'checked' ){
			$temper_unit = 'F';
		}
		
		var $backgroundimageurl = jQuery("#sparkweatherbkgimage_img_shortcode").attr('src');
		var $backgroundimagecolor = jQuery("#backgroundimagecolor").val();
		var $showingdays = jQuery("#showingdays")[0].checked;
		
		if( $showingdays == true ){
			$showingdays = 'true';
		} else {
			$showingdays = 'false';
		}
		
		window.send_to_editor( '[respondo-weather cityname="' + $weather_city + '" unit="' + $temper_unit + '" showingcityname="' + $showing_city_name + '" backgroundurl="' + $backgroundimageurl + '" backgroundcolor="' + $backgroundimagecolor + '" showing3days="' + $showingdays + '" width="' + $weather_widget_width + '" float="' + $weather_float + '" timedisplay="' + $weather_widget_time + '"]');
		tb_remove();
	});
})()