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
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
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

const ALLOWED_MEDIA_TYPES = ['image'];

export default function Styling({ setMeta, style }) {
	let {
		color,
		minWidth,
		minHeight,
		backgroundImage,
		backgroundColor,
		backgroundPosition,
		padding,
	} = style;

	backgroundPosition = JSON.parse(backgroundPosition || '0') || {
		x: 0.5,
		y: 0.5,
	};
	padding = JSON.parse(padding || '0') || {
		top: '1em',
		left: '1em',
		right: '1em',
		bottom: '1em',
	};

	function setStyle(prop, data) {
		var newStyle = { ...style };
		newStyle[prop] = data;
		setMeta({ style: newStyle });
	}

	useEffect(() => {
		Object.assign(
			document.getElementsByClassName('is-root-container')[0].style,
			{
				color: color,
				minWidth: minWidth,
				minHeight: minHeight,
				paddingTop: padding.top,
				paddingLeft: padding.left,
				paddingRight: padding.right,
				paddingBottom: padding.bottom,
				backgroundColor: backgroundColor,
				backgroundImage: backgroundImage
					? 'url(' + backgroundImage + ')'
					: '',
				backgroundPosition:
					backgroundPosition.x * 100 +
					'%  ' +
					backgroundPosition.y * 100 +
					'%',
			}
		);
	}, [
		padding,
		backgroundImage,
		backgroundPosition,
		backgroundColor,
		color,
		minWidth,
		minHeight,
	]);

	return (
		<Fragment>
			<PanelRow>
				<BoxControl
					label="Padding"
					values={padding}
					onChange={(values) =>
						setStyle('padding', JSON.stringify(values))
					}
				/>
			</PanelRow>
			<PanelRow>
				<UnitControl
					onChange={(value) => setStyle('minWidth', value)}
					label="Width"
					isUnitSelectTabbable
					value={minWidth}
				/>
				<UnitControl
					onChange={(value) => setStyle('minHeight', value)}
					label="Height"
					isUnitSelectTabbable
					value={minHeight}
				/>
			</PanelRow>
			<PanelRow>
				<ColorPaletteControl
					label="Text Color"
					value={color}
					onChange={(value) => setStyle('color', value)}
				/>
			</PanelRow>{' '}
			<PanelRow>
				<ColorPaletteControl
					label="Background Color"
					value={backgroundColor}
					onChange={(value) => setStyle('backgroundColor', value)}
				/>
			</PanelRow>
			{backgroundImage && (
				<>
					<PanelRow>
						<FocalPointPicker
							url={backgroundImage}
							value={backgroundPosition}
							onChange={(values) =>
								setStyle(
									'backgroundPosition',
									JSON.stringify(values)
								)
							}
						/>
					</PanelRow>
					<PanelRow>
						<Button
							variant="secondary"
							onClick={() => {
								setStyle('backgroundImage', '');
							}}
						>
							Remove Background Image
						</Button>
					</PanelRow>
				</>
			)}
			{!backgroundImage && (
				<PanelRow>
					<MediaPlaceholder
						onSelect={(el) => {
							setStyle('backgroundImage', el.url);
						}}
						allowedTypes={['image']}
						multiple={false}
						labels={{ title: 'Background Image' }}
					></MediaPlaceholder>
				</PanelRow>
			)}
		</Fragment>
	);
}
