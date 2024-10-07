<?php
/**
 * Its api for user
 *
 * @since: 12/07/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Api\V1
 */

namespace VitePos\Api\V1;

use Appsbd\V1\libs\API_Data_Response;
use VitePos\Libs\API_Base;
use VitePos\Libs\POS_Customer;
use Vitepos\Models\Database\Mapbd_Pos_Cash_Drawer;
use Vitepos\Models\Database\Mapbd_Pos_Role;
use Vitepos\Models\Database\Mapbd_Pos_Stock_Transfer;
use Vitepos\Models\Database\Mapbd_Pos_Warehouse;
use VitePos\Modules\POS_Settings;

/**
 * Class pos_user_api
 *
 * @package VitePos\Api\V1
 */
class Pos_User_Api extends API_Base {

	/**
	 * The set api base is generated by appsbd
	 *
	 * @return mixed|string
	 */
	public function set_api_base() {
		return 'user';
	}

	/**
	 * The routes is generated by appsbd
	 *
	 * @return mixed|void
	 */
	public function routes() {
		$this->register_rest_route( 'POST', 'login', array( $this, 'user_login' ) );
		$this->register_rest_route( 'GET', 'logout', array( $this, 'user_logout' ) );
		$this->register_rest_route( 'POST', 'list', array( $this, 'user_list' ) );
		$this->register_rest_route( 'GET', 'waiter-list', array( $this, 'waiter_list' ) );
		$this->register_rest_route( 'POST', 'change-pass', array( $this, 'change_pass' ) );
		$this->register_rest_route( 'POST', 'change-pass-force', array( $this, 'change_pass_force' ) );
		$this->register_rest_route( 'POST', 'delete-user', array( $this, 'delete_user' ) );
		$this->register_rest_route( 'GET', 'close-cash-drawer', array( $this, 'close_cash_drawer' ) );
		$this->register_rest_route( 'GET', 'cash-drawer-list', array( $this, 'cash_drawer_list' ) );
		$this->register_rest_route( 'GET', 'roles', array( $this, 'roles' ) );
		$this->register_rest_route( 'GET', 'capabilities', array( $this, 'capabilities' ) );
		$this->register_rest_route( 'POST', 'create', array( $this, 'create_user' ) );
		$this->register_rest_route( 'POST', 'outlet-panel', array( $this, 'outlet_panel' ) );
		$this->register_rest_route( 'GET', 'details/(?P<id>\d+)', array( $this, 'user_details' ) );
		$this->register_rest_route( 'GET', 'current-user', array( $this, 'api_current_user' ) );
		$this->register_rest_route( 'GET', 'get-logged-user', array( $this, 'get_logged_user' ) );
	}

	/**
	 * The set route permission is generated by appsbd
	 *
	 * @param \VitePos\Libs\any $route Its string.
	 *
	 * @return bool
	 */
	public function set_route_permission( $route ) {
		if ( 'login' == $route || 'get-logged-user' == $route ) {
			return true;
		} elseif ( 'logout' == $route ) {
			return true;
		} elseif ( 'create' == $route ) {
			return current_user_can( 'user-add' ) || current_user_can( 'user-edit' );
		} elseif ( 'delete-user' == $route ) {
			return current_user_can( 'user-delete' );
		}
		return parent::set_route_permission( $route );
	}

	/**
	 * The user login is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function user_login() {
		if ( ! POS_Settings::check_captcha( $this->payload ) ) {
			$this->response->set_response( false, '' );
			return $this->response->get_response();
		}
		$credentials                  = array();
		$credentials['user_login']    = sanitize_text_field( $this->payload['username'] );
		$credentials['user_password'] = $this->payload['password'];
		if ( ! empty( $credentials['user_login'] ) ) {
			if ( is_email( $credentials['user_login'] ) ) {
				$user = get_user_by( 'email', $credentials['user_login'] );
				if ( ! empty( $user->user_login ) ) {
					$credentials['user_login'] = $user->user_login;
				}
			} else {
				$user = get_user_by( 'login', $credentials['user_login'] );
			}
			if ( ! empty( $user ) ) {
				if ( POS_Settings::is_pos_user( $user ) ) {
					$user = wp_signon( $credentials, false );
					if ( is_wp_error( $user ) ) {
						$this->add_error( $user->get_error_message() );
						$this->response->set_response( false, '', $credentials );
						return $this->response->get_response();
					} else {
						wp_set_current_user( $user->ID );
						wp_set_auth_cookie( $user->ID, true );
						$response_data = $this->get_logged_user_response( $user );
						$this->response->set_response( true, 'Logged in successfully', $response_data );
						return $this->response->get_response();
					}
				} else {
					$this->add_error( 'You do not have permission to access this link' );
					$this->response->set_response( false );
					return $this->response->get_response();
				}
			} else {
				$this->add_error( 'Invalid login information' );
				$this->response->set_response( false );
				return $this->response->get_response();
			}
		} else {
			$this->add_error( 'Username is required' );
			$this->response->set_response( false );
			return $this->response->get_response();
		}
	}

	/**
	 * The user login is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function get_logged_user() {
		$logged_id = $this->get_current_user_id();
		if ( ! empty( $logged_id ) && is_user_logged_in() ) {
			$user = get_user_by( 'id', $logged_id );
			if ( POS_Settings::is_pos_user( $user ) ) {
				$response_data = $this->get_logged_user_response( $user );
				$this->response->set_response( true, 'Logged in successfully', $response_data );

				return $this->response->get_response();
			} else {
				wp_logout();
				$this->add_error( "You don't have permission to access this link" );
				$this->response->set_response( false );
				return $this->response->get_response();
			}
		} else {
			$this->add_error( 'No logged in user' );
			$this->response->set_response( false, '', null );
			return $this->response->get_response();
		}
	}

	/**
	 * The get logged user response is generated by appsbd
	 *
	 * @param \WP_User $user Its the user object.
	 *
	 * @return mixed|void
	 */
	private function get_logged_user_response( $user ) {
		$response_data                = new \stdClass();
		$response_data->wp_rest_nonce = wp_create_nonce( 'wp_rest' );
		$response_data->username      = $user->user_login;
		$response_data->id            = $user->ID;
		$response_data->name          = $user->first_name . ' ' . $user->last_name;
		$response_data->logged_in     = is_user_logged_in();
		if ( empty( trim( $response_data->name ) ) ) {
			$response_data->name = $user->display_name;
		}
		$response_data->img = get_avatar_url( $user->ID );
		if ( current_user_can( 'pos-discount' ) && ( current_user_can( 'pos-menu' ) || current_user_can( 'waiter-menu' ) || current_user_can( 'cashier-menu' ) ) ) {
			$response_data->max_discounts = Mapbd_Pos_Role::get_discount_percentage( $user );
		}
		$response_data->caps = Mapbd_Pos_Role::set_capabilities_by_role( $user->caps, $user );
		if ( ! empty( $response_data->caps['cashier-menu'] ) && POS_Settings::get_pos_mode() != 'G' ) {
			$response_data->caps['pos-menu'] = true;
		}
		$response_data->outlets      = Mapbd_Pos_Warehouse::get_outlet_details( $user );
		$response_data->is_temp_pass = get_user_meta( $user->ID, 'force_pw_change', true );
			
		/**
		 * Its for logged user
		 *
		 * @since 1.0
		 */
		$response_data = apply_filters( 'apbd-vitepos/filter/logged-user', $response_data, $user );
		/**
		 * Its for logged user
		 *
		 * @since 1.0
		 */
		$response_data = apply_filters( 'apbd-auth/filter/logged-user', $response_data, $user );
		return $response_data;
	}

	/**
	 * The user logout is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function user_logout() {
		wp_logout();
		if ( is_user_logged_in() ) {
			$this->response->set_response( false, 'Logout failed' );
			return $this->response;
		} else {
			$this->response->set_response( true, 'Logout successful' );
			return $this->response;
		}
	}

	/**
	 * The delete user is generated by appsbd.
	 *
	 *  @return \Appsbd\V1\libs\API_Response
	 */
	public function delete_user() {
		if ( ! empty( $this->payload ) ) {
			$id = intval( $this->payload['id'] );
			require_once ABSPATH . 'wp-admin/includes/user.php';
			$user = get_user_by( 'ID', $id );
			if ( ! empty( $user ) ) {
				if ( wp_delete_user( $user->ID ) ) {
					$this->add_info( 'Successfully deleted' );
					$this->response->set_response( true, '' );
					return $this->response;
				}
			}
		}
		$this->add_error( 'Delete failed' );
		$this->response->set_response( false, '' );
		return $this->response;
	}

	/**
	 * The change pass is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function change_pass() {
		if ( ! empty( $this->payload['newPass'] ) && ! empty( $this->payload['currentPass'] ) ) {

			$id = $this->get_current_user_id();
			if ( $id ) {
				$user_data = get_user_by( 'ID', $id );
				if ( ! empty( $user_data->ID ) ) {
					if ( wp_check_password( $this->payload['currentPass'], $user_data->user_pass, $user_data->ID ) ) {
						if ( $this->payload['currentPass'] != $this->payload['newPass'] ) {
							wp_set_password( $this->payload['newPass'], $user_data->ID );
							$credentials                  = array();
							$credentials['user_login']    = $user_data->user_login;
							$credentials['user_password'] = $this->payload['newPass'];
							$user                         = wp_signon( $credentials, false );
							$response_data                = new \stdClass();
							if ( ! is_wp_error( $user ) ) {
								$response_data->wp_rest_nonce = wp_create_nonce( 'wp_rest' );
							} else {
								$response_data->logout = true;
							}
							$this->add_info( 'Password changed successfully.' );
							$this->response->set_response( true, '', $response_data );

							return $this->response;
						} else {
							$this->add_info( 'Password changed successfully.' );
							$this->response->set_response( true, '', null );

							return $this->response;
						}
					} else {
						$this->response->set_response( false, 'Old password not matched,try again.' );

						return $this->response;
					}
				} else {
					$this->response->set_response( false, 'Invalid request.' );

					return $this->response;
				}
			} else {
				$this->add_error( 'Please login again,no user found logged in.' );
				$this->response->set_response( false );

				return $this->response;
			}
		}
	}
	/**
	 * The change pass force is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function change_pass_force() {
		if ( ! empty( $this->payload['newPass'] ) ) {
			if ( $this->payload['user_id'] ) {
				if ( current_user_can( 'change-any-user-pass' ) ) {
					$user_data = get_user_by( 'ID', $this->payload['user_id'] );
					if ( ! empty( $user_data->ID ) ) {
						wp_set_password( $this->payload['newPass'], $user_data->ID );
						if ( metadata_exists( 'user', $user_data->ID, 'force_pw_change' ) ) {
							update_user_meta( $user_data->ID, 'force_pw_change', 'Y' );
						} else {
							add_user_meta( $user_data->ID, 'force_pw_change', 'Y' );
						}
						/**
						 * Its send user temporary
						 *
						 * @since 1.0
						 */
						do_action( 'apbd-vtpos/action/send-temp-password-email', $user_data, $this->payload['newPass'] );

						$this->add_info( 'Password changed successfully.' );
						$this->response->set_response( true );
						return $this->response;
					} else {
						$this->response->set_response( false, 'Invalid request no user found' );

						return $this->response->get_response();
					}
				} else {
					$this->response->set_response( false, 'You do not have permission to do this' );
					return $this->response->get_response();
				}
			} else {
				if ( $this->get_current_user_id() ) {
					$user_data = get_user_by( 'ID', $this->get_current_user_id() );
					wp_set_password( $this->payload['newPass'], $user_data->ID );
					if ( metadata_exists( 'user', $user_data->ID, 'force_pw_change' ) ) {
						update_user_meta( $user_data->ID, 'force_pw_change', 'N' );
					} else {
						add_user_meta( $user_data->ID, 'force_pw_change', 'N' );
					}
					$credentials                  = array();
					$credentials['user_login']    = $user_data->user_login;
					$credentials['user_password'] = $this->payload['newPass'];
					$user                         = wp_signon( $credentials, false );
					$response_data                = new \stdClass();
					if ( ! is_wp_error( $user ) ) {
						$response_data->wp_rest_nonce = wp_create_nonce( 'wp_rest' );
						$response_data->is_temp_pass  = get_user_meta( $user->ID, 'force_pw_change', true );
					} else {
						$response_data->logout = true;
					}
					$this->add_info( 'Password changed successfully.' );
					$this->response->set_response( true, '', $response_data );

					return $this->response;
				} else {
					$this->add_error( 'Invalid Request' );
					$this->response->set_response( false );
				}
			}
		} else {
			$this->add_error( 'Password can not be set as empty' );
			$this->response->set_response( false );
			return $this->response;
		}
	}

	/**
	 * The roles is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function roles() {
		$response_roles = Mapbd_pos_role::get_role_list();
		$response_roles = array_filter(
			$response_roles,
			static function ( $element ) {
				return 'administrator' !== $element->slug;
			}
		);
		$this->response->set_response( true, '', $response_roles );
		return $this->response;
	}
	/**
	 * The roles is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function capabilities() {
		$user                  = wp_get_current_user();
		$response_capabilities = Mapbd_Pos_Role::set_capabilities_by_role( $user->caps, $user );
		$this->response->set_response( true, '', $response_capabilities );
		return $this->response;
	}

	/**
	 * The user list is generated by appsbd
	 *
	 * @return API_Data_Response
	 */
	public function user_list() {
		$page          = $this->get_payload( 'page', 1 );
		$limit         = $this->get_payload( 'limit', 20 );
		$response_user = array();
		$response_data = new API_Data_Response();
		$src_props     = $this->get_payload( 'src_by', array() );
		$sort_by_props = $this->get_payload( 'sort_by', array() );
		$roles         = Mapbd_Pos_Role::get_role_list();
		if ( isset( $roles['administrator'] ) ) {
			unset( $roles['administrator'] );
		}
		$args = array(
			'role__in'     => array_keys( $roles ),
			'role__not_in' => array( 'customer', 'subscriber' ),
			'count_total'  => true,
			'offset'       => $limit,
			'paged'        => $page,
		);
		if ( ! POS_Settings::is_admin_user() && ! current_user_can( 'any-outlet-user-create' ) ) {
			$outlets = get_user_meta( $this->get_current_user_id(), 'outlet_id', true );
			if ( is_array( $outlets ) ) {
				$args['meta_query'][] = array(
					'key'     => 'outlet_id',
					
					
					'value'   => '"(' . implode( '|', $outlets ) . ')"',
					'compare' => 'REGEXP',
				);
			} else {
				$this->add_error( "You don't have permission to view user of this outlet" );
				$response_data->set_total_records( 0 );
				$this->response->set_response( false, '', $response_data );
				return $this->response->get_response();
			}
		}
		POS_Customer::set_search_param( $src_props, $args );
		POS_Customer::set_sort_param( $sort_by_props, $args );
		$user_search = new \WP_User_Query( $args );
		$total_user  = $user_search->get_total();
		$users       = $user_search->get_results();
		foreach ( $users as $user ) {
			$user = POS_Customer::get_user_object( $user );
			$user_role = appsbd_get_text_by_key( $user->role, $roles );
			if ( ! empty( $user_role->name ) ) {
				$user->role = $user_role->name;
			}
			$response_user[] = $user;
		}
		$response_data->limit = $this->payload['limit'];
		$response_data->page  = $this->payload['page'];
		if ( $response_data->set_total_records( $total_user ) ) {
			$response_data->rowdata = $response_user;
		}
		return $response_data;
	}
	/**
	 * The user list is generated by appsbd
	 *
	 * @return API_Data_Response
	 */
	public function waiter_list() {
		$response_user = array();
		$response_data = new API_Data_Response();
		$src_props     = $this->get_payload( 'src_by', array() );
		$sort_by_props = $this->get_payload( 'sort_by', array() );
		$args          = array(
			'role__in' => array( 'vtpos-waiter' ),
		);
		if ( ! POS_Settings::is_admin_user() ) {
			$outlets = get_user_meta( $this->get_current_user_id(), 'outlet_id', true );
			if ( is_array( $outlets ) ) {
				$args['meta_query'][] = array(
					'key'     => 'outlet_id',
					
					
					'value'   => '"(' . implode( '|', $outlets ) . ')"',
					'compare' => 'REGEXP',
				);
			} else {
				$this->add_error( "You don't have permission to view waiter of this outlet" );
				$response_data->set_total_records( 0 );
				$this->response->set_response( false, '', $response_data );
				return $this->response->get_response();
			}
		}
		POS_Customer::set_search_param( $src_props, $args );
		POS_Customer::set_sort_param( $sort_by_props, $args );
		$user_search   = new \WP_User_Query( $args );
		$total_user    = $user_search->get_total();
		$users         = $user_search->get_results();
		$response_user = array();
		foreach ( $users as $user ) {
			$waiter          = new \stdClass();
			$waiter->id      = $user->ID;
			$waiter->name    = $user->first_name ? $user->first_name . ' ' . $user->last_name : $user->user_nicename;
			$waiter->email   = $user->user_email;
			$response_user[] = $waiter;
		}
		if ( $response_data->set_total_records( $total_user ) ) {
			$response_data->rowdata = $response_user;
		}
		return $response_data;
	}

	/**
	 * The getUserObjectById is generated by appsbd
	 *
	 * @param any $id Its Integer.
	 *
	 * @return \stdClass|stdClass|null
	 */
	private function get_user_object_by_id( $id ) {
		 $user = get_user_by( 'id', $id );
		if ( ! empty( $user ) ) {
			return POS_Customer::get_user_object( $user );
		}
		return null;
	}

	/**
	 * The current user is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function api_current_user() {
		$user_id = $this->get_current_user_id() ? $this->get_current_user_id() : 1;
		if ( ! empty( $user_id ) ) {
			$id            = intval( $user_id );
			$user_obj      = $this->get_user_object_by_id( $id );
			$user_obj->img = get_avatar_url( $user_id );
			$this->set_response( true, 'data found', $user_obj );
			return $this->response;
		}
		$this->set_response( false, 'data not found or invalid param' );
		return $this->response;

	}

	/**
	 * The user details is generated by appsbd
	 *
	 * @param any $data Its string.
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function user_details( $data ) {
		if ( ! empty( $data['id'] ) ) {
			$id       = intval( $data['id'] );
			$user_obj = $this->get_user_object_by_id( $id );
			$this->set_response( true, 'data found', $user_obj );
			return $this->response;
		}
		$this->set_response( false, 'data not found or invalid param' );
		return $this->response;

	}

	/**
	 * The outlet panel is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function outlet_panel() {
		$outlet_place                 = new \stdClass();
		$outlet_place->outlet         = $this->get_payload( 'outlet', '' );
		$outlet_place->counter        = $this->get_payload( 'counter', '' );
		$outlet_place->is_new         = $this->get_payload( 'is_new', false );

		$existing_drawer              = Mapbd_Pos_Cash_Drawer::get_by_counter( $outlet_place->outlet, $outlet_place->counter, $this->get_current_user_id() );
		$outlet_place->cd_balance     = ! empty( $existing_drawer->closing_balance ) ? $existing_drawer->closing_balance : 0;
		$outlet_place->cash_drawer_id = ! empty( $existing_drawer->id ) ? $existing_drawer->id : 0;
		$outlet_place->is_submitted   = $this->payload['is_submitted'];
		if ( $outlet_place->is_new ) {
			$outlet_place->cd_balance = $this->get_payload( 'cd_balance' );
			$cash_drawar              = Mapbd_Pos_Cash_Drawer::create_by_counter( $outlet_place->cd_balance, $outlet_place->outlet, $outlet_place->counter, $this->get_current_user_id() );
			if ( ! empty( $cash_drawar->id ) ) {
				$outlet_place->cash_drawer_id = ! empty( $cash_drawar->id ) ? $cash_drawar->id : 0;
			}
		}
		if ( 'Y' == $outlet_place->is_submitted ) {
			$outlet_place->receive_stock_count = Mapbd_Pos_Stock_Transfer::get_receive_count( $outlet_place->outlet );
		}
		if ( POS_Settings::is_single_cash_drawer() && ! empty( $existing_drawer ) ) {
			$outlet_place->drawer_info = Mapbd_Pos_Cash_Drawer::get_drawer_info( $existing_drawer );
		}
		$this->set_response( true, '', $outlet_place );
		return $this->response->get_response();
	}

	/**
	 * The close cash drawer is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function close_cash_drawer() {
		$outlet  = $this->get_outlet_id();
		$counter = $this->get_counter_id();
		if ( empty( $outlet ) || empty( $counter ) ) {
			$this->add_info( 'Request outlet or counter empty' );
			$this->set_response( false );
			return $this->response->get_response();
		}
		$existing_drawer = Mapbd_Pos_Cash_Drawer::get_by_counter( $outlet, $counter, $this->get_current_user_id() );
		if ( ! empty( $existing_drawer ) && $existing_drawer->set_close_drawer() ) {
			$this->add_info( 'Successfully closed' );
			$this->set_response( true );
			return $this->response->get_response();
		}
		$this->add_error( 'Drawer close failed' );
		$this->set_response( false );
		return $this->response->get_response();
	}
	/**
	 * The close cash drawer is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function cash_drawer_list() {

		$drawer_list = Mapbd_Pos_Cash_Drawer::get_cash_drawer_list( $this->get_current_user_id() );
		if ( ! empty( $drawer_list ) ) {
			$this->add_info( 'Successfully closed' );
			$this->set_response( true, '', $drawer_list );
			return $this->response->get_response();
		}
		$this->add_error( 'Drawer list not found' );
		$this->set_response( false );
		return $this->response->get_response();
	}

	/**
	 * The create user is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function create_user() {
		if ( ! empty( $this->payload ) ) {
			$old_cus = get_user_by( 'ID', $this->payload['id'] );
			if ( ! empty( $old_cus ) ) {
				if ( ! current_user_can( 'user-edit' ) ) {
					$this->add_error( 'You do not have permission to do this' );
					$this->response->set_response( false, '' );
					return $this->response->get_response();
				}
				$user_obj = new POS_Customer();
				$user_obj->set_from_array( $this->payload );
				$outlet_id           = $this->get_payload( 'outlet_id' );
				$user_obj->outlet_id = $this->get_payload( 'outlet_id' );
				$user_obj->outlet_id = serialize( $outlet_id );
				if ( $user_obj->is_valid_form( false ) ) {
					if ( $user_obj->update_user() ) {
						$r_user_obj = $this->get_user_object_by_id( $this->payload['id'] );
						$this->response->set_response( true, 'Successfully updated', $r_user_obj );
					} else {
						$this->add_error( 'Not updated' );
						$this->response->set_response( false, '' );
					}
				} else {
					$this->add_error( 'Form is not valid' );
					$this->response->set_response( false );
				}
				return $this->response->get_response();
			} else {
				if ( ! current_user_can( 'user-add' ) ) {
					$this->add_error( 'You do not have permission to do this' );
					$this->response->set_response( false, '' );
					return $this->response->get_response();
				}
				$user_obj = new POS_Customer();
				$user_obj->set_from_array( $this->payload );
				$outlet_id           = $this->get_payload( 'outlet_id' );
				$user_obj->outlet_id = $this->get_payload( 'outlet_id' );
				$user_obj->outlet_id = serialize( $outlet_id );
				$user_obj->added_by( $this->get_current_user_id() );
				if ( $user_obj->is_valid_form( true ) ) {
					if ( $user_obj->save_user() ) {
						$wp_user = get_user_by( 'email', $user_obj->email );
						/**
						 * Its send user temporary
						 *
						 * @since 1.0
						 */
						do_action( 'apbd-vtpos/action/send-temp-password-email', $wp_user, $user_obj->password );

						$this->response->set_response( true, 'Successfully created', $user_obj );
					} else {
						$this->response->set_response( false, appsbd_get_msg_api(), $user_obj );
					}

					return $this->response;
				} else {
					$this->response->set_response( false, appsbd_get_msg_api(), $user_obj );
					return $this->response;
				}
			}
		} else {
			$this->response->set_response( false, 'Error on creation' );
			return $this->response;
		}
	}

}
