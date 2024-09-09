<div class="os-section-header"><h3><?php _e('Default Fields', 'latepoint'); ?></h3></div>
<?php OsSettingsHelper::generate_default_form_fields($default_fields); ?>
<div class="os-section-header"><h3><?php _e('Custom Fields', 'latepoint'); ?></h3></div>
<?php echo OsUtilHelper::pro_feature_block(__('To create more fields install the PRO addon', 'latepoint')); ?>
