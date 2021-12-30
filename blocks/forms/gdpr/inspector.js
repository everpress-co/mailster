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

export default function InputFieldInspectorControls({
	attributes,
	setAttributes,
	isSelected,
}) {
	const { label, inline, required, style } = attributes;

	const [width, setWidth] = useState(100);

	function setStyle(prop, data) {
		var newStyle = { ...style };
		newStyle[prop] = data;
		setAttributes({ style: newStyle });
	}
	return (
		<InspectorControls>
			<Panel>
				<PanelBody
					title={__('Field Settings', 'mailster')}
					initialOpen={true}
				>
					<PanelRow>
						<TextControl
							label={__('Label', 'mailster')}
							value={label}
							onChange={(val) => setAttributes({ label: val })}
						/>
					</PanelRow>
					<PanelRow>
						<CheckboxControl
							label={__('Inline Labels', 'mailster')}
							checked={inline}
							onChange={() => setAttributes({ inline: !inline })}
						/>
					</PanelRow>
					<PanelRow>
						<CheckboxControl
							label={__('Required Labels', 'mailster')}
							checked={required}
							onChange={() =>
								setAttributes({ required: !required })
							}
						/>
					</PanelRow>{' '}
					<PanelRow>
						<RangeControl
							label="Width"
							value={style.width}
							onChange={(value) => setStyle('width', value)}
							min={2}
							max={100}
						/>
					</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
