/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelRow,
	PanelBody,
	CheckboxControl,
	SelectControl,
	FlexItem,
	Flex,
	BaseControl,
	TimePicker,
	DateTimePicker,
	Button,
	Popover,
	ToggleControl,
	__experimentalNumberControl as NumberControl,
	Tip,
} from '@wordpress/components';
import { dateI18n, gmdateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */

import { HelpBeacon } from '../../util';
import Conditions from '../inspector/Conditions.js';
import StepSelector from './StepSelector.js';

export default function JumperInspectorControls(props) {
	const { attributes, setAttributes } = props;
	const { conditions } = attributes;
	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<HelpBeacon id="64623a1035c39a6db5f441e4" align="right" />
					<StepSelector {...props} />
				</PanelBody>
			</Panel>
			<Panel>
				<PanelBody>
					{conditions && (
						<PanelRow>
							<p>
								{__('only if following conditions are fullfilled.', 'mailster')}
							</p>
						</PanelRow>
					)}
					<Conditions {...props} />
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
