/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
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

import { BackgroundContent } from '../shared/BackgroundContent';

export default function Background(props) {
	const { attributes, setAttributes, meta, setMeta, isSelected, clientId } =
		props;

	return (
		<PanelBody
			name="background"
			title={__('Background', 'mailster')}
			initialOpen={false}
		>
			<BackgroundContent
				attributes={attributes}
				setAttributes={setAttributes}
			/>
		</PanelBody>
	);
}
