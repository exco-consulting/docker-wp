<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

class OsFeatureGroupBookingsHelper {

	static string $step_code = 'booking__group_bookings';


	public static function add_total_attendees_to_booking_form_params( array $params, OsBookingModel $booking ) {
		if ( ! empty( $booking->total_attendees ) ) {
			$params['total_attendees'] = $booking->total_attendees;
		}

		return $params;
	}

	public static function add_step_show_next_btn_rules( $rules, $step_code ): array {
		$rules[ self::$step_code ] = true;

		return $rules;
	}

	public static function should_step_be_skipped( bool $skip, string $step_code, OsCartModel $cart, OsCartItemModel $cart_item, OsBookingModel $booking ): bool {
		if ( $step_code == self::$step_code ) {
			if ( $booking->is_part_of_bundle() ) {
				// bundle bookings have attendee quantity, no need to ask customer for it
				$skip = true;
			} else {
				if ( $cart_item->is_booking() ) {
					$booking = $cart_item->build_original_object_from_item_data();
					if ( empty( $booking->service_id ) || ! $booking->service->should_show_capacity_selector() ) {
						$skip = true;
					}
				} else {
					$skip = true;
				}
			}
		}

		return $skip;
	}

	public static function load_step_group_bookings( $step_code, $format = 'json' ) {
		if ( $step_code == self::$step_code ) {

			$group_bookings_controller                            = new OsGroupBookingsController();
			$group_bookings_controller->vars['booking']           = OsStepsHelper::$booking_object;
			$group_bookings_controller->vars['current_step_code'] = $step_code;
			$group_bookings_controller->set_layout( 'none' );
			$group_bookings_controller->set_return_format( $format );
			$group_bookings_controller->format_render( '_step_booking__group_bookings', [], [
				'step_code'        => $step_code,
				'show_next_btn'    => OsStepsHelper::can_step_show_next_btn( $step_code ),
				'show_prev_btn'    => OsStepsHelper::can_step_show_prev_btn( $step_code ),
				'is_first_step'    => OsStepsHelper::is_first_step( $step_code ),
				'is_last_step'     => OsStepsHelper::is_last_step( $step_code ),
				'is_pre_last_step' => OsStepsHelper::is_pre_last_step( $step_code )
			] );
		}
	}

	public static function add_label_for_step( array $labels ): array {
		$labels[ self::$step_code ] = __( 'Total Attendees', 'latepoint-pro-features' );

		return $labels;
	}


	public static function remove_group_bookings_step_if_preselected( array $presets, OsCartItemModel $active_cart_item, OsBookingModel $booking, OsCartModel $cart ) {
		if ( ! empty( $presets['selected_total_attendees'] ) || $booking->is_part_of_bundle() ) {
			OsStepsHelper::remove_step_by_name( 'booking__group_bookings' );
		}
	}


	public static function add_settings_for_step( array $settings ): array {
		$settings[ self::$step_code ] = [
			'side_panel_heading'     => 'Total Attendees',
			'side_panel_description' => 'Please select how many people are coming',
			'main_panel_heading'     => 'Total Attendees'
		];

		return $settings;
	}

	public static function add_step_for_group_bookings( array $steps ): array {
		$steps[ self::$step_code ] = [ 'after' => 'services', 'before' => 'datepicker' ];

		return $steps;
	}

	public static function add_data_to_webhook( $vars, $booking ) {
		if ( $booking->total_attendees ) {
			$vars['total_attendees'] = $booking->total_attendees;
		}

		return $vars;
	}

	public static function adjust_deposit_amount_for_service( $amount, OsBookingModel $booking ) {
		if ( ( $booking->total_attendees > 1 ) && ! OsUtilHelper::is_on( $booking->service->get_meta_by_key( 'dont_multiply_deposit_amount_by_attendees', 'off' ) ) ) {
			$amount = $amount * $booking->total_attendees;
		}

		return $amount;
	}


	public static function adjust_full_amount_for_service( $amount, OsBookingModel $booking ) {
		if ( ( $booking->total_attendees > 1 ) && ! OsUtilHelper::is_on( $booking->service->get_meta_by_key( 'dont_multiply_charge_amount_by_attendees', 'off' ) ) ) {
			$amount = $amount * $booking->total_attendees;
		}

		return $amount;
	}

	public static function adjust_full_amount_for_service_extra( $amount, OsBookingModel $booking, OsServiceExtraModel $service_extra ) {
		if ( $booking->total_attendees > 1 && OsUtilHelper::is_on( $service_extra->multiplied_by_attendees ) ) {
			$amount = $amount * $booking->total_attendees;
		}

		return $amount;
	}

	public static function add_capacity_info_to_service_tile( $service ) {
		if ( $service->capacity_min && $service->capacity_max ) {
			?>
            <div class="service-info-row">
                <div class="label"><?php _e( 'Capacity:', 'latepoint-pro-features' ); ?></div>
                <div class="value">
                    <strong><?php echo ( $service->capacity_min == $service->capacity_max ) ? $service->capacity_max : $service->capacity_min . ' - ' . $service->capacity_max; ?></strong> <?php _e( 'person', 'latepoint-pro-features' ); ?>
                </div>
            </div>
			<?php
		}
	}


	public static function add_service_extra_settings( $service_extra ) {
		echo OsFormHelper::checkbox_field( 'service_extra[multiplied_by_attendees]', __( 'Multiply cost of this service extra by number of attendees', 'latepoint-pro-features' ), 'on', OsUtilHelper::is_on( $service_extra->multiplied_by_attendees ) );
	}

	public static function replace_booking_vars_for_group_bookings( $text, $booking ) {
		$needles = [ '{{total_attendees}}' ];

		$replacements = [ $booking->total_attendees ];
		$text         = str_replace( $needles, $replacements, $text );

		return $text;
	}

	public static function save_service_info( $service, $is_new_record, $service_params ) {
		if ( $service ) {
			$value = isset( $service_params['fixed_total_attendees'] ) ? $service_params['fixed_total_attendees'] : 'off';
			$service->save_meta_by_key( 'fixed_total_attendees', $value );

			$value = isset( $service_params['block_timeslot_when_minimum_capacity_met'] ) ? $service_params['block_timeslot_when_minimum_capacity_met'] : 'off';
			$service->save_meta_by_key( 'block_timeslot_when_minimum_capacity_met', $value );

			$value = isset( $service_params['dont_multiply_charge_amount_by_attendees'] ) ? $service_params['dont_multiply_charge_amount_by_attendees'] : 'off';
			$service->save_meta_by_key( 'dont_multiply_charge_amount_by_attendees', $value );

			$value = isset( $service_params['dont_multiply_deposit_amount_by_attendees'] ) ? $service_params['dont_multiply_deposit_amount_by_attendees'] : 'off';
			$service->save_meta_by_key( 'dont_multiply_deposit_amount_by_attendees', $value );
		}
	}

	public static function add_group_bookings_vars() {
		echo '<li><span class="var-label">' . __( 'Total Attendees', 'latepoint-pro-features' ) . '</span> <span class="var-code os-click-to-copy">{{total_attendees}}</span></li>';
	}


	public static function add_capacity_to_service_headings( $service_headings, $booking ) {
		if ( $booking->service_id && $booking->service->should_show_capacity_selector() ) {
			$service_headings[] = sprintf( _n( '%s person', '%s people', $booking->total_attendees, 'latepoint-pro-features' ), $booking->total_attendees ) . '</strong></li>';
		}

		return $service_headings;
	}

	public static function add_attendees_to_service_row_item( array $service_row_item, OsBookingModel $booking ) {
		if ( !empty($service_row_item['items']) && ( $booking->total_attendees > 1 ) && OsUtilHelper::is_off( $booking->service->get_meta_by_key( 'dont_multiply_charge_amount_by_attendees', 'off' ) ) ) {
			$service_price            = OsMoneyHelper::format_price( $booking->service->get_full_amount_for_duration( $booking->duration ), true, false );
            foreach($service_row_item['items'] as &$service_item){
                $service_item['note'] = '(' . $booking->total_attendees . ' x ' . $service_price . ')';
            }
		}

		return $service_row_item;
	}

	public static function output_total_attendees_on_quick_form( $booking, $order_item_id ) {
		$service          = $booking->service;
		$capacity_min     = empty( $service->capacity_min ) ? 1 : $service->capacity_min;
		$capacity_max     = empty( $service->capacity_max ) ? 1 : $service->capacity_max;
		$capacity_options = [];
		for ( $i = $capacity_min; $i <= $capacity_max; $i ++ ) {
			$capacity_options[] = $i;
		}
		$hide = ( $booking->service_id && $booking->service->capacity_max > 1 ) ? '' : 'display: none;';
		echo '<div class="booking-total-attendees-selector-w" style="' . $hide . '">';
		echo '<div class="os-row">';
		echo '<div class="os-col-6">';
		echo OsFormHelper::select_field( 'order_items[' . $order_item_id . '][bookings][' . $booking->get_form_id() . '][total_attendees]', __( 'Total Attendees', 'latepoint-pro-features' ), $capacity_options, $booking->total_attendees, [ 'class' => 'os-affects-price' ] );
		echo '</div>';
		echo '<div class="os-col-6">';
		echo '<div class="capacity-info"><span>' . __( 'Max Capacity:', 'latepoint-pro-features' ) . '</span><strong>' . $capacity_max . '</strong></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}


	public static function output_capacity_on_service_form( $service ) {
		?>
        <div class="white-box">
            <div class="white-box-header">
                <div class="os-form-sub-header"><h3><?php _e( 'Group Bookings', 'latepoint-pro-features' ); ?></h3>
                </div>
            </div>
            <div class="white-box-content no-padding">
                <div class="sub-section-row">
                    <div class="sub-section-label">
                        <h3><?php _e( 'Capacity', 'latepoint-pro-features' ) ?></h3>
                    </div>
                    <div class="sub-section-content">
                        <div class="os-row">
                            <div class="os-col-lg-2">
								<?php echo OsFormHelper::text_field( 'service[capacity_min]', __( 'Minimum', 'latepoint-pro-features' ), $service->capacity_min, [ 'theme' => 'bordered' ] ); ?>
                            </div>
                            <div class="os-col-lg-2">
								<?php echo OsFormHelper::text_field( 'service[capacity_max]', __( 'Maximum', 'latepoint-pro-features' ), $service->capacity_max, [ 'theme' => 'bordered' ] ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sub-section-row">
                    <div class="sub-section-label">
                        <h3><?php _e( 'Pricing', 'latepoint-pro-features' ) ?></h3>
                    </div>
                    <div class="sub-section-content">
						<?php echo OsFormHelper::toggler_field( 'service[dont_multiply_charge_amount_by_attendees]', __( 'Do not multiply charge amount by the number of attendees', 'latepoint-pro-features' ), OsUtilHelper::is_on( $service->get_meta_by_key( 'dont_multiply_charge_amount_by_attendees', 'off' ) ) ); ?>
						<?php echo OsFormHelper::toggler_field( 'service[dont_multiply_deposit_amount_by_attendees]', __( 'Do not multiply deposit amount by the number of attendees', 'latepoint-pro-features' ), OsUtilHelper::is_on( $service->get_meta_by_key( 'dont_multiply_deposit_amount_by_attendees', 'off' ) ) ); ?>
                    </div>
                </div>
                <div class="sub-section-row">
                    <div class="sub-section-label">
                        <h3><?php _e( 'Other', 'latepoint-pro-features' ) ?></h3>
                    </div>
                    <div class="sub-section-content">
						<?php echo OsFormHelper::toggler_field( 'service[fixed_total_attendees]', __( 'Do not ask customers to select number of attendees', 'latepoint-pro-features' ), OsUtilHelper::is_on( $service->get_meta_by_key( 'fixed_total_attendees', 'off' ) ) ); ?>
						<?php echo OsFormHelper::toggler_field( 'service[block_timeslot_when_minimum_capacity_met]', __( 'Block timeslot if minimum capacity is reached', 'latepoint-pro-features' ), OsUtilHelper::is_on( $service->get_meta_by_key( 'block_timeslot_when_minimum_capacity_met', 'off' ) ) ); ?>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}


	public static function add_columns_to_bookings_table( $columns ) {
		$columns['booking']['total_attendees'] = __( 'Total Attendees', 'latepoint-pro-features' );

		return $columns;
	}


	public static function add_columns_to_booking_row_for_csv( $booking_row, $booking, $params = [] ) {
		$booking_row[] = $booking->total_attendees;

		return $booking_row;
	}


	public static function add_columns_to_bookings_data_for_csv( $bookings_data, $params = [] ) {
		$bookings_data[0][] = __( 'Total Attendees', 'latepoint-pro-features' );

		return $bookings_data;
	}

}