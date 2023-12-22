/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { BlockControls } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

export default function HomepageBlockControls(props) {
	const { attributes, setAttributes, current, onSelect } = props;

	return <BlockControls group="block"></BlockControls>;
}
