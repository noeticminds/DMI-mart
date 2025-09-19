<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://foadadeli.ir
 * @since      1.0.0
 *
 * @package    Wcfv
 * @subpackage Wcfv/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wcfv
 * @subpackage Wcfv/admin
 * @author     Foad Adeli <foad.adeli@gmail.com>
 */
class Wcfv_Admin {

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

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wcfv-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wcfv-admin.js', array( 'jquery' ), $this->version, true );
		wp_localize_script(
			$this->plugin_name,
			'wcfv_data',
			array(
				'media_library_title' 			=> esc_html__( 'Select Media' ),
				'media_library_button_text' => esc_html__( 'Select' ),
			)
		);
	}

	public function add_featured_video_tab( $tabs ) {
		$tabs['wcfv'] = array(
			'label'    => esc_html__( 'Featured Video', 'wcfv' ),
			'target'   => 'wcfv_product_data',
			'class'    => array(),
			'priority' => 80,
		);

		return $tabs;
	}

	public function add_featured_video_panel() {
    echo '<div id="wcfv_product_data" class="panel woocommerce_options_panel">';

		wp_nonce_field( 'wcfv_nonce', 'wcfv_nonce' );

		woocommerce_wp_select(
			array( 
				'id' 			=> 'wcfv_source',
				'label' 	=> esc_html__( 'Video Source', 'wcfv' ),
				'options' => array(
					'local' 	=> esc_html__( 'Local', 'wdfv' ),
					'youtube' => esc_html__( 'YouTube', 'wcfv' ),
					'vimeo' 	=> esc_html__( 'Vimeo', 'wcfv' )
				)
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          	=> 'wcfv_local_video',
				'label'       	=> esc_html__( 'Video', 'wcfv' ),
				'data_type'			=> 'url',
				'desc_tip'			=> true,
				'description'		=> esc_html__( 'You can select videos in MP4 or WebM format.', 'wcfv' ),
				'wrapper_class' => 'hidden',
				'custom_attributes'	=> array(
					'autocomplete' => 'off'
				)
    	)
		);
		$this->show_browse_media_button( 'wcfv_local_video', 'video' );

    woocommerce_wp_text_input(
			array(
				'id'          			=> 'wcfv_poster_image',
				'label'       			=> esc_html__( 'Poster Image', 'wcfv' ),
				'data_type'					=> 'url',
				'desc_tip'					=> true,
				'description'				=> esc_html__( 'If you leave the poster field empty, the featured image will be used.', 'wcfv' ),
				'wrapper_class' 		=> 'hidden',
				'custom_attributes'	=> array(
					'autocomplete' => 'off'
				)
    	)
		);
		$this->show_browse_media_button( 'wcfv_poster_image', 'image' );

		woocommerce_wp_text_input(
			array(
				'id'          	=> 'wcfv_youtube_video',
				'label'       	=> esc_html__( 'Youtube', 'wcfv' ),
				'data_type'			=> 'url',
				'wrapper_class' => 'hidden',
				'custom_attributes'	=> array(
					'autocomplete' => 'off'
				)
    	)
		);

		woocommerce_wp_text_input(
			array(
				'id'          	=> 'wcfv_youtube_image',
				'label'       	=> esc_html__( 'Thumbnail Image', 'wcfv' ),
				'data_type'			=> 'url',
				'wrapper_class' => 'hidden',
				'custom_attributes'	=> array(
					'autocomplete' => 'off'
				)
    	)
		);
		$this->show_browse_media_button( 'wcfv_youtube_image', 'image' );

		woocommerce_wp_text_input(
			array(
				'id'          	=> 'wcfv_vimeo_video',
				'label'       	=> esc_html__( 'Vimeo', 'wcfv' ),
				'data_type'			=> 'url',
				'wrapper_class' => 'hidden',
				'custom_attributes'	=> array(
					'autocomplete' => 'off'
				)
    	)
		);

		woocommerce_wp_text_input(
			array(
				'id'          	=> 'wcfv_vimeo_image',
				'label'       	=> esc_html__( 'Thumbnail Image', 'wcfv' ),
				'data_type'			=> 'url',
				'wrapper_class' => 'hidden',
				'custom_attributes'	=> array(
					'autocomplete' => 'off'
				)
    	)
		);
		$this->show_browse_media_button( 'wcfv_vimeo_image', 'image' );

    echo '</div>';
	}

	public function save_featured_video_value( $post_id ) {
		if ( get_post_type( $post_id ) !== 'product' ) return;
		if ( !isset( $_POST['wcfv_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcfv_nonce'] ) ), 'wcfv_nonce' ) ) return;

		$wcfv_source = isset( $_POST[ 'wcfv_source' ] ) 
			? sanitize_text_field( $_POST[ 'wcfv_source' ] ) 
			: '';
		$wcfv_video = isset( $_POST[ 'wcfv_local_video' ] ) 
			? sanitize_url( $_POST[ 'wcfv_local_video' ] ) 
			: '';
		$wcfv_poster = isset( $_POST[ 'wcfv_poster_image' ] ) 
			? sanitize_url( $_POST[ 'wcfv_poster_image' ] ) 
			: '';
		$wcfv_youtube_video = isset( $_POST[ 'wcfv_youtube_video' ] ) 
			? sanitize_url( $_POST[ 'wcfv_youtube_video' ] ) 
			: '';
		$wcfv_youtube_image = isset( $_POST[ 'wcfv_youtube_image' ] ) 
			? sanitize_url( $_POST[ 'wcfv_youtube_image' ] ) 
			: '';
		$wcfv_vimeo_video = isset( $_POST[ 'wcfv_vimeo_video' ] ) 
			? sanitize_url( $_POST[ 'wcfv_vimeo_video' ] ) 
			: '';
		$wcfv_vimeo_image = isset( $_POST[ 'wcfv_vimeo_image' ] ) 
			? sanitize_url( $_POST[ 'wcfv_vimeo_image' ] ) 
			: '';

		update_post_meta( $post_id, 'wcfv_source', $wcfv_source );
		update_post_meta( $post_id, 'wcfv_local_video', $wcfv_video );
		update_post_meta( $post_id, 'wcfv_poster_image', $wcfv_poster );
		update_post_meta( $post_id, 'wcfv_youtube_video', $wcfv_youtube_video );
		update_post_meta( $post_id, 'wcfv_youtube_image', $wcfv_youtube_image );
		update_post_meta( $post_id, 'wcfv_vimeo_video', $wcfv_vimeo_video );
		update_post_meta( $post_id, 'wcfv_vimeo_image', $wcfv_vimeo_image );
	}

	public function show_browse_media_button( $target, $media_type ) {
		?>
		<p class='form-field <?php echo esc_attr( $target ) . '_media_btn' ?> wcfv-browse-btn hidden'>
			<a data-target='<?php echo esc_attr( $target ) ?>' class='button button-primary' onclick='openMediaLibrary(event, "<?php echo esc_attr( $media_type ) ?>")'><?php esc_html_e( 'Browse', 'wcfv' ) ?></a>
		</p>
		<?php
	}

}