<?php

namespace Smush\Core\Integrations;

use Smush\Core\Controller;

class WP_Super_Cache_Integration extends Controller {
	public function __construct() {
		$this->register_action( 'wp_smush_post_lcp_data_updated', array( $this, 'clear_post_lcp_changes' ), 10, 2 );
		$this->register_action( 'wp_smush_home_lcp_data_updated', array( $this, 'clear_home_lcp_changes' ), 10, 3 );
	}

	public function should_run() {
		return parent::should_run() && function_exists( 'wpsc_delete_post_cache' );
	}

	public function clear_post_lcp_changes( $lcp_data, $data_store ) {
		if ( function_exists( 'wpsc_delete_post_cache' ) ) {
			$post_id = $data_store->get_object_id();
			wpsc_delete_post_cache( $post_id );
		}
	}

	public function clear_home_lcp_changes( $lcp_data, $data_store, $url ) {
		if ( function_exists( 'wpsc_delete_url_cache' ) ) {
			wpsc_delete_url_cache( $url );
		}
	}
}