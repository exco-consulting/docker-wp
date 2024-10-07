<?php
/**
 * Pos Vendor Database Model
 *
 * @package Vitepos\Models\Database
 */

namespace Vitepos\Models\Database;

use VitePos\Core\ViteposModel;

/**
 * Class Mapbd_pos_vendor
 *
 * @properties id,name,email,contact_no,vendor_note,status,added_by
 */
class Mapbd_Pos_Table extends ViteposModel {
	/**
	 * Its property id
	 *
	 * @var int
	 */
	public $id;
	/**
	 * Its property title
	 *
	 * @var string
	 */
	public $title;
	/**
	 * Its property title
	 *
	 * @var string
	 */
	public $type;
	/**
	 * Its property title
	 *
	 * @var string
	 */
	public $image;
	/**
	 * Its property title
	 *
	 * @var string
	 */
	public $des;
	/**
	 * Its property title
	 *
	 * @var int
	 */
	public $seat_cap;
	/**
	 * Its property status
	 *
	 * @var bool
	 */
	public $is_reserved;
	/**
	 * Its property status
	 *
	 * @var bool
	 */
	public $is_mergeable;
	/**
	 * Its property outlet_id
	 *
	 * @var integer
	 */
	public $outlet_id;
	/**
	 * Its property assigned_waiters
	 *
	 * @var string
	 */
	public $assigned_waiters;
	/**
	 * Its property status
	 *
	 * @var bool
	 */
	public $status;
	/**
	 * Its property added_by
	 *
	 * @var string
	 */
	public $added_by;
	/**
	 * Its property tables
	 *
	 * @var array
	 */
	protected static $tables = array();

	/**
	 * Mapbd_pos_vendor constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_validation();
		$this->table_name     = 'apbd_pos_table';
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
			'id'               => array(
				'Text' => 'Id',
				'Rule' => 'max_length[11]|integer',
			),
			'title'            => array(
				'Text' => 'Title',
				'Rule' => 'required|max_length[255]',
			),
			'type'             => array(
				'Text' => 'Type',
				'Rule' => 'max_length[1]',
			),
			'des'              => array(
				'Text' => 'Description',
				'Rule' => 'max_length[255]',
			),
			'seat_cap'         => array(
				'Text' => 'Seat Capabilities',
				'Rule' => 'max_length[11]|integer',
			),
			'image'            => array(
				'Text' => 'Image',
				'Rule' => 'max_length[255]',
			),
			'is_reserved'      => array(
				'Text' => 'Is Reserved',
				'Rule' => 'max_length[1]',
			),
			'is_mergeable'     => array(
				'Text' => 'Is Mergeable',
				'Rule' => 'max_length[1]',
			),
			'outlet_id'        => array(
				'Text' => 'Outlet id',
				'Rule' => 'required|max_length[11]|integer',
			),
			'assigned_waiters' => array(
				'Text' => 'Assigned waiters',
				'Rule' => 'max_length[255]',
			),
			'status'           => array(
				'Text' => 'Status',
				'Rule' => 'max_length[1]',
			),
			'added_by'         => array(
				'Text' => 'Added By',
				'Rule' => 'required|max_length[11]|integer',
			),

		);
	}

	/**
	 * The get property raw options is generated by appsbd
	 *
	 * @param \Appsbd\V1\Core\any $property Its string.
	 * @param false               $is_with_select Its bool.
	 *
	 * @return array|string[]
	 */
	public function get_property_raw_options( $property, $is_with_select = false ) {
		$return_obj = array();
		switch ( $property ) {
			case 'type':
				$return_obj = array(
					'T' => 'Table',
					'P' => 'Parcel',
				);
				break;
			case 'is_reserved':
				$return_obj = array(
					'Y' => 'Yes',
					'N' => 'No',
				);
				break;
			case 'is_mergeable':
				$return_obj = array(
					'Y' => 'Yes',
					'N' => 'No',
				);
				break;
			case 'status':
				$return_obj = array(
					'A' => 'Active',
					'I' => 'Inactive',
				);
				break;
			default:
		}
		if ( $is_with_select ) {
			return array_merge( array( '' => 'Select' ), $return_obj );
		}

		return $return_obj;

	}
	/**
	 * The check before set data is generated by appsbd
	 *
	 * @param mixed $key Its the property.
	 * @param mixed $value Its the value.
	 *
	 * @return bool
	 */
	public function check_before_set_data( $key, &$value ) {
		if ( 'assigned_waiters' == $key && is_array( $value ) ) {
			$value = '';
			return false;
		}
		return true;
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
  					`title` char(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  					`type` char(1) NOT NULL DEFAULT 'T' COMMENT 'radio(T=Table,P=Parcel)',
  					`seat_cap` int(11) unsigned NOT NULL,
  					`image` char(255) NOT NULL DEFAULT '',
  					`des` text  COMMENT 'textarea',
  					`is_reserved` char(1) NOT NULL DEFAULT 'N' COMMENT 'bool(Y=Yes,N=No)',
  					`is_mergeable` char(1) NOT NULL DEFAULT 'N' COMMENT 'bool(Y=Yes,N=No)',
  					`outlet_id` int(11) NOT NULL,
  					`assigned_waiters` char(255) NOT NULL DEFAULT '',
  					`status` char(1) NOT NULL DEFAULT 'A' COMMENT 'bool(A=Active,I=Inactive)',
  					`added_by` int(11) unsigned NOT NULL,
  					PRIMARY KEY (`id`)
					) ";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}

	/**
	 * The delete by id is generated by appsbd
	 *
	 * @param any $id Its vendor id param.
	 *
	 * @return bool
	 */
	public static function delete_by_id( $id ) {
		return parent::delete_by_key_value( 'id', $id );
	}

	/**
	 * The get table by id is generated by appsbd
	 *
	 * @param mixed $table_id Table id.
	 * @param array $props Props.
	 *
	 * @return mixed|self|null
	 */
	public static function get_table_by_id( $table_id, $props = array() ) {
		if ( empty( self::$tables ) ) {
			$obj          = new self();
			self::$tables = $obj->select_all_with_identity( 'id' );
		}
		if ( ! empty( self::$tables[ $table_id ] ) ) {
			if ( empty( $props ) || ! is_array( $props ) ) {
				$res = self::$tables[ $table_id ];
			} else {
				$res = new \stdClass();
				foreach ( $props as $prop ) {
					if ( isset( self::$tables[ $table_id ]->{$prop} ) ) {
						$res->{$prop} = self::$tables[ $table_id ]->{$prop};
					}
				}
			}
			return $res;
		}
		return null;
	}

	/**
	 * The get table by ids is generated by appsbd
	 *
	 * @param mixed $table_ids Table ids.
	 * @param array $props Props.
	 *
	 * @return self []
	 */
	public static function get_table_by_ids( $table_ids, $props = array() ) {
		$resp = array();
		foreach ( $table_ids as $table_id ) {
			$resp[] = self::get_table_by_id( $table_id, $props );
		}
		return $resp;
	}
}
