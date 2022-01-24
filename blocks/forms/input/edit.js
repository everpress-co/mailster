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
	InspectorControls,
	RichText,
	PlainText,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalGetSpacingClassesAndStyles as useSpacingProps,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';
import { Fragment, useState } from '@wordpress/element';

import { more } from '@wordpress/icons';
import { useSelect, select } from '@wordpress/data';

/**
 * Internal dependencies
 */

import InputFieldInspectorControls from '../input/inspector.js';
import FormElement from './FormElement';
import InputBlockControls from './InputBlockControls';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
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

	const borderProps = useBorderProps(attributes);
	const colorProps = useColorProps(attributes);
	const spacingProps = useSpacingProps(attributes);

	const styleSheets = {
		width: style.width ? style.width + '%' : undefined,
	};

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	const innerStyle = blockProps.style;
	blockProps.style = undefined;

	return (
		<>
			<div {...blockProps} style={styleSheets}>
				{hasLabel && (
					<RichText
						tagName="label"
						value={label}
						onChange={(val) => setAttributes({ label: val })}
						style={{ color: style.labelColor }}
						className="mailster-label"
						placeholder={__('Enter Label', 'mailster')}
					/>
				)}
				<FormElement
					{...props}
					borderProps={borderProps}
					colorProps={colorProps}
					spacingProps={spacingProps}
					innerStyle={innerStyle}
				/>
			</div>
			<InputBlockControls {...props} />
			<InputFieldInspectorControls {...props} />
		</>
	);
}
