<h3 class="os-wizard-sub-header"><?php echo sprintf( __( 'Step %d of %d', 'latepoint' ), $current_step_number, 3 ); ?></h3>
<h2 class="os-wizard-header"><?php _e( 'Setup Notifications', 'latepoint' ); ?></h2>
<div class="os-wizard-desc">Who would you like to send appointment notifications to?</div>
<div class="os-wizard-step-content-i">
    <div class="os-form-w">
        <form action="" class="os-wizard-default-agent-form" data-os-output-target=".os-wizard-step-content-i" data-os-pass-response="yes"
              data-os-after-call="latepoint_wizard_item_editing_cancelled"
              data-os-action="<?php echo OsRouterHelper::build_route_name( 'wizard', 'save_agent' ); ?>">
            <div class="os-row">
                <div class="os-col-6">
					<?php echo OsFormHelper::text_field( 'agent[first_name]', __( 'First Name', 'latepoint' ), $agent->first_name ); ?>
                </div>
                <div class="os-col-6">
					<?php echo OsFormHelper::text_field( 'agent[last_name]', __( 'Last Name', 'latepoint' ), $agent->last_name ); ?>
                </div>
            </div>
            <div class="os-row">
                <div class="os-col-lg-6">
					<?php echo OsFormHelper::text_field( 'agent[email]', __( 'Email Address', 'latepoint' ), $agent->email ); ?>
                </div>
                <div class="os-col-lg-6">
					<?php echo OsFormHelper::phone_number_field( 'agent[phone]', __( 'Phone Number', 'latepoint' ), $agent->phone ); ?>
                </div>
            </div>
        </form>
    </div>

</div>
