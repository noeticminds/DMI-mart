<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://foadadeli.ir
 * @since      1.0.0
 *
 * @package    Wcfv
 * @subpackage Wcfv/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wcfv
 * @subpackage Wcfv/public
 * @author     Foad Adeli <foad.adeli@gmail.com>
 */
class Wcfv_Public {

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
	 * Count how many times a function has been run.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private $counter;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->counter = 0;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'plyr', plugin_dir_url( __FILE__ ) . 'css/plyr.css' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wcfv-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'plyr', plugin_dir_url( __FILE__ ) . 'js/plyr.js', array(), false, true );
		wp_localize_script( 'plyr', 'wcfv', array( 'publicDir' => plugin_dir_url( __FILE__ ) ) );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wcfv-public.js', array( 'jquery', 'plyr' ), $this->version, true );
	}

	public function show_video_on_single_product_page( $html, $post_thumbnail_id ) {
		global $post;

		if (!$this->has_featured_video($post->ID)) return $html;

		$thumbnail_position = Wcfv_Settings::get_option( 'thumbnail_position' );
		$total_images 			= $this->count_product_gallery_images( $post->ID );
		$video_source 			= $this->get_video_source( $post->ID );

		// Add video in the beginning of the gallery
		if ( ($this->counter === 0) && ($thumbnail_position !== 'last') ) {

			if ( $video_source === 'local' ) {
				ob_start();
				$this->local_video_template( $post->ID );
				$html = ( $this->get_video_poster( $post->ID ) )
					? ob_get_clean() . $html
					: ob_get_clean();
			} else if ( $video_source === 'youtube' ) {
				ob_start();
				$this->youtube_video_template( $post->ID );
				$html = ob_get_clean() . $html;
			} else if ( $video_source === 'vimeo' ) {
				ob_start();
				$this->vimeo_video_template( $post->ID );
				$html = ob_get_clean() . $html;
			}

		}

		// Add video at the end of the gallery
		if ( ($this->counter === $total_images - 1) && ($thumbnail_position === 'last') ) {

			if ( $video_source === 'local' ) {
				ob_start();
				$this->local_video_template( $post->ID );
				$html = ( $this->get_video_poster( $post->ID ) )
					? $html . ob_get_clean()
					: ob_get_clean();
			} else if ( $video_source === 'youtube' ) {
				ob_start();
				$this->youtube_video_template( $post->ID );
				$html = $html . ob_get_clean();
			} else if ( $video_source === 'vimeo' ) {
				ob_start();
				$this->vimeo_video_template( $post->ID );
				$html = $html . ob_get_clean();
			}

		}

		$this->counter++;
		return $html;
	}

	public function local_video_template( $product_id ) {
		$local_video 		= $this->get_local_video( $product_id );
		$video_poster 	= $this->get_video_poster( $product_id );
		$featured_image = $this->get_featured_image( $product_id );
		$thumb 					= $this->get_video_poster( $product_id )
			? $this->get_video_poster( $product_id, 'woocommerce_gallery_thumbnail' )
			: $this->get_featured_image( $product_id, 'woocommerce_gallery_thumbnail' );

		if ( $local_video ) {
			?>
				<div data-thumb="<?php echo esc_url( $thumb ) ?>" class="woocommerce-product-gallery__image wcfv-wrapper <?php echo $video_poster ? 'has-poster' : '' ?>">
					<video class="wcfv-player" width="100%" playsinline controls data-poster="<?php echo $video_poster ? esc_url( $video_poster ) : esc_url( $featured_image ) ?>">
						<source src="<?php echo esc_url( $local_video ) ?>" type="video/<?php echo esc_attr( wp_check_filetype( $local_video )['ext'] ) ?>" />
					</video>
				</div>
			<?php
		}
	}

	public function youtube_video_template( $product_id ) {
		$youtube_video 	= $this->get_youtube_video( $product_id );
		$thumb 					= $this->get_youtube_image( $product_id )
			? $this->get_youtube_image( $product_id)
			: $this->get_default_video_thumbnail();
		if ( $youtube_video ) {
			?>
				<div data-thumb="<?php echo esc_url( $thumb ) ?>" class="woocommerce-product-gallery__image wcfv-wrapper">
					<div class="wcfv-player" data-plyr-provider="youtube" data-plyr-embed-id="<?php echo esc_url( $youtube_video ) ?>"></div>
				</div>
			<?php
		}
	}

	public function vimeo_video_template( $product_id ) {
		$vimeo_video 		= $this->get_vimeo_video( $product_id );
		$thumb 					= $this->get_vimeo_image( $product_id )
			? $this->get_vimeo_image( $product_id)
			: $this->get_default_video_thumbnail();
		if ( $vimeo_video ) {
			?>
				<div data-thumb="<?php echo esc_url( $thumb ) ?>" class="woocommerce-product-gallery__image wcfv-wrapper">
					<div class="wcfv-player" data-plyr-provider="vimeo" data-plyr-embed-id="<?php echo esc_url( $vimeo_video ) ?>"></div>
				</div>
			<?php
		}
	}

	public function count_product_gallery_images( $product_id ) {
		$total = 1;

		try {
			$product = new WC_Product( $product_id );

			if ( $product ) {
				$count = count( $product->get_gallery_image_ids() );
				$total += $count;
			}
		} catch ( Exception $e ) {}

		return $total;
	}

	public function get_video_source( $product_id ) {
		$source = get_post_meta( $product_id, 'wcfv_source', true );
		return $source;
	}

	public function get_local_video( $product_id ) {
		$video = get_post_meta( $product_id, 'wcfv_local_video', true );
		return $video;
	}

	public function get_youtube_video( $product_id ) {
		$video = get_post_meta( $product_id, 'wcfv_youtube_video', true );
		return $video;
	}

	public function get_youtube_image( $product_id, $size = 'woocommerce_gallery_thumbnail' ) {
		$poster_id = attachment_url_to_postid( get_post_meta( $product_id, 'wcfv_youtube_image', true ) );
		return wp_get_attachment_image_url( $poster_id, $size );
	}

	public function get_vimeo_video( $product_id ) {
		$video = get_post_meta( $product_id, 'wcfv_vimeo_video', true );
		return $video;
	}

	public function get_vimeo_image( $product_id, $size = 'woocommerce_gallery_thumbnail' ) {
		$poster_id = attachment_url_to_postid( get_post_meta( $product_id, 'wcfv_vimeo_image', true ) );
		return wp_get_attachment_image_url( $poster_id, $size );
	}

	public function get_video_poster( $product_id, $size = 'woocommerce_single' ) {
		$poster_id = attachment_url_to_postid( get_post_meta( $product_id, 'wcfv_poster_image', true ) );
		return wp_get_attachment_image_url( $poster_id, $size );
	}

	public function get_featured_image( $product_id, $size = 'woocommerce_single' ) {
		$featured_image_id = get_post_thumbnail_id( $product_id );
		return wp_get_attachment_image_url( $featured_image_id, $size );
	}

	public function get_default_video_thumbnail() {
		return plugin_dir_url( dirname( __FILE__ ) ) . '/public/img/video-icon.png';
	}

	protected function has_featured_video($product_id) {
		$local_video = $this->get_local_video($product_id);
		$youtube_video = $this->get_youtube_video($product_id);
		$vimeo_video = $this->get_vimeo_video($product_id);

		return $local_video || $youtube_video || $vimeo_video;
	}

}
