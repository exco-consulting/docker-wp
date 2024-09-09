<div class="os-coupons-w">
	<?php if($coupons){ ?>
	  <?php foreach($coupons as $coupon){ ?>
	    <?php include('new_form.php'); ?>
	  <?php } ?>
  <?php } ?>
</div>
<div class="add-coupon-box" data-os-action="<?php echo OsRouterHelper::build_route_name('coupons', 'new_form'); ?>" data-os-output-target-do="append" data-os-output-target=".os-coupons-w">
  <div class="add-coupon-graphic-w">
    <div class="add-coupon-plus"><i class="latepoint-icon latepoint-icon-plus4"></i></div>
  </div>
  <div class="add-coupon-label"><?php _e('Add Coupon', 'latepoint-pro-features'); ?></div>
</div>