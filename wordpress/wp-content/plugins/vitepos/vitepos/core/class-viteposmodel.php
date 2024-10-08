<?php
/**
 * Vitepos Model
 *
 * @package VitePos\Core
 */

namespace VitePos\Core;

use Appsbd\V1\Core\BaseModel;

/**
 * Class ViteposModel
 *
 * @package VitePos\Core
 */
class ViteposModel extends BaseModel {

	/**
	 * The __ is generated by appsbd
	 *
	 * @param any  $string Its string param.
	 * @param null $parameter Its parameter param.
	 * @param null $_ Its _ param.
	 *
	 * @return mixed
	 */
	public function __( $string, $parameter = null, $_ = null ) {
		$args = func_get_args();

		return call_user_func_array( array( VitePos::get_instance(), '__' ), $args );
	}

	/**
	 * The get update values is generated by appsbd
	 *
	 * @param array $updated_data Its updated data.
	 *
	 * @return array|mixed
	 */
	public function get_update_values( $updated_data = array() ) {
		if ( is_array( $updated_data ) ) {
			foreach ( $updated_data as $prop => $val ) {
				if ( property_exists( $this, $prop ) && $this->{$prop} == $val ) {
					unset( $updated_data[ $prop ] );
				}
			}
		}

		return $updated_data;
	}
}
