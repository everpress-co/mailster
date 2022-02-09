<?php
/**
 * Markdown Template
 *
 * @link      https://guides.github.com/features/mastering-markdown/
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Documentor
 */

namespace Pronamic\WordPress\Documentor;

if ( ! isset( $documentor ) ) {
	return;
}

$actions = $documentor->get_actions();
$filters = $documentor->get_filters();

$version = null;
$hooks   = array();
$eol     = "\n";

$readme = file_get_contents( dirname( dirname( __DIR__ ) ) . '/readme.txt' );
if ( ! defined( 'MAILSTER_VERSION' ) && preg_match( '|^(Stable tag): (.+)$|im', $readme, $match ) ) {
	$version = $match[2];
	define( 'MAILSTER_VERSION', $version );
}

$changelog = explode( '= ' . MAILSTER_VERSION . ' =', $readme );
if ( isset( $changelog[1] ) ) {
	$changelog = explode( "\n= ", $changelog )[0];
	$changelog = $eol . $eol . '### Version ' . MAILSTER_VERSION . ' (' . date( 'Y-m-d' ) . ')' . $eol . $eol . trim( $changelog ) . $eol;
	error_log( $changelog );
}

$skip_without_summery = true;

if ( is_null( $documentor->type ) || in_array( 'actions', (array) $documentor->type ) ) {

	if ( empty( $actions ) ) {
		echo '*This project does not contain any WordPress actions.*', $eol;
		echo $eol;
	} else {
		echo '# Actions', $eol;

		echo 'Mailster provides various hooks and filters you can use to alter the behavior of the plugin or write your own add-ons and extensions. We recommend to read more about hooks on the [official site](https://developer.wordpress.org/plugins/hooks/).', $eol, $eol;

		echo '!>  While there are more hooks in the plugin we only list a few here.', $eol;

		echo $eol;
		foreach ( $actions as $hook ) {
			$summary = $hook->get_summary();
			if ( $skip_without_summery && empty( $summary ) ) {
				continue;
			}
			$name = $hook->get_tag()->get_name();
			ob_start();
			include __DIR__ . '/markdown-hook.php';
			$output = ob_get_contents();
			if ( ! isset( $hooks[ $name ] ) ) {
				$hooks[ $name ] = '';
			}
			$hooks[ $name ] .= $output;
			ob_end_clean();
		}
		foreach ( $hooks as $name => $output ) {
			echo '<hr>', $eol, $eol;
			echo $output;
			echo $eol;
			echo $eol;
		}
	}
}



if ( is_null( $documentor->type ) || in_array( 'filters', (array) $documentor->type ) ) {
	if ( empty( $filters ) ) {
		echo '*This project does not contain any WordPress filters.*', $eol;
		echo $eol;
	} else {
		echo '# Filters', $eol;

		echo 'Mailster provides various hooks and filters you can use to alter the behavior of the plugin or write your own add-ons and extensions. We recommend to read more about hooks on the [official site](https://developer.wordpress.org/plugins/hooks/).', $eol, $eol;

		echo '!>  While there are more hooks in the plugin we only list a few here.', $eol;

		echo $eol;
		foreach ( $filters as $hook ) {
			$summary = $hook->get_summary();
			if ( $skip_without_summery && empty( $summary ) ) {
				continue;
			}
			$name = $hook->get_tag()->get_name();
			ob_start();
			include __DIR__ . '/markdown-hook.php';
			$output = ob_get_contents();
			if ( ! isset( $hooks[ $name ] ) ) {
				$hooks[ $name ] = '';
			}
			$hooks[ $name ] .= $output;
			ob_end_clean();

		}
		foreach ( $hooks as $name => $output ) {
			echo '<hr>', $eol, $eol;
			echo $output;
			echo $eol;
			echo $eol;
		}
	}
}


echo $eol;
