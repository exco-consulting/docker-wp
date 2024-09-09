<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsCouponsController' ) ) :


	class OsCouponsController extends OsController {

		function __construct() {
			parent::__construct();

			$this->action_access['public'] = array_merge( $this->action_access['public'], [ 'apply' ] );
			$this->views_folder            = plugin_dir_path( __FILE__ ) . '../views/coupons/';
			$this->vars['breadcrumbs'][]   = array(
				'label' => __( 'Coupons', 'latepoint-pro-features' ),
				'link'  => OsRouterHelper::build_link( OsRouterHelper::build_route_name( 'coupons', 'index' ) )
			);

			$this->vars['page_header']     = OsMenuHelper::get_menu_items_by_id( 'settings' );
			$this->vars['pre_page_header'] = OsMenuHelper::get_label_by_id( 'settings' );
		}


		public function index() {
			$coupons = new OsCouponModel();

			$this->vars['coupons'] = $coupons->order_by( 'status asc, code asc' )->get_results_as_models();

			$this->format_render( __FUNCTION__ );
		}


		public function apply() {
			OsStepsHelper::set_required_objects($this->params);
			$coupon_code = strtoupper( trim( $this->params['coupon_code'] ) );
			// clear coupon code if empty is passed
			if ( empty( $coupon_code ) ) {
				OsStepsHelper::$cart_object->clear_coupon_code();
				$status        = LATEPOINT_STATUS_SUCCESS;
				$response_html = __( 'Coupon Code was removed from your cart', 'latepoint-pro-features' );
			} else {
				$is_valid = OsCouponsHelper::is_coupon_code_valid( $coupon_code, OsStepsHelper::$cart_object, OsAuthHelper::$logged_in_customer_id);
				if ( ! is_wp_error( $is_valid ) ) {
					$is_valid = true;
					OsStepsHelper::$cart_object->set_coupon_code($coupon_code);
				}
				if ( is_wp_error( $is_valid ) ) {
					$status        = LATEPOINT_STATUS_ERROR;
					$response_html = $is_valid->get_error_message();
				} else {
					$status        = LATEPOINT_STATUS_SUCCESS;
					$response_html = __( 'Coupon Code was applied to your cart', 'latepoint-pro-features' );
				}
			}
			if ( $this->get_return_format() == 'json' ) {
				$this->send_json( array( 'status' => $status, 'message' => $response_html ) );
			}
		}


		public function destroy() {
			if ( filter_var( $this->params['id'], FILTER_VALIDATE_INT ) ) {
				$coupon = new OsCouponModel( $this->params['id'] );
				if ( $coupon->delete() ) {
					$status        = LATEPOINT_STATUS_SUCCESS;
					$response_html = __( 'Coupon Removed', 'latepoint-pro-features' );
				} else {
					$status        = LATEPOINT_STATUS_ERROR;
					$response_html = __( 'Error Removing Coupon', 'latepoint-pro-features' );
				}
			} else {
				$status        = LATEPOINT_STATUS_ERROR;
				$response_html = __( 'Error Removing Coupon', 'latepoint-pro-features' );
			}
			if ( $this->get_return_format() == 'json' ) {
				$this->send_json( array( 'status' => $status, 'message' => $response_html ) );
			}
		}

		public function new_form() {
			$this->vars['coupon'] = new OsCouponModel();
			$this->set_layout( 'none' );
			$this->format_render( __FUNCTION__ );
		}

		/*
		  Create coupon
		*/

		public function create() {
			$this->update();
		}


		/*
		  Update coupon
		*/

		public function update() {
			$is_new_record                   = ( isset( $this->params['coupon']['id'] ) && $this->params['coupon']['id'] ) ? false : true;
			$coupon                          = new OsCouponModel();
			$this->params['coupon']['rules'] = json_encode( $this->params['coupon']['rules'] );
			$coupon->set_data( $this->params['coupon'] );
			$extra_response_vars = array();

			if ( $coupon->save() ) {
				if ( $is_new_record ) {
					$response_html = __( 'Coupon Created. ID:', 'latepoint-pro-features' ) . $coupon->id;
					OsActivitiesHelper::create_activity( array(
						'code'      => 'coupon_create',
						'coupon_id' => $coupon->id
					) );
				} else {
					$response_html = __( 'Coupon Updated. ID:', 'latepoint-pro-features' ) . $coupon->id;
					OsActivitiesHelper::create_activity( array(
						'code'      => 'coupon_update',
						'coupon_id' => $coupon->id
					) );
				}
				$status                           = LATEPOINT_STATUS_SUCCESS;
				$extra_response_vars['record_id'] = $coupon->id;
			} else {
				$response_html = $coupon->get_error_messages();
				$status        = LATEPOINT_STATUS_ERROR;
			}
			if ( $this->get_return_format() == 'json' ) {
				$this->send_json( array( 'status' => $status, 'message' => $response_html ) + $extra_response_vars );
			}
		}

	}
endif;