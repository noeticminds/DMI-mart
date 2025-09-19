<?php
class Wcfv_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

		/**
	 * The settings key of the plugin.
	 */
	private static $setting_key = 'wcfv_settings';

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

	}

	public function add_plugin_setting_link( $links ) {
		$settings_link = sprintf('<a href="%s">%s</a>', esc_attr('options-general.php?page=wcfv-settings'), esc_html__('Settings', 'wcfv'));
    $links[] = $settings_link;
    return $links;
	}

	public function add_plugin_setting_page() {
		add_options_page(
      esc_html__( 'WooCommerce Featured Video Settings', 'wcfv' ),
      esc_html__( 'WC Featured Video', 'wcfv' ),
			'manage_options',
      'wcfv-settings',
      array( $this, 'render_plugin_setting_page' )
		);
	}

	public function render_plugin_setting_page() {
		?>
    <div class="wrap">
      <h1><?php esc_html_e( 'WooCommerce Featured Video Settings', 'wcfv' )?></h1>

      <form method="POST" action="<?php echo admin_url( 'admin-post.php' ) ?>">
				<input type="hidden" name="action" value="save_options">
        <?php wp_nonce_field( 'wcfv_nonce', 'wcfv_nonce' ); ?>

				<br>

				<div>
					<label for="wcfv_thumbnail_position"><?php esc_html_e( 'Thumbnail Position: ', 'wcfv' )?></label>

					<select name="wcfv_thumbnail_position" id="wcfv_thumbnail_position">
						<option <?php selected(self::get_option('thumbnail_position'), 'first') ?> value="first"><?php esc_html_e( 'First Item', 'wcfv' ) ?></option>
						<option <?php selected(self::get_option('thumbnail_position'), 'last') ?> value="last"><?php esc_html_e( 'Last Item', 'wcfv' ) ?></option>
					</select>
				</div>

				<br>

				<div>
					<button class="button button-primary" type="submit"><?php esc_html_e( 'Save Settings', 'wcfv' ) ?></button>
				</div>
      </form>
    </div>
    <?php
	}

	public function save_options() {
		if ( isset( $_POST['wcfv_nonce'] ) && !wp_verify_nonce( $_POST['wcfv_nonce'], 'wcfv_nonce' ) ) {
      return;
    }

		$thumbnail_position = isset( $_POST['wcfv_thumbnail_position'] ) ? sanitize_text_field( $_POST['wcfv_thumbnail_position'] ) : '';
		self::save_option( 'thumbnail_position', $thumbnail_position );

		wp_redirect( wp_get_referer() );
	}

	public static function get_options() {
		$options = get_option( self::$setting_key );

		if ( empty( $options ) ) {
			$options = array();
		}

		return $options;
  }

	public static function get_option( $key, $default = '' ) {
		$options = self::get_options();

		if ( !empty( $options[$key] ) ) {
			return $options[$key];
		}

		return $default;
	}

	public static function save_option( $key, $value ) {
		$options = self::get_options();
		$options[$key] = $value;
		update_option( self::$setting_key, $options );
	}

}