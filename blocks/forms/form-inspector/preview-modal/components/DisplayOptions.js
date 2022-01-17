/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { PanelBody } from '@wordpress/components';

/**
 * Internal dependencies
 */
import DisplayOptionsContent from './DisplayOptionsContent';
import PostTypeFields from './PostTypeFields';

export default function DisplayOptions(props) {
	const { options, setOptions, placement } = props;
	const { type } = placement;

	function Title() {
		return <>{__('Display Options', 'mailster')}</>;
	}

	return (
		<PanelBody title={<Title />} initialOpen={false}>
			<PostTypeFields options={options} setOptions={setOptions} />
			{'content' == type && <DisplayOptionsContent {...props} />}
		</PanelBody>
	);
}
