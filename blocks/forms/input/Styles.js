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
			className="with-panel"
			name="styles"
			initialOpen={true}
			open={true}
		>
			<StylesContent
				attributes={attributes}
				setAttributes={setAttributes}
			>
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
			</StylesContent>
		</PanelBody>
	);
}
