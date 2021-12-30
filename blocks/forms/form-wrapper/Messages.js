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

import { more } from '@wordpress/icons';

export default function Messages(props) {
	const {
		attributes,
		setAttributes,
		isSelected,
		setMessages,
		displayMessages,
		setDisplayMessages,
	} = props;
	const { success, successBackground, error, errorBackground } =
		attributes.messages;

	return (
		<PanelColorSettings
			title={__('Messages', 'mailster')}
			initialOpen={false}
			opened={displayMessages}
			onToggle={() => {
				setDisplayMessages(!displayMessages);
			}}
			colorSettings={[
				{
					value: successBackground,
					onChange: (value) =>
						setMessages('successBackground', value),
					label: __('successBackground Color'),
				},
				{
					value: success,
					onChange: (value) => setMessages('success', value),
					label: __('Success Color'),
				},
				{
					value: errorBackground,
					onChange: (value) => setMessages('errorBackground', value),
					label: __('errorBackground Color'),
				},
				{
					value: error,
					onChange: (value) => setMessages('error', value),
					label: __('Error Color'),
				},
			]}
		></PanelColorSettings>
	);
}
