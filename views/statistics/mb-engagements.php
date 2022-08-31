<aside>
	<label>
		<span class="total">&nbsp;</span>
		<span class="metric"><?php esc_html_e( 'Best Time', 'mailster' ); ?></p>
	</label>
	<span class="gain"></span>
</aside>

<div class="metabox-chart" data-apex="true" data-metric="engagement">
	<script type="application/json">
	<?php
	echo json_encode(
		array(
			'chart' => array(
				'type' => 'heatmap',
			),
			'grid'  => array(
				'padding' => array(
					'left'  => 20,
					'right' => 20,
				),
			),
			'xaxis' => array(
				'type' => 'number',
			),
			'yaxis' => array(

				'show' => true,

			),
		)
	)
	?>
	</script>
</div>
