/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	Spinner,
	CheckboxControl,
	BaseControl,
	TextControl,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';

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
