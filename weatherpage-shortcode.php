<?php
	add_shortcode('respondo-weather', 'respondo_weather_func');
	$respondo_weather_shortcode_entry_index = 0;
	
	function ordinal( $input_number ) {
		$number = (string) $input_number;
		$last_digit = substr($number, -1);
		$second_last_digit = substr($number, -2, 1);
		$suffix = 'th';
		
		if ($second_last_digit != '1') {
			switch ($last_digit) {
				case '1':
					$suffix = 'st';
					break;
				case '2':
					$suffix = 'nd';
					break;
				case '3':
					$suffix = 'rd';
					break;
				default:
					break;
			}
		}
		
		if( (string) $number === '1' ) 
			$suffix = 'st';
			
		return $number.$suffix;
	}
	
	function respondo_weather_func( $args ) {
		ob_start();
		
		global $weather_plugin_name;
		global $respondo_weather_shortcode_entry_index;
		
		$respondo_weather_shortcode_entry_index++;
		
		$apikey = get_option( 'weather_api_key' );
		
		if( $apikey == '' ) {
			// echo '<p>Please Insert Wunderground Weather API Key.</p>';
		} else {
			if( file_exists( ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json" ) && strpos( file_get_contents( ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json" ), 'error' ) ) {
				// echo '<p>Wunderground Weather API Connection Error.</p>';
			} else {
				$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
				if( count( $weather_zipcode_list ) == 0 ) {
					// echo "<h3>Please insert cities</h3>";
					$output_string = ob_get_contents();
					ob_end_clean();
					return $output_string;
				}

				$zip_code = '';
				$cityname = isset( $args['cityname'] ) ? $args['cityname'] : '';
				$unit = isset( $args['unit'] ) ? $args['unit'] : 'f';
				$backgroundurl = isset( $args['backgroundurl'] ) ? $args['backgroundurl'] : '';
				$backgroundcolor = isset( $args['backgroundcolor'] ) ? $args['backgroundcolor'] : '';
				$showcityname = isset( $args['showingcityname'] ) ? $args['showingcityname'] : 'true';
				$showing3days = isset( $args['showing3days'] ) ? $args['showing3days'] : 'true';
				$float = isset( $args['float'] ) ? $args['float'] : '';
				
				if( $unit == '' ) $unit = 'f';
				$unit = strtolower( $unit );
				
				// if( isset( $_POST['weather-city-zip_code' . $respondo_weather_shortcode_entry_index] ) ) {
					// $zip_code = $_POST['weather-city-zip_code' . $respondo_weather_shortcode_entry_index];
					// foreach( $weather_zipcode_list as $data ) {
						// if( $data['zipcode'] == $zip_code ){
							// $cityname = $data['cityname'];
							// break;
						// }
					// }
				// } else {
					if( $cityname == '' ){
						foreach( $weather_zipcode_list as $data ) {
							$zip_code = $data['zipcode'];
							$cityname = $data['cityname'];
							break;
						}
					} else {
						foreach( $weather_zipcode_list as $data ) {
							if( $cityname == $data['cityname'] ){
								$zip_code = $data['zipcode'];
								break;
							}
						}
					}
				// }
				
				$widget_width = "full-width";
				if( isset( $args['width'] ) && $args['width'] != "" ) {
					$widget_width = $args['width'];
				}
				
				$timedisplay = "24";
				if( isset( $args['timedisplay'] ) && $args['timedisplay'] != "" ) {
					$timedisplay = $args['timedisplay'];
				}
?>	
	<script language="javascript">
			function clickweathercity<?php echo $respondo_weather_shortcode_entry_index; ?>( zipcode, index ){				
				jQuery.ajax({
					url : '<?php echo plugins_url( 'ajax_getting_weather.php' , __FILE__ ); ?>',
					type: "POST",
					cache: true,
					dataType : "html",
					data : { 
						'zipcode' : zipcode,
						'timedisplay' : '<?php echo $timedisplay; ?>',
						'unit' : '<?php echo $unit; ?>',
						'showing3days' : '<?php echo $showing3days; ?>',
						'showcityname' : '<?php echo $showcityname; ?>',
						'weather_plugin_name' : '<?php echo $weather_plugin_name; ?>'
					},
					success : function( retData ){
						jQuery(".respondo_weather_shortcode_entry<?php echo $respondo_weather_shortcode_entry_index; ?> .rw_row-fluid").html( retData );
					}
				});
				
				return false;
			}
			
			function showcityitem<?php echo $respondo_weather_shortcode_entry_index; ?>( index ){
				var menuobj = document.getElementById("rw_locations" + index)
				if( menuobj.style.display == 'none' ){
					jQuery("#rw_locations" + index).fadeIn();
				} else {
					jQuery("#rw_locations" + index).fadeOut();
				}
			}
		</script>
		
	<style>
		.respondo_weather_shortcode_entry<?php echo $respondo_weather_shortcode_entry_index; ?>{
		<?php if( $backgroundurl != '' ){ ?>
			background-image:url(<?php echo $backgroundurl; ?>) !important;
		<?php } ?>
		<?php if( $backgroundcolor != '' ){ ?>
			background-color:<?php echo $backgroundcolor; ?> !important;
		<?php } ?>
		}
		.respondo_weather_shortcode_entry<?php echo $respondo_weather_shortcode_entry_index; ?> .rw_locations ul#rw_locations_section {
		<?php if( $backgroundcolor != '' ){ ?>
			background-color:<?php echo $backgroundcolor; ?> !important;
		<?php } ?>
		}
	</style>
		
	<div class="basic_block_rs respondo_weather respondo_weather_shortcode_entry<?php echo $respondo_weather_shortcode_entry_index; ?> <?php echo $widget_width; ?> <?php echo $float; ?>">
		<a class="weather_dropdown" onclick="showcityitem<?php echo $respondo_weather_shortcode_entry_index; ?>( <?php echo $respondo_weather_shortcode_entry_index; ?> )">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>
		<div class="rw_locations" id="rw_locations<?php echo $respondo_weather_shortcode_entry_index; ?>" style="display:none;">
			<ul id="rw_locations_section">
					<?php
						foreach( $weather_zipcode_list as $data ) {
							$selected = "";
							if( $zip_code == $data['zipcode'] ) {
								$selected = "selected='selected'";
							}
							
							echo '<li><a class="select-city" href="javascript:;" onclick="clickweathercity' . $respondo_weather_shortcode_entry_index . '(' . $data['zipcode'] . ',' . $respondo_weather_shortcode_entry_index . ')">' . $data['cityname'] . '</a></li>';
						}
					?>
			</ul>
		</div>
				<?php
					if( !file_exists( ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json" ) ) {
						echo '<h3 class="rw-spark_weather_title">' . date("l, F jS Y") . '</h3><br/><h3>Coming Soon</h3>';
					} else {
						$json_string = file_get_contents( ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json" );
						$weather_xml = json_decode( $json_string );
						$location = $weather_xml->{$zip_code}->{'current_day_weather'};
						$location_threeday = $weather_xml->{$zip_code}->{'three_days_weather'};
				?>
	
        <div class="clear"></div>
		<div class="rw_row-fluid">
			<div class="rw_span6 rw_scleft">
				<?php if( $showcityname == 'true' ) { ?>
				<span class="rw_title"><?php echo $cityname; ?></span>
				<?php } ?>
				<div class="rw_span6 daily">
					<span class="rw_temp"><?php echo $unit == 'f' ? $location->{'temperature'} . '&deg;F' : $location->{'temperature_c'}. '&deg;C'; ?></span>
					<span class="rw_dailydet">
						<?php echo $location->{'weather'}; ?>
						<br>
						<?php echo $unit == 'f' ? $location -> {'low'} . '&deg;F' : $location -> {'low_c'} . '&deg;C'; ?> / <?php echo $unit == 'f' ? $location -> {'high'} . '&deg;F' : $location -> {'high_c'} . '&deg;C'; ?>
					</span>
				</div>
				<div class="rw_span6 rw_dailydet2">
					<img class="respondo-weather-img" src="<?php echo plugins_url( $weather_plugin_name . '/images/weather-images/' . $location->{'weather_pic'} , dirname(__FILE__) ); ?>">
					<span class="rw_srss">
						<div class="rw_span6 rw_sunrise"><img class="respondo-weather-img" src="<?php echo plugins_url( $weather_plugin_name . '/images/sunrise.png', dirname(__FILE__)); ?>">
								<?php 
									if( $timedisplay == '24' ) {
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
									if( $timedisplay == '24' ) {
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
									if( $showing3days == 'true' ) {
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
					<div class="rw_span6 rw_3dailytemp"><?php echo $unit == 'f' ? $location_part -> {'low'} . '&deg;F' : $location_part -> {'low_c'} . '&deg;C'; ?> / <?php echo $unit == 'f' ? $location_part -> {'high'} . '&deg;F' : $location_part -> {'high_c'} . '&deg;C'; ?><img class="respondo-weather-img" src="<?php echo plugins_url( $weather_plugin_name . '/images/weather-images/' . $location_part->{'icon'} , dirname(__FILE__) ); ?>"></div>
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
<?php
					}
?>
		</div>
<?php
				}
			}

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
?>