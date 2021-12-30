/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

import { __ } from '@wordpress/i18n';

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save(props) {
	const { attributes } = props;
	const { messages } = attributes;

	return (
		<form
			method="post"
			action={attributes.action}
			novalidate
			style={{
				...{ color: attributes.color },
				...{ backgroundColor: attributes.backgroundColor },
			}}
			{...useBlockProps.save({
				className: 'mailster-block-form',
			})}
		>
			<div className="mailster-block-form-info">
				<div className="mailster-block-form-info-success"></div>
				<div className="mailster-block-form-info-error"></div>
			</div>
			<InnerBlocks.Content />
		</form>
	);
}
