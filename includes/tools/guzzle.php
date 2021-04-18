<?php namespace peroks\plugins\guzzle;
/**
 * Guzzle configuration.
 *
 * @author Per Egil Roksvaag
 */
class Guzzle
{
	use Singleton;

	/**
	 * @var string Admin settings
	 */
	const SECTION_GUZZLE         = Main::PREFIX . '_guzzle';
	const OPTION_GUZZLE_AUTOLOAD = self::SECTION_GUZZLE . '_autoload';

	/**
	 * Constructor.
	 */
	protected function __construct() {

		//	Autoload Guzzle
		if ( get_option( self::OPTION_GUZZLE_AUTOLOAD, 1 ) ) {
			include_once Main::plugin_path( 'vendor/autoload.php' );
		}

		//	Admin settings
		if ( is_admin( ) ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( Main::ACTION_ACTIVATE, array( $this, 'activate' ) );
			add_action( Main::ACTION_DELETE, array( $this, 'delete' ) );
		}
	}

	/* -------------------------------------------------------------------------
	 * Admin setting
	 * ---------------------------------------------------------------------- */

	/**
	 * Registers settings, sections and fields.
	 */
	public function admin_init() {

		//	Danger section
		Admin::instance()->add_section( array(
			'section' => self::SECTION_GUZZLE,
			'page'    => Admin::PAGE,
			'label'   => __( 'Guzzle', 'wp-guzzle' ),
		) );

		//	Delete plugin data
		Admin::instance()->add_checkbox( array(
			'option'      => self::OPTION_GUZZLE_AUTOLOAD,
			'section'     => self::SECTION_GUZZLE,
			'page'        => Admin::PAGE,
			'label'       => __( 'Autoload Guzzle', 'wp-guzzle' ),
			'description' => __( 'Check to call the Guzzle autoloader on start.', 'wp-guzzle' ),
		) );
	}

	/**
	 * Sets plugin default setting on activation.
	 */
	public function activate() {
		if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
			if ( is_null( get_option( self::OPTION_GUZZLE_AUTOLOAD, null ) ) ) {
				add_option( self::OPTION_GUZZLE_AUTOLOAD, 1 );
			}
		}
	}

	/**
	 * Removes settings on plugin deletion.
	 */
	public function delete() {
		if ( is_admin() && current_user_can( 'delete_plugins' ) ) {
			if ( get_option( Admin::OPTION_DELETE_SETTINGS ) ) {
				delete_option( self::OPTION_GUZZLE_AUTOLOAD );
			}
		}
	}
}