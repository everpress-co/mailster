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
	PanelColorSettings,
	RichText,
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	RangeControl,
	ColorPalette,
} from '@wordpress/components';

import { Fragment, Component, useState } from '@wordpress/element';

import { more } from '@wordpress/icons';

/**
 * Internal dependencies
 */

export default function InputFieldInspectorControls({
	attributes,
	setAttributes,
	isSelected,
}) {
	const { success, successBackground, error, errorBackground, width } =
		attributes;

	return (
		<InspectorControls>
			<PanelColorSettings
				title={__('Success Message', 'mailster')}
				initialOpen={false}
				//opened={displayMessages}
				onToggle={() => {
					//setDisplayMessages(!displayMessages);
				}}
				colorSettings={[
					{
						value: successBackground,
						onChange: (value) =>
							setAttributes({ successBackground: value }),
						label: __('successBackground Color'),
					},
					{
						value: success,
						onChange: (value) => setAttributes({ success: value }),
						label: __('Success Color'),
					},
				]}
			></PanelColorSettings>
			<PanelColorSettings
				title={__('Error Messages', 'mailster')}
				initialOpen={false}
				//opened={displayMessages}
				onToggle={() => {
					//setDisplayMessages(!displayMessages);
				}}
				colorSettings={[
					{
						value: errorBackground,
						onChange: (value) =>
							setAttributes({ errorBackground: value }),
						label: __('errorBackground Color'),
					},
					{
						value: error,
						onChange: (value) => setAttributes({ error: value }),
						label: __('Error Color'),
					},
				]}
			></PanelColorSettings>
			<PanelBody
				title={__('Field Settings', 'mailster')}
				initialOpen={true}
			>
				<PanelRow>
					<RangeControl
						className="widefat"
						label="Width"
						value={width}
						allowReset={true}
						initialPosition={100}
						onChange={(value) => setAttributes({ width: value })}
						min={10}
						max={100}
					/>
				</PanelRow>
			</PanelBody>
		</InspectorControls>
	);
}
