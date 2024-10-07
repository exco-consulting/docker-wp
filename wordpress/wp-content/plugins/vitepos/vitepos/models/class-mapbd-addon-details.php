<?php
/**
 * Pos Warehouse Database Model
 *
 * @package Vitepos\Models\Database
 */

namespace Vitepos\Models;

use VitePos\Core\VitePos;
use VitePos\Core\ViteposModel;
use Vitepos\Models\Database\Mapbd_Pos_Addon;
use Vitepos\Models\Database\Mapbd_Pos_Addon_Field;
use Vitepos\Models\Database\Mapbd_Pos_Addon_Field_Option;
use Vitepos\Models\Database\Mapbd_Pos_Addon_Rule;
use Vitepos\Models\Database\Mapbd_Pos_Addon_Rule_Group;

/**
 * Class Mcustom_Page
 *
 * @package Vitepos\Models
 */
class Mapbd_Addon_Details {
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
	 * Its property fields
	 *
	 * @var Mapbd_Pos_Addon_Field[]
	 */
	public $fields = array();
	/**
	 * Its property rule_group
	 *
	 * @var Mapbd_Pos_Addon_Rule_Group[]
	 */
	public $rule_group = array();

	/**
	 * The get details is generated by appsbd
	 *
	 * @param integer $id addon id.
	 * @param false   $with_id field id.
	 *
	 * @return Mapbd_Addon_Details|null
	 */
	public static function get_details( $id, $with_id = false ) {
		$addon = new Mapbd_Pos_Addon();
		$addon->id( $id );
		if ( $addon->select() ) {
			$data_obj           = new self();
			$data_obj->id       = $addon->id;
			$data_obj->title    = $addon->title;
			$data_obj->added_by = $addon->added_by;
			$data_obj->status   = $addon->status;

			$field = new Mapbd_Pos_Addon_Field();
			$field->addon_id( $addon->id );
			if ( $with_id ) {
				$data_obj->fields = $field->select_all_with_identity( 'id' );
			} else {
				$data_obj->fields = $field->select_all_grid_data();
			}

			foreach ( $data_obj->fields as &$field ) {
				$option = new Mapbd_Pos_Addon_Field_Option();
				$option->field_id( $field->id );
				if ( $with_id ) {
					$field->options = $option->select_all_with_identity( 'id' );
				} else {
					$field->options = $option->select_all_grid_data();
				}
			}
			$group = new Mapbd_Pos_Addon_Rule_Group();
			$group->addon_id( $addon->id );
			if ( $with_id ) {
				$data_obj->rule_group = $group->select_all_with_identity( 'id' );
			} else {
				$data_obj->rule_group = $group->select_all_grid_data();
			}
			foreach ( $data_obj->rule_group as &$item ) {
				$rule = new Mapbd_Pos_Addon_Rule();
				$rule->rule_group_id( $item->id );
				if ( $with_id ) {
					$item->rules = $rule->select_all_with_identity( 'id' );
				} else {
					$item->rules = $rule->select_all_grid_data();
				}
			}
			return $data_obj;
		}
		return null;
	}

	/**
	 * The get fields ids is generated by appsbd
	 *
	 * @return array
	 */
	public function get_fields_ids() {
		$fields = array();
		foreach ( $this->fields as $field ) {
			$fields[ $field->id ] = array();
			foreach ( $field->options as $option ) {
				$fields[ $field->id ][] = $option->id;
			}
		}
		return $fields;
	}

	/**
	 * The get groups ids is generated by appsbd
	 *
	 * @return array
	 */
	public function get_groups_ids() {
		$groups = array();
		foreach ( $this->rule_group as $group ) {
			$groups[ $group->id ] = array();
			foreach ( $group->rules as $rule ) {
				$groups[ $group->id ][] = $rule->id;
			}
		}
		return $groups;
	}

	/**
	 * The add update option is generated by appsbd
	 *
	 * @param mixed $field_id field id.
	 * @param mixed $old_details old details of option.
	 * @param mixed $field field object.
	 * @param array $field_old_ids field old ids.
	 *
	 * @return bool
	 */
	protected static function add_update_option( $field_id, &$old_details, &$field, &$field_old_ids = array() ) {
		if ( empty( $field_id ) ) {
			return false;
		}
		$opt_ids    = array();
		$is_updated = false;
		foreach ( $field['options'] as $option ) {
			$field_option = new Mapbd_Pos_Addon_Field_Option();
			$opt_id       = '';
			if ( isset( $option['id'] ) ) {
				$opt_id = ! empty( $option['id'] ) ? $option['id'] : '';
				unset( $option['id'] );
			}
			if ( ! empty( $opt_id ) ) {
				$opt_ids[] = $opt_id;
				if ( ! empty( $old_details->fields[ $field_id ]->options[ $opt_id ] ) && $old_details->fields[ $field_id ]->options[ $opt_id ] instanceof ViteposModel ) {
					$option = $old_details->fields[ $field_id ]->options[ $opt_id ]->get_update_values( $option );
				}
				$field_option->set_from_array( $option );
				$field_option->set_where_update( 'id', $opt_id );
				if ( $field_option->is_set_data_for_save_update() && $field_option->update( false, false ) ) {
					$is_updated = true;
				}
			} else {
				if ( $field_option->set_from_array( $option ) ) {
					$field_option->field_id( $field_id );
					if ( $field_option->save() ) {
						$opt_ids[]  = $field_option->id;
						$is_updated = true;
					}
				}
			}
		}

		if ( ! empty( $field_old_ids[ $field_id ] ) ) {
			foreach ( $field_old_ids[ $field_id ] as $field_old_id ) {
				if ( ! in_array( $field_old_id, $opt_ids ) ) {
					
					if ( Mapbd_Pos_Addon_Field_Option::delete_by_id( $field_old_id ) ) {
						$is_updated = true;
					}
				}
			}
		}

		return $is_updated;
	}

	/**
	 * The add update rule is generated by appsbd
	 *
	 * @param mixed $rule_group_id group id.
	 * @param mixed $old_details group old details.
	 * @param mixed $rule_group  rule group.
	 * @param array $old_groups old group.
	 *
	 * @return bool
	 */
	protected static function add_update_rule( $rule_group_id, &$old_details, &$rule_group, &$old_groups = array() ) {
		if ( empty( $rule_group_id ) ) {
			return false;
		}
		$rule_ids   = array();
		$is_updated = false;
		foreach ( $rule_group['rules'] as $rule ) {
			$group_rule = new Mapbd_Pos_Addon_Rule();
			$opt_id     = '';
			if ( isset( $rule['id'] ) ) {
				$opt_id = ! empty( $rule['id'] ) ? $rule['id'] : '';
				unset( $rule['id'] );
			}
			if ( ! empty( $opt_id ) ) {
				$rule_ids[] = $opt_id;
				if ( ! empty( $old_details->rule_group[ $rule_group_id ]->rules[ $opt_id ] ) && $old_details->rule_group[ $rule_group_id ]->rules[ $opt_id ] instanceof ViteposModel ) {
					$rule = $old_details->rule_group[ $rule_group_id ]->rules[ $opt_id ]->get_update_values( $rule );
				}
				$group_rule->set_from_array( $rule );
				$group_rule->set_where_update( 'id', $opt_id );
				if ( $group_rule->is_set_data_for_save_update() && $group_rule->update( false, false ) ) {
					$is_updated = true;
				}
			} else {
				if ( $group_rule->set_from_array( $rule ) ) {
					$group_rule->rule_group_id( $rule_group_id );
					if ( $group_rule->save() ) {
						$rule_ids[] = $group_rule->id;
						$is_updated = true;
					}
				}
			}
		}
		if ( ! empty( $old_groups[ $rule_group_id ] ) ) {
			foreach ( $old_groups[ $rule_group_id ] as $field_old_id ) {
				if ( ! in_array( $field_old_id, $rule_ids ) ) {
					
					if ( Mapbd_Pos_Addon_Rule::delete_by_id( $field_old_id ) ) {
						$is_updated = true;
					}
				}
			}
		}

		return $is_updated;
	}

	/**
	 * The update fields is generated by appsbd
	 *
	 * @param mixed $fields fields.
	 * @param mixed $addon_id addon id.
	 * @param mixed $old_details old details.
	 * @param array $old_fields old fields.
	 *
	 * @return bool
	 */
	protected static function update_fields( $fields, $addon_id, &$old_details, &$old_fields = array() ) {
				$is_updated = true;
		if ( ! empty( $fields ) ) {
			$all_field_ids = array();
			foreach ( $fields as $field ) {
				$demo_field          = new \stdClass();
				$demo_field->options = array();
				$addon_field         = new Mapbd_Pos_Addon_Field();
				$fld_id              = '';

				if ( isset( $field['id'] ) ) {
					$fld_id = ! empty( $field['id'] ) ? $field['id'] : '';
					unset( $field['id'] );
				}
				if ( ! empty( $fld_id ) ) {
					
					$all_field_ids[] = $fld_id;
					if ( ! empty( $old_details->fields[ $fld_id ] ) && $old_details->fields[ $fld_id ] instanceof ViteposModel ) {
						$field = $old_details->fields[ $fld_id ]->get_update_values( $field );
					}
					$addon_field->set_from_array( $field );
					$addon_field->set_where_update( 'id', $fld_id );
					if ( $addon_field->is_set_data_for_save_update() && $addon_field->update( false, false ) ) {
						$is_updated = true;
					}
				} else {
					
					if ( $addon_field->set_from_array( $field ) ) {
						$addon_field->addon_id( $addon_id );
						if ( $addon_field->save() ) {
							$is_updated      = true;
							$fld_id          = $addon_field->id;
							$all_field_ids[] = $fld_id;
						}
					}
				}
				if ( self::add_update_option( $fld_id, $old_details, $field, $old_fields ) ) {
					$is_updated = true;
				}
			}
			
			foreach ( $old_fields as $old_field_id => $old_field ) {
				if ( ! in_array( $old_field_id, $all_field_ids ) ) {
					if ( Mapbd_Pos_Addon_Field::delete_by_id( $old_field_id ) ) {
						$is_updated = true;
					}
				}
			}
		}
				return $is_updated;

	}

	/**
	 * The update rule groups is generated by appsbd
	 *
	 * @param mixed $rule_groups rule group.
	 * @param mixed $addon_id addon id.
	 * @param mixed $old_details old details.
	 * @param array $old_groups old groups.
	 *
	 * @return bool
	 */
	protected static function update_rule_groups( $rule_groups, $addon_id, &$old_details, &$old_groups = array() ) {
		$is_updated = true;
		if ( ! empty( $rule_groups ) ) {
			$all_field_ids = array();
			foreach ( $rule_groups as $rule_group ) {
				$demo_field        = new \stdClass();
				$demo_field->rules = array();
				$addon_rule_group  = new Mapbd_Pos_Addon_Rule_Group();
				$rule_group_id     = '';

				if ( isset( $rule_group['id'] ) ) {
					$rule_group_id = ! empty( $rule_group['id'] ) ? $rule_group['id'] : '';
					unset( $rule_group['id'] );
				}
				if ( ! empty( $rule_group_id ) ) {
					
					$all_field_ids[] = $rule_group_id;
					if ( ! empty( $old_details->rule_group[ $rule_group_id ] ) && $old_details->rule_group[ $rule_group_id ] instanceof ViteposModel ) {
						$rule_group = $old_details->rule_group[ $rule_group_id ]->get_update_values( $rule_group );
					}
					$addon_rule_group->set_from_array( $rule_group );
					$addon_rule_group->set_where_update( 'id', $rule_group_id );
					if ( $addon_rule_group->is_set_data_for_save_update() && $addon_rule_group->update( false, false ) ) {
						$is_updated = true;
					}
				} else {
					
					if ( $addon_rule_group->set_from_array( $rule_group ) ) {
						$addon_rule_group->addon_id( $addon_id );
						$addon_rule_group->status( 'A' );
						if ( $addon_rule_group->save() ) {
							$is_updated      = true;
							$rule_group_id   = $addon_rule_group->id;
							$all_field_ids[] = $rule_group_id;
						}
					}
				}
				if ( self::add_update_rule( $rule_group_id, $old_details, $rule_group, $old_groups ) ) {
					$is_updated = true;
				}
			}
			
			foreach ( $old_groups as $old_group_id => $old_field ) {
				if ( ! in_array( $old_group_id, $all_field_ids ) ) {
					if ( Mapbd_Pos_Addon_Rule_Group::delete_by_id( $old_group_id ) ) {
						$is_updated = true;
					}
				}
			}
		}
		return $is_updated;

	}

	/**
	 * The update is generated by appsbd
	 *
	 * @param mixed $payload addon update payload.
	 *
	 * @return bool
	 */
	public static function update( $payload ) {
		if ( ! empty( $payload['id'] ) ) {
			$id = intval( $payload['id'] );
			if ( $id > 0 ) {
				$old_details = self::get_details( $id, true );
				$old_fields  = $old_details->get_fields_ids();
				$old_groups  = $old_details->get_groups_ids();
				$is_updated  = false;
				unset( $payload[ $id ] );

				
				$addon = new Mapbd_Pos_Addon();
				if ( ! empty( $old_details ) && $old_details instanceof ViteposModel ) {
					$payload = $old_details->get_update_values( $payload );
				}
				$addon->set_where_update( 'id', $id );
				if ( $addon->set_from_array( $payload ) && $addon->is_set_data_for_save_update() && $addon->update() ) {
					$is_updated = true;
				}
				if ( ! empty( $payload['fields'] ) && self::update_fields( $payload['fields'], $id, $old_details, $old_fields ) ) {
					$is_updated = true;
				}
				
				if ( ! empty( $payload['rule_group'] ) && self::update_rule_groups( $payload['rule_group'], $id, $old_details, $old_groups ) ) {
					$is_updated = true;
				}

				
				if ( ! $is_updated ) {
					$addon->add_error( 'No change for update' );
				} else {
					$addon->add_info( 'Addon updated successfully' );
				}
				return $is_updated;
			} else {
				$addon = new Mapbd_Pos_Addon();
				$addon->add_error( 'Invalid request param' );
				return false;
			}
		} else {
			$addon = new Mapbd_Pos_Addon();
			$addon->add_error( 'Request param is empty' );
			return false;
		}

	}
}
