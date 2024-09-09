<?php

class OsFeatureTimezoneHelper {


	public static function nice_timezone_name($timezone_name){
		if(empty($timezone_name)) return 'n/a';
		$timezone_split = explode('/', $timezone_name);
		if(count($timezone_split)){
			return str_replace('_', ' ', end($timezone_split));
		}else{
			return $timezone_name;
		}
	}

	public static function change_booking_start_datetime_to_timezone($booking_start_datetime, $booking, $timezone_name = false){
		if(OsSettingsHelper::is_on('steps_show_timezone_info')){
			if($timezone_name){
		    $booking_start_datetime = $booking->format_start_date_and_time(OsSettingsHelper::get_readable_datetime_format(), false, new DateTimeZone($timezone_name));
				$booking_start_datetime.= ' <span class="os-timezone-info">('.self::nice_timezone_name($timezone_name).')</span>';
			}else{
				// no timezone passed - get it from customer meta
				$booking_start_datetime = $booking->get_nice_start_datetime_for_customer();
				$booking_start_datetime.= ' <span class="os-timezone-info">('.self::nice_timezone_name($booking->customer->get_selected_timezone_name()).')</span>';
			}
		}
		return $booking_start_datetime;
	}

	public static function add_timezone_selector_to_customer_dashboard($customer){
		if(OsSettingsHelper::is_on('steps_show_timezone_selector')){
			echo '<div class="latepoint-customer-timezone-selector-w" data-route-name="'.OsRouterHelper::build_route_name('timezone_selector', 'change_timezone').'">';
			echo OsFormHelper::select_field('latepoint_timezone_selector', __('My Timezone:', 'latepoint-pro-features'), OsTimeHelper::timezones_options_list($customer->get_selected_timezone_name()), $customer->get_selected_timezone_name());
			echo '</div>';
		}
	}

	public static function add_timezone_information_to_datepicker($booking, $timezone_name = false){
		if (OsSettingsHelper::is_on('steps_show_timezone_info')) {
			$timezone_name = $timezone_name ? $timezone_name : OsTimeHelper::get_timezone_name_from_session();
			echo '<div class="th-timezone"><strong>' . __('Timezone:', 'latepoint-pro-features') . '</strong> ' . self::nice_timezone_name($timezone_name) . '</div>';
		}
	}

  public static function add_timezone_vars_for_booking(){
    echo '<li><span class="var-label">'.__('Start Date (in customer timezone):', 'latepoint-pro-features').'</span> <span class="var-code os-click-to-copy">{{start_date_customer_timezone}}</span></li>';
    echo '<li><span class="var-label">'.__('Start Time (in customer timezone):', 'latepoint-pro-features').'</span> <span class="var-code os-click-to-copy">{{start_time_customer_timezone}}</span></li>';
    echo '<li><span class="var-label">'.__('End Time (in customer timezone)', 'latepoint-pro-features').'</span> <span class="var-code os-click-to-copy">{{end_time_customer_timezone}}</span></li>';
  }


	public static function apply_timeshift_to_resources_grouped_by_day($daily_resources, $booking_request, $date_from, $date_to, $settings){
		if($settings['timeshift_minutes'] != 0){
			$resources_to_be_moved = [];
			for($day_date = clone $date_from; $day_date->format('Y-m-d') <= $date_to->format('Y-m-d'); $day_date->modify('+1 day')){
				$next_day = clone $day_date;
				$next_day->modify('+1 day');
				$prev_day = clone $day_date;
				$prev_day->modify('-1 day');
				$resources_to_be_moved_to_next_day = [];
				$total_resources = count($daily_resources[$day_date->format('Y-m-d')]);
				for($i = 0; $i<$total_resources;$i++){
					$temp_resource_for_next_day_move = new \LatePoint\Misc\BookingResource();
					$temp_resource_for_next_day_move->agent_id = $daily_resources[$day_date->format('Y-m-d')][$i]->agent_id;
					$temp_resource_for_next_day_move->service_id = $daily_resources[$day_date->format('Y-m-d')][$i]->service_id;
					$temp_resource_for_next_day_move->location_id = $daily_resources[$day_date->format('Y-m-d')][$i]->location_id;

					// loop and apply timeshift to WORK TIME PERIODS
					// -----------
					$total_work_time_periods = count($daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods);
					for($j = 0; $j<$total_work_time_periods; $j++){
						$new_work_period_for_prev_day = false;
						$new_work_period_for_next_day = false;
						$daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->start_time+= $settings['timeshift_minutes'];
						$daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->end_time+= $settings['timeshift_minutes'];
						if($daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->start_time < 0 && $daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->end_time < 0) {
							// both start and end of work period should be moved to a previous day
							$new_work_period_for_prev_day = new \LatePoint\Misc\TimePeriod();
							$new_work_period_for_prev_day->start_time = 24*60 + $daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->start_time;
							$new_work_period_for_prev_day->end_time = 24*60 + $daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->end_time;
							// remove work periods from current day, because it was fully moved to previous day
							unset($daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]);
						}elseif($daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->start_time < 0){
							// only start time is leaking to previous day, create new period with a cutoff
							$new_work_period_for_prev_day = new \LatePoint\Misc\TimePeriod();
							$new_work_period_for_prev_day->start_time = 24*60 + $daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->start_time;
							$new_work_period_for_prev_day->end_time = 24*60;
							$daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->start_time = 0;
						}

						if($daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->start_time >= 24*60 && $daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->end_time >= 24*60) {
							// both start and end of work period should be moved to a next day
							$new_work_period_for_next_day = new \LatePoint\Misc\TimePeriod();
							$new_work_period_for_next_day->start_time = $daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->start_time - 24*60;
							$new_work_period_for_next_day->end_time = $daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->end_time - 24*60;
							// remove work periods from current day, because it was fully moved to next day
							unset($daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]);
						}elseif($daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->end_time >= 24*60){
							// only end time is leaking to next day, create new period with a cutoff
							$new_work_period_for_next_day = new \LatePoint\Misc\TimePeriod();
							$new_work_period_for_next_day->start_time = 0;
							$new_work_period_for_next_day->end_time = $daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->end_time - 24*60;
							$daily_resources[$day_date->format('Y-m-d')][$i]->work_time_periods[$j]->end_time = 24*60;
						}

						if($new_work_period_for_next_day){
							$temp_resource_for_next_day_move->work_time_periods[] = $new_work_period_for_next_day;
						}
						if($new_work_period_for_prev_day){
							$updated_day = clone $day_date;
							$updated_day->modify('-1 day');
							if(isset($daily_resources[$updated_day->format('Y-m-d')])){
								for($p = 0; $p<count($daily_resources[$updated_day->format('Y-m-d')]);$p++) {
									if($daily_resources[$updated_day->format('Y-m-d')][$p]->agent_id == $daily_resources[$day_date->format('Y-m-d')][$i]->agent_id
										&& $daily_resources[$updated_day->format('Y-m-d')][$p]->service_id == $daily_resources[$day_date->format('Y-m-d')][$i]->service_id
										&& $daily_resources[$updated_day->format('Y-m-d')][$p]->location_id == $daily_resources[$day_date->format('Y-m-d')][$i]->location_id){
										// same resource found - add those work periods to this resource
											// add to the end of prev day
											$daily_resources[$updated_day->format('Y-m-d')][$p]->work_time_periods[] = $new_work_period_for_prev_day;
									}
								}
							}
						}
					}

					// loop and apply timeshift to booking SLOTS
					// ----------
					$total_slots = count($daily_resources[$day_date->format('Y-m-d')][$i]->slots);
					for($j = 0; $j<$total_slots; $j++){
						$new_slot_for_prev_day = false;
						$new_slot_for_next_day = false;
						$daily_resources[$day_date->format('Y-m-d')][$i]->slots[$j]->start_time+= $settings['timeshift_minutes'];
						if($daily_resources[$day_date->format('Y-m-d')][$i]->slots[$j]->start_time < 0){
							$new_slot_for_prev_day = clone $daily_resources[$day_date->format('Y-m-d')][$i]->slots[$j];
							$new_slot_for_prev_day->start_time = 24*60 + $new_slot_for_prev_day->start_time;
							$new_slot_for_prev_day->start_date = $prev_day->format('Y-m-d');
							unset($daily_resources[$day_date->format('Y-m-d')][$i]->slots[$j]);
						}elseif($daily_resources[$day_date->format('Y-m-d')][$i]->slots[$j]->start_time >= 24*60){
							$new_slot_for_next_day = clone $daily_resources[$day_date->format('Y-m-d')][$i]->slots[$j];
							$new_slot_for_next_day->start_time = $new_slot_for_next_day->start_time - 24*60;
							$new_slot_for_next_day->start_date = $next_day->format('Y-m-d');
							unset($daily_resources[$day_date->format('Y-m-d')][$i]->slots[$j]);
						}
						if($new_slot_for_next_day){
							$temp_resource_for_next_day_move->slots[] = $new_slot_for_next_day;
						}
						if($new_slot_for_prev_day){
							$updated_day = clone $day_date;
							$updated_day->modify('-1 day');
							if(isset($daily_resources[$updated_day->format('Y-m-d')])){
								for($p = 0; $p<count($daily_resources[$updated_day->format('Y-m-d')]);$p++) {
									if($daily_resources[$updated_day->format('Y-m-d')][$p]->agent_id == $daily_resources[$day_date->format('Y-m-d')][$i]->agent_id
										&& $daily_resources[$updated_day->format('Y-m-d')][$p]->service_id == $daily_resources[$day_date->format('Y-m-d')][$i]->service_id
										&& $daily_resources[$updated_day->format('Y-m-d')][$p]->location_id == $daily_resources[$day_date->format('Y-m-d')][$i]->location_id){
										// same resource found - add those work periods to this resource
										$daily_resources[$updated_day->format('Y-m-d')][$p]->slots[] = $new_slot_for_prev_day;
									}
								}
							}
						}
					}


					// loop and apply timeshift to WORK MINUTES
					// -----------
					$total_work_minutes = count($daily_resources[$day_date->format('Y-m-d')][$i]->work_minutes);
					for($j = 0; $j<$total_work_minutes; $j++){
						$new_work_minute_for_prev_day = false;
						$new_work_minute_for_next_day = false;
						$daily_resources[$day_date->format('Y-m-d')][$i]->work_minutes[$j]+= $settings['timeshift_minutes'];
						if($daily_resources[$day_date->format('Y-m-d')][$i]->work_minutes[$j] < 0){
							$new_work_minute_for_prev_day = 24*60 + $daily_resources[$day_date->format('Y-m-d')][$i]->work_minutes[$j];
							unset($daily_resources[$day_date->format('Y-m-d')][$i]->work_minutes[$j]);
						}elseif($daily_resources[$day_date->format('Y-m-d')][$i]->work_minutes[$j] >= 24*60){
							$new_work_minute_for_next_day = $daily_resources[$day_date->format('Y-m-d')][$i]->work_minutes[$j] - 24*60;
							unset($daily_resources[$day_date->format('Y-m-d')][$i]->work_minutes[$j]);
						}

						if($new_work_minute_for_next_day !== false){
							$temp_resource_for_next_day_move->work_minutes[] = $new_work_minute_for_next_day;
						}
						if($new_work_minute_for_prev_day !== false){
							$updated_day = clone $day_date;
							$updated_day->modify('-1 day');
							if(isset($daily_resources[$updated_day->format('Y-m-d')])){
								for($p = 0; $p<count($daily_resources[$updated_day->format('Y-m-d')]);$p++) {
									if($daily_resources[$updated_day->format('Y-m-d')][$p]->agent_id == $daily_resources[$day_date->format('Y-m-d')][$i]->agent_id
										&& $daily_resources[$updated_day->format('Y-m-d')][$p]->service_id == $daily_resources[$day_date->format('Y-m-d')][$i]->service_id
										&& $daily_resources[$updated_day->format('Y-m-d')][$p]->location_id == $daily_resources[$day_date->format('Y-m-d')][$i]->location_id){
										// same resource found - add those work periods to this resource
											// add to the end of prev day
											$daily_resources[$updated_day->format('Y-m-d')][$p]->work_minutes[] = $new_work_minute_for_prev_day;
									}
								}
							}
						}
					}
					// if temp resource for the next day has some data created to be moved - add it to a list of resources for next day that need to be moved
					if($temp_resource_for_next_day_move->work_time_periods || $temp_resource_for_next_day_move->slots || $temp_resource_for_next_day_move->work_minutes){
						$resources_to_be_moved_to_next_day[] = $temp_resource_for_next_day_move;
					}
				}
				if($resources_to_be_moved_to_next_day) {
					$next_day = clone $day_date;
					$next_day->modify('+1 day');
					$resources_to_be_moved[$next_day->format('Y-m-d')] = $resources_to_be_moved_to_next_day;
				}
				if(isset($resources_to_be_moved[$day_date->format('Y-m-d')])){
					foreach($resources_to_be_moved[$day_date->format('Y-m-d')] as $resource_to_be_moved){
						// loop this day resources to find a matching one and append new data to it
						$total_daily_resources = count($daily_resources[$day_date->format('Y-m-d')]);
						for($b = 0; $b<$total_daily_resources;$b++){
							if($daily_resources[$day_date->format('Y-m-d')][$b]->agent_id == $resource_to_be_moved->agent_id
								&& $daily_resources[$day_date->format('Y-m-d')][$b]->service_id == $resource_to_be_moved->service_id
								&& $daily_resources[$day_date->format('Y-m-d')][$b]->location_id == $resource_to_be_moved->location_id){
								// same resource found - add those work periods to this resource
									// add to the end of prev day
									$daily_resources[$day_date->format('Y-m-d')][$b]->work_time_periods = array_merge($resource_to_be_moved->work_time_periods, $daily_resources[$day_date->format('Y-m-d')][$b]->work_time_periods);
									$daily_resources[$day_date->format('Y-m-d')][$b]->slots = array_merge($resource_to_be_moved->slots, $daily_resources[$day_date->format('Y-m-d')][$b]->slots);
									$daily_resources[$day_date->format('Y-m-d')][$b]->work_minutes = array_merge($resource_to_be_moved->work_minutes, $daily_resources[$day_date->format('Y-m-d')][$b]->work_minutes);
							}
						}
					}
					$resources_to_be_moved[$day_date->format('Y-m-d')] = [];
				}
			}
		}
		return $daily_resources;
	}


  public static function get_timezone_name_for_logged_in_customer($timezone_name){
    if(OsTimeHelper::is_timezone_saved_in_session()){
      $timezone_name = $_COOKIE[LATEPOINT_SELECTED_TIMEZONE_COOKIE];
    }else{
      if(OsAuthHelper::is_customer_logged_in()){
        $customer_timezone_name = OsMetaHelper::get_customer_meta_by_key('timezone_name', OsAuthHelper::get_logged_in_customer_id());
        if(!empty($customer_timezone_name)){
          $timezone_name = $customer_timezone_name;
        }else{
          OsMetaHelper::save_customer_meta_by_key('timezone_name', OsTimeHelper::get_wp_timezone_name(), OsAuthHelper::get_logged_in_customer_id());
          $timezone_name = OsTimeHelper::get_wp_timezone_name();
        }
      }else{
        $timezone_name = OsTimeHelper::get_wp_timezone_name();
      }
      OsTimeHelper::set_timezone_name_in_cookie($timezone_name);
    }
    return $timezone_name;
  }


  public static function add_timezone_vars_for_customer(){
    echo '<li><span class="var-label">'.__('Customer Timezone', 'latepoint-pro-features').'</span> <span class="var-code os-click-to-copy">{{customer_timezone}}</span></li>';
  }

  public static function replace_booking_vars_for_timezone($text, $booking){
    $needles = ['{{start_date_customer_timezone}}',
                '{{start_time_customer_timezone}}',
                '{{end_time_customer_timezone}}',
                '{{customer_timezone}}'];

    $replacements = [$booking->nice_start_date_for_customer,
                      $booking->nice_start_time_for_customer,
                      $booking->nice_end_time_for_customer,
                      $booking->customer->get_selected_timezone_name()];
    $text = str_replace($needles, $replacements, $text);
    return $text;
  }


  public static function add_booking_form_class($classes){
    if(OsSettingsHelper::is_on('steps_show_timezone_selector')) $classes[] = 'addon-timezone-selector-active';
    return $classes;
  }

  public static function output_timezone_selector(\LatePoint\Misc\Step $current_step){
    if(OsSettingsHelper::is_on('steps_show_timezone_selector')){
      echo '<div class="latepoint-timezone-selector-w" data-route-name="'.OsRouterHelper::build_route_name('timezone_selector', 'change_timezone').'">';
        echo OsFormHelper::select_field('latepoint_timezone_selector', __('Times are in:', 'latepoint-pro-features'), OsTimeHelper::timezones_options_list(OsTimeHelper::get_timezone_name_from_session()), OsTimeHelper::get_timezone_name_from_session());
      echo '</div>';
    }
  }

  public static function add_timezone_settings(string $settings_html, string $selected_step_code) : string{
		if($selected_step_code == 'booking__datepicker'){
			$settings_html.= '<div class="sub-section-row">
      <div class="sub-section-label">
        <h3>'.__('Timezone Settings', 'latepoint-pro-features').'</h3>
      </div>
      <div class="sub-section-content">'.
		    OsFormHelper::toggler_field('settings[steps_show_timezone_selector]', __('Show timezone selector', 'latepoint-pro-features'), OsSettingsHelper::is_on('steps_show_timezone_selector'), false, false, ['sub_label' => __('Will appear on datepicker step and customer dashboard', 'latepoint-pro-features')]).
			  OsFormHelper::toggler_field('settings[steps_show_timezone_info]', __('Show timezone information', 'latepoint-pro-features'), OsSettingsHelper::is_on('steps_show_timezone_info'), false, false, ['sub_label' => __('Timezone name will appear next to appointment time', 'latepoint-pro-features')])
      .'</div>
    </div>';
		}
		return $settings_html;
  }
}