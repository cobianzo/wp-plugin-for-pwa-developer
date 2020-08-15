<?php
/**
 * THE CORE OF THE PLUGIN - the only file I need to look at in this folder.
 *
 * includes attributes and functions used across both public/ and admin/ */
class Pwa_Generator {

	/** type Pwa_Generator_Loader $loader: Maintains and registers all hooks for the plugin. */
	protected $loader;

	/** The unique identifier of this plugin. */
	protected $plugin_name;
	protected $version;

	/** * Init; ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== 
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin and plublic area */
	public function __construct() {
		
		$this->plugin_name 	= 'pwa-generator';
		$this->version 		= defined( 'PWA_GENERATOR_VERSION' ) ? PWA_GENERATOR_VERSION : '1.0.0';

		$this->load_dependencies();  // call every includes (for public and admin)
		$this->set_locale();
		$this->define_admin_hooks(); // hooks like enqueueing ... 
		$this->define_public_hooks();
	} // ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== 



	// now, every one of the init methods: ....





	/**	CORE includes CALLER * Create an instance of the loader which will be used to register the hooks with WordPress.  */
	private function load_dependencies() {

		/** * The class that defines $this->loader->add_action( ... ). Hooks in this plugin use this class */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pwa-generator-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pwa-generator-i18n.php'; /** internationalization functionality */

		/** Actions and Filters for ADMIN: (remember, that's where the fns are defined but the hook call is here with define_admin_hooks() */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pwa-generator-admin.php';

		/** Actions and Filters for PUBLIC: same as above */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pwa-generator-public.php';

		$this->loader = new Pwa_Generator_Loader();

	}

	/** internationalization. ******************************************* */
	private function set_locale() {
		$plugin_i18n = new Pwa_Generator_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	} /* ******************************************* ******************************************* */

	/** ADMIN HOOKS CALLs. The definition is in /admin/	******************************************** */
	private function define_admin_hooks() {

		$plugin_admin = new Pwa_Generator_Admin( $this->get_plugin_name(), $this->get_version() ); // call the definitions
		// echo '<br>';	print_r($this->loader); echo '<br>';	
		$plugin_admin->init();
		
		//$this->loader->add_action( 'admin_init', $plugin_admin, 'test' );
		// call the hooks
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	} /* ******************************************* ******************************************* */

	/** PUBLIC HOOKS. /public/ ******************************************* */
	private function define_public_hooks() {

		$plugin_public = new Pwa_Generator_Public( $this->get_plugin_name(), $this->get_version() );
		// call of the hooks defined in that new class.
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	} /* ******************************************* ******************************************* */



	/** * This executes any hook registered with `$this->loader->add_action` */
	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}
	public function get_loader() {
		return $this->loader;
	}
	public function get_version() {
		return $this->version;
	}

}
