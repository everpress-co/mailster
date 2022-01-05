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
 * Internal dependencies
 */

import { StylesContent, colorSettings } from '../shared/StylesContent';

export default function Styles(props) {
	const { attributes, setAttributes, meta, setMeta, isSelected, clientId } =
		props;

	return (
		<PanelBody
			className="with-panel"
			name="styling"
			initialOpen={true}
			opened={true}
		>
			<StylesContent
				attributes={attributes}
				setAttributes={setAttributes}
			/>
		</PanelBody>
	);
}
