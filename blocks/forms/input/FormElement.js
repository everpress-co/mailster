/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

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

/**
 * Internal dependencies
 */

export default function FormElement(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const {
		label,
		name,
		selected,
		type,
		inline,
		required,
		native,
		style,
		values,
		pattern,
	} = attributes;

	const inputStyle = {
		color: style.color,
		fontSize: style.fontSize,
		backgroundColor: style.backgroundColor,
		borderColor: style.borderColor,
		borderWidth: style.borderWidth,
		borderStyle: style.borderStyle,
		borderRadius: style.borderRadius,
	};

	switch (type) {
		case 'radio':
			return (
				<div className="mailster-group mailster-group-radio">
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
									style={inputStyle}
									value={value}
									onChange={(event) => {
										setAttributes({
											selected: event.target.value,
										});
									}}
								/>
								<span>{value}</span>
							</label>
						);
					})}
				</div>
			);
		case 'checkbox':
			return (
				<div className="mailster-group mailster-group-checkbox">
					<label className="mailster-wrapper-options">
						<input
							name={name}
							aria-required={required}
							aria-label={label}
							spellCheck={false}
							required={required}
							type="checkbox"
							style={inputStyle}
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
					onChange={(event) => {
						setAttributes({
							selected: event.target.value,
						});
					}}
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
			return (
				<input
					name={name}
					type={native ? type : 'text'}
					aria-required={required}
					aria-label={label}
					spellCheck={false}
					required={required}
					className="input"
					style={inputStyle}
					placeholder=" "
				/>
			);
	}
}
