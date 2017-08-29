<?php
require plugin_dir_path( __FILE__ ) . '/../statements/Event.php';
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/lincoln-howard-jr
 * @since      1.0.0
 *
 * @package    Eventful
 * @subpackage Eventful/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Eventful
 * @subpackage Eventful/admin
 * @author     Lincoln Howard <lincoln.c.howard.jr@gmail.com>
 */
class Eventful_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->EventfulEvent = new Event ();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Eventful_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Eventful_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
    // custom css for this plugin
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/eventful-admin.css', array(), $this->version, 'all' );
    // bootstrap 4 alpha
    wp_enqueue_style( $this->plugin_name . '-twbs', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css', array(), $this->version, 'all' );
    // font-awesome
    wp_enqueue_style( $this->plugin_name . '-fa', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), $this->version, 'all' );
    // google fonts
    wp_enqueue_style( $this->plugin_name . '-gfont', '//fonts.googleapis.com/css?family=Zilla+Slab', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Eventful_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Eventful_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

    // unecessary because we don't use javascript for this plugin
    // wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/eventful-admin.js', array( 'jquery' ), $this->version, false );

	}

  // old method whene using custom database tables
	public function setup_crud () {
		add_action ('admin_post_create_event', array ($this, 'create_event'));
    add_action ('admin_post_delete_event', array ($this, 'delete_event'));
    add_action ('admin_post_update_event', array ($this, 'update_event'));
	}
  // old db method for creating event records
	public function create_event () {
		unset ($_REQUEST ['action']);
		$this->EventfulEvent->create ($_REQUEST);
    wp_redirect (get_admin_url () . 'admin.php?page=eventful');
	}
  // old db method for deleting event records
  public function delete_event () {
    unset ($_REQUEST ['action']);
    $this->EventfulEvent->delete ($_REQUEST ['id']);
    wp_redirect (get_admin_url () . 'admin.php?page=eventful');
  }
  // delete a record
  public function update_event () {
    unset ($_REQUEST ['action']);
    var_dump ($this->EventfulEvent->update ($_REQUEST ['id'], $_REQUEST));
    wp_redirect (get_admin_url () . 'admin.php?page=eventful_manage');
  }
  // add the menu page for eventful
  public function add_eventful_menu () {
    add_menu_page (
      'Eventful Calendar',
      'Eventful',
      'manage_options',
      $this->plugin_name,
      array($this, 'display_eventful_menu'),
      'dashicons-calendar-alt'
    );
  }
  // called to display the admin menu page
  public function display_eventful_menu () {
    include_once 'partials/eventful-admin-menu-display.php';
  }
  // old method to create all submenus
  public function add_eventful_submenu () {
    // add new event page
		add_submenu_page (
  		$this->plugin_name,
  		'Create Event',
  		'New Event',
  		'manage_options',
  		$this->plugin_name . '_create',
  		array ($this, 'display_eventful_submenu_create')
  	);
    // add manage events page
    add_submenu_page (
      $this->plugin_name,
      'Manage Events',
      'Event List',
      'manage_options',
      $this->plugin_name . '_manage',
      array ($this, 'display_eventful_submenu_manage')
    );
    // add manage events page
    add_submenu_page (
      $this->plugin_name,
      'Edit Events',
      'Edit',
      'manage_options',
      $this->plugin_name . '_edit',
      array ($this, 'display_eventful_submenu_edit')
    );
  }
  // display the create submenu
  public function display_eventful_submenu_create () {
  	include_once 'partials/eventful-admin-submenu-create-display.php';
  }
  // display the manage submenu
  public function display_eventful_submenu_manage () {
    include_once 'partials/eventful-admin-submenu-manage-display.php';
  }
  // display the edit submenu
  public function display_eventful_submenu_edit () {
    include_once 'partials/eventful-admin-submenu-edit-display.php';
  }
  // old method to create an options page
	public function add_options_page () {
		add_options_page(
			__('Eventful Settings', 'eventful'),
			__('Eventful', 'eventful'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_options_page')
		);
	}
  // display the options page
	public function display_options_page () {
		include_once 'partials/eventful-admin-display.php';
	}

}
