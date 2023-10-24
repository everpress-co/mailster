/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { BaseControl, TextControl } from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function WebHookSelector(props) {
	const { attributes, setAttributes } = props;
	const { webhook } = attributes;

	return (
		<>
			<BaseControl
				help={__(
					'Mailster will send a POST request to this URL with the subscriber data as JSON.',
					'mailster'
				)}
			>
				<TextControl
					label={__('URL to be called', 'mailster')}
					help={__(
						'Define a URL which will be called with this action.',
						'mailster'
					)}
					value={webhook}
					onChange={(url) => setAttributes({ webhook: url })}
				/>
			</BaseControl>
		</>
	);
}
