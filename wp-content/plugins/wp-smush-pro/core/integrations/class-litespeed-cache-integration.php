<?php

namespace Smush\Core\Integrations;

use Smush\Core\Controller;

class Litespeed_Cache_Integration extends Controller {
	public function __construct() {
		$this->register_action( 'wp_smush_post_lcp_data_updated', array( $this, 'clear_post_lcp_changes' ), 10, 2 );
		$this->register_action( 'wp_smush_home_lcp_data_updated', array( $this, 'clear_home_lcp_changes' ), 10, 3 );
	}

	public function should_run() {
		return parent::should_run() && class_exists( '\LiteSpeed\Core' );
	}

	public function clear_post_lcp_changes( $lcp_data, $data_store ) {
		$post_id = $data_store->get_object_id();
		do_action( 'litespeed_purge_post', $post_id );
	}

	public function clear_home_lcp_changes( $lcp_data, $data_store, $url ) {
		do_action( 'litespeed_purge_url', $url );
	}
}