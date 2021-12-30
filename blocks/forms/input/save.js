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

import { useBlockProps, RichText } from '@wordpress/block-editor';
import FormElement from './FormElement';

export default function save(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { label, name, type, inline, required, style, hasLabel } = attributes;
	const className = ['mailster-wrapper'];

	if (required) className.push('mailster-wrapper-required');
	if (inline) className.push('mailster-wrapper-inline');
	if ('submit' == type) className.push('wp-block-button');

	const styleSheets = {
		width: style.width ? style.width + '%' : undefined,
	};

	const labelElement = (
		<RichText.Content
			tagName="label"
			//htmlFor={type != 'radio' ? id : null}
			style={{ color: style.labelColor }}
			className="mailster-label"
			value={label}
		/>
	);

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
			//data-label={label}
			style={styleSheets}
		>
			{hasLabel && label && !inline && labelElement}
			<FormElement {...props} />
			{hasLabel && label && inline && labelElement}
		</div>
	);
}
