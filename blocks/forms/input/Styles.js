/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { PanelColorSettings } from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	Button,
	RangeControl,
	SelectControl,
	FontSizePicker,
} from '@wordpress/components';

import { external } from '@wordpress/icons';
import { select, dispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */

import { StylesContent, colorSettings } from '../shared/StylesContent';

export default function Styles(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { style, type, hasLabel } = attributes;

	function applyStyle() {
		const root = select('core/block-editor').getBlocks();
		const { width, ...newStyle } = style;
		root.map((block) => {
			var style = {
				...select('core/block-editor').getBlockAttributes(
					block.clientId
				).style,
			};

			dispatch('core/block-editor').updateBlockAttributes(
				block.clientId,
				{ style: { ...style, ...newStyle } }
			);

			dispatch('core/block-editor').clearSelectedBlock(block.clientId);
			dispatch('core/block-editor').selectBlock(block.clientId);
		});

		dispatch('core/block-editor').updateBlockAttributes(clientId, {
			style: {
				width,
			},
		});
	}

	return (
		<PanelBody
			name="styles"
			title={__('Styles', 'mailster')}
			initialOpen={false}
		>
			<StylesContent
				attributes={attributes}
				setAttributes={setAttributes}
			/>
			{type !== 'submit' && (
				<PanelRow>
					<Button
						onClick={applyStyle}
						variant="primary"
						icon={external}
					>
						{__('Apply to all input fields', 'mailster')}
					</Button>
				</PanelRow>
			)}
		</PanelBody>
	);

	//OLD
	return (
		<PanelColorSettings
			title={__('Styles', 'mailster')}
			initialOpen={false}
			colorSettings={colorSettings.flatMap((color) => {
				if (color.id == 'labelColor' && !hasLabel) {
					return [];
				}
				return {
					value: style[color.id],
					onChange: (value) => setStyle(color.id, value),
					label: color.label,
				};
			})}
		>
			<PanelRow>
				<SelectControl
					label={__('Border Style', 'mailster')}
					labelPosition="side"
					className="widefat"
					value={style.borderStyle}
					onChange={(value) => setStyle('borderStyle', value)}
					options={[
						{ value: null, label: 'not set' },
						{ value: 'dashed', label: 'Dashed' },
						{ value: 'dotted', label: 'Dotted' },
						{ value: 'solid', label: 'Solid' },
					]}
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					className="widefat"
					label={__('Border Width', 'mailster')}
					value={
						style.borderWidth
							? parseInt(style.borderWidth, 10)
							: null
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
					max={10}
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					className="widefat"
					label={__('Border Radius', 'mailster')}
					value={
						style.borderRadius
							? parseInt(style.borderRadius, 10)
							: null
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
					max={50}
				/>
			</PanelRow>
			<PanelRow>
				<FontSizePicker
					//fontSizes={fontSizes}
					value={style.fontSize}
					fallbackFontSize={fallbackFontSize}
					onChange={(value) => setStyle('fontSize', value)}
					withSlider
					withReset
				/>
			</PanelRow>
			{type !== 'submit' && (
				<PanelRow>
					<Button
						onClick={applyStyle}
						variant="primary"
						icon={external}
					>
						{__('Apply to all input fields', 'mailster')}
					</Button>
				</PanelRow>
			)}
		</PanelColorSettings>
	);
}
