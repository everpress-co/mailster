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
	PanelColorSettings,
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
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';

import { more, external } from '@wordpress/icons';
import { select, dispatch } from '@wordpress/data';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

const colorSettings = [
	{
		id: 'color',
		label: __('Input Font Color', 'mailster'),
	},
	{
		id: 'backgroundColor',
		label: __('Input Background Color', 'mailster'),
	},
	{
		id: 'borderColor',
		label: __('Input Border Color', 'mailster'),
	},
	{
		id: 'labelColor',
		label: __('Label Color', 'mailster'),
	},
];

export default function Styles(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { style } = attributes;

	function setStyle(prop, data) {
		var newStyle = { ...style };
		newStyle[prop] = data;
		setAttributes({ style: newStyle });
	}

	return (
		<PanelColorSettings
			title={__('Styles', 'mailster')}
			initialOpen={false}
			colorSettings={colorSettings.flatMap((color) => {
				return {
					value: style[color.id],
					onChange: (value) => setStyle(color.id, value),
					label: color.label,
				};
			})}
		>
			<PanelRow>
				<RangeControl
					className="widefat"
					label={__('Border Width', 'mailster')}
					value={
						style.borderWidth
							? parseInt(style.borderWidth, 10)
							: null
					}
					allowReset={true}
					onChange={(value) =>
						setStyle(
							'borderWidth',
							typeof value !== 'undefined'
								? value + 'px'
								: undefined
						)
					}
					min={0}
					max={12}
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					className="widefat"
					label={__('Border Radius', 'mailster')}
					value={
						style.borderRadius
							? parseInt(style.borderRadius, 10)
							: null
					}
					allowReset={true}
					onChange={(value) =>
						setStyle(
							'borderRadius',
							typeof value !== 'undefined'
								? value + 'px'
								: undefined
						)
					}
					min={0}
					max={60}
				/>
			</PanelRow>
		</PanelColorSettings>
	);
}
