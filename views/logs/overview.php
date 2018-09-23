
<div class="wrap">
<h1><?php esc_html_e( 'Logs', 'mailster' ) ?>
<?php if ( current_user_can( 'mailster_add_logs' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=mailster_logs&new" class="add-new-h2"><?php esc_html_e( 'Add New', 'mailster' );?></a>
<?php endif; ?>
<?php if ( current_user_can( 'mailster_add_logs' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=mailster_logs&clear" class="add-new-h2"><?php esc_html_e( 'Clear', 'mailster' );?></a>
<?php endif; ?>
<?php if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) : ?>
	<span class="subtitle"><?php printf( __( 'Search result for %s', 'mailster' ), '&quot;' . esc_html( stripslashes( $_GET['s'] ) ) . '&quot;' ) ?></span>
	<?php endif; ?>
</h1>
<?php

require_once MAILSTER_DIR . 'classes/logs.table.class.php';
$table = new Mailster_Logs_Table();

$table->prepare_items();
$table->search_box( __( 'Search Logs', 'mailster' ), 's' );
$table->views();
?><form method="post" action="" id="logs-overview-form"><?php
$table->display();
?></form>
</div>
