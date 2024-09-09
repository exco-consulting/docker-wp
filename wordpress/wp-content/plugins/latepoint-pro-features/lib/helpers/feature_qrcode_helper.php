<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

class OsFeatureQrcodeHelper {
  public static function generate_qr_code_for_order(OsOrderModel $order){
		if($order->is_single_booking()) self::generate_qr_code($order->get_items()[0]->build_original_object_from_item_data());
  }

  public static function generate_qr_code(OsBookingModel $booking){
    $ical_string = OsBookingHelper::generate_ical_event_string($booking);
    echo '<div class="qr-code-on-full-summary">';
    echo '<div class="qr-code-booking-info">';
      echo '<img src="'.(new chillerlan\QRCode\QRCode)->render($booking->booking_code).'" alt="QR Code">';
      echo '<div class="qr-code-label">'.__('Scan on Arrival', 'latepoint-pro-features').'</div>';
    echo '</div>';
    echo '<div class="qr-code-vevent">';
      echo '<img src="'.(new chillerlan\QRCode\QRCode)->render($ical_string).'" alt="QR Code">';
      echo '<div class="qr-code-label">'.__('Point your smartphone camera at the QR code and it will automatically add this appointment to your calendar', 'latepoint-pro-features').'</div>';
    echo '</div>';
    echo '<div class="qr-show-trigger">';
      echo '<div><i class="latepoint-icon latepoint-icon-qrcode"></i></div>';
      echo '<div class="qr-code-trigger-label">'.__('Show QR Code', 'latepoint-pro-features').'</div>';
    echo '</div>';
    echo '</div>';
  }

}