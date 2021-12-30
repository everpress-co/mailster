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
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
	ColorPaletteControl,
} from '@wordpress/block-editor';
import {
	Button,
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
	Spinner,
	RangeControl,
	SelectControl,
	ToggleControl,
	FocalPointPicker,
	FontSizePicker,
	__experimentalBoxControl as BoxControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';
import {
	PanelColorSettings,
	__experimentalColorGradientControl as ColorGradientControl,
} from '@wordpress/block-editor';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { more } from '@wordpress/icons';

/**
 * Internal dependencies
 */

export const BackgroundContent = (props) => {
	const { attributes, setAttributes } = props;

	if (!attributes) {
		return <Spinner />;
	}

	const [values, setValues] = useState({
		top: '50px',
		left: '10%',
		right: '10%',
		bottom: '50px',
	});

	const {
		color,
		backgroundColor,
		fontSize,
		padding,
		borderRadius,
		background,
	} = attributes;
	const { image, position, opacity, size, fixed, repeat } = background;

	function setBackground(prop, data) {
		var newBackground = { ...background };
		newBackground[prop] = data;
		setAttributes({ background: newBackground });
	}

	return (
		<>
			<ColorGradientControl
				colorValue={color}
				disableCustomGradients={true}
				label={__('Color', 'mailster')}
				onColorChange={(value) => setAttributes({ color: value })}
			/>
			<ColorGradientControl
				colorValue={backgroundColor}
				disableCustomGradients={true}
				label={__('Background Color', 'mailster')}
				onColorChange={(value) =>
					setAttributes({ backgroundColor: value })
				}
			/>
			{image && (
				<>
					<PanelRow>
						<SelectControl
							label={__('Position', 'mailster')}
							labelPosition="side"
							className="widefat"
							value={size || 'cover'}
							onChange={(value) => {
								setBackground('size', value);
							}}
							options={[
								{ value: 'auto', label: 'Auto' },
								{ value: 'contain', label: 'Contain' },
								{ value: 'cover', label: 'Cover' },
							]}
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label="Fixed background"
							checked={fixed}
							onChange={(value) => {
								setBackground('fixed', value);
							}}
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label="Repeated Background"
							checked={repeat}
							onChange={(value) => {
								setBackground('repeat', value);
							}}
						/>
					</PanelRow>
					{!fixed && (
						<PanelRow>
							<FocalPointPicker
								url={image}
								value={position}
								onChange={(value) =>
									setBackground('position', value)
								}
							/>
						</PanelRow>
					)}
					<PanelRow>
						<RangeControl
							label={__('Opacity', 'mailster')}
							value={opacity}
							className="widefat"
							onChange={(value) =>
								setBackground('opacity', value)
							}
							min={0}
							max={100}
							step={10}
							required
						/>
					</PanelRow>
					<PanelRow>
						<Button
							variant="secondary"
							isSmall
							className="block-library-cover__reset-button"
							onClick={() => {
								setBackground('image', undefined);

								return;

								setAttributes({
									background: {
										image: undefined,
										opacity: undefined,
										fixed: undefined,
										repeat: undefined,
										size: undefined,
										position: undefined,
									},
								});
							}}
						>
							{__('Clear Media', 'mailster')}
						</Button>
					</PanelRow>
				</>
			)}
			{!image && (
				<PanelRow>
					<MediaPlaceholder
						onSelect={(el) => {
							setBackground('image', el.url);
						}}
						allowedTypes={['image']}
						multiple={false}
						labels={{ title: 'Background Image' }}
					></MediaPlaceholder>
				</PanelRow>
			)}

			<FontSizePicker
				fontSizes={[
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
				]}
				value={fontSize}
				fallbackFontSize={12}
				onChange={(value) => setAttributes({ fontSize: value })}
				withSlider
			/>

			<PanelRow>
				<RangeControl
					className="widefat"
					label="Padding"
					value={padding}
					allowReset={true}
					initialPosition={0}
					onChange={(value) => setAttributes({ padding: value })}
					min={0}
					max={100}
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					className="widefat"
					label={__('Border Radius', 'mailster')}
					value={
						borderRadius ? parseInt(borderRadius, 10) : undefined
					}
					allowReset={true}
					onChange={(value) =>
						setAttributes({
							borderRadius:
								typeof value !== 'undefined'
									? value + 'px'
									: undefined,
						})
					}
					min={0}
					max={60}
				/>
			</PanelRow>
		</>
	);
};
