<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}
?>
	<p>
		<label>
		<input name="mailster_data[track_opens]" id="mailster_data_track_opens" value="1" type="checkbox" <?php echo ( isset( $this->post_data['track_opens'] ) ) ? ( ( $this->post_data['track_opens'] ) ? 'checked' : '' ) : ( mailster_option( 'track_opens' ) ? 'checked' : '' ); ?>> <?php esc_html_e( 'Track Opens', 'mailster' ); ?>
		</label>
	</p>
	<p>
		<label>
		<input name="mailster_data[track_clicks]" id="mailster_data_track_clicks" value="1" type="checkbox" <?php echo ( isset( $this->post_data['track_clicks'] ) ) ? ( ( $this->post_data['track_clicks'] ) ? 'checked' : '' ) : ( mailster_option( 'track_clicks' ) ? 'checked' : '' ); ?>> <?php esc_html_e( 'Track Clicks', 'mailster' ); ?>
		</label>
	</p>

<?php if ( $editable ) : ?>

	<span class="spinner" id="colorschema-ajax-loading"></span>
	<h4><?php esc_html_e( 'Colors', 'mailster' ); ?></h4>

	<?php $colors = mailster( 'templates' )->colors( $post, $this->get_template(), $this->get_file() ); ?>

	<?php // echo '<pre>' . print_r( $colors, true ) . '</pre>'; ?>
	
	<ul class="colors has-labels" data-original-colors='<?php echo json_encode( $colors ); ?>'>
	<?php foreach ( $colors['colors'] as $color ) : ?>
		<?php
			$color_value = substr( esc_attr( $color['value'] ), 1 );
			$label       = $color['label'];
		?>
		<li class="mailster-color">
			<label title="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $label ); ?></label>
			<input type="text" class="form-input-tip color" id="mailster-color-<?php echo esc_attr( $color['id'] ); ?>" name="mailster_data[newsletter_color][<?php echo esc_attr( $color_value ); ?>]" value="<?php echo esc_attr( $color['value'] ); ?>" data-value="<?php echo esc_attr( $color['value'] ); ?>" data-default-color="<?php echo esc_attr( $color['original'] ); ?>" data-id="<?php echo esc_attr( $color['id'] ); ?>" data-var="<?php echo esc_attr( $color['var'] ); ?>">
			<a class="default-value mailster-icon" href="#" tabindex="-1"></a>
		</li>
	<?php endforeach; ?>
		</ul>
	<p>
		<a class="savecolorschema button button-small"><?php esc_html_e( 'Save this Color Schema', 'mailster' ); ?></a>
	</p>

	<h4><?php esc_html_e( 'Colors Schemas', 'mailster' ); ?></h4>

	<div class="colorschemas">
	<ul class="colorschema" title="<?php esc_attr_e( 'original', 'mailster' ); ?>">
	<?php foreach ( $colors['colors'] as $id => $color ) : ?>
		<li class="colorschema-field" title="<?php echo esc_attr( $color['original'] ); ?>" data-id="<?php echo esc_attr( $id ); ?>"  data-hex="<?php echo esc_attr( $color['original'] ); ?>" style="background-color:<?php echo esc_attr( $color['original'] ); ?>"></li>
	<?php endforeach; ?>
	</ul>
	<?php if ( ! empty( $colors['schemas'] ) ) : ?>
		<?php foreach ( $colors['schemas'] as $hash => $colorschema ) : ?>
		<ul class="colorschema custom" title="<?php esc_html_e( 'Use this color schema', 'mailster' ); ?>">
			<?php foreach ( $colorschema->colors as $id => $color ) { ?>
			<li class="colorschema-field" title="<?php echo esc_attr( strtolower( $color ) ); ?>"  data-id="<?php echo esc_attr( $id ); ?>" data-hex="<?php echo esc_attr( strtolower( $color ) ); ?>" style="background-color:<?php echo esc_attr( $color ); ?>"></li>
		<?php } ?>
		<li class="colorschema-delete-field"><a class="colorschema-delete">&#10005;</a></li>
		</ul>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if ( ! empty( $colors['legacy'] ) ) : ?>
		<?php foreach ( $colors['legacy'] as $hash => $colorschema ) : ?>
		<ul class="colorschema custom" data-hash="<?php echo esc_attr( $hash ); ?>" title="<?php esc_html_e( 'Use this color schema', 'mailster' ); ?>">
			<?php foreach ( $colorschema as $id => $color ) { ?>
			<li class="colorschema-field" title="<?php echo esc_attr( strtolower( $color ) ); ?>"  data-id="<?php echo esc_attr( $id ); ?>" data-hex="<?php echo esc_attr( strtolower( $color ) ); ?>" style="background-color:<?php echo esc_attr( $color ); ?>"></li>
		<?php } ?>
		<li class="colorschema-delete-field"><a class="colorschema-delete">&#10005;</a></li>
		</ul>
		<?php endforeach; ?>
	<?php endif; ?>
	</div>
	<?php if ( ! empty( $customcolors ) ) : ?>
	<p>
		<a class="colorschema-delete-all button-link button-small button-link-delete"><?php esc_html_e( 'Delete all Custom Schemas', 'mailster' ); ?></a>
	</p>
	<?php endif; ?>
<?php else : ?>
	<label><?php esc_html_e( 'Colors Schema', 'mailster' ); ?></label><br>
	<ul class="colorschema finished">
	<?php
	$colors = $this->post_data['colors'];
	foreach ( $colors as $color ) :
		?>
		<li data-hex="<?php echo esc_attr( $color ); ?>" style="background-color:<?php echo esc_attr( $color ); ?>"></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
