<aside>
	<label>
		<span class="total">0</span>
		<span class="metric"><?php esc_html_e( 'Bounces', 'mailster' ); ?></p>
	</label>
	<span class="gain"></span>
</aside>

<div class="metabox-chart" data-apex="true" data-metric="bounces">
	<script type="application/json">
	<?php
	echo json_encode(
		array(
			'chart' => array(
				'type' => 'area',
			),
		)
	)
	?>
	</script>
</div>
