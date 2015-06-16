<?php
	/*
	Plugin Name: Respondo Weather Pro
	Description: A responsive Wunderground weather API plugin
	version: 1.0
	Author: 
	Author URI: 
	License: GPL2
	*/
	global $weather_plugin_name;
	$weather_plugin_name = "respondo-weather-pro";
	
	include_once('create-weather-json.php');
	include_once('spark-weather.php');
	include_once('weatherpage-shortcode.php');

	add_action('admin_menu', 'getting_weather_info_sparklogic');
	add_action('init', 'load_weather_css_scripts');
	register_deactivation_hook( __FILE__, 'respondo_weather_deactivate');
	
	function getting_weather_info_sparklogic() {
		add_menu_page("Weather", "Weather", 9, "respondo-weather-settings", "respondo_weather_settings", "");
	}
	
	function respondo_weather_settings(){
		include_once "respondo_weather_settings.php";
	}
	
	function load_weather_css_scripts() {
		// wp_register_script('weather-main-js', plugins_url( 'js/weatherinfo.js' , __FILE__ ));
		// wp_enqueue_script('weather-main-js');

		 wp_register_script('weather-sizzle-js', plugins_url( 'js/sizzle.js' , __FILE__ ));
		 wp_enqueue_script('weather-sizzle-js');

		 wp_register_script('weather-sizzle-min-js', plugins_url( 'js/sizzle.min.js' , __FILE__ ));
		 wp_enqueue_script('weather-sizzle-min-js');

		 wp_register_script('weather-elementquery-js', plugins_url( 'js/elementQuery.js' , __FILE__ ));
		 wp_enqueue_script('weather-elementquery-js');

		 wp_register_script('weather-elementquery-min-js', plugins_url( 'js/elementQuery.min.js' , __FILE__ ));
		 wp_enqueue_script('weather-elementquery-min-js');
		
		 wp_register_style('weather-main-css', plugins_url( 'css/style.css' , __FILE__ ));
		 wp_enqueue_style('weather-main-css');
		
		// wp_register_style('weather-pro-font', 'http://fonts.googleapis.com/css?family=Open+Sans%3A400%2C400italic&amp;ver=3.6.1');
		// wp_enqueue_style('weather-pro-font');
		
		// wp_register_style('weather-play-font', 'http://fonts.googleapis.com/css?family=Play:400,700');
		// wp_enqueue_style('weather-play-font');
	}
	
	/*<insert custom schedule>*/
	add_filter( 'cron_schedules', 'my_custom_every_2_mins' );
	add_filter( 'cron_schedules', 'my_custom_every_3_mins' );
	add_filter( 'cron_schedules', 'my_custom_every_5_mins' );
	add_filter( 'cron_schedules', 'my_custom_every_10_mins' );

	function my_custom_every_2_mins( $schedules ) {
		$schedules['mycustomevery2mins'] = array(
			'interval' => 120,
			'display' => __( 'Every 2 mins' )
		);
		return $schedules;
	}
	
	function my_custom_every_3_mins( $schedules ) {
		$schedules['mycustomevery3mins'] = array(
			'interval' => 180,
			'display' => __( 'Every 3 mins' )
		);
		return $schedules;
	}
	
	function my_custom_every_5_mins( $schedules ) {
		$schedules['mycustomevery5mins'] = array(
			'interval' => 300,
			'display' => __( 'Every 5 mins' )
		);
		return $schedules;
	}
	
	function my_custom_every_10_mins( $schedules ) {
		$schedules['mycustomevery10mins'] = array(
			'interval' => 600,
			'display' => __( 'Every 10 mins' )
		);
		return $schedules;
	}
	/*</insert custom schedule>*/
	/*<insert cron>*/
	
	$pull_time = get_option( 'weather_pull_time' );
	
	add_action('init', 'weathercron_info');
	add_action( 'weather_2_mins_pulling', 'do_weather_2_mins_pulling' );
	add_action( 'weather_3_mins_pulling', 'do_weather_3_mins_pulling' );
	add_action( 'weather_5_mins_pulling', 'do_weather_5_mins_pulling' );
	add_action( 'weather_10_mins_pulling', 'do_weather_10_mins_pulling' );
    
	function weathercron_info(){
		if ( !wp_next_scheduled('weather_2_mins_pulling') && !defined('WP_INSTALLING') ) {
			wp_schedule_event( time(), 'mycustomevery2mins', 'weather_2_mins_pulling');
		}
		
		if ( !wp_next_scheduled('weather_3_mins_pulling') && !defined('WP_INSTALLING') ) {
			wp_schedule_event( time(), 'mycustomevery3mins', 'weather_3_mins_pulling');
		}
		
		if ( !wp_next_scheduled('weather_5_mins_pulling') && !defined('WP_INSTALLING') ) {
			wp_schedule_event( time(), 'mycustomevery5mins', 'weather_5_mins_pulling');
		}
		
		if ( !wp_next_scheduled('weather_10_mins_pulling') && !defined('WP_INSTALLING') ) {
			wp_schedule_event( time(), 'mycustomevery10mins', 'weather_10_mins_pulling');
		}
	}

	function do_weather_2_mins_pulling(){
		$pull_time = get_option( 'weather_pull_time' );
		if( $pull_time == '2' ) {
			
			$fp = fopen("myText.txt","wb");
			$content = date('m/d/Y h:i:s a', time());
			fwrite($fp,$content);
			fclose($fp);

			$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
			create_weather_data( $weather_zipcode_list );
		}
	}
	
	function do_weather_3_mins_pulling(){
		$pull_time = get_option( 'weather_pull_time' );
		if( $pull_time == '3' ) {
			$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
			create_weather_data( $weather_zipcode_list );
		}
	}
	
	function do_weather_5_mins_pulling(){
		$pull_time = get_option( 'weather_pull_time' );
		if( $pull_time == '5' ) {
			$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
			create_weather_data( $weather_zipcode_list );
		}
	}
	
	function do_weather_10_mins_pulling(){
		$pull_time = get_option( 'weather_pull_time' );
		if( $pull_time == '10' ) {
			$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
			create_weather_data( $weather_zipcode_list );
		}
	}
	/*</insert cron>*/
	
	function respondo_weather_deactivate() {
		wp_clear_scheduled_hook('weather_2_mins_pulling');
		wp_clear_scheduled_hook('weather_3_mins_pulling');
		wp_clear_scheduled_hook('weather_5_mins_pulling');
		wp_clear_scheduled_hook('weather_10_mins_pulling');
	}
	
	/*TincyMCE Button*/
	add_filter('mce_buttons', 'filter_mce_button');
	add_filter('mce_external_plugins', 'filter_mce_plugin');
	add_filter('admin_footer', 'render');
	
   function filter_mce_button( $buttons ) {
		array_push( $buttons, '|', 'respondo_weather_sls_button' );
		return $buttons;
	}

	function filter_mce_plugin( $plugins ) {
		global $weather_plugin_name;
		$plugins['respondo_weather_sls'] = plugins_url( $weather_plugin_name . '/js/respondo-weather-shortcodebutton.js' , dirname(__FILE__) );
		return $plugins;
	}
	
	function render() {
?>
		<div id="respondo-shortcode-dialog" style="display:none">
			<div id="shortcodes-form-sls">
				<div class="respondo-shortcode-dialog-section">
					<div class="rs-weather-options-half">
						<label for="weather_widget_width">Widget Width:
						<select id="weather_widget_width" name="weather_widget_width">
							<option value="full-width">Full-Width</option>
							<option value="one-half">One-Half</option>
							<option value="one-third">One-Third</option>
							<option value="one-quarter">One-Quarter</option>
						</select></label>
					</div>
					<div class="rs-weather-options-half">
                    	<label for="weather_float">Widget Float:
						<select id="weather_float" name="weather_float">
							<option value="none">None</option>
							<option value="left">Left</option>
							<option value="right">Right</option>
						</select></label>
                    </div>
                    <p class="clear"></p>
					<div class="rs-weather-options-half">
						<label for="weather_city">Default City:
						<select id="weather_city" name="weather_city">
						<?php
							$weather_zipcode_list = unserialize( get_option( 'weather_zipcode_list' ) );
							foreach( $weather_zipcode_list as $item ){
						?>
							<option value="<?php echo $item['cityname']; ?>"><?php echo $item['cityname']; ?></option>
						<?php
							}
						?>
						</select></label>
					</div>
					<div class="rs-weather-options-half">
						<label for="weather_widget_time">Time Settings:
						<select id="weather_widget_time" name="weather_widget_time">
							<option value="12">12-hour</option>
							<option value="24">24-hour</option>
						</select></label>
					</div>
                    <p class="clear"></p>
					<div class="rs-weather-options">
						<label><input type="radio" id="f_temper_unit" name="temper_unit" checked="checked" value="f">&nbsp;Fahrenheit</label>&nbsp;&nbsp;&nbsp;
						<label><input type="radio" id="c_temper_unit" name="temper_unit" value="c">&nbsp;Celsius</label>
					</div>
					<div class="rs-weather-options">
						<label><input type="checkbox" id="showingcity" value="true" checked="checked"/>&nbsp;Show City Name</label>
					</div>
					<div class="rs-weather-options">
						<label><input type="checkbox" id="showingdays" value="true" checked="checked"/>&nbsp;Show 3-Day Forecast</label>
					</div>
                    <p class="clear"></p>
					<div class="rs-weather-options-fw">
						Background Image URL: <input type="hidden" id="sparkweatherbkgimage_shortcode" name="sparkweatherbkgimage_shortcode"/>
						<br/>
						<img id="sparkweatherbkgimage_img_shortcode" src="" style="max-width:200px;max-height:200px;"/>
						<br/><br/>
					</div>
					<div class="rs-weather-options-fw">
						<a href="#" onclick="setbackgroundimage('_shortcode', this);"/>Set Image URL</a> | <a href="#" onclick="removebackgroundimage('_shortcode');"/>Remove Image URL</a>
					</div><br/>
					<div class="rs-weather-options-fw">
						Background Color: <div id="colorselectflatmode"></div><input type="text" id="backgroundimagecolor" value="#2d74cc"/>
					</div><br/><br />
					<div class="rs-weather-options-fw">
						<input type="button" value="Insert" class="button button-primary" id="insert-weather-shortcode" />
						<input type="button" value="Cancel" class="button" id="close-weather-shortcode-dialog" />
					</div>
				</div>
			</div>
		</div>
<?php
	}
	
	/*Wordpress Image Upload Handler*/
	function my_admin_scripts() {
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('my-upload');
		
		wp_register_script('weather-colorpicker-js', plugins_url( 'js/colorpicker.js' , __FILE__ ));
		wp_enqueue_script('weather-colorpicker-js');
		
		wp_register_script('weather-eye-js', plugins_url( 'js/eye.js' , __FILE__ ));
		wp_enqueue_script('weather-eye-js');
		
		wp_register_script('weather-utils-js', plugins_url( 'js/utils.js' , __FILE__ ));
		wp_enqueue_script('weather-utils-js');
		
		wp_register_script('weather-layout-js', plugins_url( 'js/layout.js' , __FILE__ ));
		wp_enqueue_script('weather-layout-js');
		
		$wp_version = get_bloginfo('version');
		if ($wp_version  < 3.5) {
			wp_enqueue_script('custom-options-ph-admin-media-less-3.5', plugins_url('js/admin-media-less-3.5.js', __FILE__));
		} else {
			wp_enqueue_media();
			wp_register_script('custom_options_ph_media', plugins_url('js/admin-media-3.5.js', __FILE__), array('jquery'), '1.0.0', true);
			wp_localize_script('custom_options_ph_media', 'custom_options_ph_media', array(
				'title' => __('Upload or Choose Your Custom Image File', 'base_shortcode'),
				'button' => __('Insert Image into Input Field', 'base_shortcode'))
			);
			wp_enqueue_script('custom_options_ph_media');
		}		
		
		wp_register_script('weather-main-js', plugins_url( 'js/weatherinfo.js' , __FILE__ ));
		wp_enqueue_script('weather-main-js');

	}
 
	function my_admin_styles() {
		wp_enqueue_style('thickbox');
		
		wp_register_style('weather-colorpicker-css', plugins_url( 'css/colorpicker.css' , __FILE__ ));
		wp_enqueue_style('weather-colorpicker-css');
		
		wp_register_style('weather-layout-css', plugins_url( 'css/layout.css' , __FILE__ ));
		wp_enqueue_style('weather-layout-css');
		
		wp_register_style('weather-main-css', plugins_url( 'css/style.css' , __FILE__ ));
		wp_enqueue_style('weather-main-css');
	}
 
	add_action('admin_print_scripts', 'my_admin_scripts');
	add_action('admin_print_styles', 'my_admin_styles');
?>