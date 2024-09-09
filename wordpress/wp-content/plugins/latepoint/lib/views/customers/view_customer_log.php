<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */
?>

<?php
/* @var $activities OsActivityModel[] */
/* @var $customer OsCustomerModel */
?>
<div class="booking-activity-log-panel-w side-sub-panel">
	<div class="os-form-header">
		<h2><?php _e('Activity Log', 'latepoint'); ?></h2>
		<a href="#" class="booking-activity-log-panel-close"><i class="latepoint-icon latepoint-icon-x"></i></a>
	</div>
	<div class="booking-activity-log-panel-i">
		<div class="booking-activities-list">
			<div class="quick-booking-info">
				<?php if($customer->created_at) echo '<span>'.__('Registered On: ', 'latepoint').'</span><strong>'.OsTimeHelper::get_readable_date(new OsWpDateTime($customer->created_at)).'</strong>'; ?>
			</div>
			<?php
			foreach ($activities as $activity) {
				echo '<div class="booking-activity-row">';
					echo '<div class="booking-activity-name">' . $activity->name . '</div>';
					echo '<div class="spacer"></div>';
					echo '<div class="booking-activity-date">' . $activity->nice_created_at . '</div>';
					echo $activity->get_link_to_object('<i class="latepoint-icon latepoint-icon-file-text"></i>');
				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
