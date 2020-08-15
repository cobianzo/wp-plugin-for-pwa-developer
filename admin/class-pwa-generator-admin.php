<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cobianzo.com
 * @since      1.0.0
 *
 * @package    Pwa_Generator
 * @subpackage Pwa_Generator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pwa_Generator
 * @subpackage Pwa_Generator/admin
 * @author     Alvaro <cobianzo@gmail.com>
 */
class Pwa_Generator_Admin {

	
	private $plugin_name;
	private $version;
	private $loader;

	private $languages_defined;
	private $manifest_en_file;
	private $manifest_it_file;
	private $manifest_en_source;
	private $manifest_it_source;
	private $sw_file;
	private $sw_source;


	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		
		// I can use constants for the files.
		$this->languages_defined = ['it' => 'Italian', 'en' => 'English'];
		$this->manifest_en_file = 'manifest-en.json';
		$this->manifest_it_file = 'manifest-it.json';
		$this->manifest_source_relpath =  'src/manifest-template-to-json.php';
		$this->manifest_source =  get_stylesheet_directory() . '/' . $this->manifest_source_relpath;
		
		$this->sw_file = 'sw-balconi.js';;
		$this->sw_source_relpath = 'src/js/service-worker/sw-template-to-js.php';
		$this->sw_source = get_stylesheet_directory() . '/' . $this->sw_source_relpath;
		
		$this->manifest_it_path = ABSPATH . $this->manifest_it_file;
		$this->manifest_en_path = ABSPATH . $this->manifest_en_file;
		$this->sw_path = ABSPATH . $this->sw_file;
		
		$this->manifest_it_url = get_site_url() . '/' . $this->manifest_it_file;
		$this->manifest_en_url = get_site_url() . '/' . $this->manifest_en_file;
		$this->sw_url = get_site_url() . '/' . $this->sw_file;		

	}

	public function init( ) {

		$this->loader = new Pwa_Generator_Loader();
		
		// registering the menu page. I dont see the point in doing it in the loader
		add_action( 'admin_menu', function () {
			add_menu_page( __('PWAGenerator', 'aaa'), __('PWAGenerator', 'aaa'), 'manage_options', __FILE__, function () {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . '/admin/partials/pwa-generator-admin-display.php';
			} );
		} );
		// now let's use the loader, ok?
		$this->loader->add_action( 'admin_post_generate_manifests_form_submit', $this, 'process_manifest_generator');
		$this->loader->add_action( 'admin_post_generate_sw_form_submit', $this, 'process_sw_generator');

		$this->loader->run();
	}

	// When we submit the form, in ..display.php
	public function process_manifest_generator() {
		$request = $_POST;
		if ( ! isset( $request['action'] ) || $request['action'] !== 'generate_manifests_form_submit' ) {
			return;
		}
		if ( ! isset( $request['nonce_field'] ) 
			|| ! wp_verify_nonce( $request['nonce_field'], 'action_generate_manifest' ) 
		) {
			print 'Sorry, your nonce did not verify.';
			exit;
		}
		if( ! current_user_can('editor') && !current_user_can('administrator') ) {
			print 'Sorry, your dont have capabilities.';
			exit;
		} 

		global $g_lang;
		foreach( $this->languages_defined as $lang => $lang_name ) {
			ob_start();
			$template_part =  substr( $this->manifest_source_relpath, 0, strrpos($this->manifest_source_relpath, '.') ); 
			$short_name = isset($request['short_name_' . $lang]) ? $request['short_name_' . $lang] : null;
			if ( $short_name !== null)
			{
				update_option( 'short_name_' . $lang, $short_name);
			}

			// use global var to set the lang.
			$g_lang = $lang;
			get_template_part( $template_part );

			$json_code = ob_get_clean();

			// Save the code into the file:
			$prop_lang = "manifest_" . $lang ."_path";
			$filepathname = $this->$prop_lang;
						
			if (file_exists( $filepathname )) 
			{
				// write file
				try {
					$f = @fopen("$filepathname", 'w');
					if (!$f) {
						throw new Exception ('Permissions problem: '.$filepathname. ' '.  getcwd()); 
					}
					$fwrite = fwrite($f, ( $json_code ));
					if ($fwrite === false) {
						die(' permissions? ');
					}
					fclose($f);
				} catch (\Throwable $th) {
					echo "Errors accessing manifest file: ";
					echo "<h4>$this->manifest_it_file</h4>";
					echo "and";
					echo "<h4>$this->manifest_en_file</h4>";
					echo "<p>$filepathname</p>";
					echo "<p>Looks like a permissions problem</p>";
					echo "<a href=".add_query_arg( 'manifesterror', 'filepermissions', $request['_wp_http_referer'] ).">redirect</a>";
					die();
				}
				
			} 
			else 
			{
				echo "Manifest file doesnt exist. Please make sure you create the file: ";
				echo "<h4>$this->manifest_it_file</h4>";
				echo "and";
				echo "<h4>$this->manifest_en_file</h4>";
				echo "<p>$filepathname</p>";
				echo "<a href=".add_query_arg( 'manifesterror', 'filedoesntexist', $request['_wp_http_referer'] ).">redirect</a>";
				die();
			}
			

			// echo $json_code;
		} //die('');
		// create variable with the json code 
		
		
		// redirect the user to the appropriate page
		wp_redirect( add_query_arg( 'manifest', 'generated', $request['_wp_http_referer'] )  , 301);
		
		exit;
	}


	// When we submit the form for service worker, in ..display.php
	public function process_sw_generator() {
		$request = $_POST;
		if ( ! isset( $request['action'] ) || $request['action'] !== 'generate_sw_form_submit' ) {
			return;
		}
		if ( ! isset( $request['nonce_field'] ) 
			|| ! wp_verify_nonce( $request['nonce_field'], 'action_generate_sw' ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		}
		if( ! current_user_can('editor') && !current_user_can('administrator') ) {
			print 'Sorry, your dont have capabilities.';
			exit;
		} 

		$template_part =  substr( $this->sw_source_relpath, 0, strrpos($this->sw_source_relpath, '.') ); 
		ob_start();
			get_template_part( $template_part );
		$json_code = ob_get_clean();

		
		$filepathname = $this->sw_path;
		if (file_exists( $filepathname )) 
		{
			// write file
			try {
				$f = @fopen("$filepathname", 'w');
				if (!$f) {
					throw new Exception ('Permissions problem: '.$filepathname. ' '.  getcwd()); 
				}
				$fwrite = fwrite($f, ( $json_code ));
				if ($fwrite === false) {
					die(' permissions? ');
				}
				fclose($f);
			} catch (\Throwable $th) {
				echo "Errors accessing sw file: ";
				echo "<h4>$this->sw_file</h4>";
				echo "<p>$filepathname</p>";
				echo "<p>Looks like a permissions problem</p>";
				echo "<a href=".add_query_arg( 'swerror', 'filepermissions', $request['_wp_http_referer'] ).">redirect</a>";
				die();
			}	
		} 
		else 
		{
			echo "sw file doesnt exist. Please make sure you create the file: ";
			echo "<h4>$this->sw_file</h4>";
			echo "<p>$filepathname</p>";
			echo "<a href=".add_query_arg( 'swerror', 'filedoesntexist', $request['_wp_http_referer'] ).">redirect</a>";
			die();
		}




		// redirect the user to the appropriate page
		wp_redirect( add_query_arg( 'sw', 'generated', $request['_wp_http_referer'] )  , 301);

	}



	/*** Register the stylesheets for the admin area. */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Pwa_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pwa_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pwa-generator-admin.css', array(), $this->version, 'all' );

	}

	/*** Register the JavaScript for the admin area. Same philosophy as above */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pwa-generator-admin.js', array( 'jquery' ), $this->version, false );
	}



}
