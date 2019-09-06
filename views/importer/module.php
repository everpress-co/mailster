<?php

$class = array( 'mailster-box' );
// if ( $data['update'] ) {
// $class[] = 'update';
// }
?>
	<li class="<?php echo implode( ' ', $class ); ?>">
		<a class="external screenshot" title="" width="300" height="225">
		</a>
		<div class="meta">
			<h3><?php echo $this->name(); ?></h3>
		</div>
		<div class="description">
		<p><?php echo $this->description(); ?></p>
		</div>
		<div class="action-links">
			<ul>

				<li class="alignright">
					<a class="button button-primary" href="
					<?php
					echo add_query_arg(
						array(
							'importer' => $this->id(),
							'step'     => 2,
						)
					);
					?>
					"><?php echo sprintf( esc_html__( 'Import from %s', 'mailster' ), $this->name() ); ?></a>
				</li>

			</ul>
		</div>
		<div class="loader"></div>
	</li>
