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

$eol = "\n";

$skip_without_summery = false;

if ( is_null( $documentor->type ) || in_array( 'actions', (array) $documentor->type ) ) {
	if ( empty( $actions ) ) {
		echo '*This project does not contain any WordPress actions.*', $eol;
		echo $eol;
	} else {
		echo '# Actions', $eol;

		echo $eol;
		foreach ( $actions as $hook ) {
			$summary = $hook->get_summary();
			if ( $skip_without_summery && empty( $summary ) ) {
				continue;
			}
			include __DIR__ . '/markdown-hook.php';

		}
	}
}



if ( is_null( $documentor->type ) || in_array( 'filters', (array) $documentor->type ) ) {
	if ( empty( $filters ) ) {
		echo '*This project does not contain any WordPress filters.*', $eol;
		echo $eol;
	} else {
		echo '# Filters', $eol;

		echo $eol;
		foreach ( $filters as $hook ) {
			$summary = $hook->get_summary();
			if ( $skip_without_summery && empty( $summary ) ) {
				continue;
			}
			include __DIR__ . '/markdown-hook.php';

		}
	}
}

echo $eol;
