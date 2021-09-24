<?php

$currentpage = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'import';
$currentstep = isset( $_GET['step'] ) ? (int) $_GET['step'] : 1;

?>
<div class="wrap mailster-manage">
<?php if ( 'import' == $currentpage ) : ?>
<h1><?php esc_html_e( 'Import Subscribers', 'mailster' ); ?></h1>
<?php elseif ( 'export' == $currentpage ) : ?>
<h1><?php esc_html_e( 'Export Subscribers', 'mailster' ); ?></h1>
<?php elseif ( 'delete' == $currentpage ) : ?>
<h1><?php esc_html_e( 'Delete Subscribers', 'mailster' ); ?></h1>
<?php else : ?>
<h1><?php esc_html_e( 'Manage Subscribers', 'mailster' ); ?></h1>
<?php endif; ?>

<h2 class="nav-tab-wrapper">

	<?php if ( current_user_can( 'mailster_import_subscribers' ) ) : ?>
	<a class="nav-tab <?php echo ( 'import' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=import"><?php esc_html_e( 'Import', 'mailster' ); ?></a>
	<?php endif; ?>

	<?php if ( current_user_can( 'mailster_export_subscribers' ) ) : ?>
	<a class="nav-tab <?php echo ( 'export' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=export"><?php esc_html_e( 'Export', 'mailster' ); ?></a>
	<?php endif; ?>

	<?php if ( current_user_can( 'mailster_bulk_delete_subscribers' ) ) : ?>
	<a class="nav-tab <?php echo ( 'delete' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=delete"><?php esc_html_e( 'Delete', 'mailster' ); ?></a>
	<?php endif; ?>

</h2>
<div class="stuffbox">
<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>

<?php if ( 'import' == $currentpage && current_user_can( 'mailster_import_subscribers' ) ) : ?>

	<?php include MAILSTER_DIR.'views/manage/import.php' ?>

<?php elseif ( 'export' == $currentpage && current_user_can( 'mailster_export_subscribers' ) ) : ?>

	<?php include MAILSTER_DIR.'views/manage/export.php' ?>

<?php elseif ( 'delete' == $currentpage && current_user_can( 'mailster_bulk_delete_subscribers' ) ) : ?>

	<?php include MAILSTER_DIR.'views/manage/delete.php' ?>

<?php else : ?>

	<h2><?php esc_html_e( 'You do not have sufficient permissions to access this page.', 'mailster' ); ?></h2>

<?php endif; ?>

	<div id="progress" class="progress hidden"><span class="bar" style="width:0%"><span></span></span></div>

</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
