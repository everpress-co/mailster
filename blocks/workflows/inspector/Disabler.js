/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { BlockControls, InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelRow,
	PanelBody,
	ToolbarButton,
	ToggleControl,
} from '@wordpress/components';
import { help } from '@wordpress/icons';

/**
 * Internal dependencies
 */

const STEP_LABELS = {
	disable: __('Disable step', 'mailster'),
	enable: __('Enable step', 'mailster'),
	help: __(
		'Disables this step. It will be skipped when the workflow is executed.',
		'mailster'
	),
};
const TRIGGER_LABELS = {
	disable: __('Disable trigger', 'mailster'),
	enable: __('Enable trigger', 'mailster'),
	help: __(
		'Disables this trigger. The workflow will be skipped if this trigger is used.',
		'mailster'
	),
};

export default function Disabler(props) {
	const { attributes, setAttributes, name } = props;
	const { disabled = false } = attributes;

	const label =
		name == 'mailster-workflow/trigger' ? TRIGGER_LABELS : STEP_LABELS;

	return (
		<>
			<BlockControls group="other">
				<ToolbarButton
					icon={disabled ? 'hidden' : 'visibility'}
					isPressed={disabled}
					title={disabled ? label.enable : label.disable}
					onClick={() =>
						setAttributes({ disabled: disabled ? undefined : true })
					}
				/>
			</BlockControls>
			<InspectorControls>
				<Panel>
					<PanelBody>
						<PanelRow>
							<ToggleControl
								icon={disabled ? 'hidden' : 'visibility'}
								label={disabled ? label.enable : label.disable}
								help={label.help}
								checked={disabled}
								onChange={(val) =>
									setAttributes({ disabled: val ? val : undefined })
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</>
	);
}
