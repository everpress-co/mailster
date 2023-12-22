/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import HomepageInspectorControls from '../homepage/inspector';

export default function HomepageContextInspectorControls(props) {
	const { attributes, setAttributes, isSelected, context } = props;
	const { type = 'submission' } = attributes;

	const onSelect = (type, index) => {
		location.hash = '#mailster-' + type;
	};
	return <HomepageInspectorControls current={type} onSelect={onSelect} />;
}
