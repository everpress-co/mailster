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
 * Internal dependencies
 */

export default function Inputs(props) {
	const { attributes, setAttributes, isSelected, setInputs } = props;

	const { padding } = attributes.inputs;

	return (
		<PanelBody name="styling" title="Input Fields" initialOpen={false}>
			<PanelRow>
				<BoxControl
					label="Padding"
					values={padding}
					onChange={(value) => setInputs('padding', value)}
				/>
			</PanelRow>
		</PanelBody>
	);
}
