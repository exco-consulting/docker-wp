<?php
/**
 * Pos Vendor Database Model
 *
 * @package Vitepos\Models\Database
 */

namespace Vitepos\Models\Database;

use VitePos\Core\ViteposModel;
use VitePos\Modules\POS_Settings;

/**
 * Class Mapbd_pos_vendor
 *
 * @properties id,name,email,contact_no,vendor_note,status,added_by
 */
class Mapbd_Pos_Custom_Field extends ViteposModel {
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
	public $label;
	/**
	 * Its property type
	 *
	 * @var string
	 */
	public $type;
	/**
	 * Its property is half field
	 *
	 * @var bool
	 */
	public $is_half_field;
	/**
	 * Its property des
	 *
	 * @var string
	 */
	public $show_where;
	/**
	 * Its property placeholder
	 *
	 * @var string
	 */
	public $help_text;
	/**
	 * Its property is_required
	 *
	 * @var bool
	 */
	public $is_calculable;
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
	public $fld_limit;
	/**
	 * Its property status
	 *
	 * @var bool
	 */
	public $status;
	/**
	 * Its property options
	 *
	 * @var array
	 */
	public $options;
	/**
	 * Its property options
	 *
	 * @var string
	 */
	public $operator;
	/**
	 * Its property options
	 *
	 * @var string
	 */
	public $position;
	/**
	 * Its property field orders
	 *
	 * @var int
	 */
	public $fld_order;
	/**
	 * Its property field orders
	 *
	 * @var int
	 */
	public $param;


	/**
	 * Mapbd_pos_vendor constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_validation();
		$this->table_name     = 'apbd_pos_custom_field';
		$this->primary_key    = 'id';
		$this->unique_key     = array();
		$this->multi_key      = array();
		$this->auto_inc_field = array( 'id' );
		$this->app_base_name  = 'apbd-elite-pos';

	}

	/**
	 * The select is generated by appsbd
	 *
	 * @param string $select its select param.
	 * @param false  $add_field_error its add_field_error param.
	 *
	 * @return bool
	 */
	public function select( $select = '', $add_field_error = false ) {
		$is_selected = parent::select( $select, $add_field_error );
		if ( $is_selected ) {
			if ( ! empty( $this->options ) && is_string( $this->options ) ) {
				$this->options = unserialize( $this->options );
			}
		}

		return $is_selected;
	}
	/**
	 * The set validation is generated by appsbd
	 */
	public function set_validation() {
		$this->validations = array(
			'id'            => array(
				'Text' => 'Id',
				'Rule' => 'max_length[11]|integer',
			),
			'label'         => array(
				'Text' => 'Title',
				'Rule' => 'required|max_length[150]',
			),
			'type'          => array(
				'Text' => 'Type',
				'Rule' => 'max_length[1]',
			),
			'is_half_field' => array(
				'Text' => 'Half field',
				'Rule' => 'max_length[1]',
			),
			'show_where'    => array(
				'Text' => 'Show Where',
				'Rule' => 'max_length[1]',
			),
			'help_text'     => array(
				'Text' => 'Placeholder',
				'Rule' => 'max_length[255]',
			),
			'is_required'   => array(
				'Text' => 'Is Required',
				'Rule' => 'max_length[1]',
			),
			'fld_limit'     => array(
				'Text' => 'Field Limit',
				'Rule' => 'max_length[3]|integer',
			),
			'fld_order'     => array(
				'Text' => 'Field Order',
				'Rule' => 'max_length[3]|integer',
			),
			'operator'      => array(
				'Text' => 'Status',
				'Rule' => 'max_length[2]',
			),
			'status'        => array(
				'Text' => 'Status',
				'Rule' => 'max_length[1]',
			),
			'param'        => array(
				'Text' => 'Param',
				'Rule' => 'max_length[255]',
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
					'D' => 'Date',
					'W' => 'Dropdown',
					'C' => 'Checkbox',
					'R' => 'Radio',
					'T' => 'Textbox',
					'M' => 'Textarea',
					'N' => 'Numeric',
					'S' => 'Switch',
					'U' => 'URL Input',
				);
				break;
			case 'is_required':
				$return_obj = array(
					'Y' => 'Yes',
					'N' => 'No',
				);
				break;
			case 'is_half_field':
				$return_obj = array(
					'Y' => 'Yes',
					'N' => 'No',
				);
				break;
			case 'is_half_field':
				$return_obj = array(
					'Y' => 'Yes',
					'N' => 'No',
				);
				break;
			case 'show_where':
				$return_obj = array(
					'C' => 'Customer Add',
					'U' => 'User Add',
					'I' => 'Invoice',
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
  						`label` char(150) CHARACTER SET utf8 NOT NULL DEFAULT '',
  						`type` char(1) CHARACTER SET utf8 NOT NULL DEFAULT 'T' COMMENT 'radio(T=Textbox,M=Textarea,N=Numeric,D=Date,S=Switch,R=Radio,W=Dropdown,U=URL Input,C=Checkbox)',
  						`is_half_field` char(1) CHARACTER SET utf8 NOT NULL DEFAULT 'Y' COMMENT 'radio(Y=Yes,N=No)',
  						`is_required` char(1) CHARACTER SET utf8 NOT NULL DEFAULT 'N' COMMENT 'radio(Y=Yes,N=No)',
  						`status` char(1) NOT NULL DEFAULT 'A' COMMENT 'radio(A=Active,I=Inactive)',
  						`help_text` char(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  						`is_calculable` char(1) CHARACTER SET utf8 NOT NULL DEFAULT 'N' COMMENT 'radio(Y=Yes,N=No)',
  						`show_where` char(1) CHARACTER SET utf8 NOT NULL DEFAULT 'C' COMMENT 'radio(C=Customer Add, U=User Add, I=Invoice)',
  						`options` text CHARACTER SET utf8,
  						`fld_order` int(3) unsigned NOT NULL,
  						`fld_limit` int(3) unsigned NOT NULL,
  						`operator` char(2) CHARACTER SET utf8 DEFAULT '',
  						`position` char(1) CHARACTER SET utf8 NOT NULL DEFAULT 'A' COMMENT 'radio(A=Above The Buttons,B=Below The Buttons,I=Into The Buttons)',
  						`param` varchar(255) NOT NULL DEFAULT '',  						
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
	 * The delete by id is generated by appsbd
	 *
	 * @param any $field_id Its vendor id param.
	 *
	 * @return bool
	 */
	public static function get_all_field_by( $field_id ) {
		$fields = self::find_all_grid_data_by( 'id', $field_id );
		foreach ( $fields as &$field ) {
			$field->options = array();
			$field->type    = strtoupper( $field->type );
		}
		return $fields;
	}

	/**
	 * The validate custom fields is generated by appsbd
	 *
	 * @param mixed $custom_fields Its custom_fields param.
	 * @param mixed $show_where Its show_where param.
	 *
	 * @return bool
	 */
	public static function validate_custom_fields( $custom_fields, $show_where ) {
		$fields = self::find_all_grid_data_by( 'show_where', $show_where, array( 'status' => 'A' ) );
		$is_ok  = true;
		foreach ( $fields as $field ) {
			if ( 'Y' == $field->is_required && ! array_key_exists( $field->id, $custom_fields ) ) {
				POS_Settings::get_module_instance()->add_error( '%s field is required', $field->label );
				$is_ok = false;
			} else {
				foreach ( $custom_fields as $key => $fld ) {
					if ( 'Y' == $field->is_required && $field->id == $key && empty( $fld ) ) {
						POS_Settings::get_module_instance()->add_error( '%s field is required', $field->label );
						$is_ok = false;
					}
				}
			}
		}
		return $is_ok;
	}

	/**
	 * The save is generated by appsbd
	 *
	 * @return bool
	 */
	public function save() {
		$this->fld_order( $this->get_new_inc_id( 'fld_order', 1 ) );
		return parent::save();
	}
}
