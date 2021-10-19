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

export default function Background(props) {
	const { attributes, setAttributes, isSelected, setBackground } = props;

	const { image, position, opacity, size, fixed, repeat } =
		attributes.background;

	return (
		<PanelBody name="background" title="Background" initialOpen={false}>
			{image && (
				<>
					<PanelRow>
						<SelectControl
							label={__('Position', 'mailster')}
							labelPosition="side"
							className="widefat"
							value={size}
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
								setBackground('image', '');
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
		</PanelBody>
	);
}
