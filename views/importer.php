<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
<?php $classes = array( 'wrap', 'mailster-importers' ); ?>

<?php $step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 1; ?>

<?php
	$importers = glob( MAILSTER_DIR . 'classes/importer.*' );
	$importers = array_values( preg_grep( '/importer\.(.*)\.class/', $importers ) );
foreach ( $importers as $file ) :
	require_once $file;
	endforeach;
	$importer = isset( $_GET['importer'] ) && ! empty( $_GET['importer'] ) ? 'MailsterImporter' . basename( $_GET['importer'] ) : null;
if ( $importer && class_exists( $importer, false ) ) {
	$importer = new $importer();
} else {
	$importer = null;
}
	?>

<div class="<?php echo implode( ' ', $classes ) ?>">
<h1><?php esc_html_e( 'Importer', 'mailster' ); ?></h1>

<?php if ( 1 == $step ) : ?>

<p><?php esc_html_e( 'Mailster will now run some importers to ensure everything is running smoothly. Please keep this browser window open until all importers are finished.', 'mailster' ); ?></p>

<h2>Available</h2>

<?php foreach ( $importers as $file ) : ?>
	<?php
		$classname = 'Mailster' . str_replace( ' ', '', ucwords( str_replace( array( '.class.php', '.' ), array( '', ' ' ), basename( $file ) ) ) );
		$i = new $classname();
		$i->display();
		?>
<?php endforeach; ?>

<?php elseif ( 2 == $step ) : ?>

	<?php

	if ( $importer ) {

		?>
		<form action="<?php echo esc_url( add_query_arg( array( 'importer' => $importer->id(), 'step' => 3 ) ) ) ?>" method="POST">

		<?php $importer->step2(); ?>

		<input type="submit" class="button button-primary"  value="<?php echo sprintf( esc_attr__( 'Define things to import from %s', 'mailster' ), $importer->name() );?>">

		</form>
		<?php

	} else {
		esc_html_e( 'No such Importer', 'mailster' );
	}


	?>


<?php elseif ( 3 == $step ) : ?>

	<?php

	if ( $importer ) {

		?>
		<form action="<?php echo esc_url( add_query_arg( array( 'importer' => $importer->id(), 'step' => 4 ) ) ) ?>" method="POST">

		<input type="text" id="importer" name="importer" value="<?php echo esc_attr( $importer->id() ) ?>">

		<?php $importer->step3(); ?>

		<input type="submit" class="button button-primary"  value="<?php echo sprintf( esc_attr__( 'Import from %s', 'mailster' ), $importer->name() );?>">

		</form>
		<?php

	} else {
		esc_html_e( 'No such Importer', 'mailster' );
	}


	?>

<?php endif; ?>

<div id="ajax-response"></div>
<br class="clear">
</div>
