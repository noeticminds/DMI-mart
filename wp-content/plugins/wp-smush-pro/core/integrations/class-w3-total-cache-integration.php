<?php

namespace Smush\Core\Integrations;

use Smush\Core\Controller;

class W3_Total_Cache_Integration extends Controller {
	public function __construct() {
		$this->register_action( 'wp_smush_post_lcp_data_updated', array( $this, 'clear_post_lcp_changes' ), 10, 2 );
		$this->register_action( 'wp_smush_home_lcp_data_updated', array( $this, 'clear_home_lcp_changes' ), 10, 3 );
	}

	public function should_run() {
		return parent::should_run() && function_exists( 'w3tc_flush_post' );
	}

	public function clear_post_lcp_changes( $lcp_data, $data_store ) {
		if ( function_exists( 'w3tc_flush_post' ) ) {
			$post_id = $data_store->get_object_id();
			w3tc_flush_post( $post_id );
		}
	}

	public function clear_home_lcp_changes( $lcp_data, $data_store, $url ) {
		if ( function_exists( 'w3tc_flush_url' ) ) {
			w3tc_flush_url( $url );
		}
	}
}