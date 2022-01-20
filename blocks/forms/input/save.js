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

import FormElement from './FormElement';

export default function save(props) {
	const { attributes, setAttributes } = props;
	const {
		label,
		type,
		inline,
		required,
		asterisk,
		style,
		hasLabel,
		align,
		labelAlign,
	} = attributes;
	const className = ['mailster-wrapper'];

	if (required) className.push('mailster-wrapper-required');
	if (align) className.push('mailster-wrapper-align-' + align);
	if (labelAlign)
		className.push('mailster-wrapper-label-align-' + labelAlign);
	if (inline) className.push('mailster-wrapper-inline');
	if (required && asterisk) className.push('mailster-wrapper-asterisk');
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
