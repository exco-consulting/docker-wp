<?php
/**
 * Its for Pos Purchase Item module
 *
 * @package VitePos\Modules
 */

namespace VitePos\Modules;

use Appsbd\V1\Core\BaseModule;

/**
 * Class Apbd_pos_purchase_item
 */
class POS_Purchase_Item extends BaseModule {
	/**
	 * The initialize is generated by appsbd
	 */
	public function initialize() {
		 $this->add_ajax_action( 'add', array( $this, 'add' ) );
		 $this->add_ajax_action( 'edit', array( $this, 'edit' ) );
		 $this->add_ajax_action( 'delete_item', array( $this, 'delete_item' ) );

	}

	/**
	 * The OptionForm is generated by appsbd
	 */
	public function option_form() {
		$this->set_title( 'Purchase Item List' );
		$this->set_subtitle( '' );
		$this->display();
	}

	/**
	 * The get menu title is generated by appsbd
	 *
	 * @return mixed Its mixed.
	 */
	public function get_menu_title() {
		return $this->__( 'Purchase Item' );
	}

	/**
	 * The get menu sub title is generated by appsbd
	 *
	 * @return mixed Its mixed.
	 */
	public function get_menu_sub_title() {
		return $this->__( 'View All Purchase Item' );
	}

	/**
	 * The get menu icon is generated by appsbd
	 *
	 * @return string Its string.
	 */
	public function get_menu_icon() {
		return 'fa fa-circle';
	}

	/**
	 * The add is generated by appsbd
	 */
	public function add() {
		 $this->set_title( 'Add New Purchase ' );
		 $this->set_popup_col_class( 'col-sm-6' );

		if ( APPSBD_IS_POST_BACK ) {
			  $nobject = new Mapbd_pos_purchase_item();
			if ( $nobject->SetFromPostData( true ) ) {
				if ( $nobject->Save() ) {
					   $this->add_info( 'Successfully added' );
					   APBD_AddLog( 'A', $nobject->settedPropertyforLog(), 'l001', '' );
					   $this->display_popup_msg();
					   return;
				}
			}
		}
		 $mainobj = new Mapbd_pos_purchase_item();
		 $this->add_view_data( 'isUpdateMode', false );
		 $this->add_view_data( 'mainobj', $mainobj );
		 $this->display_popup( 'add' );
	}

	/**
	 * The edit is generated by appsbd
	 *
	 * @param string $param_id Its string.
	 */
	public function edit( $param_id = '' ) {
		 $this->set_popup_col_class( 'col-sm-6' );

		$param_id = APBD_GetValue( 'id' );
		if ( empty( $param_id ) ) {
			  $this->add_error( 'Invalid request' );
			 $this->display_popup_msg();
			 return;
		}
		 $this->set_title( 'Edit Purchase Item' );
		if ( APPSBD_IS_POST_BACK ) {
				  $uobject = new Mapbd_pos_purchase_item();
			if ( $uobject->SetFromPostData( false ) ) {
				$uobject->SetWhereUpdate( 'id', $param_id );
				if ( $uobject->Update() ) {
						   APBD_AddLog( 'U', $uobject->settedPropertyforLog(), 'l002', '' );
						   $this->add_info( 'Successfully updated' );
						   $this->display_popup_msg();
						   return;
				}
			}
		}
		 $mainobj = new Mapbd_pos_purchase_item();
		 $mainobj->id( $param_id );
		if ( ! $mainobj->Select() ) {
				$this->add_error( 'Invalid request' );
			   $this->display_popup_msg();
			   return;
		}
			  APBD_OldFields( $mainobj, 'purchase_id,product_id,purchase_cost,stock_quantity,product_name,in_stock,bar_code,total_cost' );
			  			  $this->add_view_data( 'mainobj', $mainobj );
			  $this->add_view_data( 'isUpdateMode', true );
			  $this->display_popup( 'add' );
	}

	/**
	 * The data is generated by appsbd
	 */
	public function data() {
		 $main_response = new AppsbdAjaxDataResponse();
		 $main_response->setDownloadFileName( 'apbd-pos-purchase-item-list' );
		 $mainobj = new Mapbd_pos_purchase_item();
		 $main_response->setDateRange( $mainobj );
		 $records = $mainobj->CountALL( $main_response->src_item, $main_response->src_text, $main_response->multiparam, 'after' );
		if ( $records > 0 ) {
			  $main_response->SetGridRecords( $records );

			  			  $result = $mainobj->SelectAllGridData( '', $main_response->order_by, $main_response->order, $main_response->rows, $main_response->limit_start, $main_response->src_item, $main_response->src_text, $main_response->multiparam, 'after' );
			if ( $result ) {

				foreach ( $result as &$data ) {
					   $data->action  = '';
					   $data->action .= "<a data-effect='mfp-move-from-top' class='popupformWR btn btn-info btn-xs' href='" . $this->get_action_url( 'edit', array( 'id' => $data->id ) ) . "'>" . $this->__( 'Edit' ) . '</a>';
					   $data->action .= " <a class='ConfirmAjaxWR btn btn-danger btn-xs' data-on-complete='APPSBDAPPJS.confirmAjax.ConfirmWRChange' data-msg='" . $this->__( 'Are you sure to delete?' ) . "' href='" . $this->get_action_url( 'delete_item', array( 'id' => $data->id ) ) . "'>" . $this->__( 'Delete' ) . '</a>';

				}
			}
			  $main_response->SetGridData( $result );
		}
		 $main_response->DisplayGridResponse();
	}

	/**
	 * The delete item is generated by appsbd
	 *
	 * @param string $param Its string.
	 */
	public function delete_item( $param = '' ) {
		$main_response = new AppsbdAjaxConfirmResponse();
				$main_response->DisplayWithResponse( false, __( 'Delete is temporary disabled' ) );
		return;
		if ( empty( $param ) ) {
			 $main_response->DisplayWithResponse( false, __( 'Invalid Request' ) );
			 return;
		}
		$mr = new Mapbd_pos_purchase_item();
		$mr->id( $param );
		if ( $mr->Select() ) {
			if ( Mapbd_pos_purchase_item::DeleteByKeyValue( 'id', $param ) ) {
				APBD_AddLog( 'D', "id={$param}", 'l003', 'Wp_apbd_pos_purchase_item_confirm' );
				$main_response->DisplayWithResponse( true, __( 'Successfully deleted' ) );
			} else {
				$main_response->DisplayWithResponse( false, __( 'Delete failed try again' ) );
			}
		}
	}




}
