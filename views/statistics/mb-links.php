<aside>
	<label>
		<span class="value"><?php esc_html_e( 'Top Links', 'mailster' ); ?></span>
		<span class="metric"><?php esc_html_e( 'Of you campaigns', 'mailster' ); ?></p>
	</label>
	<span class="gain"></span>
</aside>

<div class="metabox-chart" data-metric="links">
	<script type="application/json">
	<?php
	echo json_encode(
		array()
	)
	?>
	</script>
</div>
