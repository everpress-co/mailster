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

/**
 * Internal dependencies
 */

const STEP_LABELS = {
	label: __('Skip Step', 'mailster'),
	help: __('Skip this step when the workflow is executed.', 'mailster'),
};
const TRIGGER_LABELS = {
	label: __('Skip Trigger', 'mailster'),
	help: __('Disable this trigger for the workflow.', 'mailster'),
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
					title={label.label}
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
								label={label.label}
								help={label.help}
								checked={disabled}
								onChange={(val) =>
									setAttributes({ disabled: val ? true : undefined })
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</>
	);
}
