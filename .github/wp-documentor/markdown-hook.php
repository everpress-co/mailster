<?php
/**
 * Markdown Hook Template.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Documentor
 */

if ( ! function_exists( 'github_link' ) ) :
	function github_link( $path ) {
		$base = 'https://github.com/evrpress/mailster/tree/master';

		return rtrim( $base, '/' ) . '/' . $path;
	}
endif;

$name = $hook->get_tag()->get_name();

if ( ! isset( $hooks[ $name ] ) ) {

	echo '#### `', $name, '`', $eol;
	// echo '`', $name, '`', $eol;
	echo $eol;

	$summary     = $hook->get_summary();
	$description = $hook->get_description();

	if ( ! empty( $summary ) ) {

			echo '###### ', '**' . $summary . '**', $eol;

		echo $eol;
	}


	if ( ! empty( $description ) ) {
		echo nl2br( $description ), $eol;
		echo $eol;
	}

	$arguments = $hook->get_arguments();

	if ( \count( $arguments ) > 0 ) {
		echo '**Arguments**', $eol;

		echo $eol;

		echo 'Argument | Type | Description', $eol;
		echo '-------- | ---- | -----------', $eol;

		foreach ( $arguments as $argument ) {
			$type = $argument->get_type();

			\printf(
				'%s | %s | %s',
				\sprintf( '`%s`', $argument->get_name() ),
				empty( $type ) ? '' : \sprintf( '`%s`', \addcslashes( $type, '|' ) ),
				strtr(
					\addcslashes( $argument->get_description(), '|' ),
					array(
						"\r\n" => '<br>',
						"\r"   => '<br>',
						"\n"   => '<br>',
					)
				)
			);

			echo $eol;
		}
	}

	echo $eol;

	/**
	 * Changelog.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/activated_plugin/#changelog
	 * @link https://github.com/phpDocumentor/ReflectionDocBlock/blob/5.2.2/src/DocBlock/Tags/Since.php
	 */
	$changelog = $hook->get_changelog();

	if ( null !== $changelog && \count( $changelog ) > 0 ) {
		echo '**Changelog**', $eol;

		echo $eol;

		echo 'Version | Description', $eol;
		echo '------- | -----------', $eol;

		foreach ( $changelog as $item ) {
			\printf(
				'%s | %s',
				\sprintf( '`%s`', $item->get_version() ),
				$item->get_description()
			);

			echo $eol;
		}

		echo $eol;
	}

	if ( file_exists( __DIR__ . '/examples/' . $name . '.md' ) ) {

		echo '**Example**', $eol;
		echo $eol;

		include __DIR__ . '/examples/' . $name . '.md';
	}
}
printf(
	'Source: %s[%s]',
	\sprintf(
		'[%s](%s)',
		$hook->get_file()->getPathname(),
		github_link( $hook->get_file() )
	),
	\sprintf(
		'[%s](%s)',
		$hook->get_start_line(),
		\sprintf(
			'%s#L%d-L%d',
			github_link( $hook->get_file() ),
			$hook->get_start_line(),
			$hook->get_end_line()
		)
	)
);
echo '<br>';
