<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

/* @var $transaction OsTransactionModel */ ?>
<div class="quick-transaction-info-w"
	data-os-action="<?php echo OsRouterHelper::build_route_name('transactions', 'edit_form'); ?>"
	data-os-params="<?php echo OsUtilHelper::build_os_params(['id' => $transaction->id]) ?>"
	data-os-after-call="latepoint_init_quick_transaction_form"
	data-os-before-after="replace">
  <div class="quick-transaction-head">
    <div class="quick-transaction-amount"><?php echo OsMoneyHelper::format_price($transaction->amount, true, false); ?></div>
    <div class="lp-processor-logo lp-processor-logo-<?php echo $transaction->processor; ?>"><?php echo $transaction->processor; ?></div>
    <div class="lp-transaction-status lp-transaction-status-<?php echo $transaction->status; ?>"><?php echo $transaction->status; ?></div>
  </div>
  <div class="quick-transaction-sub">
    <div>
	    <?php echo $transaction->formatted_created_date(OsSettingsHelper::get_date_format()); ?></div>
    <div><?php echo $transaction->token; ?></div>
  </div>
</div>