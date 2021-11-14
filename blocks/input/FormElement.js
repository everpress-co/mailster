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
	const { label, id, type, inline, required, native, style, values } =
		attributes;

	const htmlAttributes = {
		name: id,
		type: native ? type : 'text',
		id: 'mailster-' + id,
		className: 'input mailster-required',
		'aria-required': required,
		'aria-label': label,
		spellCheck: false,
		placeholder: ' ',
		required: required,
	};

	switch (type) {
		case 'radio':
			return (
				<>
					{values.map((value, i) => {
						return (
							<label key={i} className="mailster-label">
								<input
									{...htmlAttributes}
									type="radio"
									className=""
								/>
								<span>{value}</span>
							</label>
						);
					})}
				</>
			);
		case 'checkbox':
			return (
				<label className="mailster-label">
					<input
						{...htmlAttributes}
						type="checkbox"
						className=""
						defaultChecked={true}
					/>
					<span>{label}</span>
				</label>
			);
		case 'dropdown':
			return (
				<select {...htmlAttributes}>
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
					<input {...htmlAttributes} />
				</>
			);
	}
}
