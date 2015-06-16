<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "weather_spark_pro" );' ) );

class weather_spark_pro extends WP_Widget {
	private $respondo_weather_widget_entry_index;
	
	function widget($args, $instance) {
		global $weather_plugin_name;
		
		$this->respondo_weather_widget_entry_index++;
		
        extract( $args );
		
		$apikey = get_option( 'weather_api_key' );
		
		if( $apikey == '' ) {
			// echo '<p>Please Insert Wunderground Weather API Key.</p>';
		} else {
			if( file_exists( ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json" ) && strpos( file_get_contents( ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json" ), 'error' ) ) {
				// echo '<p>Wunderground Weather API Connection Error.</p>';
			} else {
				echo $before_widget;
				
				$zipcode = $instance['zipcode'];
				
				if( $zipcode == '' ){
					echo '<h3 class="spark_weather_title">' . date("l, F jS Y") . '</h3><br/><h3>Please Select City</h3>';
					echo $after_widget;
					return false;
				}
				
				if( !file_exists( ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json" ) ) {
					echo '<h3 class="spark_weather_title">' . date("l, F jS Y") . '</h3><br/><h3>Coming Soon</h3>';
					echo $after_widget;
					return false;
				}
				
				$json_string = file_get_contents(ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json");
				$parsed_json = json_decode($json_string);
				$location = $parsed_json-> {$zipcode} -> {'current_day_weather'};
				$location_threeday = $parsed_json-> {$zipcode} -> {'three_days_weather'};
				
				$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
				if( $weather_zipcode_list == '' ) $weather_zipcode_list = array();
				$cityname = '';
				foreach( $weather_zipcode_list as $data ) {
					if( $data['zipcode'] == $zipcode ) {
						$cityname = $data['cityname'];
						break;
					}
				}
				
				$clickevent = "";
				
				if( $instance['weather_page_url'] != '' ){
					$clickevent = "window.location = '" . $instance['weather_page_url'] . "'";
				}
?>
		<style>
			.respondo_weather_widget_entry_index<?php echo $this->respondo_weather_widget_entry_index; ?>{
			<?php if( $instance['backgroundimageurl'] != '' ){ ?>
				background-image:url(<?php echo $instance['backgroundimageurl']; ?>);
			<?php } ?>
			<?php if( $instance['backgroundcolor'] != '' ){ ?>
				background-color:<?php echo $instance['backgroundcolor']; ?>;
			<?php } ?>
			}
		</style>

		<div class="basic_block_rs respondo_weather respondo_weather_widget_entry_index<?php echo $this->respondo_weather_widget_entry_index; ?>" onclick="<?php echo $clickevent; ?>">
		    <div class="rw_row-fluid">
				<div class="rw_span6 rw_scleft">
					<?php
						if( $instance['showcityname'] == 'true' ) {
					?>
		            <span class="rw_title"><?php echo $cityname; ?></span>
					<?php
						}
					?>
					<div class="rw_span6 daily">
						<span class="rw_temp"><?php echo $instance['weather_unit'] == 'f' ? $location->{'temperature'} . '&deg;F' : $location->{'temperature_c'} . '&deg;C'; ?></span>
						<span class="rw_dailydet">
							<?php echo $location->{'weather'}; ?>
							<br>
							<?php echo $instance['weather_unit'] == 'f' ? $location -> {'low'} . '&deg;F' : $location -> {'low_c'} . '&deg;C'; ?> / <?php echo $instance['weather_unit'] == 'f' ? $location -> {'high'} . '&deg;F' : $location -> {'high_c'} . '&deg;C'; ?>
						</span>
					</div>
					<div class="rw_span6 rw_dailydet2">
						<img class="respondo-weather-img" src="<?php echo plugins_url( $weather_plugin_name . '/images/weather-images/' . $location->{'weather_pic'} , dirname(__FILE__) ); ?>">
						<span class="rw_srss">
							<div class="rw_span6 rw_sunrise"><img class="respondo-weather-img" src="<?php echo plugins_url( $weather_plugin_name . '/images/sunrise.png', dirname(__FILE__)); ?>">
								<?php 
									if( $instance['timedisplay'] == '24' ) {
										echo $location->{'sunrise_hour'} . ':' . $location->{'sunrise_minute'}; 
									} else {
										if( $location->{'sunrise_hour'} > 13 ) {
											echo ( $location->{'sunrise_hour'} - 12 ) . ':' . $location->{'sunrise_minute'} . ' pm'; 
										} else {
											echo $location->{'sunrise_hour'} . ':' . $location->{'sunrise_minute'} . ' am'; 
										}
									}
								?>
							</div>
							<div class="rw_span6 rw_sunset"><img class="respondo-weather-img" src="<?php echo plugins_url( $weather_plugin_name . '/images/sunset.png', dirname(__FILE__)); ?>">
								<?php 
									if( $instance['timedisplay'] == '24' ) {
										echo $location->{'sunset_hour'} . ':' . $location->{'sunrise_minute'}; 
									} else {
										if( $location->{'sunset_hour'} > 13 ) {
											echo ( $location->{'sunset_hour'} - 12 ) . ':' . $location->{'sunrise_minute'} . ' pm'; 
										} else {
											echo $location->{'sunset_hour'} . ':' . $location->{'sunrise_minute'} . ' am'; 
										}
									}
								?>
							</div>
							<div class="clear"></div>
						</span>
					</div>
					<div class="clear"></div>
				</div>
				<?php
					if( $instance['show3day'] == 'true' ) {
				?>
				<div class="rw_span6 rw_scright">
				<?php
						for( $i = 0; $i < count( $location_threeday ); $i++ ) {
						$location_part = $location_threeday[$i];
				?>
		            <span class="rw_daily3">
		                <div class="rw_span6 rw_3date">
		                	<span><?php echo $location_part->{'day'} ; ?></span><small><?php echo $location_part->{'weather'} ; ?></small>
						</div>
		                <div class="rw_span6 rw_3dailytemp"><?php echo $instance['weather_unit'] == 'f' ? $location_part -> {'low'} . '&deg;F' : $location_part -> {'low_c'} . '&deg;C'; ?> / <?php echo $instance['weather_unit'] == 'f' ? $location_part -> {'high'} . '&deg;F' : $location_part -> {'high_c'} . '&deg;C'; ?><img class="respondo-weather-img" src="<?php echo plugins_url( $weather_plugin_name . '/images/weather-images/' . $location_part->{'icon'} , dirname(__FILE__) ); ?>"></div>
		                <div class="clear"></div>
		            </span>
				<?php
						}
				?>
				</div>
				<?php
					}
				?>
		    </div>
		</div>
		<?php
				echo $after_widget;
			}
		}
	}

    function weather_spark_pro() {
        parent::__construct( false, 'Respondo Weather Pro Widget' );
	}
	
    function update($new_instance,$old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = $new_instance['title'];
		$instance['zipcode'] = $new_instance['zipcode'];
		$instance['weather_page_url'] = $new_instance['weather_page_url'];
		$instance['weather_unit'] = $new_instance['weather_unit'];
		$instance['showcityname'] = isset( $new_instance['showcityname'] ) ? $new_instance['showcityname'] : 'false';
		$instance['backgroundimageurl'] = isset( $new_instance['backgroundimageurl'] ) ? $new_instance['backgroundimageurl'] : '';
		$instance['backgroundcolor'] = isset( $new_instance['backgroundcolor'] ) ? $new_instance['backgroundcolor'] : '';
		$instance['show3day'] = isset( $new_instance['show3day'] ) ? $new_instance['show3day'] : 'false';
		$instance['timedisplay'] = isset( $new_instance['timedisplay'] ) ? $new_instance['timedisplay'] : '24';
		
        return $instance;
	}
	
    function form($instance){
		$defaults = array('zipcode' => '', 'showcityname' => 'true', 'weather_page_url' => '', 'weather_unit' => 'f', 'title'=>'', 'backgroundimageurl' => '', 'backgroundcolor' => '#727782', 'show3day' => 'true', 'timedisplay' => '24' );
		$instance = wp_parse_args((array) $instance, $defaults);
		
		$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
		if( $weather_zipcode_list == '' ) $weather_zipcode_list = array();
?>
		Title :
		<input type="text" id="<?php echo $this->get_field_name('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"/>
		<p></p>
		City : <select id="<?php echo $this->get_field_name('zipcode'); ?>" name="<?php echo $this->get_field_name('zipcode'); ?>">
			<option value=''></option>
		<?php foreach( $weather_zipcode_list as $data ) { ?>
			<option value="<?php echo $data['zipcode']; ?>" <?php echo ($instance['zipcode'] == $data['zipcode']) ? 'selected="selected"' : ''; ?>><?php echo $data['cityname']; ?></option>
		<?php } ?>
		</select>
		<p></p>
		Time Display : <select id="<?php echo $this->get_field_name('timedisplay'); ?>" name="<?php echo $this->get_field_name('timedisplay'); ?>">
			<option value="24" <?php echo ($instance['timedisplay'] == '24') ? 'selected="selected"' : ''; ?>>24 hr</option>
			<option value="12" <?php echo ($instance['timedisplay'] == '12') ? 'selected="selected"' : ''; ?>>12 hr</option>
		</select>
		<p></p>
		<input type="radio" name="<?php echo $this->get_field_name('weather_unit'); ?>" value="f" <?php echo ( $instance['weather_unit'] == 'f' ? 'checked="checked"' : '' ); ?>/> Fahrenheit
		<input type="radio" name="<?php echo $this->get_field_name('weather_unit'); ?>" value="c" <?php echo ( $instance['weather_unit'] == 'c' ? 'checked="checked"' : '' ); ?>/> Celsius
		<p></p>
		<input type="checkbox" id="<?php echo $this->get_field_name('showcityname'); ?>" name="<?php echo $this->get_field_name('showcityname'); ?>" value="true" <?php echo ( $instance['showcityname'] == 'true' ? 'checked="checked"' : '' ); ?>/> Show City Name On Widget
		<p></p>
		<input type="checkbox" id="<?php echo $this->get_field_name('show3day'); ?>" name="<?php echo $this->get_field_name('show3day'); ?>" value="true" <?php echo ( $instance['show3day'] == 'true' ? 'checked="checked"' : '' ); ?>/> Show 3-Day Forecast
		<p></p>
		Weather Page URL:
		<input type="text" id="<?php echo $this->get_field_name('weather_page_url'); ?>" name="<?php echo $this->get_field_name('weather_page_url'); ?>" value="<?php echo $instance['weather_page_url']; ?>" style="width:100%;"/>
		<p></p>
		<?php 
			$imagebtnid = str_replace("[", "", $this->get_field_name('backgroundimageurl')); 
			$imagebtnid = str_replace("]", "", $imagebtnid);
			
			$suffix_index = '__i__';
			
			if( strpos( $this->get_field_name('backgroundimageurl'), '__i__' ) == false ) {
				$suffix_index = explode( "[", $this->get_field_name('backgroundimageurl'));
				$suffix_index = explode( "]", $suffix_index[1]);
				$suffix_index = $suffix_index[0];
			}
		?>
		Background Image URL:
		<input class="sparkweatherbkgimage" id="sparkweatherbkgimage<?php echo $suffix_index; ?>" name="<?php echo $this->get_field_name('backgroundimageurl'); ?>" type="text" style="width: 100%;" name="upload_image" value="<?php echo $instance['backgroundimageurl']; ?>" />
		<p></p>
		<p><img id="sparkweatherbkgimage_img<?php echo $suffix_index; ?>" src="<?php echo $instance['backgroundimageurl']; ?>" style="width:100%;"/></p>
		<a class="sparkweatherbkgimage_set" index="<?php echo $suffix_index; ?>" render="0" href="javascript:return false;" onclick="setbackgroundimage(<?php echo $suffix_index; ?>, this);">Set Background Image</a><br /><a class="sparkweatherbkgimage_remove" index="<?php echo $suffix_index; ?>" href="javascript:return false;" onclick="removebackgroundimage(<?php echo $suffix_index; ?>);">Remove Background Image</a>
		<p></p>
		Background Color:
		<?php 
			$backgroundcolorid = str_replace("[", "", $this->get_field_name('backgroundcolor')); 
			$backgroundcolorid = str_replace("]", "", $backgroundcolorid); 
		?>
		<input class="sparkweatherbkgcolor" name="<?php echo $this->get_field_name('backgroundcolor'); ?>" type="text" style="width: 100%;" name="upload_image" value="<?php echo $instance['backgroundcolor']; ?>"/>
		<script language="javascript">
			jQuery('.sparkweatherbkgimage_remove').click(function(){
				var index = jQuery(this).attr('index');
				jQuery('#sparkweatherbkgimage_img' + index).attr('src', '');
				jQuery('#sparkweatherbkgimage' + index).val('');
			});
			
			jQuery('.sparkweatherbkgcolor').each(function(){
				var thiscolorobj = this;
				jQuery(thiscolorobj).ColorPicker({
					onSubmit: function(hsb, hex, rgb, el) {
						jQuery(el).val(hex);
						jQuery(el).ColorPickerHide();
					},
					onBeforeShow : function () {
						jQuery(this).ColorPickerSetColor(this.value);
					},
					onChange: function (hsb, hex, rgb) {
						jQuery(thiscolorobj).val('#' + hex);
					}
				})
				.bind('keyup', function(){
					jQuery(this).ColorPickerSetColor(this.value);
				});
			});
		</script>
<?php
	}
}

function check_link($link) {
    if( strpos($link,"http") !== false || strpos($link,"https") !== false ) {
        return $link;
	} else {
        $link = "http://" . $link;
        return $link;
	}
}    

function shareit_register_widgets() {
    register_widget( 'weather_spark_pro' );
}
?>