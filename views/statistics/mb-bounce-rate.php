<aside>
	<label>
		<span class="value">&nbsp;</span>
		<span class="metric"><?php esc_html_e( 'Bounce Rate', 'mailster' ); ?></p>
	</label>
	<span class="gain"></span>
</aside>

<div class="metabox-chart" data-apex="true" data-metric="bounce_rate">
	<script type="application/json">
	<?php
	echo json_encode(
		array(
			'chart' => array(
				'group' => 'sparklines',
				'type' => 'area',
			),
		)
	)
	?>
	</script>
</div>
