<?php

namespace Rtcl\Controllers\Admin\Meta;


class RemoveMetaBox {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'remove_meta_box' ) );
	}

	function remove_meta_box() {
		remove_meta_box( rtcl()->category.'div', rtcl()->post_type, 'side' );
		remove_meta_box( rtcl()->location.'div', rtcl()->post_type, 'side' );
		remove_meta_box( 'submitdiv', rtcl()->post_type_payment, 'side' );
	}
}