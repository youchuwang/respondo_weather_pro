<?php
	require_once '../../../wp-config.php';
	
	$zip_code = $_POST['zipcode'];
	$timedisplay = $_POST['timedisplay'];
	$showing3days = $_POST['showing3days'];
	$showcityname = $_POST['showcityname'];
	$weather_plugin_name = $_POST['weather_plugin_name'];
	$unit = $_POST['unit'];
	
	$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
	$cityname = "";
	
	foreach( $weather_zipcode_list as $data ) {
		if( $zip_code == $data['zipcode'] ){
			$cityname = $data['cityname'];
			break;
		}
	}
				
	if( !file_exists( dirname( __FILE__ ) . "/weather_data/cities-weather.json" ) ) {
		echo '<h3 class="rw-spark_weather_title">' . date("l, F jS Y") . '</h3><br/><h3>Coming Soon</h3>';
		return false;
	} else {
		$json_string = file_get_contents( dirname( __FILE__ ) . "/weather_data/cities-weather.json" );
		$weather_xml = json_decode( $json_string );
		$location = $weather_xml->{$zip_code}->{'current_day_weather'};
		$location_threeday = $weather_xml->{$zip_code}->{'three_days_weather'};
?>
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
										echo ( $location->{'sunrise_hour'} - 12 ) . ':' . $location->{'sunrise_minute'} . 'pm'; 
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
										echo ( $location->{'sunset_hour'} - 12 ) . ':' . $location->{'sunrise_minute'} . 'pm'; 
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
<?php
	}
?>