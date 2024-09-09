<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

/* @var $order OsOrderModel */
?>

<div class="os-form-sub-header">
  <h3><?php _e('Balance & Payments', 'latepoint'); ?></h3>
  <div class="os-form-sub-header-actions">
    <?php echo OsFormHelper::select_field('order[payment_status]', false, OsOrdersHelper::get_order_payment_statuses_list(), $order->payment_status, ['class' => 'size-small']) ?>
  </div>
</div>
<div class="balance-payment-info" data-route="<?php echo OsRouterHelper::build_route_name('orders', 'reload_balance_and_payments') ?>">
  <div class="payment-info-values">
    <?php
    $total_paid = $order->get_total_amount_paid_from_transactions();
    $total_balance = $order->get_total_balance_due();

		?>
    <div class="pi-smaller">
      <?php echo OsMoneyHelper::format_price($total_paid, true, false); ?>
    </div>
    <div class="pi-balance-due <?php if($total_balance > 0) echo 'pi-red'; ?>">
      <?php echo OsMoneyHelper::format_price($total_balance, true, false); ?>
    </div>
  </div>
  <div class="payment-info-labels">
    <div><?php _e('Total Payments', 'latepoint') ?></div>
    <div><?php _e('Balance Due', 'latepoint') ?></div>
  </div>
</div>