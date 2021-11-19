/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	useBlockProps,
	InspectorControls,
	RichText,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
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
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
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

	switch (type) {
		case 'radio':
			return (
				<div className="mailster-group mailster-group-radio">
					{values.map((value, i) => {
						return (
							<label key={i}>
								<input
									name={name}
									aria-required={required}
									aria-label={label.replace(/<[^>]+>/g, '')}
									spellCheck={false}
									required={required}
									type="radio"
									checked={selected == value}
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
					<input
						name={name}
						aria-required={required}
						aria-label={label.replace(/<[^>]+>/g, '')}
						spellCheck={false}
						required={required}
						type="checkbox"
						defaultChecked={true}
					/>
					<label className="mailster-label">{label}</label>
				</div>
			);
		case 'dropdown':
			return (
				<select
					name={name}
					className="input"
					aria-required={required}
					aria-label={label.replace(/<[^>]+>/g, '')}
					spellCheck={false}
					required={required}
					value={selected}
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

		default:
			return (
				<>
					<input
						name={name}
						type={native ? type : 'text'}
						aria-required={required}
						aria-label={label.replace(/<[^>]+>/g, '')}
						spellCheck={false}
						required={required}
						className="input"
						pattern={pattern}
						placeholder=" "
					/>
				</>
			);
	}
}
