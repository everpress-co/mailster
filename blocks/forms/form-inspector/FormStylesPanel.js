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
	__experimentalPanelColorGradientSettings as PanelColorGradientSettings,
} from '@wordpress/block-editor';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { more } from '@wordpress/icons';

/**
 * Internal dependencies
 */

export const FormStylesPanel = (props) => {
	const { attributes, setAttributes, children } = props;

	if (!attributes) {
		return null;
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
		borderRadius,
		background,
		style,
	} = attributes;
	const { image, position, opacity, scale, size, fixed, repeat } = background;

	function setBackground(prop, data) {
		var newBackground = { ...background };
		newBackground[prop] = data;
		setAttributes({ background: newBackground });
	}

	function setStyle(prop, value) {
		var newStyle = { ...style };
		newStyle[prop] = {
			...newStyle[prop],
			...value,
		};

		setAttributes({ style: newStyle });
	}

	function setColor(value) {
		setStyle('color', {
			text: value,
		});
	}
	function setBackgroundColor(value) {
		if (!value) return;
		setStyle('color', {
			background: value,
			gradient: undefined,
		});
	}
	function setGradient(value) {
		if (!value) return;
		setStyle('color', {
			background: undefined,
			gradient: value,
		});
	}

	return (
		<PanelColorGradientSettings
			__experimentalHasMultipleOrigins
			__experimentalIsRenderedInSidebar
			title={__('Form Styles', 'mailster')}
			name="form-styles-panel"
			initialOpen={false}
			settings={[
				{
					colorValue: style?.color?.text,
					disableCustomGradients: true,
					label: __('Color', 'mailster'),
					onColorChange: setColor,
				},
				{
					colorValue: style?.color?.background,
					label: __('Background Color', 'mailster'),
					gradientValue: style?.color?.gradient,
					onColorChange: setBackgroundColor,
					onGradientChange: setGradient,
				},
			]}
		>
			{image && (
				<>
					<PanelRow>
						<SelectControl
							label={__('Size', 'mailster')}
							labelPosition="side"
							className="widefat"
							value={isNaN(size) ? size : size || 'cover'}
							onChange={(value) => {
								setBackground('size', value);
							}}
							options={[
								{
									value: isNaN(size) ? 100 : size,
									label: 'Scale',
								},
								{ value: 'auto', label: 'Auto' },
								{ value: 'contain', label: 'Contain' },
								{ value: 'cover', label: 'Cover' },
							]}
						/>
					</PanelRow>
					{!isNaN(size) && (
						<PanelRow>
							<RangeControl
								value={size}
								className="widefat"
								onChange={(value) =>
									setBackground('size', value)
								}
								min={0}
								max={200}
								initialPosition={100}
								step={1}
								required
							/>
						</PanelRow>
					)}
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
							// prefer first image between a threshold and fallback to the original
							const image = Object.values(el.sizes).filter(
								(size) => size.width > 300 && size.width < 1200
							)[0];
							setBackground('image', image?.url || el.url);
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
				<BoxControl
					label={__('Form Padding', 'mailster')}
					values={style?.spacing?.padding}
					help={__('Set the padding of your form in %', 'mailster')}
					resetValues={{
						top: undefined,
						left: undefined,
						right: undefined,
						bottom: undefined,
					}}
					onChange={(val) =>
						setAttributes({
							padding: val,
						})
					}
					onChange={(value) => {
						setStyle('spacing', { padding: value });
					}}
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
			{!!children && <>{children}</>}
		</PanelColorGradientSettings>
	);
};
