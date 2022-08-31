<aside>
	<label>
		<span class="total"></span>
		<span class="metric"><?php esc_html_e( 'Subscribers', 'mailster' ); ?></span>
	</label>
	<span class="gain"></span>
</aside>

<div class="metabox-chart" data-apex="true" data-metric="subscribers">
	<script type="application/json">
	<?php
	echo json_encode(
		array(
			'chart' => array(
				'type' => 'area',
			),
			'xaxis' => array(
				'type' => 'datetime',
			),
		)
	)
	?>
	</script>
</div>
