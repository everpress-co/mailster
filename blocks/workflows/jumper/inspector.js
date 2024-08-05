/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelRow, PanelBody } from '@wordpress/components';

/**
 * Internal dependencies
 */

import { HelpBeacon } from '../../util';
import Conditions from '../inspector/Conditions.js';
import StepSelector from './StepSelector.js';

export default function JumperInspectorControls(props) {
	const { attributes } = props;
	const { conditions } = attributes;
	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<HelpBeacon id="66336f4fc3d8e87cfb53c423" align="right" />
					<StepSelector {...props} />
				</PanelBody>
			</Panel>
			<Panel>
				<PanelBody>
					<Conditions
						{...props}
						label={
							conditions
								? __('Only if following conditions are fullfilled.', 'mailster')
								: __('Conditions', 'mailster')
						}
						title={__(
							'Only jump if these condtions are fullfilled',
							'mailster'
						)}
					/>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
