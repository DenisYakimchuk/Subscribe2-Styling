<?php
if ( ! defined('ABSPATH') ) {
	exit();
} ?>
<div class="wrap">
	<?php global $title, $s2s_settings; ?>
	<h1><?php echo esc_html( $title ); ?></h1>
	
	<form method="post" action="options.php">
		
		<?php settings_fields( 's2s_form_styling' ); ?>
		<?php do_action( 's2s_form_styling_before_fields_table' ); ?>
		<table class="form-table">
			<?php do_action( 's2s_form_styling_before_fields' ); ?>
			<?php foreach( $s2s_settings as $option => $properties ) :
				$field_type = explode(':', $properties['type']);
				$current_option_value = get_option($option);
				if ( ! empty($properties['default_value'] ) && empty( $current_option_value ) ) :
					update_option( $option, $properties['default_value'] );
					$current_option_value = $properties['default_value'];
				endif; ?>
				<tr valign="top">
					<th scope="row"><?php echo esc_html($properties['label']); ?></th>
					<td><<?php echo $field_type[0];
						if ( !empty( $field_type[1] ) ) :
							echo' type="' . $field_type[1] . '"';
							echo ( $field_type[1] == 'text' ) ? ' class="regular-text"' : '';
							echo ( $field_type[1] == 'checkbox' ) ? checked( 1, $current_option_value, false ) : '';
						endif; ?> name="<?php echo $option; ?>" <?php
						if ( $field_type[0] != 'select' ) :
							?> value="<?php echo ( ! empty( $field_type[1] ) && $field_type[1] == 'checkbox' ) ? 1 : esc_attr( $current_option_value ); ?>" /<?php
						endif; ?>><?php
						if ( $field_type[0] == 'select') :
							if ( ! empty( $properties['values'] ) ) :
								$properties['values'] = is_array($properties['values']) ? $properties['values'] : array($properties['values']);
								foreach ( $properties['values'] as $key => $value ) :
									$selected = ( $key == $current_option_value ) ? ' selected="selected"' : '';
									echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
								endforeach;
							endif;
							echo '</select>';
						endif;
						if ( ! empty($properties['description']) ) : ?>
							<p class="description" id="tagline-description"><?php echo $properties['description']; ?></p>
						<?php endif; ?>
					</td>			
				</tr>
			<?php endforeach; ?>
			<?php do_action( 's2s_form_styling_after_fields' ); ?>
		</table>
		<?php do_action( 's2s_form_styling_after_fields_table' ); ?>
		
		<?php submit_button(); ?>
		
	</form>
	
</div>