<form class="os-coupon-form os-coupon-status-<?php echo $coupon->status; ?> <?php if($coupon->is_new_record()) echo 'os-is-editing'; ?>" action="" 
			data-os-success-action="reload" 
			data-os-action="<?php echo $coupon->is_new_record() ? OsRouterHelper::build_route_name('coupons', 'create') : OsRouterHelper::build_route_name('coupons', 'update'); ?>">
	<div class="os-coupon-form-i">
		<div class="os-coupon-form-info">
			<div class="os-coupon-name"><?php echo !empty($coupon->name) ? $coupon->name : __('New Coupon', 'latepoint-pro-features'); ?></div>
			<div class="os-coupon-code"><?php echo $coupon->code; ?></div>
			<div class="os-coupon-edit-btn"><i class="latepoint-icon latepoint-icon-edit-3"></i></div>
		</div>
		<div class="os-coupon-form-params">
			<div class="os-row">
				<div class="os-col-lg-3">
			    <?php echo OsFormHelper::text_field('coupon[code]', __('Coupon Code', 'latepoint-pro-features'), $coupon->code, ['class' => 'os-coupon-code-input']); ?>
				</div>
				<div class="os-col-lg-3">
			    <?php echo OsFormHelper::text_field('coupon[name]', __('Name (For Internal Use)', 'latepoint-pro-features'), $coupon->name, ['class' => 'os-coupon-name-input']); ?>
				</div>
				<div class="os-col-6 os-col-lg-3">
			    <?php echo OsFormHelper::text_field('coupon[discount_value]', __('Discount Value', 'latepoint-pro-features'), $coupon->discount_value); ?>
				</div>
				<div class="os-col-6 os-col-lg-3">
			    <?php echo OsFormHelper::select_field('coupon[discount_type]', false, ['percent' => __('Percent', 'latepoint-pro-features'), 'fixed' => __('Fixed Value', 'latepoint-pro-features')], $coupon->discount_type); ?>
				</div>
			</div>
			<div class="coupon-restrictions-w">
				<h3><?php _e('Coupon Use Restrictions:', 'latepoint-pro-features'); ?></h3>
				<div class="os-row">
					<div class="os-col-lg-3">
				    <?php echo OsFormHelper::text_field('coupon[rules][limit_per_customer]', __('Use Limit Per Customer', 'latepoint-pro-features'), $coupon->get_rule('limit_per_customer')); ?>
					</div>
					<div class="os-col-lg-3">
				    <?php echo OsFormHelper::text_field('coupon[rules][limit_total]', __('Total Use Limit', 'latepoint-pro-features'), $coupon->get_rule('limit_total')); ?>
					</div>
					<div class="os-col-lg-3">
				    <?php echo OsFormHelper::text_field('coupon[rules][orders_more]', __('Min Number of Customer Orders', 'latepoint-pro-features'), $coupon->get_rule('orders_more')); ?>
					</div>
					<div class="os-col-lg-3">
				    <?php echo OsFormHelper::text_field('coupon[rules][orders_less]', __('Max Number of Customer Orders', 'latepoint-pro-features'), $coupon->get_rule('orders_less')); ?>
					</div>
				</div>
				<div class="os-row">
					<div class="os-col-lg-3">
				    <?php echo OsFormHelper::text_field('coupon[rules][agent_ids]', __('Agent IDs', 'latepoint-pro-features'), $coupon->get_rule('agent_ids')); ?>
					</div>
					<div class="os-col-lg-3">
				    <?php echo OsFormHelper::text_field('coupon[rules][service_ids]', __('Service IDs', 'latepoint-pro-features'), $coupon->get_rule('service_ids')); ?>
					</div>
					<div class="os-col-lg-3">
				    <?php echo OsFormHelper::text_field('coupon[rules][customer_ids]', __('Customer IDs', 'latepoint-pro-features'), $coupon->get_rule('customer_ids')); ?>
					</div>
					<div class="os-col-lg-3">
			    <?php echo OsFormHelper::select_field('coupon[status]', false, [LATEPOINT_COUPON_STATUS_ACTIVE => __('Coupon is Active', 'latepoint-pro-features'), LATEPOINT_COUPON_STATUS_DISABLED => __('Coupon is Disabled', 'latepoint-pro-features')], $coupon->status); ?>
					</div>
				</div>
			</div>
			<button type="submit" class="latepoint-btn latepoint-btn-outline"><?php _e('Save Coupon', 'latepoint-pro-features'); ?></button>
		</div>
	</div>
  <?php if(!$coupon->is_new_record()){
  	echo OsFormHelper::hidden_field('coupon[id]', $coupon->id); ?>
		<a href="#" data-os-prompt="<?php _e('Are you sure you want to remove this coupon?', 'latepoint-pro-features'); ?>"  data-os-after-call="latepoint_coupon_removed" data-os-pass-this="yes" data-os-action="<?php echo OsRouterHelper::build_route_name('coupons', 'destroy'); ?>" data-os-params="<?php echo OsUtilHelper::build_os_params(['id' => $coupon->id]) ?>" class="os-remove-coupon"><i class="latepoint-icon latepoint-icon-cross"></i></a>
  <?php } ?>
</form>