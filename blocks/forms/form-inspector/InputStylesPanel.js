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
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
	Spinner,
	RangeControl,
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

export const colorSettings = [
	{
		id: 'labelColor',
		label: __('Label Color', 'mailster'),
	},
	{
		id: 'inputColor',
		label: __('Input Font Color', 'mailster'),
	},
	{
		id: 'backgroundColor',
		label: __('Input Background Color', 'mailster'),
	},
	{
		id: 'borderColor',
		label: __('Input Border Color', 'mailster'),
	},
];

export const InputStylesPanel = (props) => {
	const { attributes, setAttributes, children } = props;

	if (!attributes) {
		return null;
	}

	const { style = {} } = attributes;

	function setStyle(prop, value) {
		var newStyle = { ...style };
		newStyle[prop] = value;
		setAttributes({ style: newStyle });
	}

	return (
		<PanelColorGradientSettings
			title={__('Input Styles', 'mailster')}
			name="input-styles-panel"
			initialOpen={false}
			settings={colorSettings.flatMap((color, i) => {
				return {
					colorValue: style?.[color.id],
					disableCustomGradients: true,
					label: color.label,
					onColorChange: (value) => setStyle(color.id, value),
				};
			})}
		>
			<PanelRow>
				<RangeControl
					className="widefat"
					label={__('Border Width', 'mailster')}
					value={
						style.borderWidth
							? parseInt(style.borderWidth, 10)
							: undefined
					}
					allowReset={true}
					onChange={(value) =>
						setStyle(
							'borderWidth',
							typeof value !== 'undefined'
								? value + 'px'
								: undefined
						)
					}
					min={0}
					max={12}
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					className="widefat"
					label={__('Border Radius', 'mailster')}
					value={
						style.borderRadius
							? parseInt(style.borderRadius, 10)
							: undefined
					}
					allowReset={true}
					onChange={(value) =>
						setStyle(
							'borderRadius',
							typeof value !== 'undefined'
								? value + 'px'
								: undefined
						)
					}
					min={0}
					max={60}
				/>
			</PanelRow>
			{!!children && <>{children}</>}
		</PanelColorGradientSettings>
	);
};