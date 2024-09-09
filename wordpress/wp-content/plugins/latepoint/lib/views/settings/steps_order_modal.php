<?php
/**
 * @var $steps array
 */
?>
<div class="latepoint-lightbox-heading">
	<h2><?php _e('Order of Steps', 'latepoint'); ?></h2>
</div>
<div class="latepoint-lightbox-content">
	<div class="os-ordered-steps-description">
		<?php _e('Drag steps up and down to reorder. Some steps have sub steps, click on arrow to show them, they can also be reordered.', 'latepoint'); ?>
	</div>
	<div class="os-ordered-steps" data-route-name="<?php echo OsRouterHelper::build_route_name('settings', 'update_steps_order'); ?>">
		<?php
		foreach($steps as $step_name => $step_children){
			echo '<div class="os-ordered-step" data-step-code="'.$step_name.'">';
				echo '<div class="os-ordered-step-info">';
					echo '<div class="os-ordered-step-drag os-ordered-step-drag-handle"><i class="latepoint-icon latepoint-icon-menu os-ordered-step-drag-handle"></i></div>';
					echo '<div class="os-ordered-step-name">'.OsStepsHelper::get_step_label_by_code($step_name).'</div>';
					if(!empty($step_children)){
						echo '<div class="os-ordered-step-expand"><i class="latepoint-icon latepoint-icon-chevron-right"></i></div>';
					}
				echo '</div>';
				if(!empty($step_children)){
					echo '<div class="os-ordered-step-children">';
					foreach($step_children as $sub_step_name => $sub_step_children){
						echo '<div class="os-ordered-step-child" data-step-code="'.$step_name.'__'.$sub_step_name.'">';
							echo '<div class="os-ordered-step-child-info">';
								echo '<div class="os-ordered-step-drag os-ordered-step-child-drag-handle"><i class="latepoint-icon latepoint-icon-menu os-ordered-step-child-drag-handle"></i></div>';
								echo '<div class="os-ordered-step-child-name">'.OsStepsHelper::get_step_label_by_code($step_name.'__'.$sub_step_name).'</div>';
							echo '</div>';
						echo '</div>';
					}
					echo '</div>';
				}
			echo '</div>';
		}
		?>
	</div>
</div>