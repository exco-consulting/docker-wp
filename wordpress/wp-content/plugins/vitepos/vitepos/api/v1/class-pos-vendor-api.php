<?php
/**
 * Its api for vendor
 *
 * @since: 12/07/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Api\V1
 */

namespace VitePos\Api\V1;

use Appsbd\V1\libs\API_Data_Response;
use VitePos\Libs\API_Base;
use Vitepos\Models\Database\Mapbd_pos_vendor;

/**
 * Class pos_vendor_api
 *
 * @package VitePos\Api\V1
 */
class Pos_Vendor_Api extends API_Base {

	/**
	 * The set api base is generated by appsbd
	 *
	 * @return mixed|string
	 */
	public function set_api_base() {
		return 'vendor';
	}

	/**
	 * The routes is generated by appsbd
	 *
	 * @return mixed|void
	 */
	public function routes() {
		$this->register_rest_route( 'POST', 'list', array( $this, 'vendor_list' ) );
		$this->register_rest_route( 'POST', 'create', array( $this, 'create_vendor' ) );
		$this->register_rest_route( 'POST', 'update_status', array( $this, 'update_status' ) );
		$this->register_rest_route( 'POST', 'delete-vendor', array( $this, 'delete_vendor' ) );
		$this->register_rest_route( 'GET', 'details/(?P<id>\d+)', array( $this, 'vendor_details' ) );
	}
	/**
	 * The set route permission is generated by appsbd
	 *
	 * @param \VitePos\Libs\any $route Its string.
	 *
	 * @return bool
	 */
	public function set_route_permission( $route ) {
		switch ( $route ) {
			case 'create':
				return current_user_can( 'vendor-add' ) || current_user_can( 'vendor-edit' );
			case 'delete-vendor':
				return current_user_can( 'vendor-delete' );
			default:
				break;
		}

		return parent::set_route_permission( $route );
	}

	/**
	 * The vendor list is generated by appsbd
	 *
	 * @return API_Data_Response
	 */
	public function vendor_list() {
		$mainobj              = new Mapbd_pos_vendor();
		$response_data        = new API_Data_Response();
		$response_data->limit = $this->get_payload( 'limit', 20 );
		$response_data->page  = $this->get_payload( 'page', 1 );
		$src_props            = $this->get_payload( 'src_by', array() );
		$sort_props           = $this->get_payload( 'sort_by', array() );
		$mainobj->set_search_by_param( $src_props );
		$mainobj->set_sort_by_param( $sort_props );
		if ( $response_data->set_total_records( $mainobj->count_all() ) ) {
			$response_data->rowdata = $mainobj->select_all_grid_data( '', '', '', $response_data->limit, $response_data->limit_start() );
		}
		return $response_data;
	}

	/**
	 * The vendor details is generated by appsbd
	 *
	 * @param any $data Its string.
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function vendor_details( $data ) {
		if ( ! empty( $data['id'] ) ) {
			$id         = intval( $data['id'] );
			$vendor_obj = new Mapbd_pos_vendor();
			$vendor_obj->id( $id );
			if ( $vendor_obj->Select() ) {
				$this->set_response( true, 'data found', $vendor_obj );
				return $this->response;
			}
		}
		$this->set_response( false, 'data not found or invalid param' );
		return $this->response;

	}

	/**
	 * The create vendor is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function create_vendor() {
		if ( empty( $this->payload['id'] ) ) {
			if ( ! current_user_can( 'vendor-add' ) ) {
				$this->add_error( 'You do not have permission to do this' );
				$this->response->set_response( false, '' );
				return $this->response->get_response();
			}
			$vendor_obj = new Mapbd_Pos_Vendor();
			$vendor_obj->set_from_array( $this->payload );
				$vendor_obj->added_by( $this->get_current_user_id() );
			if ( $vendor_obj->is_valid_form( true ) ) {
				if ( $vendor_obj->save() ) {
					$this->response->set_response( true, 'Successfully created', $vendor_obj );
				} else {
					$this->response->set_response( true, appsbd_get_msg_api(), $vendor_obj );
				}
				return $this->response->get_response();
			}
			$this->response->set_response( false, appsbd_get_msg_api() );
			return $this->response;
		} else {
			if ( ! current_user_can( 'vendor-edit' ) ) {
				$this->add_error( 'You do not have permission to do this' );
				$this->response->set_response( false, '' );
				return $this->response->get_response();
			}
			$old_object = Mapbd_Pos_Vendor::find_by( 'id', $this->payload['id'] );
			if ( $old_object ) {
				$vendor_obj = new Mapbd_pos_vendor();
				$vendor_obj->set_from_array( $this->payload );
				if ( $vendor_obj->is_valid_form( false ) ) {
					$vendor_obj->set_where_update( 'id', $this->payload['id'] );
					if ( $vendor_obj->update() ) {
						$updated_obj = Mapbd_pos_vendor::find_by( 'id', $vendor_obj->id );
						$this->response->set_response( true, 'Successfully updated', $updated_obj );
					} else {
						$this->response->set_response( false, appsbd_get_msg_api(), $vendor_obj );
					}
					return $this->response->get_response();
				}
			}
			$this->response->set_response( false, appsbd_get_msg_api() );
			return $this->response->get_response();
		}
	}

	/**
	 * The update status is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function update_status() {
		if ( ! empty( $this->payload['id'] ) ) {
			$old_object = Mapbd_pos_vendor::find_by( 'id', $this->payload['id'] );
			if ( $old_object ) {
				$vendor_obj = new Mapbd_pos_vendor();
				$vendor_obj->set_from_array( $this->payload );
				if ( $vendor_obj->is_valid_form( false ) ) {
					$vendor_obj->set_where_update( 'id', $this->payload['id'] );
					$vendor_obj->unset_all_excepts( 'status' );
					if ( $vendor_obj->update() ) {
						$updated_obj = Mapbd_pos_vendor::find_by( 'id', $vendor_obj->id );
						$this->response->set_response( true, 'Successfully updated', $updated_obj );
					} else {
						$this->response->set_response( false, appsbd_get_msg_api(), $vendor_obj );
					}
					return $this->response;
				}
				$this->response->set_response( false, appsbd_get_msg_api() );
				return $this->response;
			}
			$this->response->set_response( false, appsbd_get_msg_api() );
			return $this->response;
		} else {
			$this->response->set_response( false, 'Nothing to update' );
			return $this->response;
		}
	}

	/**
	 * The delete vendor is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function delete_vendor() {
		if ( ! empty( $this->payload['id'] ) ) {
			$id = intval( $this->payload['id'] );
			$mr = new Mapbd_pos_vendor();
			$mr->id( $id );
			if ( $mr->Select() ) {
				if ( Mapbd_pos_vendor::delete_by_id( $id ) ) {
					$this->add_info( 'Vendor deleted successfully' );
					$this->response->set_response( true );

				} else {
					$this->add_error( 'Vendor delete failed' );
					$this->response->set_response( false );
				}
			} else {
				$this->add_error( 'No vendor found with this param' );
				$this->response->set_response( false );
			}
			return $this->response->get_response();
		}
	}
}
