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
					<StepSelector {...props} />
				</PanelBody>
			</Panel>
			<Panel>
				<PanelBody>
					<PanelRow>
						{conditions && (
							<p>
								{__('only if following conditions are fullfilled.', 'mailster')}
							</p>
						)}
					</PanelRow>
					<Conditions {...props} />
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
