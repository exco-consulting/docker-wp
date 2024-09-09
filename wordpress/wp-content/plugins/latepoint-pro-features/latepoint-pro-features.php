<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

/**
 * Plugin Name: LatePoint Addon - Pro Features
 * Plugin URI:  https://latepoint.com/
 * Description: LatePoint Addon that adds a set of Pro features to a base plugin
 * Version:     1.0.8
 * Author:      LatePoint
 * Author URI:  https://latepoint.com/
 * Text Domain: latepoint-pro-features
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// If no LatePoint class exists - exit, because LatePoint plugin is required for this addon

if ( ! class_exists( 'LatePointAddonProFeatures' ) ) :

	/**
	 * Main Addon Class.
	 *
	 */

	class LatePointAddonProFeatures {

		/**
		 * Addon version.
		 *
		 */
		public $version = '1.0.8';
		public $db_version = '1.0.6';
		public $addon_name = 'latepoint-pro-features';


		/**
		 * LatePoint Constructor.
		 */
		public function __construct() {
			$this->define_constants();
			$this->init_hooks();
		}

		/**
		 * Define LatePoint Constants.
		 */
		public function define_constants() {
			$upload_dir = wp_upload_dir();

			global $wpdb;

			/* Locations */
			if ( ! defined( 'LATEPOINT_ADDON_LOCATIONS_ABSPATH' ) ) {
				define( 'LATEPOINT_ADDON_LOCATIONS_ABSPATH', dirname( __FILE__ ) . '/' );
			}
			if ( ! defined( 'LATEPOINT_ADDON_LOCATIONS_LIB_ABSPATH' ) ) {
				define( 'LATEPOINT_ADDON_LOCATIONS_LIB_ABSPATH', LATEPOINT_ADDON_LOCATIONS_ABSPATH . 'lib/' );
			}
			if ( ! defined( 'LATEPOINT_ADDON_LOCATIONS_VIEWS_ABSPATH' ) ) {
				define( 'LATEPOINT_ADDON_LOCATIONS_VIEWS_ABSPATH', LATEPOINT_ADDON_LOCATIONS_LIB_ABSPATH . 'views/' );
			}

			/* Messages */
			if ( ! defined( 'LATEPOINT_TABLE_MESSAGES' ) ) {
				define( 'LATEPOINT_TABLE_MESSAGES', $wpdb->prefix . 'latepoint_messages' );
			}
			if ( ! defined( 'LATEPOINT_MESSAGE_CONTENT_TYPE_TEXT' ) ) {
				define( 'LATEPOINT_MESSAGE_CONTENT_TYPE_TEXT', 'text' );
			}
			if ( ! defined( 'LATEPOINT_MESSAGE_CONTENT_TYPE_ATTACHMENT' ) ) {
				define( 'LATEPOINT_MESSAGE_CONTENT_TYPE_ATTACHMENT', 'attachment' );
			}

			/* Service Extras */
			if ( ! defined( 'LATEPOINT_SERVICE_EXTRA_STATUS_ACTIVE' ) ) {
				define( 'LATEPOINT_SERVICE_EXTRA_STATUS_ACTIVE', 'active' );
			}
			if ( ! defined( 'LATEPOINT_SERVICE_EXTRA_STATUS_DISABLED' ) ) {
				define( 'LATEPOINT_SERVICE_EXTRA_STATUS_DISABLED', 'disabled' );
			}

			if ( ! defined( 'LATEPOINT_TABLE_SERVICE_EXTRAS' ) ) {
				define( 'LATEPOINT_TABLE_SERVICE_EXTRAS', $wpdb->prefix . 'latepoint_service_extras' );
			}
			if ( ! defined( 'LATEPOINT_TABLE_SERVICES_SERVICE_EXTRAS' ) ) {
				define( 'LATEPOINT_TABLE_SERVICES_SERVICE_EXTRAS', $wpdb->prefix . 'latepoint_services_service_extras' );
			}
			if ( ! defined( 'LATEPOINT_TABLE_BOOKINGS_SERVICE_EXTRAS' ) ) {
				define( 'LATEPOINT_TABLE_BOOKINGS_SERVICE_EXTRAS', $wpdb->prefix . 'latepoint_bookings_service_extras' );
			}

			/* Coupons */
			if ( ! defined( 'LATEPOINT_TABLE_COUPONS' ) ) {
				define( 'LATEPOINT_TABLE_COUPONS', $wpdb->prefix . 'latepoint_coupons' );
			}
			if ( ! defined( 'LATEPOINT_COUPON_STATUS_ACTIVE' ) ) {
				define( 'LATEPOINT_COUPON_STATUS_ACTIVE', 'active' );
			}
			if ( ! defined( 'LATEPOINT_COUPON_STATUS_DISABLED' ) ) {
				define( 'LATEPOINT_COUPON_STATUS_DISABLED', 'disabled' );
			}
		}


		public static function public_stylesheets() {
			return plugin_dir_url( __FILE__ ) . 'public/stylesheets/';
		}

		public static function public_javascripts() {
			return plugin_dir_url( __FILE__ ) . 'public/javascripts/';
		}


		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			// COMPOSER AUTOLOAD
			require( dirname( __FILE__ ) . '/vendor/autoload.php' );

			// CONTROLLERS
			include_once( dirname( __FILE__ ) . '/lib/controllers/coupons_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/timezone_selector_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/service_categories_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/bundles_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/agents_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/locations_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/location_categories_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/messages_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/reminders_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/webhooks_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/service_extras_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/roles_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/service_durations_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/taxes_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/custom_fields_controller.php' );
			include_once( dirname( __FILE__ ) . '/lib/controllers/group_bookings_controller.php' );

			// HELPERS
			include_once( dirname( __FILE__ ) . '/lib/helpers/coupons_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/messages_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/webhooks_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/group_bookings_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/service_extras_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/service_extras_connector_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/taxes_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/custom_fields_helper.php' );

			// MODELS
			include_once( dirname( __FILE__ ) . '/lib/models/message_model.php' );
			include_once( dirname( __FILE__ ) . '/lib/models/service_extra_model.php' );
			include_once( dirname( __FILE__ ) . '/lib/models/booking_service_extra_model.php' );
			include_once( dirname( __FILE__ ) . '/lib/models/service_extra_connector_model.php' );
			include_once( dirname( __FILE__ ) . '/lib/models/coupon_model.php' );


			// MISC
			include_once( dirname( __FILE__ ) . '/lib/misc/tax.php' );

		}

		public function init_hooks() {
			add_action( 'latepoint_includes', [ $this, 'includes' ] );

			add_action( 'init', array( $this, 'init' ), 0 );
			add_filter( 'latepoint_installed_addons', [ $this, 'register_addon' ] );
			add_filter( 'latepoint_addons_sqls', [ $this, 'db_sqls' ] );

			// INCLUDE FEATURES USED IN ACTIONS
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_timezone_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_qrcode_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_locations_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_messages_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_reminders_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_webhooks_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_service_extras_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_group_bookings_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_service_durations_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_coupons_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_custom_fields_helper.php' );
			include_once( dirname( __FILE__ ) . '/lib/helpers/feature_service_categories_helper.php' );


			add_action( 'latepoint_wp_enqueue_scripts', [ $this, 'load_front_scripts_and_styles' ] );
			add_action( 'latepoint_admin_enqueue_scripts', [ $this, 'load_admin_scripts_and_styles' ] );


			add_filter( 'latepoint_localized_vars_front', [ $this, 'localized_vars_for_front' ] );
			add_filter( 'latepoint_localized_vars_admin', [ $this, 'localized_vars_for_admin' ] );

			/* ************************ */
			/* TimeZone */
			/* ************************ */
			add_action( 'latepoint_get_step_settings_edit_form_html', 'OsFeatureTimezoneHelper::add_timezone_settings', 10, 2 );
			add_action( 'latepoint_steps_side_panel_after', 'OsFeatureTimezoneHelper::output_timezone_selector' );
			add_action( 'latepoint_available_vars_booking', 'OsFeatureTimezoneHelper::add_timezone_vars_for_booking', 15 );
			add_action( 'latepoint_available_vars_customer', 'OsFeatureTimezoneHelper::add_timezone_vars_for_customer', 15 );
			add_action( 'latepoint_customer_dashboard_before_appointments', 'OsFeatureTimezoneHelper::add_timezone_selector_to_customer_dashboard', 10 );
			add_action( 'latepoint_step_datepicker_appointment_time_header_label', 'OsFeatureTimezoneHelper::add_timezone_information_to_datepicker', 10, 2 );
			add_filter( 'latepoint_booking_form_classes', 'OsFeatureTimezoneHelper::add_booking_form_class' );
			add_filter( 'latepoint_replace_booking_vars', 'OsFeatureTimezoneHelper::replace_booking_vars_for_timezone', 10, 2 );
			add_filter( 'latepoint_get_resources_grouped_by_day', 'OsFeatureTimezoneHelper::apply_timeshift_to_resources_grouped_by_day', 10, 5 );
			add_filter( 'latepoint_timezone_name_from_session', 'OsFeatureTimezoneHelper::get_timezone_name_for_logged_in_customer' );
			add_filter( 'latepoint_booking_summary_formatted_booking_start_datetime', 'OsFeatureTimezoneHelper::change_booking_start_datetime_to_timezone', 10, 3 );

			/* ************************ */
			/* Webhooks */
			/* ************************ */
			add_filter( 'latepoint_activity_codes', 'OsFeatureWebhooksHelper::add_webhook_code' );
			add_filter( 'latepoint_process_action_settings_fields_html_after', 'OsFeatureWebhooksHelper::add_webhook_settings', 10, 2 );
			add_filter( 'latepoint_process_action_generate_preview', 'OsFeatureWebhooksHelper::generate_webhook_preview', 10, 2 );
			add_filter( 'latepoint_process_action_run', 'OsFeatureWebhooksHelper::process_webhook_action', 10, 2 );
			add_filter( 'latepoint_process_prepare_data_for_run', 'OsFeatureWebhooksHelper::prepare_data_for_run' );

			/* ************************ */
			/* Misc */
			/* ************************ */
			add_action( 'latepoint_general_settings_section_restrictions_after', [
				$this,
				'add_cart_restrictions_settings'
			], 10, 2 );
			add_filter( 'latepoint_can_checkout_multiple_items', [ $this, 'enable_multiple_items_checkout' ], 10, 2 );

			/* ************************ */
			/* QR Code */
			/* ************************ */
			add_action( 'latepoint_step_confirmation_head_info_before', 'OsFeatureQrcodeHelper::generate_qr_code_for_order' );
			add_action( 'latepoint_booking_full_summary_head_info_before', 'OsFeatureQrcodeHelper::generate_qr_code' );

			/* ************************ */
			/* Reminders */
			/* ************************ */
			add_filter( 'latepoint_event_time_offset_settings_html', 'OsFeatureRemindersHelper::add_event_time_offset_settings_html', 10, 2 );
			add_action( 'latepoint_process_scheduled_jobs', 'OsFeatureRemindersHelper::process_scheduled_jobs' );

			/* ************************ */
			/* Group Bookings */
			/* ************************ */
			add_filter( 'latepoint_generated_params_for_booking_form', 'OsFeatureGroupBookingsHelper::add_total_attendees_to_booking_form_params', 10, 2 );
			add_filter( 'latepoint_step_show_next_btn_rules', 'OsFeatureGroupBookingsHelper::add_step_show_next_btn_rules', 10, 2 );
			add_filter( 'latepoint_should_step_be_skipped', 'OsFeatureGroupBookingsHelper::should_step_be_skipped', 10, 5 );
			add_action( 'latepoint_load_step', 'OsFeatureGroupBookingsHelper::load_step_group_bookings', 10, 2 );
			add_filter( 'latepoint_get_step_codes_with_rules', 'OsFeatureGroupBookingsHelper::add_step_for_group_bookings', 10, 2 );
			add_action( 'latepoint_settings_for_step_codes', 'OsFeatureGroupBookingsHelper::add_settings_for_step' );
			add_action( 'latepoint_step_labels_by_step_codes', 'OsFeatureGroupBookingsHelper::add_label_for_step' );
			add_action( 'latepoint_booking_data_form_after_service', 'OsFeatureGroupBookingsHelper::output_total_attendees_on_quick_form', 10, 2 );
			add_action( 'latepoint_service_form_after', 'OsFeatureGroupBookingsHelper::output_capacity_on_service_form' );
			add_filter( 'latepoint_booking_summary_service_headings', 'OsFeatureGroupBookingsHelper::add_capacity_to_service_headings', 10, 2 );
			add_action( 'latepoint_price_breakdown_service_row_for_booking', 'OsFeatureGroupBookingsHelper::add_attendees_to_service_row_item', 10, 2 );

			add_action( 'latepoint_available_vars_booking', 'OsFeatureGroupBookingsHelper::add_group_bookings_vars', 15 );
			add_action( 'latepoint_service_saved', 'OsFeatureGroupBookingsHelper::save_service_info', 15, 3 );
			add_action( 'latepoint_after_service_extra_form', 'OsFeatureGroupBookingsHelper::add_service_extra_settings' );
			add_action( 'latepoint_service_tile_info_rows_after', 'OsFeatureGroupBookingsHelper::add_capacity_info_to_service_tile' );
			add_filter( 'latepoint_replace_booking_vars', 'OsFeatureGroupBookingsHelper::replace_booking_vars_for_group_bookings', 10, 2 );
			add_filter( 'latepoint_full_amount_for_service', 'OsFeatureGroupBookingsHelper::adjust_full_amount_for_service', 10, 2 );
			add_filter( 'latepoint_full_amount_for_service_extra', 'OsFeatureGroupBookingsHelper::adjust_full_amount_for_service_extra', 10, 4 );
			add_filter( 'latepoint_deposit_amount_for_service', 'OsFeatureGroupBookingsHelper::adjust_deposit_amount_for_service', 10, 2 );
			add_filter( 'latepoint_bookings_data_for_csv_export', 'OsFeatureGroupBookingsHelper::add_columns_to_bookings_data_for_csv', 11, 2 );
			add_filter( 'latepoint_booking_row_for_csv_export', 'OsFeatureGroupBookingsHelper::add_columns_to_booking_row_for_csv', 11, 3 );
			// -- for webhooks addon
			add_filter( 'latepoint_webhook_variables_for_new_booking', 'OsFeatureGroupBookingsHelper::add_data_to_webhook', 10, 2 );
			add_filter( 'latepoint_bookings_table_columns', 'OsFeatureGroupBookingsHelper::add_columns_to_bookings_table' );
			add_action( 'latepoint_remove_preset_steps', 'OsFeatureGroupBookingsHelper::remove_group_bookings_step_if_preselected', 10, 4 );

			/* ************************ */
			/* Service Durations */
			/* ************************ */
			add_filter( 'latepoint_step_show_next_btn_rules', 'OsFeatureServiceDurationsHelper::add_step_show_next_btn_rules', 10, 2 );
			add_filter( 'latepoint_should_step_be_skipped', 'OsFeatureServiceDurationsHelper::should_step_be_skipped', 10, 5 );
			add_action( 'latepoint_service_edit_durations', 'OsFeatureServiceDurationsHelper::edit_durations_html' );
			add_action( 'latepoint_get_step_settings_edit_form_html', 'OsFeatureServiceDurationsHelper::add_duration_settings', 10, 2 );
			add_action( 'latepoint_load_step', 'OsFeatureServiceDurationsHelper::load_step_service_durations', 10, 2 );
			add_filter( 'latepoint_get_step_codes_with_rules', 'OsFeatureServiceDurationsHelper::add_step_for_service_durations', 10, 2 );
			add_action( 'latepoint_settings_for_step_codes', 'OsFeatureServiceDurationsHelper::add_settings_for_step' );
			add_action( 'latepoint_step_labels_by_step_codes', 'OsFeatureServiceDurationsHelper::add_label_for_step' );
			add_action( 'latepoint_booking_get_service_name_for_summary', 'OsFeatureServiceDurationsHelper::add_duration_to_booking_service_name_for_summary', 10, 2 );
			add_action( 'latepoint_remove_preset_steps', 'OsFeatureServiceDurationsHelper::remove_durations_step_if_preselected', 10, 4 );
			add_filter( 'latepoint_svg_for_step_code', 'OsFeatureServiceDurationsHelper::add_svg_for_step', 10, 2 );

			/* ************************ */
			/* Messages */
			/* ************************ */
			add_action( 'latepoint_booking_data_form_after', 'OsFeatureMessagesHelper::output_messages_on_quick_form', 12, 2 );
			add_action( 'latepoint_customer_dashboard_after_tabs', 'OsFeatureMessagesHelper::output_messages_tab_on_customer_dashboard' );
			add_action( 'latepoint_customer_dashboard_after_tab_contents', 'OsFeatureMessagesHelper::output_messages_tab_contents_on_customer_dashboard' );
			add_action( 'latepoint_settings_notifications_other_after', 'OsFeatureMessagesHelper::new_message_notification_template_settings' );
			add_action( 'latepoint_top_bar_before_actions', 'OsFeatureMessagesHelper::add_messages_link_to_top_bar' );
			add_action( 'latepoint_booking_deleted', 'OsFeatureMessagesHelper::delete_messages_for_deleted_booking_id' );
			add_action( 'latepoint_available_vars_booking', 'OsFeatureMessagesHelper::add_messages_vars', 15 );

			/* ************************ */
			/* Locations */
			/* ************************ */
			add_action( 'latepoint_load_step', 'OsFeatureLocationsHelper::load_step_locations', 10, 2 );
			add_filter( 'latepoint_get_step_codes_with_rules', 'OsFeatureLocationsHelper::add_step_for_locations', 10, 2 );
			add_action( 'latepoint_settings_for_step_codes', 'OsFeatureLocationsHelper::add_settings_for_step' );
			add_action( 'latepoint_step_labels_by_step_codes', 'OsFeatureLocationsHelper::add_label_for_step' );
			add_action( 'latepoint_get_step_settings_edit_form_html', 'OsFeatureLocationsHelper::add_location_categories_setting', 10, 2 );
			add_filter( 'latepoint_model_view_as_data', 'OsFeatureLocationsHelper::add_location_data_vars_to_booking', 10, 2 );
			add_filter( 'latepoint_custom_field_condition_properties', 'OsFeatureLocationsHelper::add_custom_field_condition_properties' );
			add_filter( 'latepoint_available_values_for_condition_property', 'OsFeatureLocationsHelper::add_values_for_condition_property', 10, 2 );
			add_filter( 'latepoint_model_options_for_multi_select', 'OsFeatureLocationsHelper::add_options_for_multi_select', 10, 2 );
			add_filter( 'latepoint_process_event_trigger_condition_properties', 'OsFeatureLocationsHelper::add_process_event_condition_properties', 10, 2 );
			add_filter( 'latepoint_available_values_for_process_event_condition_property', 'OsFeatureLocationsHelper::add_values_for_process_event_condition_properties', 10, 2 );
			add_filter( 'latepoint_bookings_data_for_csv_export', 'OsFeatureLocationsHelper::add_location_to_bookings_data_for_csv', 11, 2 );
			add_filter( 'latepoint_booking_row_for_csv_export', 'OsFeatureLocationsHelper::add_location_to_booking_row_for_csv', 11, 3 );
			add_filter( 'latepoint_webhook_variables_for_new_booking', 'OsFeatureLocationsHelper::add_booking_location_to_webhook', 10, 2 );
			add_filter( 'latepoint_capabilities_for_controllers', 'OsFeatureLocationsHelper::add_capabilities_for_controllers' );


			/* ************************ */
			/* Coupons */
			/* ************************ */
			add_filter( 'latepoint_filter_payment_total_info', 'OsCouponsHelper::get_payment_total_info_with_coupon_html', 10, 2 );
			add_filter( 'latepoint_order_reload_price_breakdown', 'OsFeatureCouponsHelper::reload_coupon_discount' );
			add_filter( 'latepoint_step_show_next_btn_rules', 'OsFeatureCouponsHelper::add_step_show_next_btn_rules', 10, 2 );
			add_filter( 'latepoint_bookings_data_for_csv_export', 'OsFeatureCouponsHelper::add_coupon_code_to_bookings_data_for_csv', 11, 2 );
			add_filter( 'latepoint_booking_row_for_csv_export', 'OsFeatureCouponsHelper::add_coupon_code_to_booking_row_for_csv', 11, 3 );
			add_filter( 'latepoint_bookings_table_columns', 'OsFeatureCouponsHelper::add_coupon_columns_to_bookings_table' );
			add_filter( 'latepoint_cart_price_breakdown_rows', 'OsFeatureCouponsHelper::add_coupon_info_to_cart_price_breakdown_rows', 9, 3 );
			add_filter( 'latepoint_roles_get_all_available_actions_list', 'OsFeatureCouponsHelper::add_coupon_actions_to_roles' );
			add_filter( 'latepoint_roles_action_names', 'OsFeatureCouponsHelper::add_coupon_name_to_roles', 10, 2 );
			add_action( 'latepoint_order_quick_form_price_after_total', 'OsFeatureCouponsHelper::show_coupon_code_on_order_quick_edit_form' );
			add_filter( 'latepoint_model_view_as_data', 'OsFeatureCouponsHelper::add_coupon_data_vars_to_booking', 10, 2 );
			add_filter( 'latepoint_capabilities_for_controllers', 'OsFeatureCouponsHelper::add_capabilities_for_controllers' );
			add_action( 'latepoint_after_verify_step_content', 'OsFeatureCouponsHelper::add_coupon_form_to_verify_step', 10, 1 );
			add_action( 'latepoint_cart_calculate_prices', 'OsFeatureCouponsHelper::add_coupons_to_cart_calculations', 10, 1 );


			/* ************************ */
			/* Taxes */
			/* ************************ */
			add_filter( 'latepoint_cart_price_breakdown_rows', 'OsTaxesHelper::add_taxes_to_cart_price_breakdown_rows', 11, 3 );
            add_action( 'latepoint_cart_calculate_prices', 'OsTaxesHelper::calculate_taxes_for_cart', 10, 1 );


			/* ************************ */
			/* Custom Fields */
			/* ************************ */
			add_action( 'latepoint_step_labels_by_step_codes', 'OsFeatureCustomFieldsHelper::add_label_for_step' );
			add_action( 'latepoint_settings_for_step_codes', 'OsFeatureCustomFieldsHelper::add_settings_for_step' );
			add_filter( 'latepoint_get_step_codes_with_rules', 'OsFeatureCustomFieldsHelper::add_step_for_custom_fields', 10, 2 );
			add_action( 'latepoint_custom_step_info', 'OsFeatureCustomFieldsHelper::show_step_info' );
			add_filter( 'latepoint_step_show_next_btn_rules', 'OsFeatureCustomFieldsHelper::add_step_show_next_btn_rules', 10, 2 );
			add_filter( 'latepoint_model_loaded_by_id', 'OsFeatureCustomFieldsHelper::load_custom_fields_for_model' );
			add_filter( 'latepoint_get_results_as_models', 'OsFeatureCustomFieldsHelper::load_custom_fields_for_model' );
			add_filter( 'latepoint_should_step_be_skipped', 'OsFeatureCustomFieldsHelper::should_step_be_skipped', 10, 5 );
			add_filter( 'latepoint_generated_params_for_booking_form', 'OsFeatureCustomFieldsHelper::add_custom_fields_to_booking_form_params', 10, 2 );
			// -- CSV Export Filters
			add_filter( 'latepoint_bookings_data_for_csv_export', 'OsFeatureCustomFieldsHelper::add_custom_fields_to_bookings_data_for_csv', 10, 2 );
			add_filter( 'latepoint_booking_row_for_csv_export', 'OsFeatureCustomFieldsHelper::add_custom_fields_to_booking_row_for_csv', 10, 3 );
			add_filter( 'latepoint_customers_data_for_csv_export', 'OsFeatureCustomFieldsHelper::add_custom_fields_to_customers_data_for_csv', 10, 2 );
			add_filter( 'latepoint_customer_row_for_csv_export', 'OsFeatureCustomFieldsHelper::add_custom_fields_to_customer_row_for_csv', 10, 3 );
			// -- Template variables
			add_filter( 'latepoint_replace_booking_vars', 'OsFeatureCustomFieldsHelper::replace_booking_vars_in_template', 10, 2 );
			add_filter( 'latepoint_replace_customer_vars', 'OsFeatureCustomFieldsHelper::replace_customer_vars_in_template', 10, 2 );
			// -- Model View as Data
			add_filter( 'latepoint_model_view_as_data', 'OsFeatureCustomFieldsHelper::add_customer_custom_fields_data_vars_to_customer', 10, 2 );
			add_filter( 'latepoint_model_view_as_data', 'OsFeatureCustomFieldsHelper::add_booking_custom_fields_data_vars_to_booking', 10, 2 );
			// -- Processes
			add_filter( 'latepoint_process_event_trigger_condition_properties', 'OsFeatureCustomFieldsHelper::add_custom_fields_to_processes', 10, 2 );
			// -- Booking Index
			add_filter( 'latepoint_bookings_table_columns', 'OsFeatureCustomFieldsHelper::add_custom_fields_to_bookings_table_columns' );
			add_action( 'latepoint_customer_dashboard_information_form_after', 'OsFeatureCustomFieldsHelper::output_customer_custom_fields_on_customer_dashboard' );
			add_action( 'latepoint_customer_edit_form_after', 'OsFeatureCustomFieldsHelper::output_customer_custom_fields_on_form' );
			add_action( 'latepoint_customer_inline_edit_form_after', 'OsFeatureCustomFieldsHelper::output_customer_custom_fields_on_quick_form' );
			add_action( 'latepoint_booking_data_form_after', 'OsFeatureCustomFieldsHelper::output_booking_custom_fields_on_quick_form', 10, 2 );
			add_action( 'latepoint_load_step', 'OsFeatureCustomFieldsHelper::load_step_custom_fields_for_booking', 10, 2 );
			add_action( 'latepoint_process_step', 'OsFeatureCustomFieldsHelper::process_step_custom_fields', 10, 2 );
			add_filter( 'latepoint_svg_for_step_code', 'OsFeatureCustomFieldsHelper::add_svg_for_step', 10, 2 );
			// Confirmation and Verification Booking Steps
			add_filter( 'latepoint_booking_summary_service_attributes', 'OsFeatureCustomFieldsHelper::add_booking_custom_fields_to_service_attributes', 10, 2 );
			add_filter( 'latepoint_booking_summary_customer_attributes', 'OsFeatureCustomFieldsHelper::add_customer_custom_fields_to_service_attributes', 10, 2 );
			add_filter( 'latepoint_capabilities_for_controllers', 'OsFeatureCustomFieldsHelper::add_capabilities_for_controller' );
			// TODO this needs to be integrated into the order intent (create_or_update method), read the github for old method
			add_filter( 'latepoint_cart_data_for_order_intent', 'OsFeatureCustomFieldsHelper::process_custom_fields_in_booking_data_for_order_intent' );
			add_action( 'latepoint_available_vars_after', 'OsFeatureCustomFieldsHelper::output_custom_fields_vars' );
			add_action( 'latepoint_settings_general_other_after', 'OsFeatureCustomFieldsHelper::output_google_autocomplete_settings' );
			add_action( 'latepoint_model_set_data', 'OsFeatureCustomFieldsHelper::set_custom_fields_data', 10, 2 );
			add_action( 'latepoint_model_save', 'OsFeatureCustomFieldsHelper::save_custom_fields' );
			add_action( 'latepoint_model_validate', 'OsFeatureCustomFieldsHelper::validate_custom_fields', 10, 3 );
			add_action( 'latepoint_booking_steps_contact_after', 'OsFeatureCustomFieldsHelper::add_custom_fields_for_contact_step', 10, 2 );


			/* ************************ */
			/* Service Extras */
			/* ************************ */
			add_action( 'latepoint_service_form_after', 'OsFeatureServiceExtrasHelper::output_extras_on_service_form' );
			add_action( 'latepoint_service_saved', 'OsFeatureServiceExtrasHelper::save_extras_in_service', 10, 3 );
			add_action( 'latepoint_load_step', 'OsFeatureServiceExtrasHelper::load_step_service_extras', 10, 2 );
			add_action( 'latepoint_settings_for_step_codes', 'OsFeatureServiceExtrasHelper::add_settings_for_step' );
			add_action( 'latepoint_step_labels_by_step_codes', 'OsFeatureServiceExtrasHelper::add_label_for_step' );
			add_action( 'latepoint_booking_data_form_after_service', 'OsFeatureServiceExtrasHelper::add_service_extras_to_quick_form', 10, 2 );
			add_action( 'latepoint_booking_deleted', 'OsFeatureServiceExtrasHelper::delete_service_extras_for_booking' );
			add_action( 'latepoint_booking_created', 'OsFeatureServiceExtrasHelper::save_service_extras_for_booking', 9 );
			add_action( 'latepoint_booking_updated', 'OsFeatureServiceExtrasHelper::save_service_extras_for_booking', 9, 2 );
			add_action( 'latepoint_available_vars_booking', 'OsFeatureServiceExtrasHelper::add_service_extras_vars' );
			add_action( 'latepoint_service_deleted', 'OsServiceExtrasConnectorHelper::delete_service_connections_after_deletion' );
			add_action( 'latepoint_process_step', 'OsFeatureServiceExtrasHelper::process_service_extras_step', 10, 2 );
			add_filter( 'latepoint_model_view_as_data', 'OsFeatureServiceExtrasHelper::add_service_extras_data_vars_to_booking', 10, 2 );
			add_filter( 'latepoint_bookings_data_for_csv_export', 'OsFeatureServiceExtrasHelper::add_service_extras_to_bookings_data_for_csv', 11, 2 );
			add_filter( 'latepoint_booking_row_for_csv_export', 'OsFeatureServiceExtrasHelper::add_service_extras_to_booking_row_for_csv', 11, 3 );
			add_filter( 'latepoint_replace_booking_vars', 'OsFeatureServiceExtrasHelper::replace_booking_vars_for_service_extras', 10, 2 );
			add_filter( 'latepoint_calculated_total_duration', 'OsFeatureServiceExtrasHelper::calculated_total_duration', 10, 2 );
			add_filter( 'latepoint_model_set_data', 'OsFeatureServiceExtrasHelper::set_data_for_models', 10, 2 );
			add_filter( 'latepoint_model_allowed_params', 'OsFeatureServiceExtrasHelper::set_allowed_params_for_service_extra_model', 10, 3 );
			add_filter( 'latepoint_generated_params_for_booking_form', 'OsFeatureServiceExtrasHelper::add_extras_to_form_params', 10, 2 );
			add_filter( 'latepoint_capabilities_for_controllers', 'OsFeatureServiceExtrasHelper::add_capabilities_for_controller' );
			add_filter( 'latepoint_price_breakdown_service_row_for_booking', 'OsFeatureServiceExtrasHelper::add_service_extras_to_price_breakdown_service_row', 10, 2 );
			add_filter( 'latepoint_order_reload_price_breakdown', 'OsFeatureServiceExtrasHelper::sync_service_extras_on_price_reload' );
			add_filter( 'latepoint_svg_for_step_code', 'OsFeatureServiceExtrasHelper::add_svg_for_step', 10, 2 );
			add_filter( 'latepoint_calculate_full_amount_for_booking', 'OsServiceExtrasHelper::calculate_service_extras_prices', 9, 2 );
			add_filter( 'latepoint_should_step_be_skipped', 'OsFeatureServiceExtrasHelper::should_step_be_skipped', 10, 5 );
			add_filter( 'latepoint_booking_summary_service_attributes', 'OsFeatureServiceExtrasHelper::add_service_extras_to_service_attributes', 10, 2 );
			add_filter( 'latepoint_get_step_codes_with_rules', 'OsFeatureServiceExtrasHelper::add_step_for_service_extras', 10 );
			add_filter( 'latepoint_step_show_next_btn_rules', 'OsFeatureServiceExtrasHelper::add_step_show_next_btn_rules', 10, 2 );

			/* Side menu */
			add_filter( 'latepoint_side_menu', [ $this, 'add_menu_links' ] );

			register_activation_hook( __FILE__, [ $this, 'on_activate' ] );
			register_deactivation_hook( __FILE__, [ $this, 'on_deactivate' ] );
		}

		/**
		 * Init LatePoint when WordPress Initialises.
		 */
		public function init() {
			// Set up localisation.
			$this->load_plugin_textdomain();
		}

		public function add_menu_links( $menus ) {
            $user_role = OsAuthHelper::get_current_user()->backend_user_type;
            switch($user_role) {
	            case LATEPOINT_USER_TYPE_ADMIN:
	            case LATEPOINT_USER_TYPE_CUSTOM:
                case LATEPOINT_USER_TYPE_AGENT:
		            for ( $i = 0; $i < count( $menus ); $i ++ ) {
                        if(empty($menus[$i]['id'])) continue;

			            // Multi agents
			            if ( $menus[ $i ]['id'] == 'agents' ) {
				            $menus[ $i ]['link'] = OsRouterHelper::build_link( [ 'agents', 'index' ] );
			            }


			            // Services
			            if ( $menus[ $i ]['id'] == 'services' && isset( $menus[ $i ]['children'] ) ) {
				            for ( $j = 0; $j < count( $menus[ $i ]['children'] ); $j ++ ) {
					            // Categories
					            if ( isset( $menus[ $i ]['children'][ $j ]['id'] ) && $menus[ $i ]['children'][ $j ]['id'] == 'categories' ) {
						            $menus[ $i ]['children'][ $j ]['link'] = OsRouterHelper::build_link( [
							            'service_categories',
							            'index'
						            ] );
					            }
					            // Bundles
					            if ( isset( $menus[ $i ]['children'][ $j ]['id'] ) && $menus[ $i ]['children'][ $j ]['id'] == 'bundles' ) {
						            $menus[ $i ]['children'][ $j ]['link'] = OsRouterHelper::build_link( [
							            'bundles',
							            'index'
						            ] );
					            }
					            // Extras
					            if ( isset( $menus[ $i ]['children'][ $j ]['id'] ) && $menus[ $i ]['children'][ $j ]['id'] == 'service_extras' ) {
						            $menus[ $i ]['children'][ $j ]['link'] = OsRouterHelper::build_link( [
							            'service_extras',
							            'index'
						            ] );
					            }
				            }
			            }


			            // Settings
			            if ( $menus[ $i ]['id'] == 'settings' && isset( $menus[ $i ]['children'] ) ) {
				            for ( $j = 0; $j < count( $menus[ $i ]['children'] ); $j ++ ) {
					            // Roles
					            if ( isset( $menus[ $i ]['children'][ $j ]['id'] ) && $menus[ $i ]['children'][ $j ]['id'] == 'roles' ) {
						            $menus[ $i ]['children'][ $j ]['link'] = OsRouterHelper::build_link( [
							            'roles',
							            'index'
						            ] );
					            }
					            // Taxes
					            if ( isset( $menus[ $i ]['children'][ $j ]['id'] ) && $menus[ $i ]['children'][ $j ]['id'] == 'taxes' ) {
						            $menus[ $i ]['children'][ $j ]['link'] = OsRouterHelper::build_link( [
							            'taxes',
							            'index'
						            ] );
					            }
					            // Coupons
					            if ( isset( $menus[ $i ]['children'][ $j ]['id'] ) && $menus[ $i ]['children'][ $j ]['id'] == 'coupons' ) {
						            $menus[ $i ]['children'][ $j ]['link'] = OsRouterHelper::build_link( [
							            'coupons',
							            'index'
						            ] );
					            }
				            }
			            }

			            // Custom Fields
			            if ( $menus[ $i ]['id'] == 'form_fields' ) {
				            $menus[ $i ] = [
					            'id'       => 'form_fields',
					            'label'    => __( 'Form Fields', 'latepoint-pro-features' ),
					            'icon'     => 'latepoint-icon latepoint-icon-browser',
					            'link'     => OsRouterHelper::build_link( [ 'custom_fields', 'for_customer' ] ),
					            'children' => [
						            [
							            'label' => __( 'Customer Fields', 'latepoint-pro-features' ),
							            'icon'  => '',
							            'link'  => OsRouterHelper::build_link( [ 'custom_fields', 'for_customer' ] )
						            ],
						            [
							            'label' => __( 'Booking Fields', 'latepoint-pro-features' ),
							            'icon'  => '',
							            'link'  => OsRouterHelper::build_link( [ 'custom_fields', 'for_booking' ] )
						            ],
					            ]
				            ];
			            }


			            // Locations
			            if ( $menus[ $i ]['id'] == 'locations' ) {
				            $menus[ $i ] = [
					            'id'       => 'locations',
					            'label'    => __( 'Locations', 'latepoint-pro-features' ),
					            'icon'     => 'latepoint-icon latepoint-icon-map-marker',
					            'link'     => OsRouterHelper::build_link( [ 'locations', 'index' ] ),
					            'children' => [
						            [
							            'label' => __( 'Locations', 'latepoint-pro-features' ),
							            'icon'  => '',
							            'link'  => OsRouterHelper::build_link( [ 'locations', 'index' ] )
						            ],
						            [
							            'label' => __( 'Categories', 'latepoint-pro-features' ),
							            'icon'  => '',
							            'link'  => OsRouterHelper::build_link( [ 'location_categories', 'index' ] )
						            ],
					            ]
				            ];
			            }
		            }
		            break;
            }
			return $menus;
		}

		public function enable_multiple_items_checkout( $can ) {
			$can = true;

			return $can;
		}

		public function add_cart_restrictions_settings() {
			?>
            <div class="sub-section-row">
                <div class="sub-section-label">
                    <h3><?php _e( 'Cart Restrictions', 'latepoint-pro-features' ) ?></h3>
                </div>
                <div class="sub-section-content">
					<?php echo OsFormHelper::toggler_field( 'settings[disable_checkout_multiple_items]', __( 'Disable Shopping Cart Functionality', 'latepoint-pro-features' ), OsSettingsHelper::is_on( 'disable_checkout_multiple_items' ), false, false, [ 'sub_label' => __( 'This will disable ability to book multiple services in one order', 'latepoint-pro-features' ) ] ); ?>
                </div>
            </div>
			<?php
		}


		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'latepoint-pro-features', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		public function on_deactivate() {
			wp_clear_scheduled_hook( 'latepoint_process_scheduled_jobs' );
		}

		public function on_activate() {
			if ( class_exists( 'OsDatabaseHelper' ) ) {
				OsDatabaseHelper::check_db_version_for_addons();
			}
			do_action( 'latepoint_on_addon_activate', $this->addon_name, $this->version );

			if ( ! wp_next_scheduled( 'latepoint_process_scheduled_jobs' ) ) {
				wp_schedule_event( time(), 'latepoint_5_minutes', 'latepoint_process_scheduled_jobs' );
			}
		}

		public function register_addon( $installed_addons ) {
			$installed_addons[] = [
				'name'       => $this->addon_name,
				'db_version' => $this->db_version,
				'version'    => $this->version
			];

			return $installed_addons;
		}

		public function db_sqls( $sqls ) {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			/* Messages */
			$sqls[] = "CREATE TABLE " . LATEPOINT_TABLE_MESSAGES . " (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      content text NOT NULL,
      content_type varchar(20) NOT NULL,
      author_id mediumint(9) NOT NULL,
      booking_id mediumint(9) NOT NULL,
      author_type varchar(20) NOT NULL,
      is_hidden boolean,
      is_read boolean,
      created_at datetime,
      updated_at datetime,
      KEY content_type_index (content_type),
      KEY author_id_index (author_id),
      KEY booking_id_index (booking_id),
      KEY author_type_index (author_type),
      PRIMARY KEY  (id)
    ) $charset_collate;";


			/* Service Extras */
			$sqls[] = "CREATE TABLE " . LATEPOINT_TABLE_SERVICE_EXTRAS . " (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      short_description text,
      charge_amount decimal(20,4),
      duration int(11) NOT NULL,
      maximum_quantity int(3),
      selection_image_id int(11),
      description_image_id int(11),
      multiplied_by_attendees varchar(10),
      status varchar(20) NOT NULL,
      created_at datetime,
      updated_at datetime,
      KEY status_index (status),
      PRIMARY KEY  (id)
    ) $charset_collate;";

			$sqls[] = "CREATE TABLE " . LATEPOINT_TABLE_SERVICES_SERVICE_EXTRAS . " (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      service_id int(11) NOT NULL,
      service_extra_id int(11) NOT NULL,
      created_at datetime,
      updated_at datetime,
      KEY service_id_index (service_id),
      KEY service_extra_id_index (service_extra_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";

			$sqls[] = "CREATE TABLE " . LATEPOINT_TABLE_BOOKINGS_SERVICE_EXTRAS . " (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      booking_id int(11) NOT NULL,
      service_extra_id int(11) NOT NULL,
      duration int(11) NOT NULL,
      quantity int(3) NOT NULL,
      price decimal(20,4),
      created_at datetime,
      updated_at datetime,
      KEY booking_id_index (booking_id),
      KEY service_extra_id_index (service_extra_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";

			/* Coupons */
			$sqls[] = "CREATE TABLE " . LATEPOINT_TABLE_COUPONS . " (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      code varchar(110) NOT NULL,
      name varchar(110),
      discount_type varchar(110),
      discount_value decimal(20,4),
      description text,
      rules text,
      status varchar(20) NOT NULL,
      created_at datetime,
      updated_at datetime,
      UNIQUE KEY code_index (code),
      PRIMARY KEY  (id)
    ) $charset_collate;";


			return $sqls;
		}


		public function localized_vars_for_admin( $localized_vars ) {
			/* Custom Fields */
			$localized_vars['google_places_country_restriction']           = OsSettingsHelper::get_settings_value( 'google_places_country_restriction', '' );
			$localized_vars['custom_fields_remove_file_prompt']            = __( 'Are you sure you want to remove this file?', 'latepoint-pro-features' );
			$localized_vars['custom_fields_remove_required_file_prompt']   = __( 'This file is required and can not be removed, but you can replace it with a different file. Do you want to replace it?', 'latepoint-pro-features' );
			$localized_vars['custom_field_default_value_field_html_route'] = OsRouterHelper::build_route_name( 'custom_fields', 'default_value_field' );
			$localized_vars['custom_field_types_with_default_value']       = json_encode( OsCustomFieldsHelper::get_custom_field_types_with_default_value() );

			return $localized_vars;
		}


		public function localized_vars_for_front( $localized_vars ) {
			/* Custom Fields */
			$localized_vars['google_places_country_restriction']         = OsSettingsHelper::get_settings_value( 'google_places_country_restriction', '' );
			$localized_vars['custom_fields_remove_file_prompt']          = __( 'Are you sure you want to remove this file?', 'latepoint-pro-features' );
			$localized_vars['custom_fields_remove_required_file_prompt'] = __( 'This file is required and can not be removed, but you can replace it with a different file. Do you want to replace it?', 'latepoint-pro-features' );

			return $localized_vars;
		}

		public function load_front_scripts_and_styles() {
			// Stylesheets
			wp_enqueue_style( 'latepoint-pro-features-front', $this->public_stylesheets() . 'latepoint-pro-features-front.css', false, $this->version );

			// Javascripts
			wp_enqueue_script( 'latepoint-pro-features-front', $this->public_javascripts() . 'latepoint-pro-features-front.js', array( 'jquery' ), $this->version );

			// Google Places API
			if ( ! empty( OsSettingsHelper::get_settings_value( 'google_places_api_key' ) ) ) {
				wp_enqueue_script( 'google-places-api', OsCustomFieldsHelper::get_google_places_api_url(), false, null, [
					'strategy'  => 'async',
					'in_footer' => true
				] );
			}
		}


		public function load_admin_scripts_and_styles( $localized_vars ) {

			// Stylesheets
			wp_enqueue_style( 'latepoint-pro-features-admin', $this->public_stylesheets() . 'latepoint-pro-features-admin.css', false, $this->version );

			// Javascripts
			wp_enqueue_script( 'latepoint-pro-features-admin', $this->public_javascripts() . 'latepoint-pro-features-admin.js', array( 'jquery' ), $this->version );

			if ( ! empty( OsSettingsHelper::get_settings_value( 'google_places_api_key' ) ) ) {
				wp_enqueue_script( 'google-places-api', OsCustomFieldsHelper::get_google_places_api_url(), false, null, [
					'strategy'  => 'async',
					'in_footer' => true
				] );
			}
		}


	}

endif;
if ( in_array( 'latepoint/latepoint.php', get_option( 'active_plugins', array() ) ) || array_key_exists( 'latepoint/latepoint.php', get_site_option( 'active_sitewide_plugins', array() ) ) ) {
	$LATEPOINT_ADDON_PRO_FEATURES = new LatePointAddonProFeatures();
}
$latepoint_session_salt = 'ZTQ5NDMwODEtNjBmOS00ZjEzLThiM2UtYjgyMDhhYzdiODg3';
