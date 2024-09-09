<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

/**
 * @var $order OsOrderModel
 */
?>
<div class="customer-order status-<?php echo $order->status; ?>" data-id="<?php echo $order->id; ?>" data-route-name="<?php echo OsRouterHelper::build_route_name('customer_cabinet', 'reload_order_tile'); ?>">
	<div class="customer-order-confirmation">
		<?php echo $order->confirmation_code; ?>
	</div>
	<div class="customer-order-datetime">
		<?php echo OsTimeHelper::get_readable_date(new OsWpDateTime($order->created_at)); ?>
	</div>
	<?php OsPriceBreakdownHelper::output_price_breakdown($order->generate_price_breakdown_rows()); ?>
	<div class="customer-order-bottom-actions">
		<div class="load-booking-summary-btn-w">
			<a href="#"
			   class="latepoint-btn latepoint-btn-primary latepoint-btn-outline latepoint-btn-block"
			   data-os-after-call="latepoint_init_order_summary_lightbox"
			   data-os-params="<?php echo OsUtilHelper::build_os_params(['order_id' => $order->id]) ?>"
			   data-os-action="<?php echo OsRouterHelper::build_route_name('customer_cabinet', 'view_order_summary_in_lightbox'); ?>"
			   data-os-output-target="lightbox"
				data-os-lightbox-classes="width-500 customer-dashboard-booking-summary-lightbox">
				<i class="latepoint-icon latepoint-icon-list"></i>
				<span><?php _e('View Summary', 'latepoint'); ?></span>
			</a>
		</div>
		<?php if(OsPaymentsHelper::is_accepting_payments()){ ?>
			<?php if(OsSettingsHelper::is_on('show_pay_balance_button') && $order->get_total_balance_due()){ ?>
				<a href="#" class="latepoint-btn latepoint-btn-primary latepoint-btn-outline latepoint-btn-block">
					<i class="latepoint-icon latepoint-icon-calendar"></i>
					<span><?php _e('Pay Balance', 'latepoint'); ?></span>
				</a>
			<?php } ?>
		<?php } ?>
	</div>
</div>