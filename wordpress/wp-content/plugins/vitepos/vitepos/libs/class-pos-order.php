<?php
/**
 * Its pos order model
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Libs
 */

namespace VitePos\Libs;

use VitePos\Api\V1\Pos_User_Api;
use Vitepos\Models\Database\Mapbd_Pos_Counter;
use Vitepos\Models\Database\Mapbd_Pos_Table;
use Vitepos\Models\Database\Mapbd_Pos_Warehouse;
use VitePos\Modules\POS_Settings;

/**
 * Class Pos Order
 *
 * @package VitePos\Libs
 */
class POS_Order {

	/**
	 * Its property order_id
	 *
	 * @var int
	 */
	public $order_id;
	/**
	 * Its property cart_id
	 *
	 * @var int
	 */
	public $cart_id;
	/**
	 * Its property cart_unique_id
	 *
	 * @var int
	 */
	public $cart_unique_id;
	/**
	 * Its property items
	 *
	 * @var array
	 */
	public $items = array();

	/**
	 * Its property fees
	 *
	 * @var array
	 */
	public $fees = array();
	/**
	 * Its property discounts
	 *
	 * @var array
	 */
	public $discounts = array();

	/**
	 * Its property note
	 *
	 * @var string
	 */
	public $note = '';
	/**
	 * Its property payment_note
	 *
	 * @var string
	 */
	public $payment_note = '';
	/**
	 * Its property payment_method
	 *
	 * @var string
	 */
	public $payment_method = '';
	/**
	 * Its property customer
	 *
	 * @var string
	 */
	public $customer;
	/**
	 * Its property sub_total
	 *
	 * @var int
	 */
	public $sub_total;
	/**
	 * Its property tax_total
	 *
	 * @var int
	 */
	public $tax_total;
	/**
	 * Its property grand_total
	 *
	 * @var int
	 */
	public $grand_total;
	/**
	 * Its property given_amount
	 *
	 * @var int
	 */
	public $given_amount;
	/**
	 * Its property returned_amount
	 *
	 * @var int
	 */
	public $returned_amount;
	/**
	 * Its property currency
	 *
	 * @var string
	 */
	public $currency = '';
	/**
	 * Its property outlet_info
	 *
	 * @var null
	 */
	public $outlet_info;
	/**
	 * Its property processed_by
	 *
	 * @var string
	 */
	public $processed_by;
	/**
	 * Its property offline_id
	 *
	 * @var string
	 */
	public $offline_id;
	/**
	 * Its property has_refund
	 *
	 * @var mixed|bool|string
	 */

	public $refund_left;
	/**
	 * Its property refund amount
	 *
	 * @var bool|mixed|string
	 */
	public $refund_amount;
	/**
	 * Its property refund amount
	 *
	 * @var array
	 */
	public $refund_orders = array();
	/**
	 * The get order by meta is generated by appsbd
	 *
	 * @param mixed  $key Its key param.
	 * @param mixed  $value Its value param.
	 * @param string $compare Its compare param.
	 *
	 * @return \WC_Order|null
	 */
	public static function get_order_by_meta( $key, $value, $compare = '=' ) {
		$args   = array(
			'limit'        => 1,
			'meta_key'     => $key, 
			'meta_value'   => $value, 
			'meta_compare' => $compare, 
		);
		$orders = wc_get_orders( $args );
		if ( ! empty( $orders ) && ! empty( $orders[0] ) && $orders[0] instanceof \WC_Order ) {
			return $orders[0];
		}
		return null;
	}
	/**
	 * The getFromWooOrderDetailsByID is generated by appsbd
	 *
	 * @param any $id Its integer.
	 *
	 * @return POS_Order
	 */
	public static function get_from_woo_order_details_by_id( $id ) {
		 $order = new \WC_Order( $id );
		return self::get_from_woo_order_details( $order );
	}

	/**
	 * The get from woo order details is generated by appsbd
	 *
	 * @param \WC_Order $order Its string.
	 *
	 * @param bool      $with_items Is with item.
	 *
	 * @return \stdClass
	 */
	public static function get_from_woo_order_details( $order, $with_items = true ) {
		return self::get_from_woo_order( $order, false, $with_items );
	}

	/**
	 * The get from woo order restro by id is generated by appsbd
	 *
	 * @param mixed   $id order id.
	 * @param boolean $is_online is online order.
	 * @param boolean $with_items Is with item.
	 *
	 * @return \stdClass
	 */
	public static function get_from_woo_order_restro_by_id( $id, $is_online = false, $with_items = false ) {
		$order = new \WC_Order( $id );
		return self::get_from_woo_order( $order, $is_online, $with_items );
	}

	/**
	 * The get from woo order is generated by appsbd
	 *
	 * @param \WC_Order $order Its string.
	 * @param bool      $is_online Its Boolean.
	 *
	 * @param bool      $with_items Is with item.
	 *
	 * @return \stdClass Its string.
	 */
	public static function get_from_woo_order( $order, $is_online = false, $with_items = false ) {
		$v_order = new \stdClass();
		if ( $order instanceof \WC_Order ) {
			$v_order->order_id      = $order->get_id();
			$complete_date          = $order->get_date_completed();
			$create_date            = $order->get_date_created();
			$v_order->order_c_date  = appsbd_get_wp_datetime_with_format( $create_date );
			$v_order->order_c_ts    = strtotime( $create_date ) * 1000;
			$v_order->order_date    = appsbd_get_wp_datetime_with_format( $complete_date );
			$v_order->currency      = $order->get_currency();
			$v_order->currency_code = get_woocommerce_currency_symbol( $order->get_currency() );
			$v_order->customer_id   = intval( $order->get_customer_id( '' ) );
			$v_order->is_tax_in     = $order->get_prices_include_tax();
			$v_order->refund_left = vitepos_wc_amount( $order->get_remaining_refund_amount() );
			$v_order->refund_amount = vitepos_wc_amount( $order->get_total_refunded() );
			$v_order->refund_tax = vitepos_wc_amount( $order->get_total_tax_refunded() );
			if ( ! empty( $v_order->customer_id ) ) {
				$v_order->customer = POS_Settings::get_customer_object_by_id( $v_order->customer_id );
			}
			if ( $with_items ) {
				$v_order->items = array();
				self::set_items_to_order( $v_order, $order );
				self::set_refunds_to_order( $v_order, $order );
			}
			foreach ( $order->get_items( 'fee' ) as $item ) {
				if ( $item instanceof \WC_Order_Item_Fee ) {
					$dis_fee_obj       = new \stdClass();
					$dis_fee_obj->type = $item->get_meta( '_vtp_cal_type' );
					$dis_fee_obj->val  = floatval( $item->get_meta( '_vtp_cal_val' ) );
					$dis_fee_obj->amount  = $item->get_total();
					if ( $item->get_total() > 0 ) {
						
						$v_order->fees[] = $dis_fee_obj;
					} else {
						
						$v_order->discounts[] = $dis_fee_obj;
					}
				}
			}
			$v_order->outlet_id = (int) $order->get_meta( '_vtp_outlet_id' );

			if ( ! empty( $v_order->outlet_id ) ) {
				$outlet_info          = Mapbd_Pos_Warehouse::find_by( 'id', $v_order->outlet_id );
				$v_order->outlet_info = new \stdClass();
				$props                = array(
					'id',
					'name',
					'email',
					'phone',
					'country',
					'state',
					'city',
					'street',
					'zip_code',
				);
				foreach ( $props as $prop ) {
					$v_order->outlet_info->{$prop} = ! empty( $outlet_info ) ? $outlet_info->{$prop} : '';
				}
			}
			$processed_by = $order->get_meta( '_vtp_processed_by' );
			if ( ! empty( $processed_by ) ) {
				$user                        = get_user_by( 'id', $processed_by );
				$v_order->processed_by       = new \stdClass();
				$v_order->processed_by->id   = $processed_by;
				$v_order->processed_by->name = ! empty( $user ) ? appsbd_get_user_title_by_user( $user ) : 'Unknown';
			} elseif ( ! metadata_exists( 'post', $v_order->order_id, '_vtp_processed_by' ) ) {
				$v_order->processed_by       = new \stdClass();
				$v_order->processed_by->id   = null;
				$v_order->processed_by->name = 'Online';
			} else {
				$v_order->processed_by       = new \stdClass();
				$v_order->processed_by->id   = null;
				$v_order->processed_by->name = 'Unknown';
			}

			$v_order->note            = $order->get_meta( '_vtp_order_note' );
			$v_order->payment_note    = $order->get_meta( '_vtp_payment_note' );
			$v_order->payment_method  = $order->get_meta( '_vtp_payment_method' );
			$v_order->given_amount    = floatval( $order->get_meta( '_vtp_tendered_amount' ) );
			$v_order->returned_amount = floatval( $order->get_meta( '_vtp_change_amount' ) );

			$is_vt_refund = $order->get_meta( '_vt_has_refund' );

			if ( empty( $is_vt_refund ) || 'Y' != $is_vt_refund ) {
				if ( $v_order->refund_amount > 0 ) {
					$order->update_meta_data( '_vt_has_refund', 'Y' );
					$order->save();
				}
			}

			$v_order->payment_list    = $order->get_meta( '_vtp_payment_list' );
			/**
			 * Its for check is there any change before process
			 *
			 * @since 2.1
			 */
			$v_order->payment_list = apply_filters( 'vitepos/filter/order/payment-list', $v_order->payment_list );

			$v_order->is_paid         = $order->get_meta( '_vt_is_paid' );
			$v_order->tax_method      = ! empty( $order->get_meta( '_vt_tax_method' ) ) ? $order->get_meta( '_vt_tax_method' ) : 'A';
			if ( empty( $v_order->is_paid ) ) {
				$v_order->is_paid = 'N';
			}
			$v_order->taxes = array();
			$include_tax = $order->get_prices_include_tax();
			if ( $include_tax ) {
				$v_order->sub_total = floatval( $order->get_subtotal() ) + floatval( $order->get_total_tax( 'view' ) );
				$v_order->tax_total = 0;
			} else {
				$v_order->sub_total = $order->get_subtotal();
				$v_order->tax_total = floatval( $order->get_total_tax( 'view' ) );
				foreach ( $order->get_taxes() as $item ) {
					$tobj             = new \stdClass();
					$tobj->tax_class  = $item->get_tax_class();
					$tobj->val        = $item->get_tax_total();
					$tobj->name       = $item->get_label();
					$v_order->taxes[] = $tobj;
				}
			}
			$v_order->grand_total = floatval( $order->get_total( '' ) );
			$v_order->offline_id  = $order->get_meta( '_vtp_offline_id', true );
			$v_order->counter     = '';
			$v_order->counter_id  = $order->get_meta( '_vtp_counter_id' );
			if ( $order->meta_exists( '_vtp_custom_fields' ) ) {
				$v_order->custom_fields  = $order->get_meta( '_vtp_custom_fields' );
			}
			if ( POS_Settings::is_restaurant_mode() ) {
				if ( ! empty( $order->get_meta( '_vtp_tables' ) ) ) {
					$v_order->table_id   = (array) $order->get_meta( '_vtp_tables' );
					$v_order->table_info = Mapbd_Pos_Table::get_table_by_ids(
						$v_order->table_id,
						array( 'id', 'title', 'seat_cap' )
					);
				} else {
					$v_order->table_id   = array();
					$v_order->table_info = Mapbd_Pos_Table::get_table_by_ids(
						$v_order->table_id,
						array( 'id', 'title', 'seat_cap' )
					);
				}

				$v_order->order_type = (array) $order->get_meta( '_vtp_order_type' );
				$v_order->persons    = (int) $order->get_meta( '_vtp_persons' );
				if ( $order->meta_exists( '_vt_can_cancel' ) ) {
					$v_order->can_cancel = 'N';
				} else {
					$v_order->can_cancel = 'Y';
				}
				if ( $order->meta_exists( '_vtp_is_item_wise' ) ) {
					$v_order->is_item_wise = $order->get_meta( '_vtp_is_item_wise' );
				} else {
					$v_order->is_item_wise = 'N';
				}
				$v_order->msgs = (array) $order->get_meta( '_vt_order_msgs' );
				self::process_order_msgs( $v_order->msgs );
				$v_order->time_logs = (array) $order->get_meta( '_vt_time_log' );
				self::process_order_time_logs( $v_order->time_logs );
				$v_order->order_by = '';
				$order_by          = $order->get_meta( '_vtp_order_by' );

				if ( ! empty( $order_by ) ) {
					$v_order->waiter_id         = intval( $order_by );
					$user                       = get_user_by( 'id', $order_by );
					$v_order->waiter_info       = new \stdClass();
					$v_order->waiter_info->id   = $order_by;
					$v_order->waiter_info->name = ! empty( $user ) ? appsbd_get_user_title_by_user( $user ) : 'Unknown';
				}
			}
			$v_order->status       = $order->get_status( '' );
			$v_order->status_title = wc_get_order_status_name( $v_order->status );

			$counter_id  = $order->get_meta( '_vtp_counter_id' );
			$counter_obj = null;
			if ( ! empty( $counter_id ) ) {
				$counter_obj = Mapbd_Pos_Counter::find_by( 'id', $counter_id );
			}
			$v_order->counter        = $counter_obj;
			$v_order->cash_drawer_id = $order->get_meta( '_vtp_cash_drawer_id' );
		}

		return $v_order;
	}

	/**
	 * The process order notes is generated by appsbd
	 *
	 * @param array $msgs order messages.
	 */
	public static function process_order_msgs( &$msgs ) {
		if ( ! empty( $msgs ) && is_array( $msgs ) ) {
			foreach ( $msgs as $key => &$msg ) {
				if ( ! empty( $msg->msg ) ) {
					$msg->date = appsbd_get_wp_date_with_format( $msg->time );
					$msg->time = appsbd_get_wp_time_with_format( $msg->time );
				} else {
					unset( $msgs[ $key ] );
				}
			}
		}
	}
	/**
	 * The process order notes is generated by appsbd
	 *
	 * @param array $logs order logs.
	 */
	public static function process_order_time_logs( &$logs ) {
		if ( ! empty( $logs ) && is_array( $logs ) ) {
			foreach ( $logs as $key => &$log ) {
				if ( ! empty( $log->time ) ) {
					$log->status_title = wc_get_order_status_name( $log->status );

					$log->date = appsbd_get_wp_datetime_with_format( $log->time );
					$log->time = appsbd_get_wp_time_with_format( $log->time );
				} else {
					unset( $logs[ $key ] );
				}
			}
		}
	}

	/**
	 * The process order notes is generated by appsbd
	 *
	 * @param int           $order_id order id.
	 * @param string        $msg order message.
	 *
	 * @param \WP_User|null $user message user.
	 *
	 * @return array|false|mixed
	 */
	public static function add_resto_order_msg( $order_id, $msg, $user = null ) {
		if ( ! empty( $order_id ) && ! empty( $msg ) ) {
			$msg_obj       = new \stdClass();
			$msg_obj->msg  = $msg;
			$msg_obj->time = gmdate( 'Y-m-d H:i:s' );
			if ( empty( $user ) || ! ( $user instanceof \WP_User ) ) {
				$user = wp_get_current_user();
			}
			if ( ! empty( $user->ID ) ) {
				$msg_obj->by_id   = $user->ID;
				$msg_obj->by_name = appsbd_get_user_title_by_user( $user );
			} else {
				$msg_obj->by_id   = '';
				$msg_obj->by_name = '';
			}
			$order = wc_get_order( $order_id );
			$order_msgs = $order->get_meta( '_vt_order_msgs', true );
			if ( empty( $order_msgs ) ) {
				$order_msgs = array();
			}
			$order_msgs[] = $msg_obj;
			$order->update_meta_data( '_vt_order_msgs', $order_msgs );
			if ( $order->save() ) {
				unset( $order );
				self::process_order_msgs( $order_msgs );
				return $order_msgs;
			}
		}
		return false;
	}

	/**
	 * The get order items is generated by appsbd
	 *
	 * @param mixed $order Its order object.
	 *
	 * @param bool  $is_key_object True if you want with item id index array.
	 *
	 * @return \stdClass
	 */
	public static function get_order_items( $order, $is_key_object = false ) {
		$order_data = new \stdClass();
		$order_data->items = array();
		self::set_items_to_order( $order_data, $order );
		if ( $is_key_object ) {
			return self::get_order_items_key_object( $order_data->items );
		}
		return $order_data->items;

	}

	/**
	 * The get order items key object is generated by appsbd
	 *
	 * @param mixed $items Its items param.
	 *
	 * @return array
	 */
	public static function get_order_items_key_object( $items ) {
		$return_items = array();
		foreach ( $items as $item ) {
			$return_items[ $item->item_id ] = $item;
		}
		return $return_items;
	}
	/**
	 * The set items to order is generated by appsbd
	 *
	 * @param mixed $order_data orders data.
	 * @param mixed $order order.
	 */
	public static function set_items_to_order( &$order_data, $order ) {
		if ( $order instanceof \WC_Order ) {
			foreach ( $order->get_items() as $item ) {
				if ( $item instanceof \WC_Order_Item_Product ) {
					$product_id = $item->get_product_id();
					if ( ! empty( $product_id ) ) {
						$order_data->items[] = POS_Order_Item::get_product_item( $item, $order );
					}
				}
			}
		}
	}
	/**
	 * The set items to order is generated by appsbd
	 *
	 * @param self  $order_data orders data.
	 * @param mixed $order order.
	 */
	public static function set_refunds_to_order( &$order_data, $order ) {
		
		if ( $order instanceof \WC_Order ) {
			$refunds = $order->get_refunds();
			$items_key_val = self::get_order_items_key_object( $order_data->items );
			foreach ( $refunds as $refund ) {
				$obj = new \stdClass();
				$obj->order_id = $refund->get_id();
				$create_date            = $refund->get_date_created();
				$obj->order_c_date  = appsbd_get_wp_datetime_with_format( $create_date );
				$obj->order_c_ts    = strtotime( $create_date ) * 1000;
				$outlet_id = $refund->get_meta( '_vtp_outlet_id' );
				$counter_id = $refund->get_meta( '_vtp_counter_id' );

				$obj->outlet_info    = new \stdClass();
				$obj->outlet_info->outlet_id = $outlet_id;
				$obj->outlet_info->outlet_name = '';
				$obj->outlet_info->counter_id = $counter_id;
				$obj->outlet_info->counter_name = '';
				$obj->reason = $refund->get_reason();
				if ( ! empty( $outlet_id ) ) {
					$outlet = Mapbd_Pos_Warehouse::find_by( 'id', $outlet_id );
					if ( ! empty( $outlet ) ) {
						$obj->outlet_info->outlet_name = $outlet->name;
						if ( ! empty( $counter_id ) ) {
							$counter = Mapbd_Pos_Counter::find_by(
								'id',
								$counter_id,
								array( 'outlet_id' => $outlet_id )
							);
							if ( ! empty( $counter ) ) {
								$obj->outlet_info->counter_name = $counter->name;
							}
						}
					}
				}
				$obj->items    = array();
				$obj->tax_total = 0.0;
				$obj->refund_total = 0.0;
				foreach ( $refund->get_items() as $item ) {
					if ( $item instanceof \WC_Order_Item_Product ) {
						$item_id = absint( $item->get_meta( '_refunded_item_id' ) );
						if ( ! isset( $items_key_val[ $item_id ] ) ) {
							continue;
						}
						$main_item = $items_key_val[ $item_id ];
						$item_obj = new \stdClass();
						$item_obj->item_id = vitepos_unsigned_value( absint( $item->get_meta( '_refunded_item_id' ) ) );
						$item_obj->name = $main_item->product_name;
						$item_obj->price = $main_item->price;
						$item_obj->regular_price = $main_item->regular_price;
						$item_obj->qty = absint( $item->get_quantity() );
						$item_total = $item->get_total();
						$item_obj->total = vitepos_wc_amount( ( ( $item_total > 0 ) ? $item_total : ( ( -1 ) * $item_total ) ) );
						$item_obj->addons = ! empty( $main_item->addons ) ? $main_item->addons : array();
						$item_obj->addon_total = ! empty( $main_item->addon_total ) ? $main_item->addon_total : 0.0;
						$item_obj->tax_total = ! empty( $main_item->tax_total ) ? $main_item->tax_total : 0.0;
						$obj->tax_total += $item_obj->tax_total;
						$obj->refund_total += $item_obj->total;
						$obj->items[] = $item_obj;
					}
				}
				$order_data->refund_orders[] = $obj;

			}
		}
	}
	/**
	 * The set search props is generated by appsbd
	 *
	 * @param any $filters Its string.
	 * @param any $src_props Its string.
	 */
	public static function order_search_props( &$filters, $src_props ) {
		if ( ! empty( $src_props ) ) {
			foreach ( $src_props as $src_prop ) {
				$filter = array();
				if ( ! empty( $src_prop['prop'] ) && isset( $src_prop['val'] ) ) {
					if ( 'outlet_id' == $src_prop['prop'] ) {
						$src_prop['val']   = trim( $src_prop['val'] );
						$filter['key']     = '_vtp_outlet_id';
						$filter['value']   = intval( $src_prop['val'] );
						$filter['compare'] = '=';

					} elseif ( 'processed_by' == $src_prop['prop'] ) {
						$user_src_prop         = array();
						$user_src_prop['prop'] = '*';
						$user_src_prop['val']  = trim( $src_prop['val'] );
						$users                 = self::order_user( 'U', array( $user_src_prop ) );
						if ( ! empty( $users ) ) {
							$filter['key']     = '_vtp_processed_by';
							$filter['value']   = $users;
							$filter['compare'] = 'IN';
						}
					} elseif ( 'customer' == $src_prop['prop'] ) {
						$user_src_prop         = array();
						$user_src_prop['prop'] = '*';
						$user_src_prop['val']  = trim( $src_prop['val'] );
						$users                 = self::order_user( 'C', array( $user_src_prop ) );
						if ( ! empty( $users ) ) {
							$filter['key']     = '_customer_user';
							$filter['value']   = $users;
							$filter['compare'] = 'IN';
						}
					} elseif ( '*' == $src_prop['prop'] ) {
						$src_prop['val'] = trim( $src_prop['val'] );
						$user            = get_user_by( 'login', $src_prop['val'] );
						if ( !empty($user->ID )) {
							$filter['key']     = '_vtp_processed_by';
							$filter['value']   = $user->ID;
							$filter['compare'] = '=';
						}
						$filters['meta_query'] = array(
							'relation' => 'OR',
							array(
								'key'     => '_order_key',
								'value'   => $src_prop['val'],
								'compare' => 'LIKE',
							),
							array(
								'key'     => '_billing_first_name',
								'value'   => $src_prop['val'],
								'compare' => 'LIKE',
							),
							array(
								'key'     => '_billing_last_name',
								'value'   => $src_prop['val'],
								'compare' => 'LIKE',
							),
						);
						return;

					} elseif ( '_vtp_offline_id' == $src_prop['prop'] ) {
						$src_prop['val']       = trim( $src_prop['val'] );
							$filter['key']     = '_vtp_offline_id';
							$filter['value']   = $src_prop['val'];
							$filter['compare'] = '=';
					} elseif ( 'status' == $src_prop['prop'] ) {
						$filters['status'] = array( trim( $src_prop['val'] ) );
					} elseif ( 'order_id' == $src_prop['prop'] ) {
						$src_prop['val']     = trim( $src_prop['val'], "# \t\n\r\0\x0B" );
						$filters['post__in'] = array( intval( $src_prop['val'] ) );
					} elseif ( 'order_date' == $src_prop['prop'] ) {
						if ( 'eq' == $src_prop['opr'] ) {
							$start_date = $src_prop['val'] . ' 00:00:00';
							$end_date   = $src_prop['val'] . ' 23:59:59';
						} elseif ( 'dr' == $src_prop['opr'] || 'bt' == $src_prop['opr'] ) {
							$prop = (object) $src_prop['val'];
							if ( ! empty( $prop->start ) ) {
								if ( empty( $prop->end ) ) {
									$prop->end = $prop->start;
								}
								$start_date = $prop->start . ' 00:00:00';
								$end_date   = $prop->end . ' 23:59:59';
							} else {
								continue;
							}
						} else {
							continue;
						}
						if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
							$filter['key']     = '_created_date';
							$filter['value']   = array( $start_date, $end_date );
							$filter['compare'] = 'BETWEEN';
						}
					} elseif ( 'dr' == $src_prop->opr ) {
						$prop = (object) $src_prop->val;
						if ( ! empty( $prop->start ) ) {
							if ( empty( $prop->end ) ) {
								$prop->end = $prop->start;
							}
							$start_date = gmdate(
								'Y-m-d 00:00:00',
								strtotime( $prop->start )
							);
							$end_date   = gmdate(
								'Y-m-d 23:59:59',
								strtotime( $prop->end )
							);
							if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
								$filter['key']     = 'date_created';
								$filter['value']   = array( $start_date, $end_date );
								$filter['compare'] = 'BETWEEN';
							}
						}
					}
				}
				if ( ! empty( $filter ) ) {
					if ( ! isset( $filters['vt_meta_query'] ) ) {
						$filters['vt_meta_query'] = array();
					}
					$filters['vt_meta_query'] [] = $filter;
				}
			}
		}
	}

	/**
	 * The order user is generated by appsbd
	 *
	 * @param string $user_type Its string.
	 * @param array  $src_props Its array of src_props.
	 */
	public static function order_user( $user_type, $src_props ) {
		if ( 'U' == $user_type ) {
			$args = array(
				'role__not_in' => array( 'customer', 'subscriber', 'administrator' ),
				'count_total'  => true,
			);
			if ( ! POS_Settings::is_admin_user() && ! current_user_can( 'any-outlet-user-create' ) ) {
				$outlets = get_user_meta( get_current_user_id(), 'outlet_id', true );
				if ( is_array( $outlets ) ) {
					$args['meta_query'][] = array(
						'key'     => 'outlet_id',
						
						
						'value'   => '"(' . implode( '|', $outlets ) . ')"',
						'compare' => 'REGEXP',
					);
				}
			}
		} elseif ( 'C' == $user_type ) {
			$args = array(
				'role__in'    => array( 'customer', 'subscriber' ),
				'count_total' => true,
			);
		}
		POS_Customer::set_search_param( $src_props, $args );
		$args        = wp_parse_args( $args );
		$user_search = new \WP_User_Query( $args );
		$users       = $user_search->get_results();
		$user_ids    = array_column( $users, 'ID' );
		return $user_ids;
	}

	/**
	 * The order sort param is generated by appsbd
	 *
	 * @param any $props It's sort property.
	 * @param any $sort_param It's sort param.
	 */
	public static function order_sort_param( $props, &$sort_param ) {
		foreach ( $props as $prop ) {
			if ( ! empty( $prop['prop'] ) ) {
				$prop['prop'] = strtolower( trim( $prop['prop'] ) );
				$prop['ord']  = strtolower( trim( $prop['ord'] ) );
				if ( in_array( $prop['ord'], array( 'asc', 'desc' ) ) ) {
					$sort_param['orderby'] = $prop['prop'];
					$sort_param['order']   = $prop['ord'];
				}
			}
		}
	}
	/**
	 * The add product item is generated by appsbd
	 *
	 * @param any $item Its string.
	 * @param any $order Its string.
	 */
	public function add_product_item( $item, &$order ) {
		if ( $item instanceof \WC_Order_Item_Product ) {
			$this->items[] = POS_Order_Item::get_product_item( $item, $order );
		}
	}

	/**
	 * The add time by status is generated by appsbd
	 *
	 * @param any $order its order param.
	 * @param any $status its status param.
	 *
	 * @return bool
	 */
	public static function add_time_by_status( $order, $status ) {
		$time_meta_key = '_vt_time_log';
		$time_logs     = $order->get_meta( $time_meta_key );
		if ( ! is_array( $time_logs ) ) {
			$time_logs = array();
		}
		$time_obj         = new \stdClass();
		$time_obj->status = $status;
		$time_obj->time   = gmdate( 'Y-m-d H:i:s' );
		$time_logs[]      = $time_obj;
		if ( vitepos_wc_update_meta( $order->get_id(), $time_meta_key, $time_logs ) ) {
			return true;
		}
		return false;
	}
}
