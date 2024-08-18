<?php
/**
 * Its api for product
 *
 * @since: 12/07/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos_Lite\Api\V1
 */

namespace VitePos_Lite\Api\V1;

use Appsbd_Lite\V1\libs\API_Data_Response;
use Appsbd_Lite\V1\libs\AppInput;
use VitePos_Lite\Libs\API_Base;
use VitePos_Lite\Libs\POS_Product;
use VitePos_Lite\Modules\POS_Settings;

/**
 * Class pos_product_api
 *
 * @package VitePos_Lite\Api\V1
 */
class Pos_Product_Api extends API_Base {

	/**
	 * The set api base is generated by appsbd
	 *
	 * @return mixed|string
	 */
	public function set_api_base() {
		return 'product';
	}

	/**
	 * The routes is generated by appsbd
	 *
	 * @return mixed|void
	 */
	public function routes() {
		$this->register_rest_route( 'POST', 'list', array( $this, 'product_list' ) );
		$this->register_rest_route( 'POST', 'scan-product', array( $this, 'scan_product' ) );
		$this->register_rest_route( 'POST', 'list-variation', array( $this, 'product_with_variation_list' ) );
		$this->register_rest_route( 'GET', 'categories', array( $this, 'categories' ) );
		$this->register_rest_route( 'GET', 'all-categories', array( $this, 'all_categories' ) );
		$this->register_rest_route( 'GET', 'all-taxes', array( $this, 'all_taxes' ) );
		$this->register_rest_route( 'GET', 'attributes', array( $this, 'attributes' ) );
		$this->register_rest_route( 'GET', 'getStock/(?P<id>\d+)', array( $this, 'getStock' ) );
		$this->register_rest_route( 'GET', 'details/(?P<id>\d+)', array( $this, 'product_details' ) );
	}

	/**
	 * The set route permission is generated by appsbd
	 *
	 * @param \VitePos_Lite\Libs\any $route Its string.
	 *
	 * @return bool
	 */
	public function set_route_permission( $route ) {
		switch ( $route ) {
			case 'update':
			case 'create':
			case 'delete-product':
				return false;
			default:
				break;
		}

		return parent::set_route_permission( $route );
	}

	/**
	 * The query search filter is generated by appsbd
	 *
	 * @param \VitePos_Lite\Libs\any $where Its string.
	 * @param \VitePos_Lite\Libs\any $wp_query Its string.
	 *
	 * @return mixed|string|\VitePos_Lite\Libs\any
	 */
	public function query_search_filter( $where, $wp_query ) {
		$api_src = $wp_query->get( 'api_src' );
		if ( ! empty( $api_src ) ) {
			foreach ( $api_src as $src_item ) {
				if ( ! empty( $src_item ) ) {
					$where .= $src_item;
				}
			}
		}
		return $where;
	}
	/**
	 * The product list is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function scan_product() {
		self::set_vite_pos_request();
		$barcode   = $this->get_payload( 'barcode', '' );
		$cart_item = vitepos_get_product_by_barcode( $barcode );
		$this->response->set_response( ! empty( $cart_item ), '', $cart_item );
		return $this->response;
	}
	/**
	 * The product list is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function product_list() {
		self::set_vite_pos_request();
		$page                 = $this->get_payload( 'page', 1 );
		$limit                = $this->get_payload( 'limit', 20 );
		$src_props            = $this->get_payload( 'src_by', array() );
		$sort_by_props        = $this->get_payload( 'sort_by', array() );
		$response_product     = POS_Product::get_product_from_woo_products( $page, $limit, $src_props, $sort_by_props );
		$response_data        = new API_Data_Response();
		$response_data->page  = $page;
		$response_data->limit = $limit;

		if ( $response_data->set_total_records( $response_product->records ) ) {
			$response_data->rowdata = $response_product->products;
		}
		$this->response->set_response( true, '', $response_data );

		return $this->response;
	}

	/**
	 * The product list is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function product_with_variation_list() {
		self::set_vite_pos_request();
		$page                 = $this->get_payload( 'page', 1 );
		$limit                = $this->get_payload( 'limit', 20 );
		$src_props            = $this->get_payload( 'src_by', array() );
		$sort_by_props        = $this->get_payload( 'sort_by', array() );
		$response_product     = POS_Product::get_product_from_woo_products_with_variations(
			$page,
			$limit,
			$src_props,
			$sort_by_props
		);
		$response_data        = new API_Data_Response();
		$response_data->page  = $page;
		$response_data->limit = $limit;

		if ( $response_data->set_total_records( $response_product->records ) ) {
			$response_data->rowdata = $response_product->products;
		}
		$this->response->set_response( true, '', $response_data );

		return $this->response;
	}

	/**
	 * The categories is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function categories() {
		$response_product = POS_Product::get_categories();
		$this->response->set_response( true, '', $response_product );

		return $this->response;
	}
	/**
	 * The categories is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function all_categories() {
		$response_product = POS_Product::get_categories( true );
		$this->response->set_response( true, '', $response_product );

		return $this->response;
	}
	/**
	 * The categories is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function all_taxes() {
		$this->response->set_response( true, '', array() );
		return $this->response;
	}

	/**
	 * The attributes is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function attributes() {
		$response_product = array();
		$attrs_product    = wc_get_attribute_taxonomies();
		foreach ( $attrs_product as $attr ) {
			$attr_item          = new \stdClass();
			$attr_item->id      = $attr->attribute_id;
			$attr_item->name    = $attr->attribute_label;
			$attr_item->slug    = wc_attribute_taxonomy_name( $attr->attribute_name );
			$attr_item->visible = ! empty( $attr->attribute_public );
			$attr_item->options = array();
			$terms              = get_terms(
				array(
					'taxonomy'   => $attr_item->slug,
					'hide_empty' => false,
				)
			);
			foreach ( $terms as $term ) {
				$term_item            = new \stdClass();
				$term_item->id        = $term->term_id;
				$term_item->name      = $term->name;
				$term_item->slug      = $term->slug;
				$attr_item->options[] = $term_item;
			}
			$response_product[] = $attr_item;
		}
		$this->response->set_response( true, '', $response_product );

		return $this->response;
	}

	/**
	 * The getProductStockById is generated by appsbd
	 *
	 * @param any $id Its integer.
	 *
	 * @return \stdClass|null
	 */
	private function get_product_stock_by_id( $id ) {
		$product = wc_get_product( $id );
		if ( ! empty( $product ) ) {
			$product_obj                   = new \stdClass();
			$product_obj->id               = $product->get_id();
			$product_obj->name             = $product->get_name();
			$product_obj->stock_quantity   = $product->get_stock_quantity();
			$product_obj->low_stock_amount = $product->get_low_stock_amount();
			$product_obj->manage_stock     = $product->get_manage_stock();

			return $product_obj;
		}

		return null;
	}

	/**
	 * The setAttributes is generated by appsbd
	 *
	 * @param any $args Its string.
	 * @param any $type Its string.
	 *
	 * @return array
	 */
	public function get_attributes( $args, $type ) {
		$pos        = 0;
		$attributes = array();
		foreach ( $args as $attr ) {
			$attribute_object = new \WC_Product_Attribute();
			$opt_arr          = array();
			$attr['id']       = ! empty( $attr['id'] ) ? absint( $attr['id'] ) : $attr['id'];
			if ( ! empty( $attr['id'] ) ) {
				$attribute_object->set_id( wc_attribute_taxonomy_id_by_name( $attr['slug'] ) );
				$attribute_object->set_name( $attr['slug'] );
			} else {
				$attribute_object->set_name( $attr['name'] );

			}
			foreach ( $attr['options'] as $option ) {
				$opt_arr[] = $option['name'];
			}
			if ( 'variable' == $type ) {
				$attribute_object->set_variation( true );
			}
			$attribute_object->set_position( $pos++ );
			$attribute_object->set_options( $opt_arr );
			$attribute_object->set_visible( $attr['visible'] );
			array_push( $attributes, $attribute_object );
		}
		return $attributes;
	}

	/**
	 * The getStock is generated by appsbd
	 *
	 * @param any $data Its string.
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function get_stock( $data ) {
		if ( ! empty( $data['id'] ) ) {
			$id          = intval( $data['id'] );
			$product_obj = $this->get_product_stock_by_id( $id );
			$this->set_response( true, 'data found', $product_obj );

			return $this->response;
		}
		$this->set_response( false, 'data not found or invalid param' );

		return $this->response;
	}

	/**
	 * The delete product is generated by appsbd
	 *
	 *  @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function delete_product() {
		if ( ! empty( $this->payload ) ) {
			$id      = intval( $this->payload['id'] );
			$product = wc_get_product( $id );
			if ( ! empty( $product ) ) {
				if ( $product->is_type( 'variable' ) ) {
					foreach ( $product->get_children() as $child_id ) {
						$this->delete_variationProduct( $child_id );
					}
				}
				if ( $product->delete() ) {
					$this->add_info( 'Successfully deleted' );
					$this->response->set_response( true, '' );
					return $this->response;
				} else {
					$this->add_error( 'Delete failed' );
					$this->response->set_response( false, '' );
					return $this->response;
				}
			} else {
				$this->add_error( 'Delete failed' );
				$this->response->set_response( false, '' );
				return $this->response;
			}
		} else {
			$this->add_error( 'Invalid request' );
			$this->response->set_response( false, '' );
			return $this->response;
		}
	}

	/**
	 * The delete variationProduct is generated by appsbd
	 *
	 * @param any $child_id Its child id param.
	 *
	 * @return mixed
	 */
	public function delete_variationProduct( $child_id ) {
		$child = wc_get_product( $child_id );
		if ( $child ) {
			$child->delete();
			return $child;
		} else {
			$this->add_error( 'invalid requiest' );
		}
	}


	/**
	 * The getProductById is generated by appsbd
	 *
	 * @param any $id Its integer.
	 *
	 * @return POS_Product
	 */
	public function get_product_by_id( $id ) {
		$product     = wc_get_product( $id );
		$pos_product = POS_Product::get_product_data( $product, true );
		return $pos_product;
	}

	/**
	 * The product details is generated by appsbd
	 *
	 * @param any $data Its string.
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function product_details( $data ) {
		if ( ! empty( $data['id'] ) ) {
			$id          = intval( $data['id'] );
			$product_obj = $this->get_product_by_id( $id );
			$this->set_response( true, 'data found', $product_obj );

			return $this->response;
		}
		$this->set_response( false, 'Data not found or invalid param' );

		return $this->response;
	}
}
