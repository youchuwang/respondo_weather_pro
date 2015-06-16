<?php
	$action = isset($_POST['action']) ? $_POST['action'] : '';
	$schedule_list = array( '2', '3', '5', '10');
	
	$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
	if( $weather_zipcode_list == '' ) $weather_zipcode_list = array();
		
	if( $action == 'zipcodeadd' ){
		$zipcode = isset($_POST['zipcode']) ? $_POST['zipcode'] : '';		
		$cityname = isset($_POST['cityname']) ? $_POST['cityname'] : '';		
		
		$weather_zipcode_list[] = array( 'zipcode' => $zipcode, 'cityname' => $cityname );

		delete_option( 'weather_zipcode_list' );
		add_option( 'weather_zipcode_list', serialize( $weather_zipcode_list ), '', 'no' );
	} elseif( $action == 'delete' ) {
		$zipcode = isset($_POST['zipcode']) ? $_POST['zipcode'] : '';
		
		$new_data = array();
		foreach( $weather_zipcode_list as $data ){
			if( $data['zipcode'] != $zipcode ) {
				$new_data[] = $data;
			}
		}
		
		delete_option( 'weather_zipcode_list' );
		add_option( 'weather_zipcode_list', serialize( $new_data ), '', 'no' );
		
		$weather_zipcode_list = $new_data;
	} elseif( $action == 'timesave' ) {
		$timeschedule = isset($_POST['timeschedule']) ? $_POST['timeschedule'] : '';	
		
		delete_option( 'weather_pull_time' );
		add_option( 'weather_pull_time', $timeschedule, '', 'no' );
	} else if( $action == "apisave" ) {
		$apikey = isset($_POST['apikey']) ? $_POST['apikey'] : '';
		
		delete_option( 'weather_api_key' );
		add_option( 'weather_api_key', $apikey, '', 'no' );
	}
	
	$apikey = get_option( 'weather_api_key' );
	$pulltime = get_option( 'weather_pull_time' );
	if( !isset( $pulltime ) || $pulltime == '' ){
		add_option( 'weather_pull_time', '2', '', 'no' );
	}
?>
<div id="cron-gui" class="wrap">
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Weather Information</h2>
	
	<h3 style="margin-bottom:0;">Please Insert Your Wunderground Weather API Key</h3>
    <span style="font-style: italic;">Don't have one yet? <a href="http://www.wunderground.com/weather/api/" target="_blank">Get it here.</a></span>
	<form method="post" action="">
		<input type="hidden" name="action" value="apisave" />
		<input type="text" name="apikey" value="<?php echo $apikey; ?>" size="30"/>		
		<input type="submit" class="button-primary" value="Save Key"/>
	</form>
	
	<h3 style="margin:30px 0 0;">Current Pulling Time : Every <?php echo get_option( 'weather_pull_time' ); ?> Mins</h3>
	<form method="post" action="">
		<input type="hidden" name="action" value="timesave" />
		<select id="timeschedule" name="timeschedule">
			<?php foreach( $schedule_list as $schedule ) { ?>
				<option value="<?php echo $schedule; ?>" <?php echo ( $schedule == get_option( 'weather_pull_time' ) ) ? 'selected="selected"' : '' ; ?>>every <?php echo $schedule; ?> minutes</option>
			<?php } ?>
		</select>
		<input type="submit" class="button-primary" value="Change Schedule"/>
	</form>
	<h3 style="margin:30px 0 0;">Zip Code List</h3>
	<form method="post" action="">
		<input type="hidden" name="action" value="zipcodeadd" />
		<label for="zipcode">Zip Code : </label><input type="text" name="zipcode" id="zipcode" size="9"/>
		<label for="zipcode">City Name : </label><input type="text" name="cityname" id="cityname"/>
		<input type="submit" class="button-primary" value="Add New"/>
	</form><br/>
	<table class="widefat fixed" style="width:425px;">
		<thead>
			<tr>
				<th scope="col" style="text-align:center;">Zip Code</th>
				<th scope="col" style="text-align:center;">City Name</th>
				<th scope="col" style="text-align:center;"></th>
			</tr>
		</thead>
		<tbody>
			<?php 
				if( $weather_zipcode_list != '' ) {
					$index = 0;
					foreach( $weather_zipcode_list as $data ) { 
						$index++;
			?>
			<tr id="row_<?php echo $index; ?>">
				<td id="row_<?php echo $index; ?>_zipcode" style="text-align:center;"><?php echo $data['zipcode']; ?></td>
				<td id="row_<?php echo $index; ?>_zipcode" style="text-align:center;"><?php echo $data['cityname']; ?></td>
				<td id="row_<?php echo $index; ?>_control" style="text-align:center;"><a href="javascript: schedule_delete('<?php echo $data['zipcode']; ?>'); ">Delete</a></td>
			</tr>
			<?php 
					}
				} 
			?>
		</tbody>
	</table>
</div>
<form id="deleteform" method="post" action="" style="display:none;">
	<input type="hidden" name="action" id="deleteaction" value="delete" />
	<input type="hidden" name="zipcode" id="deletezipcode" value="" />
</form>