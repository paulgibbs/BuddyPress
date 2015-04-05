<?php
abstract class P2P_Side {

	protected $item_type;

	abstract function get_object_type();

	abstract function get_base_qv( $q );
	abstract function translate_qv( $qv );
	abstract function do_query( $args );
	abstract function capture_query( $args );
	abstract function get_list( $query );

	abstract function is_indeterminate( $side );

	final function is_same_type( $side ) {
		return $this->get_object_type() == $side->get_object_type();
	}

	/**
	 * @param object Raw object or BP_Relations_Item
	 * @return bool|BP_Relations_Item
	 */
	function item_recognize( $arg ) {
		$class = $this->item_type;

		if ( is_a( $arg, 'BP_Relations_Item' ) ) {
			if ( !is_a( $arg, $class ) ) {
				return false;
			}

			$arg = $arg->get_object();
		}

		$raw_item = $this->recognize( $arg );
		if ( !$raw_item )
			return false;

		return new $class( $raw_item );
	}

	/**
	 * @param object Raw object
	 */
	abstract protected function recognize( $arg );
}
