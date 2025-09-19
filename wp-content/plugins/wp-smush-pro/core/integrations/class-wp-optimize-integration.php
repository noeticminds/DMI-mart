<?php

namespace Smush\Core\Integrations;

use Smush\Core\Controller;
use WPO_Page_Cache;

class WP_Optimize_Integration extends Controller {
	public function __construct() {
		$this->register_action( 'wp_smush_post_lcp_data_updated', array( $this, 'clear_post_lcp_changes' ), 10, 2 );
		$this->register_action( 'wp_smush_home_lcp_data_updated', array( $this, 'clear_home_lcp_changes' ) );
	}

	public function should_run() {
		return parent::should_run() && class_exists( 'WPO_Page_Cache' );
	}

	public function clear_post_lcp_changes( $lcp_data, $data_store ) {
		if ( method_exists( 'WPO_Page_Cache', 'delete_single_post_cache' ) ) {
			$post_id = $data_store->get_object_id();
			WPO_Page_Cache::delete_single_post_cache( $post_id );
		}
	}

	public function clear_home_lcp_changes() {
		if ( method_exists( 'WPO_Page_Cache', 'delete_homepage_cache' ) ) {
			WPO_Page_Cache::delete_homepage_cache();
		}
	}
}