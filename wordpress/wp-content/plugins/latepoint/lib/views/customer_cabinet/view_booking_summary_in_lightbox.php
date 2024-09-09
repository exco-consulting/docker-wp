<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

/* @var $booking OsBookingModel */
/* @var $order OsOrderModel */
/* @var $order_item OsOrderItemModel */
?>
<div class="latepoint-lightbox-heading">
	<h2><?php _e('Booking Summary', 'latepoint'); ?></h2>
</div>
<div class="latepoint-lightbox-content">
<?php include(LATEPOINT_VIEWS_ABSPATH.'bookings/_full_summary.php'); ?>
</div>