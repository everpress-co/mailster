/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelBody, RangeControl } from '@wordpress/components';

/**
 * Internal dependencies
 */

import Conditions from '../inspector/Conditions.js';

export default function ConditionInspectorControls(props) {
	const { attributes, setAttributes } = props;
	const { weight } = attributes;

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<h3>{__('This is the help message of this step.', 'mailster')}</h3>
					<Conditions {...props} />
				</PanelBody>
			</Panel>
			{false && (
				<Panel>
					<PanelBody>
						<RangeControl
							label="weight"
							value={weight}
							min={0}
							max={1}
							step={0.01}
							withInputField={false}
							onChange={(value) => setAttributes({ weight: parseFloat(value) })}
						/>
					</PanelBody>
				</Panel>
			)}
		</InspectorControls>
	);
}
