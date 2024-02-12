/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel } from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function StopInspectorControls(props) {
	const { attributes, setAttributes } = props;

	return (
		<InspectorControls>
			<Panel></Panel>
		</InspectorControls>
	);
}
