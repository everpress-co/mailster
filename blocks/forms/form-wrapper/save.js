/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

export default function save(props) {
	const { attributes } = props;

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
			<div className="mailster-block-form-inner">
				<InnerBlocks.Content />
			</div>
		</form>
	);
}
