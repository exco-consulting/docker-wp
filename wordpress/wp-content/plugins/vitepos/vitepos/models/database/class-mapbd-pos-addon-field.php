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
class Mapbd_Pos_Addon_Field extends ViteposModel {
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
	 * Its property type
	 *
	 * @var string
	 */
	public $type;
	/**
	 * Its property des
	 *
	 * @var string
	 */
	public $des;
	/**
	 * Its property des
	 *
	 * @var string
	 */
	public $def_value;
	/**
	 * Its property placeholder
	 *
	 * @var string
	 */
	public $placeholder;
	/**
	 * Its property is_required
	 *
	 * @var bool
	 */
	public $is_required;
	/**
	 * Its property is_required
	 *
	 * @var int
	 */
	public $field_limit;
	/**
	 * Its property status
	 *
	 * @var bool
	 */
	public $status;
	/**
	 * Its property addon_id
	 *
	 * @var int
	 */
	public $addon_id;


	/**
	 * Mapbd_pos_vendor constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_validation();
		$this->table_name     = 'apbd_pos_addon_field';
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
			'id'          => array(
				'Text' => 'Id',
				'Rule' => 'max_length[11]|integer',
			),
			'title'       => array(
				'Text' => 'Title',
				'Rule' => 'required|max_length[150]',
			),
			'type'        => array(
				'Text' => 'Type',
				'Rule' => 'max_length[1]',
			),
			'des'         => array(
				'Text' => 'Description',
				'Rule' => 'max_length[250]',
			),
			'def_value'   => array(
				'Text' => 'Default Value',
				'Rule' => 'max_length[250]',
			),
			'placeholder' => array(
				'Text' => 'Placeholder',
				'Rule' => 'max_length[150]',
			),
			'is_required' => array(
				'Text' => 'Is Required',
				'Rule' => 'max_length[1]',
			),
			'field_limit' => array(
				'Text' => 'Field Limit',
				'Rule' => 'max_length[11]|integer',
			),
			'status'      => array(
				'Text' => 'Status',
				'Rule' => 'max_length[1]',
			),
			'addon_id'    => array(
				'Text' => 'Addon Id',
				'Rule' => 'max_length[11]|integer',
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
					'D' => 'Dropdown',
					'C' => 'Checkbox',
					'R' => 'Radio',
					'T' => 'Textbox',
					'M' => 'Textarea',
					'N' => 'Numberbox',
				);
				break;
			case 'is_required':
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
	 * The create db table is generated by appsbd
	 */
	public static function create_db_table() {
		$this_obj = new static();
		$table    = $this_obj->db->prefix . $this_obj->table_name;
		if ( $this_obj->db->get_var( "show tables like '{$table}'" ) != $table ) {
			$sql = "CREATE TABLE `{$table}` (
					  	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 	 					`title` char(150) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  						`type` char(1) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'T' COMMENT 'radio(D=Dropdown,C=Checkbox,R=Radio,T=Textbox,M=Textarea,N=Numberbox)',
  						`des` char(255) NOT NULL DEFAULT '',
  						`def_value` char(255) NOT NULL DEFAULT '',
  						`placeholder` char(150) NOT NULL DEFAULT '',
  						`is_required` char(1) NOT NULL DEFAULT 'N' COMMENT 'bool(Y=Yes,N=No)',
  						`field_limit` int(11) unsigned NOT NULL,
  						`addon_id` int(11) unsigned NOT NULL,
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
		if ( parent::delete_by_key_value( 'id', $id ) ) {
			Mapbd_Pos_Addon_Field_Option::delete_by_field_id( $id );

			return true;
		}

		return false;
	}

	/**
	 * The delete by id is generated by appsbd
	 *
	 * @param any $addon_id Its vendor id param.
	 *
	 * @return bool
	 */
	public static function delete_by_addon_id( $addon_id ) {
		$fields     = self::find_all_by( 'addon_id', $addon_id );
		$is_deleted = true;
		foreach ( $fields as $field ) {
			if ( ! self::delete_by_id( $field->id ) ) {
				$is_deleted = false;
			}
		}
		return $is_deleted;
	}
	/**
	 * The delete by id is generated by appsbd
	 *
	 * @param any $addon_id Its vendor id param.
	 *
	 * @return bool
	 */
	public static function get_all_field_by( $addon_id ) {
		$fields = self::find_all_grid_data_by( 'addon_id', $addon_id );
		foreach ( $fields as &$field ) {
			$field->options = array();
			$field->type    = strtoupper( $field->type );
			if ( in_array( $field->type, array( 'D', 'C', 'R' ) ) ) {   				$field->options = Mapbd_Pos_Addon_Field_Option::find_all_grid_data_by( 'field_id', $field->id );
			}
		}
		return $fields;
	}
}
