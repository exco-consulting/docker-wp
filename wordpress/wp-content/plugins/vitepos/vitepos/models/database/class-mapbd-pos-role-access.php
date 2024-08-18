<?php
/**
 * Pos Role Access Database Model
 *
 * @package Vitepos\Models\Database
 */

namespace Vitepos\Models\Database;

use VitePos\Core\ViteposModel;

/**
 * Class Mapbd_pos_role_access
 *
 * @properties id,role_id,resource_id,role_access
 */
class Mapbd_Pos_Role_Access extends ViteposModel {
	/**
	 * Its property id
	 *
	 * @var int
	 */
	public $id;
	/**
	 * Its property role_slug
	 *
	 * @var string
	 */
	public $role_slug;
	/**
	 * Its property resource_id
	 *
	 * @var int
	 */
	public $resource_id;
	/**
	 * Its property role_access
	 *
	 * @var string
	 */
	public $role_access;

	/**
	 * Its property all_access_list
	 *
	 * @var Array
	 */
	protected static $all_access_list = null;

	/**
	 * Mapbd_pos_role_access constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_validation();
		$this->table_name     = 'apbd_pos_role_access';
		$this->primary_key    = 'id';
		$this->unique_key     = array( array( 'resource_id', 'role_slug' ) );
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
			'role_slug'   => array(
				'Text' => 'Role Slug',
				'Rule' => 'required|max_length[100]|integer',
			),
			'resource_id' => array(
				'Text' => 'Resource Id',
				'Rule' => 'required|max_length[100]',
			),
			'role_access' => array(
				'Text' => 'Role Access',
				'Rule' => 'max_length[1]',
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
			case 'role_access':
				$return_obj = array(
					'Y' => "<i class='grid-icon  fa fa-check text-success'></i>",
					'N' => "<i class='grid-icon  fa fa-times text-danger'></i>",
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
	 * The DeleteByRoleSlug is generated by appsbd
	 *
	 * @param any $role_slug Its string.
	 *
	 * @return bool
	 */
	public static function delete_by_role_slug( $role_slug ) {
		$obj = new self();
		$obj->role_slug( $role_slug );
		$total_actions = $obj->count_all();
		if ( $total_actions > 0 ) {
			return parent::delete_by_key_value( 'role_slug', $role_slug, true );
		} else {
			return true;
		}
	}

	/**
	 * The UpdateStatus is generated by appsbd
	 *
	 * @param any $id Its int.
	 * @param any $role_access Its string.
	 *
	 * @return bool
	 */
	public static function update_status( $id, $role_access ) {
		$up = new self();
		$up->role_access( $role_access );
		$up->set_where_update( 'id', $id );

		return $up->update();
	}

	/**
	 * The AddAccessStatus is generated by appsbd
	 *
	 * @param any $role_slug Its string.
	 * @param any $res_id Its integer.
	 *
	 * @return bool
	 */
	public static function add_access_status( $role_slug, $res_id ) {
		$n = new self();
		$n->role_slug( $role_slug );
		$n->resource_id( $res_id );
		$n->role_access( 'Y' );

		return $n->save();
	}

	/**
	 * The AddAccessIfNotExits is generated by appsbd
	 *
	 * @param any $role_slug Its string.
	 * @param any $res_id Its integer.
	 *
	 * @return bool
	 */
	public static function AddAccessIfNotExits( $role_slug, $res_id ) {
		$s = new self();
		$s->role_slug( $role_slug );
		$s->resource_id( $res_id );
		if ( ! $s->select() ) {
			$n = new self();
			$n->role_slug( $role_slug );
			$n->resource_id( $res_id );
			$n->role_access( 'Y' );

			return $n->save();
		} else {
			return false;
		}
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
                  `role_slug` char(100) NOT NULL COMMENT 'FK(wp_apbd_wps_role,role_slug,name)',
                  `resource_id` char(100) NOT NULL,
                  `role_access` char(1) NOT NULL DEFAULT 'N' COMMENT 'bool(Y=Yes,N=No)',
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `role_resource` (`resource_id`,`role_slug`) USING BTREE
			) ";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}

	/**
	 * To get resource list
	 *
	 * @return Appsbd\V1\libs\ACL_Resource [];
	 */
	public static function get_resource_list() {
		$resources = array();
		/**
		 * Its for acl resource
		 *
		 * @since 1.0
		 */
		$resources = apply_filters( 'apbd-vtpos/acl-resource', $resources );

		return $resources;
	}

	/**
	 * The GetAccessList is generated by appsbd
	 *
	 * @return mixed|void
	 */
	public static function get_access_list() {
		$acls  = self::fetch_all();
		$roles = array();
		foreach ( $acls as $acl ) {
			if ( ! isset( $roles[ $acl->resource_id ] ) ) {
				$roles[ $acl->resource_id ] = array();
			}
			$roles[ $acl->resource_id ][ $acl->role_slug ] = $acl->role_access;
		}

		/**
		 * Its for role access list
		 *
		 * @since 1.0
		 */
		return apply_filters( 'vite-pos/filter/role-access-list', $roles );
	}

	/**
	 * The get all access list is generated by appsbd
	 *
	 * @return self [] |false|Array|null
	 */
	public static function get_all_access_list() {
		if ( is_null( self::$all_access_list ) ) {
			self::$all_access_list = self::find_all_by( 'role_access', 'Y', array(), 'role_slug' );
		}
		return self::$all_access_list;
	}
}
