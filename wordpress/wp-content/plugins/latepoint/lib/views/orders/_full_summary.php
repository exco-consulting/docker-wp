<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

/**
 * @var $order OsOrderModel
 */
?>
<div class="full-summary-wrapper">
	<?php
	/**
	 * Order Summary - before
	 *
	 * @param {OsOrderModel} $order instance of order model
	 *
	 * @since 5.0.0
	 * @hook latepoint_order_full_summary_before
	 *
	 */
	do_action( 'latepoint_order_full_summary_before', $order ); ?>
    <div class="full-summary-head-info">
		<?php
		/**
		 * Order Summary Head Section - before
		 *
		 * @param {OsOrderModel} $order instance of order model
		 *
		 * @since 5.0.0
		 * @hook latepoint_order_full_summary_head_info_before
		 *
		 */
		do_action( 'latepoint_order_full_summary_head_info_before', $order ); ?>
        <div class="full-summary-number"><?php _e( 'Confirmation #', 'latepoint' ); ?>
            <strong><?php echo $order->confirmation_code; ?></strong></div>
        <div class="booking-full-summary-actions">
            <a href="<?php echo OsRouterHelper::build_admin_post_link( [
				'customer_cabinet',
				'print_order_info'
			], [ 'latepoint_order_id' => $order->id ] ); ?>" class="print-booking-btn" target="_blank"><i
                        class="latepoint-icon latepoint-icon-printer"></i><span><?php _e( 'Print', 'latepoint' ); ?></span></a>
        </div>
		<?php
		/**
		 * Order Summary Head Section - after
		 *
		 * @param {OsOrderModel} $order instance of order model
		 *
		 * @since 5.0.0
		 * @hook latepoint_order_full_summary_head_info_after
		 *
		 */
		do_action( 'latepoint_order_full_summary_head_info_after', $order ); ?>
    </div>
    <div class="full-summary-info-w">
		<?php include( LATEPOINT_VIEWS_ABSPATH . 'steps/partials/_order_summary.php' ); ?>
    </div>
</div>