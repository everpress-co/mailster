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
	FontSizePicker,
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

export default function Styles(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { style, type, hasLabel } = attributes;

	function setStyle(prop, data) {
		var newStyle = { ...style };
		newStyle[prop] = data;
		setAttributes({ style: newStyle });
	}

	function applyStyle() {
		const root = select('core/block-editor').getBlocks();
		const { width, ...newStyle } = style;
		root.map((block) => {
			var style = {
				...select('core/block-editor').getBlockAttributes(
					block.clientId
				).style,
			};

			dispatch('core/block-editor').updateBlockAttributes(
				block.clientId,
				{ style: { ...style, ...newStyle } }
			);

			dispatch('core/block-editor').clearSelectedBlock(block.clientId);
			dispatch('core/block-editor').selectBlock(block.clientId);
		});

		dispatch('core/block-editor').updateBlockAttributes(clientId, {
			style: {
				width,
			},
		});
	}

	const fontSizes = [
		{
			name: __('Small'),
			slug: 'small',
			size: 12,
		},
		{
			name: __('Big'),
			slug: 'big',
			size: 26,
		},
	];
	const fallbackFontSize = 16;

	const colorSettings = [
		{
			id: 'color',
			label: __('color Color', 'mailster'),
		},
		{
			id: 'backgroundColor',
			label: __('BackgroundColor Color', 'mailster'),
		},
		{
			id: 'borderColor',
			label: __('borderColor Color', 'mailster'),
		},
		{
			id: 'labelColor',
			label: __('labelColor Color', 'mailster'),
		},
	];

	return (
		<PanelColorSettings
			title={__('Styles', 'mailster')}
			initialOpen={false}
			colorSettings={colorSettings.flatMap((color) => {
				if (color.id == 'labelColor' && !hasLabel) {
					return [];
				}
				return {
					value: style[color.id],
					onChange: (value) => setStyle(color.id, value),
					label: color.label,
				};
			})}
		>
			<PanelRow>
				<SelectControl
					label={__('Style', 'mailster')}
					labelPosition="side"
					className="widefat"
					value={style.borderStyle}
					onChange={(value) => setStyle('borderStyle', value)}
					options={[
						{ value: 'dashed', label: 'Dashed' },
						{ value: 'dotted', label: 'Dotted' },
						{ value: 'solid', label: 'Solid' },
					]}
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					className="widefat"
					label="borderWidth"
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
					max={10}
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					className="widefat"
					label="borderRadius"
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
					max={50}
				/>
			</PanelRow>
			<PanelRow>
				<FontSizePicker
					fontSizes={fontSizes}
					value={style.fontSize}
					fallbackFontSize={fallbackFontSize}
					onChange={(value) => setStyle('fontSize', value)}
					withSlider
				/>
			</PanelRow>
			{type !== 'submit' && (
				<PanelRow>
					<Button
						onClick={applyStyle}
						variant="primary"
						icon={external}
					>
						{__('Apply to all input fields', 'mailster')}
					</Button>
				</PanelRow>
			)}
		</PanelColorSettings>
	);
}
