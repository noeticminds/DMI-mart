<?php

namespace Smush\Core\Integrations;

use Smush\Core\Controller;

class WP_Fastest_Cache_Integration extends Controller {
	public function __construct() {
		$this->register_action( 'wp_smush_post_lcp_data_updated', array( $this, 'clear_post_lcp_changes' ), 10, 2 );
	}

	public function should_run() {
		return parent::should_run() && function_exists( 'wpfc_clear_post_cache_by_id' );
	}

	public function clear_post_lcp_changes( $lcp_data, $data_store ) {
		if ( function_exists( 'wpfc_clear_post_cache_by_id' ) ) {
			$post_id = $data_store->get_object_id();
			wpfc_clear_post_cache_by_id( $post_id );
		}
	}
}