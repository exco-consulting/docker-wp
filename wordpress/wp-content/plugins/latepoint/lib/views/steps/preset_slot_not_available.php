<?php
/* @var $booking OsBookingModel */
?>
<div class="slot-not-available-wrapper">
    <div class="latepoint-lightbox-close">
        <i class="latepoint-icon-common-01"></i>
    </div>
	<div class="icon-w a-rotate-scale">
		<i class="latepoint-icon latepoint-icon-calendar"></i>
	</div>
	<h2 class="a-up-20 a-delay-1"><?php _e('Timeslot Unavailable', 'latepoint'); ?></h2>
	<div class="desc a-up-20 a-delay-2"><?php _e('Sorry, the selected timeslot is no longer available.', 'latepoint'); ?></div>
	<div class="booking-date-time-info a-up-20 a-delay-3">
		<div class="info-label"><?php _e('Requested:', 'latepoint'); ?></div>
		<div class="info-value">
			<?php
			if ($booking->start_date) {
				$booking_start_datetime = $booking->get_nice_start_datetime();
				$booking_start_datetime = apply_filters('latepoint_booking_summary_formatted_booking_start_datetime', $booking_start_datetime, $booking, OsTimeHelper::get_timezone_name_from_session());
				echo '<div>'.$booking->service->name.'</div>';
				echo '<div>'.$booking_start_datetime.'</div>';
			} ?>
		</div>
	</div>
</div>