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

	const { image, position, opacity, size } = attributes.background;

	const units = [
		{ value: 'px', label: 'px', default: 0 },
		{ value: '%', label: '%', default: 10 },
	];

	return (
		<PanelBody name="background" title="Background" initialOpen={false}>
			{image && (
				<>
					<PanelRow>
						<FocalPointPicker
							url={image}
							value={position}
							onChange={(value) =>
								setBackground('position', value)
							}
						/>
					</PanelRow>
					<PanelRow>
						<Grid columns={2}>
							<UnitControl
								onChange={(value) =>
									setBackground('opacity', value)
								}
								label={__('Opacity', 'mailster')}
								value={opacity}
								units={units}
							/>
							<SelectControl
								label={__('Position', 'mailster')}
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
						</Grid>
					</PanelRow>
					<PanelRow>
						<Button
							variant="secondary"
							onClick={() => {
								setBackground('image', '');
							}}
						>
							Remove Background Image
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
