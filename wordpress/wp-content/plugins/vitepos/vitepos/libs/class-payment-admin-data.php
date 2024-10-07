<?php
/**
 * Its payment method base.
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Libs
 */

namespace VitePos\Libs;

/**
 * Class payment_admin_data
 *
 * @package VitePos\Libs
 */
class Payment_Admin_Data {
	/**
	 * Its property name
	 *
	 * @var String
	 */
	public $name;
	/**
	 * Its property title
	 *
	 * @var string
	 */
	public $title = '';
	/**
	 * Its property params
	 *
	 * @var array
	 */
	public $params = array();
	/**
	 * Its property desc
	 *
	 * @var string
	 */
	public $desc = '';
	/**
	 * Its property is_enable
	 *
	 * @var string
	 */
	public $is_enable;
	/**
	 * Its property can_split
	 *
	 * @var string
	 */
	public $can_split;
	/**
	 * Its property settings
	 *
	 * @var \stdClass
	 */
	public $settings;
	/**
	 * Its property cards
	 *
	 * @var array
	 */
	public $cards = array();
	/**
	 * Its property tab_title
	 *
	 * @var string
	 */
	public $tab_title;
	/**
	 * Its property tab_icon
	 *
	 * @var mixed|string
	 */
	public $tab_icon = '';
	/**
	 * Its property _is_viewable
	 *
	 * @var bool
	 */
	protected $_is_viewable = true;

	/**
	 * Its property settings
	 *
	 * @var \stdClass
	 */
	protected $admin_data;

	/**
	 * Payment_admin_data constructor.
	 */
	public function __construct() {
		$this->admin_data = new \stdClass();
		$this->is_enable  = 'N';
		$this->can_split  = 'Y';
		$this->settings   = array();
		$this->tab_title  = '';
		$this->cards      = array();
	}

	/**
	 * The set viewable is generated by appsbd
	 *
	 * @param mixed $viewable Its viewable.
	 */
	public function set_viewable( $viewable ) {
		$this->_is_viewable = $viewable;
	}

	/**
	 * The is viewable is generated by appsbd
	 *
	 * @return bool
	 */
	public function is_viewable() {
		return $this->_is_viewable;
	}

	/**
	 * The set name is generated by appsbd
	 *
	 * @param mixed $name Its name param.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * The set title is generated by appsbd
	 *
	 * @param mixed $title Its title param.
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	/**
	 * The set params is generated by appsbd
	 *
	 * @param array $params Its params param.
	 */
	public function set_params( $params = array() ) {
		$this->params = $params;
	}

	/**
	 * The set desc is generated by appsbd
	 *
	 * @param mixed $desc Its desc param.
	 */
	public function set_desc( $desc ) {
		$this->desc = $desc;
	}

	/**
	 * The get name is generated by appsbd
	 *
	 * @return String
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * The add field is generated by appsbd
	 *
	 * @param mixed  $title Its title param.
	 * @param mixed  $name Its name param.
	 * @param false  $is_required Its is_required param.
	 * @param string $type Its type param.
	 */
	public function add_settings_field( $title, $name, $is_required = false, $type = 'T' ) {
		$obj              = new \stdClass();
		$obj->title       = $title;
		$obj->name        = $name;
		$obj->is_required = $is_required;
		$obj->type        = $type;
		$this->settings[] = $obj;
	}

	/**
	 * The get admin settings array is generated by appsbd
	 *
	 * @return $this
	 */
	public function get_admin_settings_array() {
		return $this;
	}

	/**
	 * The add tab is generated by appsbd
	 *
	 * @param mixed  $title Its title param.
	 *
	 * @param string $icon Its icon param.
	 *
	 * @return void
	 */
	public function set_setting_tab_title( $title, $icon = '' ) {
		$this->tab_title = $title;
		$this->tab_icon  = $icon;
	}
	/**
	 * The add tab is generated by appsbd
	 *
	 * @param mixed $name Its name param.
	 * @param mixed $title Its title param.
	 *
	 * @return Payment_Admin_Card
	 */
	public function &add_card( $name, $title ) {
		$card          = new Payment_Admin_Card( $name, $title );
		$this->cards[] = &$card;
		return $card;
	}

}
