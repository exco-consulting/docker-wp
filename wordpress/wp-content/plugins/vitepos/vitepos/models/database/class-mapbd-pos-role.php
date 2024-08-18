<?php
/**
 * Pos Role Database Model
 *
 * @package Vitepos\Models\Database
 */

namespace Vitepos\Models\Database;

use VitePos\Core\ViteposModel;
use VitePos\Models\Database\Mapbd_Pos_Role_Access;

/**
 * Class Mapbd_pos_role
 *
 * @properties id,name,slug,role_description,status
 */
class Mapbd_Pos_Role extends ViteposModel {

	/**
	 * Its property id
	 *
	 * @var int
	 */
	public $id;
	/**
	 * Its property name
	 *
	 * @var string
	 */
	public $name;
	/**
	 * Its property parent_role
	 *
	 * @var string
	 */
	public $parent_role;
	/**
	 * Its property slug
	 *
	 * @var string
	 */
	public $slug;
	/**
	 * Its property slug
	 *
	 * @var int
	 */
	public $max_discount;
	/**
	 * Its property slug
	 *
	 * @var string
	 */
	public $discount_type;
	/**
	 * Its property role_description
	 *
	 * @var string
	 */
	public $role_description;
	/**
	 * Its property is_agent
	 *
	 * @var bool
	 */
	public $is_agent;
	/**
	 * Its property is_editable
	 *
	 * @var bool
	 */
	public $is_editable;
	/**
	 * Its property status
	 *
	 * @var bool
	 */
	public $status;
	/**
	 * Its property is_admin_role
	 *
	 * @var bool
	 */
	public $is_admin_role = false;
	/**
	 * Its property _rolelist
	 *
	 * @var null
	 */
	protected static $_rolelist = null;


	/**
	 * Mapbd_pos_role constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_validation();
		$this->table_name     = 'apbd_pos_role';
		$this->primary_key    = 'id';
		$this->unique_key     = array( array( 'slug' ) );
		$this->multi_key      = array();
		$this->auto_inc_field = array( 'id' );
		$this->app_base_name  = 'apbd-elite-pos';

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
			 'name'          => array(
				 'Text' => 'Name',
				 'Rule' => 'max_length[150]',
			 ),
			 'parent_role'   => array(
				 'Text' => 'Parent Role',
				 'Rule' => 'max_length[11]',
			 ),
			 'slug'          => array(
				 'Text' => 'Slug',
				 'Rule' => 'max_length[255]',
			 ),
			 'max_discount'  => array(
				 'Text' => 'Max discount',
				 'Rule' => 'max_length[7]',
			 ),
			 			 'discount_type' => array(
				 'Text' => 'Discount Type',
				 'Rule' => 'max_length[1]',
			 ),
			 'is_editable'   => array(
				 'Text' => 'Status',
				 'Rule' => 'max_length[1]',
			 ),
			 'is_agent'      => array(
				 'Text' => 'Is Agent',
				 'Rule' => 'max_length[1]',
			 ),
			 'status'        => array(
				 'Text' => 'Status',
				 'Rule' => 'max_length[1]',
			 ),

		 );
	}

	/**
	 * The get property raw options is generated by appsbd
	 *
	 * @param \Appsbd\V1\Core\any $property  Its string.
	 * @param false               $is_with_select Its bool.
	 *
	 * @return array|string[]
	 */
	public function get_property_raw_options( $property, $is_with_select = false ) {
		$return_obj = array();
		switch ( $property ) {
			case 'status':
				$return_obj = array(
					'A' => 'Active',
					'I' => 'Inactive',
				);
				break;
			case 'is_agent':
				$return_obj = array(
					'Y' => 'Yes',
					'N' => 'No',
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
	 * The get property options color is generated by appsbd
	 *
	 * @param \Appsbd\V1\Core\any $property Its string.
	 *
	 * @return array|string[]
	 */
	public function get_property_options_color( $property ) {
		$return_obj = array();
		switch ( $property ) {
			case 'status':
				$return_obj = array(
					'A' => 'success',
					'I' => 'danger',
				);
				break;
			default:
		}
		return $return_obj;

	}

	/**
	 * The get agent roles is generated by appsbd
	 *
	 * @return array
	 */
	public static function get_agent_roles() {
		$agent_roles = self::find_all_by( 'status', 'A', array( 'is_agent' => 'Y' ) );
		$res         = array();
		foreach ( $agent_roles as $agent_role ) {
			$res[] = $agent_role->slug;
		}
		return $res;
	}

	/**
	 * The DeleteBySlug is generated by appsbd
	 *
	 * @param any $slug Its string.
	 *
	 * @return bool
	 */
	public static function delete_by_slug( $slug ) {
		if ( self::delete_by_key_value( 'slug', $slug ) ) {
			Mapbd_pos_role_access::delete_by_role_slug( $slug );
			return true;
		}
		return false;
	}

	/**
	 * The GetSlugBy is generated by appsbd
	 *
	 * @param any $str Its string.
	 *
	 * @return false|string
	 */
	public static function get_slug_by( $str ) {
		$slug = sanitize_title_with_dashes( 'vtpos-' . $str );
		if ( strlen( $slug ) > 97 ) {
			$slug = substr( $slug, 0, 97 );
		}
		$obj      = new self();
		$new_slug = $slug;
		$counter  = 1;
		while ( $obj->is_exists( 'slug', $new_slug ) ) {
			$obj      = new self();
			$new_slug = $slug . array( $counter++ );
		}
		if ( 'administrator' == $new_slug ) {
			$new_slug = 'admin_' . hash( 'crc32b', time() );
		}
		return $new_slug;

	}

	/**
	 * The IsBuiltInRole is generated by appsbd
	 *
	 * @param any $role Its string.
	 *
	 * @return bool
	 */
	public static function is_built_in_role( $role ) {
		$predefined = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
		return in_array( $role, $predefined );
	}

	/**
	 * The set default role is generated by appsbd
	 */
	public static function set_default_role() {
		$manager_slug = sanitize_title_with_dashes( 'vtpos-outlet-manager' );
		$agent_slug   = sanitize_title_with_dashes( 'vtpos-cashier' );
		if ( ! self::is_role_exists( 'administrator' ) ) {
			self::add_role_if_not_exists( 'administrator', 'Administrator', false, true );
		}
		if ( ! self::is_role_exists( $agent_slug ) ) {
			self::add_role_if_not_exists( $manager_slug, 'Outlet Manager', true, true );
			$outlet_manager = array( 'pos-menu', 'order-menu,order-sale', 'order-hold', 'order-offline', 'order-details', 'payment-note', 'customer-menu', 'customer-add', 'customer-edit', 'product-menu', 'product-add', 'product-edit', 'stock-menu', 'stock-add', 'purchase-menu', 'purchase-add ', 'purchase-details', 'vendor-menu', 'vendor-edit', 'vendor-add', 'user-menu', 'user-add', 'user-edit', 'barcode-menu', 'change-pass', 'change-any-user-pass', 'order-list' );
			foreach ( $outlet_manager as $acc ) {
				Mapbd_Pos_Role_Access::add_access_status( $manager_slug, $acc );
			}
		}

		if ( ! self::is_role_exists( $agent_slug ) ) {
			self::add_role_if_not_exists( $agent_slug, 'Cashier', true, true );
			$cashier_access = array( 'pos-menu', 'order-menu', 'order-sale', 'order-hold', 'order-offline', 'order-details', 'payment-note', 'customer-menu', 'customer-add', 'change-pass', 'order-list', 'user-add-any-outlet', 'customer-edit', 'barcode-menu', 'product-menu' );
			foreach ( $cashier_access as $acc ) {
				Mapbd_Pos_Role_Access::add_access_status( $agent_slug, $acc );
			}
		}
	}

	/**
	 * The set default restro role is generated by appsbd
	 *
	 * @param mixed $is_force
	 */
	public static function set_default_restro_role($is_force=false) {
		$is_called=get_option('_vt_rs_role');
		if($is_force || !$is_called) {
			$outlet_manager = array( 'order-list', 'order-online', 'make-complete', 'addon-menu', 'addon-add ', 'addon-edit', 'addon-delete', 'addon-status-change', 'cashier-menu', 'cancel-order', 'cancel-order-request', 'table-menu', 'table-add', 'table-edit', 'table-delete' );
			self::add_default_role( 'vtpos-outlet-manager', 'Outlet Manager', $outlet_manager ,true,true,true);

			$cashier_access = array( 'cashier-menu', 'table-menu', 'table-add', 'table-edit', 'addon-menu', 'addon-add', 'addon-edit', 'addon-delete', 'addon-status-change', 'product-menu' );
			self::add_default_role( 'vtpos-cashier', 'Cashier', $cashier_access,true,true,true);

			$chef_access = array( 'kitchen-menu', 'start-preparing', 'deny-order', 'ready-order', 'accept-cancel', 'deny-cancel', 'print-order-kitchen' );
			self::add_default_role( 'vtpos-chef', 'Chef', $chef_access );

			$waiter_access = array( 'waiter-menu', 'serve-order', 'cancel-waiter-order', 'waiter-cancel-request', 'waiter-to-kitchen', 'can-check-out' );
			self::add_default_role( 'vtpos-waiter', 'Waiter', $waiter_access );
			update_option('_vt_rs_role',true);
		}
	}

	/**
	 * The add default role is generated by appsbd
	 *
	 * @param mixed $role_slug Its role_slug param.
	 * @param mixed $role_name Its role_name param.
	 * @param mixed $role_access Its role_access param.
	 * @param bool $is_editable Its is_editable param.
	 * @param bool $is_agent Its is_agent param.
	 */
	public static function add_default_role($role_slug,$role_name,$role_access,$is_editable=true,$is_agent=true,$force_update=false) {
		$role_slug   = sanitize_title_with_dashes( $role_slug );
		if ( ! self::is_role_exists( $role_slug ) ) {
			self::add_role_if_not_exists( $role_slug, $role_name, $is_editable, $is_agent );
			$force_update=true;
		}
		if($force_update) {
			foreach ( $role_access as $acc ) {
				$acc = trim( $acc );
				Mapbd_Pos_Role_Access::AddAccessIfNotExits( $role_slug, $acc );
			}
		}
	}
	/**
	 * The is role exists is generated by appsbd
	 *
	 * @param mixed $slug Its slug.
	 *
	 * @return bool
	 */
	public static function is_role_exists( $slug ) {
		$n = new self();
		return $n->is_exists( 'slug', $slug );
	}
	/**
	 * The AddRole is generated by appsbd
	 *
	 * @param any $slug Its string.
	 * @param any $name Its string.
	 * @param any $is_editable Its bool.
	 * @param any $is_agent Its bool.
	 *
	 * @return bool
	 */
	public static function add_role( $slug, $name, $is_editable, $is_agent ) {
		$n = new self();
		$n->slug( $slug );
		$n->name( $name );
		$n->is_editable( $is_editable ? 'Y' : 'N' );
		$n->is_agent( $is_agent ? 'Y' : 'N' );
		return $n->save();
	}

	/**
	 * The AddRoleIfNotExists is generated by appsbd
	 *
	 * @param any $slug Its string.
	 * @param any $name Its string.
	 * @param any $is_editable Its bool.
	 * @param any $is_agent Its bool.
	 *
	 * @return bool
	 */
	public static function add_role_if_not_exists( $slug, $name, $is_editable, $is_agent ) {
		 $n = new self();
		if ( ! $n->is_exists( 'slug', $slug ) ) {
			return self::add_role( $slug, $name, $is_editable, $is_agent );
		}
		return true;
	}


	/**
	 * The save is generated by appsbd
	 *
	 * @return bool
	 */
	public function save() {
		if ( ! $this->is_set_prperty( 'slug' ) ) {
			$this->slug( self::get_slug_by( $this->name ) );
		}
		if ( ! $this->is_set_prperty( 'is_agant' ) ) {
			$this->is_agent( 'Y' );
		}
		if ( parent::save() ) {
			/**
			 * Its for role Added
			 *
			 * @since 1.0
			 */
			do_action( 'apbd-vtpos/action/role-added', $this );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * The create db table is generated by appsbd
	 */
	public static function create_db_table() {
		 $this_obj = new static();
		$table     = $this_obj->db->prefix . $this_obj->table_name;
		if ( $this_obj->db->get_var( "show tables like '{$table}'" ) != $table ) {
			$sql = "CREATE TABLE `{$table}` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL DEFAULT '',
                    `parent_role` int(11) unsigned NOT NULL DEFAULT 0,
                    `slug` char(100) NOT NULL DEFAULT '',
                    `max_discount` decimal(6, 2) NOT NULL DEFAULT 20.00,
                    `discount_type` char(1) NOT NULL DEFAULT 'P' COMMENT 'radio(P=Percentage,A=Amount)',
                    `role_description` text NOT NULL COMMENT 'textarea',
                    `is_editable` char(1) NOT NULL DEFAULT 'Y' COMMENT 'bool(Y=Yes,N=No)',
                    `is_agent` char(1) NOT NULL DEFAULT 'N' COMMENT 'bool(Y=Yes,N=No)',
                    `status` char(1) NOT NULL DEFAULT 'A' COMMENT 'bool(A=Active,I=Inactive)',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `slug_ind` (`slug`) USING BTREE
					) ";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}

	/**
	 * The GetRoleObjectBy is generated by appsbd
	 *
	 * @param any   $slug Its string.
	 * @param any   $name Its string.
	 * @param any   $parent_role Its string.
	 * @param any   $description Its string.
	 * @param false $is_admin_role Its bool.
	 *
	 * @return Mapbd_pos_role
	 */
	public static function get_role_object_by( $slug, $name, $parent_role, $description, $is_admin_role = false ) {
		$role_object                   = new self();
		$role_object->name             = $name;
		$role_object->slug             = $slug;
		$role_object->is_admin_role    = $is_admin_role;
		$role_object->role_description = $description;
		$role_object->parent_role      = $parent_role;
		return $role_object;

	}

	/**
	 * The get role list is generated by appsbd
	 *
	 * @return mixed|Mapbd_Pos_Role[]|void|null
	 */
	public static function get_role_list() {
		if ( is_null( self::$_rolelist ) ) {
			self::$_rolelist = self::find_all_by_identiry( 'status', 'A', 'slug' );
			/**
			 * Its for acl role
			 *
			 * @since 1.0
			 */
			self::$_rolelist = apply_filters( 'elite-wps/acl-roles', self::$_rolelist );
		}
		return self::$_rolelist;
	}

	/**
	 * The get role list with capabilities is generated by appsbd
	 *
	 * @return Mapbd_Pos_Role[]|null
	 */
	public static function get_role_list_with_capabilities() {
		$roles = self::get_role_list();
				$acls = Mapbd_pos_role_access::get_all_access_list();
		foreach ( $acls as $acl ) {
			if ( ! empty( $roles[ $acl->role_slug ] ) ) {
				if ( empty( $roles[ $acl->role_slug ]->capabilities ) ) {
					$roles[ $acl->role_slug ]->capabilities = array();
				}
				$roles[ $acl->role_slug ]->capabilities[ $acl->resource_id ] = true;
			}
		}

		return $roles;
	}

	/**
	 * The set capabilities by role is generated by appsbd
	 *
	 * @param any     $all_caps Its string.
	 * @param WP_User $user Its string.
	 *
	 * @return array
	 */
	public static function set_capabilities_by_role( $all_caps, $user ) {
		$roles = self::get_role_list_with_capabilities();

		if ( $user instanceof \WP_User ) {
			$resource = Mapbd_pos_role_access::get_resource_list();
			foreach ( $user->roles as $role_slug ) {
				if ( 'administrator' == $role_slug ) {
					foreach ( $resource as $res ) {
						$all_caps[ $res->action_param ] = true;
					}
					break;
				} else {
					if ( ! empty( $roles[ $role_slug ] ) && ! empty( $roles[ $role_slug ]->capabilities ) ) {
						$all_caps = array_merge( $all_caps, $roles[ $role_slug ]->capabilities );
					}
				}
			}
		}
		return $all_caps;
	}
	/**
	 * The set capabilities by role is generated by appsbd
	 *
	 * @param WP_User $user Its string.
	 *
	 * @return array
	 */
	public static function get_discount_percentage( $user ) {
		$roles      = self::get_role_list();
		$percentage = 0.00;
		if ( $user instanceof \WP_User ) {
			;
			foreach ( $user->roles as $role_slug ) {
				if ( 'administrator' == $role_slug ) {
					$percentage = 100.00;
					break;
				} else {
					if ( ! empty( $roles[ $role_slug ] ) && ! empty( $roles[ $role_slug ]->max_discount ) ) {
						$percentage = doubleval( $roles[ $role_slug ]->max_discount );
					}
				}
			}
		}
		return $percentage;
	}

	/**
	 * The IsAdminRole is generated by appsbd
	 *
	 * @param any $slug Its string.
	 *
	 * @return bool
	 */
	public static function is_admin_role( $slug ) {
		if ( is_null( self::$_rolelist ) ) {
			self::get_role_list();
		}
		if ( ! empty( self::$_rolelist[ $slug ] ) && self::$_rolelist[ $slug ]->is_admin_role ) {
			return true;
		}
		return false;
	}

	/**
	 * The update max discount non editable is generated by appsbd
	 *
	 * @param mixed $max_discount Its max discount.
	 *
	 * @return bool
	 */
	public static function update_max_discount_non_editable( $max_discount ) {
		$obj = new self();
		$obj->max_discount( $max_discount );
		$obj->set_where_update( 'is_editable', 'N' );
		if ( $obj->update( true ) ) {
			return true;
		}

		return false;
	}

}
