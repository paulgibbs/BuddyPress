<?php

class P2P_Side_User extends P2P_Side {

	protected $item_type = 'BP_Relations_Item_User';

	function __construct( $query_vars ) {
		$this->query_vars = $query_vars;
	}

	function get_object_type() {
		return 'user';
	}

	function translate_qv( $qv ) {
		if ( isset( $qv['p2p:include'] ) )
			$qv['include'] = wp_list_pluck( $qv, 'p2p:include' );

		if ( isset( $qv['p2p:exclude'] ) )
			$qv['exclude'] = wp_list_pluck( $qv, 'p2p:exclude' );

		if ( isset( $qv['p2p:search'] ) && $qv['p2p:search'] )
			$qv['search'] = '*' . wp_list_pluck( $qv, 'p2p:search' ) . '*';

		if ( isset( $qv['p2p:page'] ) && $qv['p2p:page'] > 0 ) {
			if ( isset( $qv['p2p:per_page'] ) && $qv['p2p:per_page'] > 0 ) {
				$qv['number'] = $qv['p2p:per_page'];
				$qv['offset'] = $qv['p2p:per_page'] * ( $qv['p2p:page'] - 1 );
			}
		}

		return $qv;
	}

	function do_query( $args ) {
		return new WP_User_Query( $args );
	}

	function capture_query( $args ) {
		$args['count_total'] = false;

		$uq = new WP_User_Query;
		$uq->_p2p_capture = true; // needed by P2P_URL_Query

		// see http://core.trac.wordpress.org/ticket/21119
		$uq->query_vars = wp_parse_args( $args, array(
			'blog_id' => $GLOBALS['blog_id'],
			'role' => '',
			'meta_key' => '',
			'meta_value' => '',
			'meta_compare' => '',
			'include' => array(),
			'exclude' => array(),
			'search' => '',
			'search_columns' => array(),
			'orderby' => 'login',
			'order' => 'ASC',
			'offset' => '',
			'number' => '',
			'count_total' => true,
			'fields' => 'all',
			'who' => ''
		) );

		$uq->prepare_query();

		return "SELECT $uq->query_fields $uq->query_from $uq->query_where $uq->query_orderby $uq->query_limit";
	}

	function is_indeterminate( $side ) {
		return true;
	}

	function get_base_qv( $q ) {
		return array_merge( $this->query_vars, $q );
	}

	protected function recognize( $arg ) {
		if ( is_a( $arg, 'WP_User' ) )
			return $arg;

		return get_user_by( 'id', $arg );
	}
}

