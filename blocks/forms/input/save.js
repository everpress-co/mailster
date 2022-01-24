/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
	__experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
} from '@wordpress/block-editor';

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
		style = {},
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

	const borderProps = getBorderClassesAndStyles(attributes);
	const colorProps = getColorClassesAndStyles(attributes);
	const spacingProps = getSpacingClassesAndStyles(attributes);

	const blockProps = useBlockProps.save({
		className: classnames({}, className),
	});

	const innerStyle = blockProps.style;
	blockProps.style = undefined;

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
		<div {...blockProps} style={styleSheets}>
			{hasLabel && label && !inline && labelElement}
			<FormElement
				{...props}
				borderProps={borderProps}
				colorProps={colorProps}
				spacingProps={spacingProps}
				innerStyle={innerStyle}
			/>
			{hasLabel && label && inline && labelElement}
		</div>
	);
}
