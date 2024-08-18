<?php
/**
 * Its pos product query class
 *
 * @since: 10/04/2023
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos\Libs
 */

namespace VitePos\Libs;

/**
 * Class POS Product
 *
 * @package VitePos\Libs
 */
class POS_Product_Query {
	/**
	 * Its property wp_post
	 *
	 * @var string
	 */
	protected $wp_post;
	/**
	 * Its property wp_post_meta
	 *
	 * @var string
	 */
	protected $wp_post_meta;
	/**
	 * Its property limit_str
	 *
	 * @var string
	 */
	protected $limit_str = '';
	/**
	 * Its property select
	 *
	 * @var string
	 */
	protected $select = '';
	/**
	 * Its property join
	 *
	 * @var string
	 */
	protected $join = '';
	/**
	 * Its property group_by
	 *
	 * @var string
	 */
	protected $group_by = '';
	/**
	 * Its property src_props
	 *
	 * @var array|mixed
	 */
	private $src_props;
	/**
	 * Its property sort_props
	 *
	 * @var array|mixed
	 */
	private $sort_props;
	/**
	 * Its property post_types
	 *
	 * @var mixed|string[]
	 */
	private $post_types;
	/**
	 * Its property post_status
	 *
	 * @var mixed|string[]
	 */
	private $post_status;
	/**
	 * Its property where
	 *
	 * @var string
	 */
	protected $where = '';
	/**
	 * Its property order_by
	 *
	 * @var string
	 */
	protected $order_by = '';
	/**
	 * Its property from
	 *
	 * @var string
	 */
	protected $from = '';
	/**
	 * Its property joined
	 *
	 * @var bool
	 */
	protected $joined = false;
	/**
	 * Its property joined_list
	 *
	 * @var array
	 */
	protected $joined_list = array();
	/**
	 * Its property db
	 *
	 * @var \QM_DB|\wpdb
	 */
	protected $db;
	/**
	 * Its property where_prefix
	 *
	 * @var string
	 */
	private $where_prefix='';

	/**
	 * To get group by you can call this
	 *
	 * @return string
	 */
	protected function get_group_by() {

		return ! empty( $this->group_by ) ? ' GROUP BY ' . $this->group_by : '';
	}


	/**
	 * The get order by is generated by appsbd
	 *
	 * @return string
	 */
	protected function get_order_by() {

		return ! empty( $this->order_by ) ? ' ORDER BY ' . $this->order_by : '';
	}


	/**
	 * POS_Product_query constructor.
	 *
	 * @param int      $page Its page param.
	 * @param int      $limit Its limit param.
	 * @param array    $src_props Its src_props param.
	 * @param array    $sort_props Its sort_props param.
	 * @param string[] $post_types Its post_types param.
	 * @param string[] $post_status Its post_status param.
	 */
	public function __construct( $page = 1, $limit = 10, $src_props = array(), $sort_props = array(), $post_types = array( 'product' ), $post_status = array( 'publish' ) ) {
		global $wpdb;
		$this->db           =& $wpdb;
		$this->wp_post      = $wpdb->prefix . 'posts';
		$this->wp_post_meta = $wpdb->prefix . 'postmeta';
		$this->post_types   = $post_types;

		$this->post_status = $post_status;

		$this->select = "SELECT SQL_CALC_FOUND_ROWS {$this->wp_post}.ID";
		if ( $limit > 0 ) {
			$limit_start     = ( $page * $limit ) - $limit;
			$this->limit_str = "LIMIT {$limit_start},{$limit}";
		}
		$this->group_by   = "{$this->wp_post}.ID ";
		$this->from       = "{$this->wp_post}";
		$this->order_by   = '';
		$this->src_props  = $src_props;
		$this->sort_props = $sort_props;

	}

	/**
	 * The reset where is generated by appsbd
	 */
	public function reset_where() {
		$in_status     = "('" . implode( "','", $this->post_status ) . "')";
		$in_post_types = "('" . implode( "','", $this->post_types ) . "')";

		$this->where_prefix = "1=1 
		AND {$this->wp_post}.post_type IN {$in_post_types} 
		AND  {$this->wp_post}.post_status in {$in_status}";
	}

	/**
	 * The set join is generated by appsbd
	 */
	public function set_join() {
		if ( ! $this->joined ) {
			$this->joined = true;
			$this->join  .= "INNER JOIN {$this->wp_post_meta} ON ( {$this->wp_post}.ID = {$this->wp_post_meta}.post_id )";
		}
	}

	/**
	 * The set join table is generated by appsbd
	 *
	 * @param mixed  $name Its name param.
	 * @param mixed  $meta_key Its meta_key param.
	 * @param string $type Its type param.
	 */
	public function set_join_table( $name, $meta_key, $type = 'INNER' ) {
		if ( empty( $this->joined_list[ $name ] ) ) {
			$this->joined_list[ $name ] = true;
			$this->join                .= "$type JOIN {$this->wp_post_meta} as $name ON ( {$this->wp_post}.ID = $name.post_id and  $name.meta_key='$meta_key')";
		}
	}

	/**
	 * The get term ids by term id is generated by appsbd
	 *
	 * @param mixed $term_id Its term_id param.
	 *
	 * @return array|null
	 */
	protected function get_term_ids_by_term_id( $term_id ) {
		$term_id          = intval( $term_id );
		$wp_term_taxonomy = $this->db->prefix . 'term_taxonomy';
		$sub_query        = "SELECT  t.term_id
			FROM wp_terms AS t  
			INNER JOIN {$wp_term_taxonomy} AS tt ON t.term_id = tt.term_id
			WHERE tt.taxonomy IN ('product_cat') AND tt.term_id =$term_id or tt.parent=$term_id and tt.count>0";
		$result           = $this->db->get_results( $sub_query );
		if ( empty( $result ) ) {
			return null;
		}
		$term_ids = array();
		if ( ! empty( $result ) ) {
			foreach ( $result as $item ) {
				$term_ids[] = $item->term_id;
			}
		}

		return $term_ids;
	}

	/**
	 * The get term ids by slug is generated by appsbd
	 *
	 * @param mixed $slug Its slug param.
	 *
	 * @return array|null
	 */
	protected function get_term_ids_by_slug( $slug ) {

		$term_row = $this->db->get_row( "SELECT wp_terms.term_id FROM wp_terms  WHERE wp_terms.slug ='$slug'" );
		if ( ! empty( $term_row->term_id ) ) {
			return $this->get_term_ids_by_term_id( $term_row->term_id );
		}

		return null;
	}

	/**
	 * The set terms in query is generated by appsbd
	 *
	 * @param mixed $terms_ids Its terms_ids param.
	 */
	public function set_terms_in_query( $terms_ids ) {
		$in_terms              = "('" . implode( "','", $terms_ids ) . "')";
		$wp_term_relationships = $this->db->prefix . 'term_relationships';
		$this->join           .= "LEFT JOIN {$wp_term_relationships} ON (wp_posts.ID = {$wp_term_relationships}.object_id)";
		$this->where          .= "AND ({$wp_term_relationships}.term_taxonomy_id IN {$in_terms})";
	}

	/**
	 * The get query sql is generated by appsbd
	 *
	 * @return string
	 */
	protected function get_query_sql() {
		$this->reset_where();
		if ( ! empty( $this->src_props ) ) {
			$has_star = false;
			foreach ( $this->src_props as $src_prop ) {
				if(is_string($src_prop['val'])) {
					$prop_val = esc_sql( appsbd_get_alphanumeric( $src_prop['val'] ) );
				}else{
					$prop_val=$src_prop['val'];
				}
				if ( '*' == $src_prop['prop'] ) {
					$this->set_join();
					$has_star     = true;
					$this->where .= "
					AND(({$this->wp_post}.post_title LIKE '%{$prop_val}%') 
						OR ({$this->wp_post_meta}.meta_key = '_sku' AND {$this->wp_post_meta}.meta_value LIKE '%{$prop_val}%' ) 
						OR ({$this->wp_post}.ID = '{$prop_val}')
					)";
				} elseif ( 'status' == $src_prop['prop'] ) {
					$this->post_status=array($prop_val);
					$this->reset_where();
				}elseif ( '_vt_is_favorite' == $src_prop['prop'] ) {
					$this->set_join_table( 'mt1', '_vt_is_favorite', 'LEFT' );
					if ( 'Y' == $prop_val ) {
						$this->where .= "AND mt1.meta_value='Y' ";
					} elseif ( 'N' == $prop_val ) {
						$this->where .= "AND (mt1.meta_value='N' OR mt1.post_id IS NULL)";
					}
				} elseif ( '_vt_purchase_price_change' == $src_prop['prop'] ) {
					$this->join .= "LEFT JOIN {$this->wp_post_meta} as mt2 ON ( {$this->wp_post}.ID = mt2.post_id AND  mt2.meta_key='_vt_purchase_price_change')";
					if ( 'Y' == $prop_val ) {
						$this->where .= "AND (mt2.meta_value='Y' )";
					} elseif ( 'N' == $prop_val ) {
						$this->where .= "AND (mt2.meta_value='N' OR mt2.post_id IS NULL)";
					}
				} elseif ( 'manage_stock' == $src_prop['prop'] && isset( $src_prop['val'] ) ) {
					$this->set_join();
					$this->join .= "INNER JOIN {$this->wp_post_meta} as mt3 ON ( {$this->wp_post}.ID = mt3.post_id AND  mt3.meta_key='_manage_stock')";
					if ( ! empty( $prop_val ) ) {
						$this->where .= "AND (mt3.meta_value='yes')";
					} else {
						$this->where .= "AND (mt3..meta_value='no'";
					}
				}elseif ( 'category_id' == $src_prop['prop'] && isset( $prop_val ) && 'all_cat' != $src_prop['val'] ) {
					$terms_ids = $this->get_term_ids_by_term_id( $prop_val );
					if ( ! empty( $terms_ids ) ) {
						$this->set_terms_in_query( $terms_ids );
					} else {
						$this->where .= 'AND(0=1)';
					}
				} elseif ( 'category' == $src_prop['prop'] && isset( $prop_val ) && 'all_cat' != $prop_val ) {
					$terms_ids = $this->get_term_ids_by_slug( $prop_val );
					if ( ! empty( $terms_ids ) ) {
						$this->set_terms_in_query( $terms_ids );
					} else {
						$this->where .= 'AND(0=1)';
					}
				} elseif ( 'name' == $src_prop['prop'] ) {
					if ( ! $has_star ) {
						$this->where .= " AND({$this->wp_post}.post_title LIKE '%{$prop_val}%')";
					}
				} elseif ( 'price' == $src_prop['prop'] ) {
										$this->set_join_table( 'mtp', '_price', 'INNER' );
					if ( 'bt' == $src_prop['opr'] && isset( $src_prop['val'] ) ) { 						$from         = floatval( $src_prop['val']['start'] );
						$to           = floatval( $src_prop['val']['end'] );
						$this->where .= " AND ( CAST(mtp.meta_value AS SIGNED) BETWEEN $from AND $to ) ";

					} elseif ( in_array(
						$src_prop['opr'],
						array( 'gt', 'lt', 'ge', 'le', 'eq' )
					) && isset( $src_prop['val'] ) ) {
						$opr          = array(
							'eq' => '=',
							'gt' => '>',
							'lt' => '<',
							'ge' => '>=',
							'le' => '<=',
						);
						$amount       = floatval( $src_prop['val'] );
						$to_per       = isset( $opr[ $src_prop['opr'] ] ) ? $opr[ $src_prop['opr'] ] : '>=';
						$this->where .= " AND ( CAST(mtp.meta_value AS SIGNED) {$to_per} {$amount} ";
					}
				}
			}
		}

		if ( ! empty( $this->sort_props ) ) {
			foreach ( $this->sort_props as $prop ) {
				if ( 'is_favorite' == $prop['prop'] ) {
					$this->set_join_table( 'mt1', '_vt_is_favorite', 'LEFT' );
					$prop['ord']    = 'asc' == strtolower( $prop['ord'] ) ? 'desc' : 'asc';
					$this->order_by = "mt1.meta_value ".$prop['ord'];
				} elseif ( 'price' == $prop['prop'] ) {
					$this->set_join_table( 'mtp', '_price', 'INNER' );
					$this->order_by = "mtp.meta_value ";
				} elseif ( 'name' == $prop['prop'] ) {
					$this->order_by = "{$this->wp_post}.post_title ".$prop['ord'];
				} elseif ( 'id' == $prop['prop'] ) {
					$this->order_by = "{$this->wp_post}.ID ".$prop['ord'];
				}
			}
		}

		return "{$this->select} FROM {$this->from} {$this->join} WHERE {$this->where_prefix} {$this->where} {$this->get_group_by()} {$this->get_order_by()} {$this->limit_str}";
	}

	/**
	 * The get product ids is generated by appsbd
	 *
	 * @return \stdClass
	 */
	public function get_product_ids() {
		$query       = $this->get_query_sql();
		$data        = new \stdClass();
		$data->posts = array();
		$products    = $this->db->get_results( $query );
		foreach ( $products as $product ) {
			$data->posts[] = $product->ID;
		}
		$count             = $this->db->get_row( 'SELECT FOUND_ROWS() as total' );
		$data->found_posts = ! empty( $count->total ) ? $count->total : 0;
		return $data;
	}

	/**
	 * The get products is generated by appsbd
	 *
	 * @param int      $page Its page param.
	 * @param int      $limit Its limit param.
	 * @param array    $src_props Its src_props param.
	 * @param array    $sort_props Its sort props param.
	 * @param string[] $post_types Its post types param.
	 * @param string[] $post_status Its post status param.
	 *
	 * @return \stdClass
	 */
	public static function get_products( $page = 1, $limit = 10, $src_props = array(), $sort_props = array(), $post_types = array( 'product' ), $post_status = array( 'publish' ) ) {
		$obj = new self( $page, $limit, $src_props, $sort_props, $post_types, $post_status );
		return $obj->get_product_ids();
	}
}
