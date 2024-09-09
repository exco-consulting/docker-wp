<?php
/**
 * @var $booking OsBookingModel
 * @var $current_step_code string
 * @var $custom_fields_for_booking array
 *
 */
 ?>
<div class="step-custom-fields-for-booking-w latepoint-step-content" data-step-code="<?php echo $current_step_code; ?>">
  <div class="os-row">
  <?php
    if(isset($custom_fields_for_booking) && !empty($custom_fields_for_booking)){
		  echo OsCustomFieldsHelper::output_custom_fields_for_model($custom_fields_for_booking, $booking, 'booking');
    }?>
  </div>
</div>