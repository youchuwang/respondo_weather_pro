<?php

function create_weather_data( $zipcodearray ) {
	
	global $weather_plugin_name;
	$three_days = '{';
	
	$apikey = get_option( 'weather_api_key' );
	
	if( count( $zipcodearray ) > 0 ) {
		for( $cities = 0; $cities < count( $zipcodearray ); $cities++ ) {
			$add_main_comma = ",";
			if( $cities == 0 ) {
				$add_main_comma = "";
			}
			
			$zip_code = $zipcodearray[$cities]['zipcode'];
			$cityname = $zipcodearray[$cities]['cityname'];
			
			$json_string = file_get_contents("http://api.wunderground.com/api/" . $apikey . "/astronomy/conditions/forecast10day/forecast/q/" . $zip_code . ".json");
			
			if( strpos( $json_string, 'error' ) ){
					$three_days .= 'error';
					break;
			}
			
			$parsed_json = json_decode($json_string);
			$location = $parsed_json->{'current_observation'};

			$three_days .= $add_main_comma . '"' . $zip_code . '" :';
			
			$parsed_json = json_decode($json_string);
			$weather_high_low = $parsed_json -> {'forecast'} -> {'simpleforecast'};

			$parsed_json = json_decode($json_string);
			$moon_phase = $parsed_json -> {'moon_phase'};
			
			$daynight = "day_time";
			if(strpos($location->{'icon_url'},"nt_")) {
				$daynight = "night_time";
				$icon_name = "nt_".$location->{'icon'};
			} else {
				$icon_name = $location->{'icon'};
			}
			
			$curent_date = $weather_high_low -> {'forecastday'}[0] -> {'date'}->{'weekday'} .", ". $weather_high_low -> {'forecastday'}[0] -> {'date'}->{'monthname'};
			
			$jason_file_data = '
			{
			"current_day_weather" : 
				{
					"zip_code" : "'.$zip_code.'",
					"City_Name" : "'.$cityname.'",
					"temperature" : "'. round($location->{'temp_f'}) .'",
					"temperature_c" : "'. round($location->{'temp_c'}) .'",
					"weather" : "'.$location->{'weather'}.'",
					"high_c" : "'. round($weather_high_low -> {'forecastday'}[0] -> {'high'} -> {'celsius'}).'",
					"high" : "'. round($weather_high_low -> {'forecastday'}[0] -> {'high'} -> {'fahrenheit'}).'",
					"low_c" : "'. round($weather_high_low -> {'forecastday'}[0] -> {'low'}->{'celsius'}).'",
					"low" : "'. round($weather_high_low -> {'forecastday'}[0] -> {'low'}->{'fahrenheit'}).'",
					"weather_pic" : "'.$icon_name.'.png",
					"sunrise_hour" : "'.$moon_phase -> {'sunrise'} -> {'hour'}.'",
					"sunrise_minute" : "'.$moon_phase -> {'sunrise'} -> {'minute'}.'",
					"sunset_hour" : "'.$moon_phase -> {'sunset'} -> {'hour'}.'",
					"sunset_minute" : "'.$moon_phase -> {'sunset'} -> {'minute'}.'",
					"wind" : "'.$location->{'wind_mph'}.'",
					"city" : "'.$location -> {'display_location'} -> {'city'}.'",
					"feels_like" : "'.round($location -> {'feelslike_f'}).'",
					"feels_like_c" : "'.round($location -> {'feelslike_c'}).'",
					"snow" : "'.$weather_high_low -> {'forecastday'}[0] -> {'snow_allday'}->{'in'}.'",
					"rain" : "'.$weather_high_low -> {'forecastday'}[0] -> {'qpf_allday'}->{'in'}.'",
					"day-night" : "' .$daynight. '",
					"today-day-month" : "'.$curent_date.'",
					"current-date" : "'.$weather_high_low -> {'forecastday'}[0] -> {'date'}->{'day'}.'",
					"current-year" : "'.$weather_high_low -> {'forecastday'}[0] -> {'date'}->{'year'}.'"
				},';

			$three_days .= $jason_file_data . '
			"three_days_weather" : 
				[';
			$add_comma = "";
			for( $i = 1; $i < 4; $i++ ) {
				$add_comma = ", ";			

				if($i == 1) {
					$add_comma = "";
				}
				
				$date = $add_comma . '
					{
						"day" : "' . $weather_high_low -> {'forecastday'}[$i] -> {'date'} -> {'weekday'} . '",
						"month" : "' . $weather_high_low -> {'forecastday'}[$i] -> {'date'} -> {'monthname'} . '",
						"date" : "' . $weather_high_low -> {'forecastday'}[$i] -> {'date'} -> {'day'} . '",
						"high" : "' . round($weather_high_low -> {'forecastday'}[$i] -> {'high'} -> {'fahrenheit'}) . '",
						"high_c" : "' . round($weather_high_low -> {'forecastday'}[$i] -> {'high'} -> {'celsius'}) . '",
						"low" : "' . round($weather_high_low -> {'forecastday'}[$i] -> {'low'} -> {'fahrenheit'}) . '",
						"low_c" : "' . round($weather_high_low -> {'forecastday'}[$i] -> {'low'} -> {'celsius'}) . '",
						"weather" : "' . $weather_high_low -> {'forecastday'}[$i] -> {'conditions'} . '",
						"icon" : "' . $weather_high_low -> {'forecastday'}[$i] -> {'icon'} . '.png",
						"precipitation" : "'.$weather_high_low -> {'forecastday'}[$i] -> {'qpf_allday'} -> {'in'}.'"
					}
				';
				$three_days .= $date;
			}
			$three_days .= ']
			}';
		}
	} else {
		$three_days .= 'error';
	}
	
	$three_days .= '
	}';
	$path = ABSPATH . "wp-content/plugins/" . $weather_plugin_name . "/weather_data/cities-weather.json";
	$file=fopen($path,"w") or die("can't open file");
	fwrite($file, $three_days);
	fclose($file);
	chmod($path, 0777);
}
?>