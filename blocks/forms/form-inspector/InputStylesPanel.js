/**
 * External dependencies
 */
import styled from '@emotion/styled';
const deepmerge = require('deepmerge');

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

import { PanelRow, RangeControl, Button } from '@wordpress/components';
import {
	__experimentalPanelColorGradientSettings as PanelColorGradientSettings,
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	Panel,
	__experimentalBoxControl as BoxControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	__experimentalUnitControl as UnitControl,
	__experimentalBorderBoxControl as BorderBoxControl,
} from '@wordpress/components';
import { external, typography } from '@wordpress/icons';
import { useState } from '@wordpress/element';
import { select, dispatch } from '@wordpress/data';
import { searchBlocks } from '../../util';

/**
 * Internal dependencies
 */

const PanelDescription = styled.div`
	grid-column: span 2;
`;
const PanelWrapper = styled.div`
	grid-column: span 2;
`;

const SingleColumnItem = styled(ToolsPanelItem)`
	grid-column: span 1;
`;
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
];

export const InputStylesPanel = (props) => {
	const { attributes, setAttributes, children, clientId, inInput } = props;

	const { style = {}, type, inline, asterisk } = attributes;

	function setStyle(prop, data) {
		let newStyle = { ...style };
		if (data === undefined) {
			delete newStyle[prop];
		} else {
			newStyle[prop] = data;
		}
		setAttributes({
			style: Object.keys(newStyle).length ? newStyle : undefined,
		});
	}

	const applyStyle = (type) => {
		const root = select('core/block-editor').getBlocks();
		const blocks = searchBlocks('^mailster/field-');

		//foreach block
		blocks.map((block) => {
			if (block.clientId == clientId) return;
			let attr = select('core/block-editor').getBlockAttributes(block.clientId);
			let newStyle = { ...style };

			if (newStyle.width) delete newStyle.width;

			switch (type) {
				case 'typography':
					attr.fontSize = attributes.fontSize;
					attr.style.typography = newStyle.typograph;
					break;
				case 'dimensions':
					attr.style.spacing = newStyle.spacing;
					break;
				case 'border':
					attr.borderColor = attributes.borderColor;
					attr.style.border = newStyle.border;
					break;
			}

			dispatch('core/block-editor').updateBlockAttributes(block.clientId, {
				style: deepmerge(newStyle, attr.style),
				inline: inline,
				asterisk: asterisk,
			});
		});

		return;
		const { width, ...newStyle } = style;
		root.map((block) => {
			let style = {
				...select('core/block-editor').getBlockAttributes(block.clientId).style,
			};

			dispatch('core/block-editor').updateBlockAttributes(block.clientId, {
				style: { ...style, ...newStyle },
			});

			dispatch('core/block-editor').clearSelectedBlock(block.clientId);
			dispatch('core/block-editor').selectBlock(block.clientId);
		});

		dispatch('core/block-editor').updateBlockAttributes(clientId, {
			style: {
				width,
			},
		});
	};
	const resetColors = () => {
		let newStyle = { ...style };
		delete newStyle['labelColor'];
		delete newStyle['inputColor'];
		delete newStyle['backgroundColor'];
		setAttributes({
			style: Object.keys(newStyle).length ? newStyle : undefined,
		});
	};
	const resetBorders = () => {
		let newStyle = { ...style };
		delete newStyle['borderRadius'];
		delete newStyle['borders'];
		setAttributes({
			style: Object.keys(newStyle).length ? newStyle : undefined,
		});
	};

	const legacyBorder = {
		color: style.borderColor,
		style: 'solid',
		width: style.borderWidth,
	};

	const setBorders = (newBorders) => {
		setAttributes({ borders: newBorders });
	};
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	return (
		<>
			{inInput && type != 'submit' && (
				<>
					<InspectorControls group="typography">
						<PanelDescription>
							<Button
								onClick={() => applyStyle('typography')}
								variant="secondary"
								icon={external}
							>
								{__('Apply to all input fields', 'mailster')}
							</Button>
						</PanelDescription>
					</InspectorControls>
					<InspectorControls group="dimensions">
						<PanelDescription>
							<Button
								onClick={() => applyStyle('dimensions')}
								variant="secondary"
								icon={external}
							>
								{__('Apply to all input fields', 'mailster')}
							</Button>
						</PanelDescription>
					</InspectorControls>{' '}
					<InspectorControls group="border">
						<PanelDescription>
							<Button
								onClick={() => applyStyle('border')}
								variant="secondary"
								icon={external}
							>
								{__('Apply to all input fields', 'mailster')}
							</Button>
						</PanelDescription>
					</InspectorControls>
				</>
			)}
			<ToolsPanel
				label={__('Colors', 'mailster')}
				resetAll={resetColors}
				__experimentalFirstVisibleItemClass="first"
				__experimentalLastVisibleItemClass="last"
			>
				<PanelWrapper>
					{inInput && (
						<PanelDescription>
							{__(
								'Change the color of your input fields here. You can apply the style to all your other fields with the button below.',
								'mailster'
							)}
						</PanelDescription>
					)}
					{colorSettings.map(({ id, label }) => (
						<ToolsPanelItem
							key={id}
							hasValue={() => style?.[id] != undefined}
							label={label}
							isShownByDefault
							onDeselect={() => setStyle(id, undefined)}
						>
							<ColorGradientSettingsDropdown
								__experimentalIsRenderedInSidebar
								settings={[
									{
										colorValue: style?.[id],
										label: label,
										onColorChange: (value) => setStyle(id, value),
										isShownByDefault: true,
										resetAllFilter: { resetColors },
										enableAlpha: true,
									},
								]}
								panelId={clientId}
								{...colorGradientSettings}
							/>
						</ToolsPanelItem>
					))}
				</PanelWrapper>
				{inInput && type != 'submit' && (
					<PanelDescription>
						<Button onClick={applyStyle} variant="secondary" icon={external}>
							{__('Apply to all input fields', 'mailster')}
						</Button>
					</PanelDescription>
				)}
			</ToolsPanel>
		</>
	);
};
