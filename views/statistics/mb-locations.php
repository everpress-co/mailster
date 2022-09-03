<aside>
	<label>
		<span class="value"><?php esc_html_e( 'Top Locations', 'mailster' ); ?></span>
		<span class="metric"><?php esc_html_e( 'of your latest signups', 'mailster' ); ?></p>
	</label>
	<span class="gain"></span>
</aside>

<div class="metabox-chart" data-metric="locations">
	<script type="application/json">
	<?php
	echo json_encode(
		array()
	)
	?>
	</script>
</div>
