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

import InspectorControls from './inspector.js';
import icon from './Icon';
import Step from '../inspector/Step';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;

	return (
		<Step {...props} inspectorControls={<InspectorControls {...props} />}>
			<div className="mailster-stop">{icon}</div>
		</Step>
	);
}
