/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { PanelBody, PanelRow } from '@wordpress/components';

/**
 * Internal dependencies
 */
import DisplayOptionsContent from './DisplayOptionsContent';
import PostTypeFields from './PostTypeFields';

export default function Schedule(props) {
	const { options, setOptions, placement } = props;
	const { type } = placement;

	console.warn(placement, options);

	return (
		<PanelBody title={__('Schedule', 'mailster')} initialOpen={true}>
			<PanelRow>Schedule</PanelRow>
		</PanelBody>
	);
}
