<?php
/**
 * @var $order OsOrderModel
 * @var $booking OsBookingModel
 * @var $bundles OsBundleModel[]
 * @var $services OsServiceModel[]
 * @var $transactions OsTransactionModel[]
 * @var $customers OsCustomerModel[]
 * @var $order_bookings OsBookingModel[]
 * @var $order_bundles OsBundleModel[]
 * @var $preselected_booking OsBundleModel
 * @var $preselected_order_item OsOrderItemModel
 * @var $show_only_preselected_items bool
 **/ ?>



<div class="os-form-w quick-order-form-w <?php echo ($order->is_new_record()) ? 'is-new-order' : 'is-existing-order' ;?>" data-refresh-route-name="<?php echo OsRouterHelper::build_route_name('orders', 'quick_edit'); ?>">
  <form action=""
    data-route-name="<?php echo ($order->is_new_record()) ? OsRouterHelper::build_route_name('orders', 'create') : OsRouterHelper::build_route_name('orders', 'update'); ?>"
    class="order-quick-edit-form">
    <div class="os-form-header">
      <?php if($order->is_new_record()){ ?>
        <h2><?php _e('New Order', 'latepoint'); ?></h2>
      <?php }else{ ?>
        <h2><?php _e('Edit Order', 'latepoint'); ?></h2>
      <?php } ?>
      <a href="#" class="latepoint-side-panel-close latepoint-side-panel-close-trigger"><i class="latepoint-icon latepoint-icon-x"></i></a>
    </div>
    <div class="os-form-content">
	    <?php if(!$order->is_new_record()){ ?>
	    <div class="quick-booking-info">
		    <?php echo '<span>'.__('Order ID:', 'latepoint').'</span><strong>'. $order->id.'</strong>'; ?>
		    <?php echo '<span>'.__('Code:', 'latepoint').'</span><strong>'. $order->confirmation_code.'</strong>'; ?>
		    <?php if(OsAuthHelper::get_current_user()->has_capability('activity__view')) echo '<a href="#" data-order-id="'.$order->id.'" data-route="'.OsRouterHelper::build_route_name('orders', 'view_order_log').'" class="quick-order-form-view-log-btn"><i class="latepoint-icon latepoint-icon-clock"></i>'.__('History', 'latepoint').'</a>'; ?>
	    </div>
	    <?php } ?>
      <div class="os-row">
        <div class="os-col-6">
          <?php echo OsFormHelper::select_field('order[status]', __('Order Status', 'latepoint'), OsOrdersHelper::get_order_statuses_list(), $order->status, array('placeholder' => __('Set Status', 'latepoint'))); ?>
        </div>
        <div class="os-col-6">
          <?php echo OsFormHelper::select_field('order[fulfillment_status]', __('Fulfillment Status', 'latepoint'), OsOrdersHelper::get_fulfillment_statuses_list(), $order->fulfillment_status, array('placeholder' => __('Set Status', 'latepoint'))); ?>
        </div>
      </div>

      <div class="order-items-info-w <?php if(empty($bundles)) echo 'no-bundles'; ?> <?php if($show_only_preselected_items) echo 'show-preselected-only'; ?>">
        <div class="os-form-sub-header">
          <h3><?php _e('Order Items', 'latepoint'); ?></h3>
          <div class="os-form-sub-header-actions">
	          <?php if(OsCartsHelper::can_checkout_multiple_items()){ ?>
            <a href="#" data-add-label="<?php _e('Add Another Item', 'latepoint'); ?>" data-cancel-label="<?php _e('Cancel', 'latepoint'); ?>" class="latepoint-btn latepoint-btn-sm latepoint-btn-link order-form-add-item-btn"
               data-booking-form-route-name="<?php echo OsRouterHelper::build_route_name('orders', 'generate_booking_order_item_block'); ?>"
               data-bundle-form-route-name="<?php echo OsRouterHelper::build_route_name('orders', 'generate_bundle_order_item_block'); ?>"
               data-fold-booking-data-route-name="<?php echo OsRouterHelper::build_route_name('orders', 'fold_booking_data_form'); ?>">
              <i class="latepoint-icon latepoint-icon-plus"></i><span><?php _e('Add Another Item', 'latepoint'); ?></span>
            </a>
	          <?php } ?>
          </div>
        </div>
	      <?php if(!empty($bundles)) { ?>
		      <div class="new-order-item-list-bundles-wrapper">
			      <div class="centered-question-label"><?php _e('Select a bundle that you want to add', 'latepoint'); ?></div>
			      <div class="new-order-item-list-bundles">
				      <?php
				      foreach($bundles as $bundle){
								echo '<div class="new-order-item-list-bundle" 
													data-os-after-call="latepoint_bundle_added_to_quick_order"
													data-os-output-target=".order-items-list"
													data-os-output-target-do="prepend"
												  data-os-action="'.OsRouterHelper::build_route_name('orders', 'generate_bundle_order_item_block').'"
													data-os-params="'.OsUtilHelper::build_os_params(['order_id' => $order->id, 'bundle_id' => $bundle->id]).'">
												<div class="noi-description-wrapper">
													<div class="noi-label">'.$bundle->name.'</div>
													<div class="noi-description">'.implode(', ', $bundle->get_service_and_quantity_descriptions()).'</div>
												</div>
												<div class="noi-price">'.$bundle->get_formatted_charge_amount().'</div>
											</div>';
				      }
							?>
			      </div>
		      </div>
		      <?php if($bundles){ ?>
		      <div class="new-order-item-variant-selector-wrapper">
			      <div class="centered-question-label"><?php _e('What type of item would you like to add?', 'latepoint'); ?></div>
			      <div class="new-order-item-variant-selector">
				      <div class="new-order-item-variant new-order-item-variant-booking">
					      <i class="latepoint-icon latepoint-icon-calendar2"></i>
					      <div><?php _e('Booking', 'latepoint'); ?></div>
				      </div>
				      <div class="new-order-item-variant new-order-item-variant-bundle">
					      <i class="latepoint-icon latepoint-icon-layers"></i>
					      <div><?php _e('Bundle', 'latepoint'); ?></div>
				      </div>
			      </div>
		      </div>
		      <?php } ?>
	      <?php } ?>
        <div class="order-items-list">
	        <?php if(empty($order_bookings) && empty($order_bundles)){
						echo '<div class="no-results">'.__('Order is empty', 'latepoint').'</div>';
	        }else{ ?>
		        <?php foreach($order_bundles as $order_item_id => $order_bundle){
							$preselected_bundle_booking_id = ($preselected_order_item && ($preselected_order_item->id == $order_item_id)) ? $preselected_booking->id : false;
							$preselected_css = ($preselected_bundle_booking_id ? 'holds-preselected-booking is-open' : '');
							echo '<div class="order-item order-item-variant-bundle '.$preselected_css.'" data-order-item-id="'.$order_item_id.'">';
							echo OsOrdersHelper::generate_order_item_pill_for_bundle($order_bundle, $order_item_id, $preselected_bundle_booking_id);
							echo '</div>';
		        }?>
		        <?php foreach($order_bookings as $order_item_id => $order_booking){
							$unfold = (count($order_bookings) == 1 && empty($order_bundles) || ($preselected_booking && $preselected_booking->id == $order_booking->id));
							$preselected_css = $unfold ? 'holds-preselected-booking is-open' : '';

							echo '<div class="order-item order-item-variant-booking '.$preselected_css.'" data-order-item-id="'.$order_item_id.'">';
							echo OsOrdersHelper::booking_data_form_for_order_item_id($order_item_id, $order_booking, LATEPOINT_ITEM_VARIANT_BOOKING, !$unfold);
							echo '</div>';
		        }
	        }
					?>
        </div>
	      <?php if($show_only_preselected_items){
					$total_count_other_items = count($order->get_items()) - 1;
                    if($total_count_other_items > 0){
                        echo '<div class="hidden-order-items-notice">';
                            if($total_count_other_items > 1){
                                echo '<div class="hidden-order-items-notice-message">'.sprintf(__('There are %d more items in this order.', 'latepoint'), ($total_count_other_items)).'</div>';
                            }else{
                                echo '<div class="hidden-order-items-notice-message">'.__('There is one more item in this order.', 'latepoint').'</div>';
                            }
                            echo '<div class="hidden-order-items-notice-link">'.__('Show All Items', 'latepoint').'</div>';
                        echo '</div>';
                    }
	      } ?>
      </div>

      <div class="customer-info-w selected">
        <div class="os-form-sub-header">
          <h3><?php _e('Customer', 'latepoint'); ?></h3>
          <div class="os-form-sub-header-actions">
	          <?php if(OsRolesHelper::can_user('customer__create')){ ?>
            <a href="#" class="latepoint-btn latepoint-btn-sm latepoint-btn-link customer-info-create-btn"
              data-os-output-target=".customer-quick-edit-form-w"
               data-os-after-call="latepoint_quick_order_customer_cleared"
              data-os-action="<?php echo OsRouterHelper::build_route_name('customers', 'inline_edit_form'); ?>">
              <i class="latepoint-icon latepoint-icon-plus"></i><span><?php _e('New', 'latepoint'); ?></span>
            </a>
	          <?php } ?>
            <a href="#" class="latepoint-btn latepoint-btn-sm latepoint-btn-link customer-info-load-btn">
              <i class="latepoint-icon latepoint-icon-search"></i><span><?php _e('Find', 'latepoint'); ?></span>
            </a>
          </div>
        </div>
        <div class="customers-selector-w">
          <div class="customers-selector-search-w">
            <i class="latepoint-icon latepoint-icon-search"></i>
            <input type="text" data-route="<?php echo OsRouterHelper::build_route_name('customers', 'query_for_booking_form'); ?>" class="customers-selector-search-input" placeholder="<?php _e('Start typing to search...', 'latepoint'); ?>">
            <span class="customers-selector-cancel">
              <i class="latepoint-icon latepoint-icon-x"></i>
              <span><?php _e('cancel', 'latepoint'); ?></span>
            </span>
          </div>
          <?php if($customers){ ?>
            <div class="customers-options-list">
              <?php foreach($customers as $customer){ ?>
                <div class="customer-option" data-os-params="<?php echo OsUtilHelper::build_os_params(['customer_id' => $customer->id]); ?>"
                    data-os-after-call="latepoint_quick_order_customer_selected"
                    data-os-after-call-error="latepoint_quick_order_customer_selected"
                    data-os-output-target=".customer-quick-edit-form-w"
                    data-os-action="<?php echo OsRouterHelper::build_route_name('customers', 'inline_edit_form'); ?>">
                  <div class="customer-option-avatar" style="background-image: url(<?php echo OsCustomerHelper::get_avatar_url($customer); ?>)"></div>
                  <div class="customer-option-info">
                    <h4 class="customer-option-info-name"><span><?php echo esc_html($customer->full_name); ?></span></h4>
                    <ul>
                      <li>
                        <?php _e('Email: ','latepoint'); ?>
                        <strong><?php echo esc_html($customer->email); ?></strong>
                      </li>
                      <li>
                        <?php _e('Phone: ','latepoint'); ?>
                        <strong><?php echo esc_html($customer->phone); ?></strong>
                      </li>
                    </ul>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
        <div class="customer-quick-edit-form-w">
          <?php require(LATEPOINT_VIEWS_ABSPATH.'customers/inline_edit_form.php'); ?>
        </div>
      </div>
      <div>
        <div class="os-form-sub-header">
          <h3><?php _e('Price Breakdown', 'latepoint'); ?></h3>
          <div class="os-form-sub-header-actions">
            <a href="#" class="latepoint-btn latepoint-btn-sm latepoint-btn-link reload-price-breakdown"
              data-route="<?php echo OsRouterHelper::build_route_name('orders', 'reload_price_breakdown') ?>">
              <i class="latepoint-icon latepoint-icon-refresh-cw"></i>
	            <span><?php _e('Recalculate', 'latepoint'); ?></span>
            </a>
          </div>
        </div>
	      <div class="price-breakdown-wrapper">
		      <?php include '_price_breakdown.php'; ?>
	      </div>
      </div>
			<div class="balance-payment-wrapper">
		    <?php include '_balance_and_payments.php'; ?>
			</div>

      <?php if(OsRolesHelper::can_user('transaction__view')){ ?>
	      <div class="transactions-info-w">
	        <div class="os-form-sub-header">
	          <h3><?php _e('Transactions', 'latepoint'); ?></h3>
	        </div>
	        <div class="quick-transactions-list-w">
	        <?php
	          if($transactions){
	            foreach ($transactions as $transaction):
	              include '_transaction_box.php';
	            endforeach;
					  }
	        ?>
	        </div>
		      <?php if(OsRolesHelper::can_user('transaction__create')){ ?>
	        <div class="quick-add-transaction trigger-add-transaction-btn"
	             data-os-after-call="latepoint_init_quick_transaction_form"
	             data-os-before-after="before"
	             data-os-action="<?php echo OsRouterHelper::build_route_name('transactions', 'edit_form'); ?>">
	          <i class="latepoint-icon latepoint-icon-plus2"></i>
	          <span><?php _e('Add Transaction', 'latepoint'); ?></span>
	        </div>
		      <?php } ?>
	      </div>
      <?php } ?>
    </div>
    <div class="os-form-buttons os-quick-form-buttons">
      <?php if($order->is_new_record()) { ?>
        <button type="submit" class="latepoint-btn latepoint-btn-block latepoint-btn-lg"><?php _e('Create Order', 'latepoint'); ?></button>
      <?php }else{ ?>
        <div class="os-full">
          <button type="submit" class="latepoint-btn latepoint-btn-block latepoint-btn-lg"><?php _e('Save Changes', 'latepoint'); ?></button>
        </div>
        <div class="os-compact">
          <a href="#"
              data-os-success-action="reload"
              data-os-action="<?php echo OsRouterHelper::build_route_name('orders', 'destroy'); ?>"
              data-os-params="<?php echo OsUtilHelper::build_os_params(['id' => $order->id], 'destroy_order_'.$order->id); ?>"
              data-os-prompt="<?php _e('Are you sure you want to delete this order? All appointments that are attached ot this order will be removed as well', 'latepoint'); ?>"
              class="latepoint-delete-order latepoint-btn latepoint-btn-secondary latepoint-btn-lg latepoint-btn-just-icon"
              title="<?php _e('Delete Order', 'latepoint'); ?>">
                <i class="latepoint-icon latepoint-icon-trash1"></i>
          </a>
        </div>
      <?php } ?>
    </div>
    <?php
    echo OsFormHelper::hidden_field('order[id]', $order->id);
		wp_nonce_field($order->is_new_record() ? 'new_order' : 'edit_order_'.$order->id);
    ?>
  </form>
</div>