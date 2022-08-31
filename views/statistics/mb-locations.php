<aside>
	<label>
		<span class="total"><?php esc_html_e( 'Top Locations', 'mailster' ); ?></span>
		<span class="metric"></p>
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
