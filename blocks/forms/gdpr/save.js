/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

export default function save(props) {
	const { attributes, setAttributes } = props;
	const { content } = attributes;
	const className = ['mailster-wrapper'];

	//if (required) className.push('mailster-wrapper-required');
	//if (inline) className.push('mailster-wrapper-inline');

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
		>
			<label>
				<input type="checkbox" name="_gdpr" value="1" />
				<RichText.Content tagName="span" value={content} />
			</label>
		</div>
	);
}
