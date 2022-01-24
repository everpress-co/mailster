/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	RichText,
	ColorPaletteControl,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	Card,
	CardBody,
	CardMedia,
	Button,
	RangeControl,
	FocalPointPicker,
	SelectControl,
	ToggleControl,
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';

import { more } from '@wordpress/icons';

import { format } from '@wordpress/date';

/**
 * Internal dependencies
 */

export default function FormElement(props) {
	const {
		attributes,
		setAttributes,
		isSelected,
		clientId,
		borderProps,
		colorProps,
		spacingProps,
		innerStyle,
	} = props;
	const {
		label,
		name,
		selected,
		type,
		inline,
		required,
		native,
		style = {},
		values,
		pattern,
	} = attributes;

	const elem = classnames(colorProps.className, borderProps.className);

	const inputStyle = {
		...borderProps.style,
		...colorProps.style,
		...spacingProps.style,
		...innerStyle,
		...{
			color: style.inputColor,
			backgroundColor: style.backgroundColor,
			borderColor: style.borderColor,
			borderWidth: style.borderWidth,
			borderStyle: style.borderStyle,
			borderRadius: style.borderRadius,
		},
	};

	switch (type) {
		case 'radio':
			return (
				<div
					className="mailster-group mailster-group-radio"
					style={inputStyle}
				>
					{values.map((value, i) => {
						return (
							<label key={i} className="mailster-wrapper-options">
								<input
									name={name}
									aria-required={required}
									aria-label={label}
									required={required}
									type="radio"
									checked={selected == value}
									value={value}
									onChange={(event) =>
										setAttributes({
											selected: event.target.value,
										})
									}
								/>
								<span>{value}</span>
							</label>
						);
					})}
				</div>
			);
		case 'checkbox':
			return (
				<div
					className="mailster-group mailster-group-checkbox"
					style={inputStyle}
				>
					<label className="mailster-wrapper-options">
						<input
							name={name}
							aria-required={required}
							aria-label={label}
							spellCheck={false}
							required={required}
							type="checkbox"
							defaultChecked={true}
						/>
						<span className="mailster-label">{label}</span>
					</label>
				</div>
			);
		case 'dropdown':
			return (
				<select
					name={name}
					className="input"
					aria-required={required}
					aria-label={label}
					required={required}
					value={selected}
					style={inputStyle}
					onChange={(event) =>
						setAttributes({
							selected: event.target.value,
						})
					}
				>
					{values.map((value, i) => {
						return (
							<option key={i} value={value}>
								{value}
							</option>
						);
					})}
				</select>
			);

		case 'submit':
			return (
				<input
					name={name}
					type="submit"
					style={inputStyle}
					value={label}
					className="wp-block-button__link submit-button"
				/>
			);
		default:
			const sample =
				'date' == type
					? format('Y-m-d', new Date())
					: sprintf(__('Sample text for %s'), label);

			return (
				<input
					name={name}
					type={native ? type : 'text'}
					aria-required={required}
					aria-label={label}
					spellCheck={false}
					required={required}
					value={isSelected && !inline ? sample : ''}
					onChange={() => {}}
					className="input"
					style={inputStyle}
					placeholder=" "
				/>
			);
	}
}
