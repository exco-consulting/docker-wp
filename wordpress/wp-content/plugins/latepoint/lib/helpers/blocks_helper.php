<?php

class OsBlockHelper {
	public static function register_blocks() {
		self::register_latepoint_category();
		self::register_block_book_button();
		self::register_block_book_form();
		self::register_block_list_of_resources();
		self::register_block_calendar();
		self::register_block_customer_dashboard();
		self::register_block_customer_login();
	}

	public static function localized_vars_for_blocks() : array {

		$has_to_pick = [ 'label' => __('Customer will pick', 'latepoint'), 'value' => '' ];
		$localized_block_vars = [];

		// AGENTS
		$localized_block_vars['agents'] = [];
		$localized_block_vars['selected_agents_options'][] = $has_to_pick;
		$localized_block_vars['selected_agents_options'][] = [ 'label' => __('Any Available', 'latepoint'), 'value' => LATEPOINT_ANY_AGENT ];
		$agents = new OsAgentModel();
		$agents = $agents->get_results_as_models();
		if($agents){
			foreach($agents as $agent){
				$localized_block_vars['selected_agents_options'][] = ['label' => $agent->full_name, 'value' => $agent->id];
				$localized_block_vars['agents'][] = [
					'name' => $agent->full_name,
					'id' => $agent->id,
					'title' => $agent->title,
					'short_description' => $agent->short_description,
					'avatar_url' => empty($agent->avatar_image_id) ? '' : $agent->get_avatar_url()];
			}
		}

		// SERVICES
		$localized_block_vars['services'] = [];
		$localized_block_vars['selected_services_options'][] = $has_to_pick;
		$services = new OsServiceModel();
		$services = $services->get_results_as_models();
		if($services){
			foreach($services as $service){
				$localized_block_vars['selected_services_options'][] = ['label' => $service->name, 'value' => $service->id];
				$localized_block_vars['services'][] = [
					'name' => $service->name,
					'id' => $service->id,
					'image_url' => empty($service->description_image_id) ? '' : $service->get_description_image_url(),
					'description' => $service->short_description,
					'category_id' => $service->category_id
				];
			}
		}


		// SERVICE CATEGORIES
		$localized_block_vars['selected_service_categories_options'][] = [ 'label' => __('Show All', 'latepoint'), 'value' => '' ];
		$service_categories = new OsServiceCategoryModel();
		$service_categories = $service_categories->get_results_as_models();
		if($service_categories){
			foreach($service_categories as $service_category){
				$localized_block_vars['selected_service_categories_options'][] = ['label' => $service_category->name, 'value' => $service_category->id];
			}
		}


		// LOCATIONS
		$localized_block_vars['locations'] = [];
		$localized_block_vars['selected_locations_options'][] = $has_to_pick;
		$localized_block_vars['selected_locations_options'][] = [ 'label' => __('Any Available', 'latepoint'), 'value' => LATEPOINT_ANY_LOCATION ];
		$locations = new OsLocationModel();
		$locations = $locations->get_results_as_models();
		if($locations){
			foreach($locations as $location){
				$localized_block_vars['selected_locations_options'][] = ['label' => $location->name, 'value' => $location->id];
				$localized_block_vars['locations'][] = [
					'name' => $location->name,
					'id' => $location->id,
					'category_id' => $location->category_id];
			}
		}
		return $localized_block_vars;
	}

	public static function register_latepoint_category() {
		add_filter('block_categories_all', function ($categories) {
			// Adding a new category.
			$categories[] = [
				'slug' => 'latepoint',
				'title' => 'LatePoint',
			];
			return $categories;
		});
	}

	public static function register_block_book_button() {
		register_block_type(LATEPOINT_ABSPATH . 'blocks/build/book-button/block.json',
			[
				'render_callback' => 'OsBlockHelper::render_book_button',
				'editor_script_handles' => ['latepoint-block-book-button']
			]);
	}

	public static function register_block_book_form() {
		register_block_type(LATEPOINT_ABSPATH . 'blocks/build/book-form/block.json',
			[
				'render_callback' => 'OsBlockHelper::render_book_form',
				'editor_script_handles' => ['latepoint-block-book-form']
			]);
	}

	public static function register_block_list_of_resources() {
		register_block_type(LATEPOINT_ABSPATH . 'blocks/build/list-of-resources/block.json',
			[
				'render_callback' => 'OsBlockHelper::render_list_of_resources',
				'editor_script_handles' => ['latepoint-block-list-of-resources']
			]);
	}


	public static function register_block_calendar(): void {
		register_block_type( LATEPOINT_ABSPATH . 'blocks/build/calendar/block.json',
			[
				'render_callback'       => 'OsBlockHelper::render_calendar',
				'editor_script_handles' => [ 'latepoint-block-calendar' ]
			] );
	}

	public static function register_block_customer_dashboard(): void {
		register_block_type( LATEPOINT_ABSPATH . 'blocks/build/customer-dashboard/block.json',
			[
				'render_callback'       => 'OsBlockHelper::render_customer_dashboard',
				'editor_script_handles' => [ 'latepoint-block-customer-dashboard' ]
			] );
	}

	public static function register_block_customer_login(): void {
		register_block_type( LATEPOINT_ABSPATH . 'blocks/build/customer-login/block.json',
			[
				'render_callback'       => 'OsBlockHelper::render_customer_login',
				'editor_script_handles' => [ 'latepoint-block-customer-login' ]
			] );
	}


	public static function render_book_button($attributes, $content) {
		return do_shortcode('[latepoint_book_button ' . self::attributes_to_data_params($attributes) . ']');
	}

	public static function render_book_form($attributes, $content) {
		return do_shortcode('[latepoint_book_form ' . self::attributes_to_data_params($attributes) . ']');
	}

	public static function render_list_of_resources($attributes, $content) {
		return do_shortcode('[latepoint_resources ' . self::attributes_to_data_params($attributes) . ']');
	}

	public static function render_calendar($attributes, $content) {
		return do_shortcode('[latepoint_calendar ' . self::attributes_to_data_params($attributes) . ']');
	}

	public static function render_customer_dashboard($attributes, $content) {
		return do_shortcode('[latepoint_customer_dashboard ' . self::attributes_to_data_params($attributes) . ']');
	}

	public static function render_customer_login($attributes, $content) {
		return do_shortcode('[latepoint_customer_login ' . self::attributes_to_data_params($attributes) . ']');
	}

	public static function attributes_to_data_params(array $attributes) {
		$data_html = '';
		foreach ($attributes as $name => $value) {
			if ($value === true) $value = 'yes';
			if ($value === false) $value = 'no';
			$data_html .= $name . '="' . esc_attr($value) . '" ';
		}
		return $data_html;
	}
}