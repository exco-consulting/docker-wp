<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

class OsFeatureCouponsHelper {


	public static function add_coupons_to_cart_calculations( OsCartModel $cart ): void {
		if(empty($cart->get_coupon_code())) return;
		$cart_total_coupon_discount = 0;
		foreach ( $cart->get_items() as &$item ) {
			$item->coupon_discount = 0;
			switch ( $item->variant ) {
				case LATEPOINT_ITEM_VARIANT_BOOKING:
					$booking        = $item->build_original_object_from_item_data();
					$booking_amount = OsBookingHelper::calculate_full_amount_for_booking( $booking );

					if ( ! is_wp_error( OsCouponsHelper::is_coupon_code_valid( $cart->get_coupon_code(), $cart ) ) ) {
						$coupon = OsCouponsHelper::get_coupon_by_code( $cart->get_coupon_code() );
						if ( $coupon->discount_type == 'percent' ) {
							if ( $coupon->discount_value ) {
								$item->coupon_discount = round( $booking_amount * ( $coupon->discount_value / 100 ), 4 );
							}
						} elseif ( $coupon->discount_type == 'fixed' ) {
							if ( $coupon->discount_value ) {
								$item->coupon_discount = $coupon->discount_value;
							}
						}
						if($item->coupon_discount > 0) $item->total = $item->get_subtotal() - $item->coupon_discount;
					}
					break;
				case LATEPOINT_ITEM_VARIANT_BUNDLE:
					break;
			}
			$cart_total_coupon_discount+= $item->coupon_discount;
		}
		$cart->coupon_discount = $cart_total_coupon_discount;
		$cart->total = $cart->get_total() - $cart->coupon_discount;
	}

	public static function add_coupon_name_to_roles( array $action_names, $action_code ): array {
		$action_names['coupon'] = __( 'Coupons', 'latepoint-pro-features' );

		return $action_names;
	}

	public static function add_capabilities_for_controllers( $capabilities ) {
		$capabilities['OsCouponsController'] = [
			'default'    => [ 'coupon__edit' ],
			'per_action' => [
				'destroy' => [ 'coupon__delete' ],
				'index'   => [ 'coupon__view' ]
			]
		];

		return $capabilities;
	}

	public static function add_coupon_actions_to_roles( array $actions ): array {
		$actions[] = 'coupon__view';
		$actions[] = 'coupon__delete';
		$actions[] = 'coupon__create';
		$actions[] = 'coupon__edit';

		return $actions;
	}

	public static function add_coupon_data_vars_to_booking( array $data, OsModel $model ): array {
		if ( is_a( $model, 'OsBookingModel' ) ) {
			if ( ! empty( $model->coupon_code ) && ! empty( $model->coupon_discount ) ) {
				$data['coupon'] = [
					'coupon_code'     => $model->coupon_code,
					'coupon_discount' => $model->coupon_discount * 1
				];
			}
		}

		return $data;
	}

	// reloads discount to show it on price breakdown
	public static function reload_coupon_discount( OsOrderModel $order ) {
		OsCouponsHelper::calculate_coupon_discount( $order );

		return $order;
	}


	public static function add_coupon_form_to_verify_step( $step_code ) {
		if ( $step_code != 'verify' ) {
			return;
		}
		$cart           = OsCartsHelper::get_or_create_cart();
		if($cart->get_subtotal() == 0) return;
		$coupon_code    = $cart->get_coupon_code();
		$coupon_applied = ! empty( $coupon_code ) ? 'coupon-is-applied' : '';
		$html           = '<div class="coupon-code-wrapper-on-verify ' . $coupon_applied . '">
							<div class="coupon-code-trigger-on-verify-w"><a href="#">' . __( 'Have a coupon code?', 'latepoint-pro-features' ) . '</a></div>
              <div class="coupon-code-input-w">
                <input type="text" value="' . $coupon_code . '" name="coupon_code" class="coupon-code-input" data-route="' . OsRouterHelper::build_route_name( 'coupons', 'apply' ) . '" placeholder="' . __( 'Enter Code...', 'latepoint-pro-features' ) . '">
                <div class="coupon-code-input-submit">' . __( 'Apply', 'latepoint-pro-features' ) . '</div>
                <div class="coupon-code-input-cancel">' . __( 'Cancel', 'latepoint-pro-features' ) . '</div>
              </div>
              <div class="applied-coupon-code-wrapper">
              <div class="coupon-code-label">' . __( 'Applied Coupon:', 'latepoint-pro-features' ) . '</div>
              <div class="applied-coupon-code">
                <span class="coupon-code-self">' . $coupon_code . '</span>
                <span class="coupon-code-clear"><i class="latepoint-icon latepoint-icon-common-01"></i></span>
              </div>
              </div>
            </div>';
		echo $html;
	}


	public static function add_step_show_next_btn_rules( $rules, $step_code ) {
		// if not accepting payments - the payment step can be skipped because it only shows coupon code input
		if ( ! OsPaymentsHelper::is_accepting_payments() ) {
			$rules['payment'] = true;
		}

		return $rules;
	}

	public static function show_coupon_code_on_order_quick_edit_form( OsOrderModel $order ) {
		$show_coupon = (bool) $order->coupon_code;
		if ( $show_coupon ) {
			$cart          = $order->view_as_cart();
			$coupon_status = OsCouponsHelper::is_coupon_code_valid( $order->coupon_code, $cart, $order->customer_id );
			if ( is_wp_error( $coupon_status ) ) {
				echo '<div class="os-form-message-w status-error">' . $coupon_status->get_error_message() . '</div>';
			}
		}
		echo '<div class="coupon-option-wrapper">';
		echo OsFormHelper::toggler_field( 'apply_coupon_toggler', __( 'Use Coupon', 'latepoint-pro-features' ), $show_coupon, 'optionalCouponCode' );
		echo '<div id="optionalCouponCode" style="display: ' . ( $show_coupon ? 'block' : 'none' ) . '">';
		echo '<div class="coupon-input-wrapper">';
		echo OsFormHelper::text_field( 'order[coupon_code]', '', $order->coupon_code, [
			'theme'       => 'bordered',
			'placeholder' => __( 'Coupon Code', 'latepoint-pro-features' )
		] );
		echo OsFormHelper::hidden_field( 'order[coupon_discount]', $order->coupon_discount );
		echo '<a href="#" class="apply-coupon-button latepoint-btn latepoint-btn-white latepoint-btn-sm">' . __( 'apply', 'latepoint-pro-features' ) . '</a>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	public static function add_coupon_info_to_cart_price_breakdown_rows( array $rows, OsCartModel $cart, array $rows_to_hide ): array {
		$coupon_code = $cart->get_coupon_code();
		if ( empty( $coupon_code ) ) {
			return $rows;
		}

		$discount = $cart->get_coupon_discount();

		$rows['after_subtotal']['discounts']['items'][] = [
			'label'     => __( 'Coupon', 'latepoint-pro-features' ),
			'raw_value' => - OsMoneyHelper::pad_to_db_format( $discount ),
			'value'     => '-' . OsMoneyHelper::format_price( $discount, true, false ),
			'badge'     => $coupon_code,
			'type'      => 'credit'
		];

		return $rows;
	}


	public static function add_coupon_info_to_order_price_breakdown_rows( array $rows, OsOrderModel $order, array $rows_to_hide, bool $force_regenerate ): array {
		if ( empty( $order->coupon_code ) ) {
			return $rows;
		}
		$cart              = $order->view_as_cart();
		$cart->coupon_code = $order->coupon_code;

		$discount              = $cart->coupon_discount;

		$rows['after_subtotal']['discounts']['items'][] = [
			'label'     => __( 'Coupon', 'latepoint-pro-features' ),
			'raw_value' => - OsMoneyHelper::pad_to_db_format( $discount ),
			'value'     => '-' . OsMoneyHelper::format_price( $discount, true, false ),
			'badge'     => $order->coupon_code,
			'type'      => 'credit'
		];

		return $rows;
	}


	public static function add_coupon_code_to_booking_row_for_csv( $booking_row, $booking, $params = [] ) {
		$booking_row[] = $booking->coupon_code;
		$booking_row[] = $booking->coupon_discount;

		return $booking_row;
	}


	public static function add_coupon_code_to_bookings_data_for_csv( $bookings_data, $params = [] ) {
		$bookings_data[0][] = __( 'Coupon Code', 'latepoint-pro-features' );
		$bookings_data[0][] = __( 'Coupon Discount', 'latepoint-pro-features' );

		return $bookings_data;
	}


	public static function add_coupon_columns_to_bookings_table( $columns ) {
		$columns['booking']['coupon_code']     = __( 'Coupon Code', 'latepoint-pro-features' );
		$columns['booking']['coupon_discount'] = __( 'Coupon Discount', 'latepoint-pro-features' );

		return $columns;
	}

}