<?php
/**
 * Pos Purchase Item Database Model
 *
 * @package Vitepos\Models\Database
 */

namespace Vitepos\Models\Database;

use VitePos\Core\ViteposModel;

/**
 * Class Mapbd_pos_purchase_item
 *
 * @properties id,purchase_id,product_id,purchase_cost,stock_quantity,product_name,in_stock,bar_code,total_cost
 */
class Mapbd_Pos_Purchase_Item extends ViteposModel {
	/**
	 * Its property id
	 *
	 * @var int
	 */
	public $id;
	/**
	 * Its property purchase_id
	 *
	 * @var int
	 */
	public $purchase_id;
	/**
	 * Its property product_id
	 *
	 * @var int
	 */
	public $product_id;
	/**
	 * Its property purchase_cost
	 *
	 * @var float
	 */
	public $purchase_cost;
	/**
	 * Its property purchase_cost
	 *
	 * @var float
	 */
	public $prev_purchase_cost;
	/**
	 * Its property stock_quantity
	 *
	 * @var int
	 */
	public $stock_quantity;
	/**
	 * Its property product_name
	 *
	 * @var string
	 */
	public $product_name;
	/**
	 * Its property in_stock
	 *
	 * @var bool
	 */
	public $in_stock;
	/**
	 * Its property bar_code
	 *
	 * @var int
	 */
	public $bar_code;
	/**
	 * Its property total_cost
	 *
	 * @var float
	 */
	public $total_cost;


	/**
	 * Mapbd_pos_purchase_item constructor.
	 */
	public function __construct() {

		parent::__construct();
		$this->set_validation();
		$this->table_name     = 'apbd_pos_purchase_item';
		$this->primary_key    = 'id';
		$this->unique_key     = array();
		$this->multi_key      = array();
		$this->auto_inc_field = array( 'id' );
		$this->app_base_name  = 'apbd-elite-pos';

	}


	/**
	 * The set validation is generated by appsbd
	 */
	public function set_validation() {

		$this->validations = array(
			'id'                 => array(
				'Text' => 'Id',
				'Rule' => 'max_length[11]|integer',
			),
			'purchase_id'        => array(
				'Text' => 'Purchase Id',
				'Rule' => 'max_length[11]|integer',
			),
			'product_id'         => array(
				'Text' => 'Product Id',
				'Rule' => 'required|max_length[11]|integer',
			),
			'purchase_cost'      => array(
				'Text' => 'Purchase Cost',
				'Rule' => 'max_length[11]|numeric|min[0]',
			),
			'prev_purchase_cost' => array(
				'Text' => 'Purchase Cost',
				'Rule' => 'max_length[11]|numeric',
			),
			'stock_quantity'     => array(
				'Text' => 'Stock Quantity',
				'Rule' => 'max_length[9]|numeric|min[0]',
			),
			'product_name'       => array(
				'Text' => 'Product Name',
				'Rule' => 'max_length[255]',
			),
			'in_stock'           => array(
				'Text' => 'In Stock',
				'Rule' => 'max_length[11]|numeric',
			),
			'bar_code'           => array(
				'Text' => 'Bar Code',
				'Rule' => 'max_length[255]',
			),
			'total_cost'         => array(
				'Text' => 'Total Cost',
				'Rule' => 'max_length[11]|numeric|min[0]',
			),

		);
	}

	/**
	 * The get purchase history is generated by appsbd
	 *
	 * @param any    $product_id Its product id param.
	 * @param string $outlet_id Its outlet id param.
	 * @param string $limit its Limit param.
	 *
	 * @return false|Mapbd_Pos_Purchase_Item[]
	 */
	public static function get_purchase_history( $product_id, $outlet_id = '', $limit = '' ) {
		$purchase_item = new Mapbd_Pos_Purchase_Item();
		$purchase_item->product_id( $product_id );
		$purchase = new Mapbd_Pos_Purchase();
		if ( ! empty( $outlet_id ) ) {
			$purchase->warehouse_id( $outlet_id );
		}
		$outlets = Mapbd_Pos_Warehouse::fetch_all_key_value( 'id', 'name' );
		$purchase_item->join( $purchase, 'id', 'purchase_id' );
		$history = $purchase_item->select_all_grid_data( 'product_id,stock_quantity,purchase_cost,prev_purchase_cost,purchase_date,warehouse_id,added_by', 'purchase_id', 'DESC', $limit );
		foreach ( $history as &$item ) {
			$item->outlet_name = appsbd_get_text_by_key( $item->warehouse_id, $outlets );
		}

		return $history;
	}

	/**
	 * The create db table is generated by appsbd
	 */
	public static function create_db_table() {

		$this_obj = new static();
		$table    = $this_obj->db->prefix . $this_obj->table_name;
		if ( $this_obj->db->get_var( "show tables like '{$table}'" ) != $table ) {
			$sql = "CREATE TABLE `{$table}` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `purchase_id` int(11) unsigned NOT NULL,
					  `product_id` int(11) unsigned NOT NULL,
					  `purchase_cost` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
					  `prev_purchase_cost` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
					  `stock_quantity` decimal(8,2) unsigned NOT NULL DEFAULT 0.00,
					  `product_name` char(255) NOT NULL DEFAULT '',
					  `in_stock` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
					  `bar_code` char(255) NOT NULL DEFAULT '',
					  `total_cost` decimal(10,2) unsigned DEFAULT 0.00,
					  PRIMARY KEY (`id`)
					) ";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}


}

