<?php

namespace Smush\Core\Integrations;

use Smush\Core\Controller;

class WP_Rocket_Integration extends Controller {
	public function __construct() {
		$this->register_action( 'wp_smush_post_lcp_data_updated', array( $this, 'clear_post_lcp_changes' ), 10, 2 );
		$this->register_action( 'wp_smush_home_lcp_data_updated', array( $this, 'clear_home_lcp_changes' ) );
	}

	public function clear_post_lcp_changes( $lcp_data, $data_store ) {
		if ( function_exists( 'rocket_clean_post' ) ) {
			$post_id = $data_store->get_object_id();
			rocket_clean_post( $post_id );
		}
	}

	public function clear_home_lcp_changes() {
		if ( function_exists( 'rocket_clean_home' ) ) {
			rocket_clean_home();
		}
	}
}