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
import { useEffect } from '@wordpress/element';
import { dispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */

import { useUpdateEffect } from '../../util';

const STEP_LABELS = {
	label: __('Step Enabled', 'mailster'),
	help: __(
		'Disable this step to skip it when the workflow is executed.',
		'mailster'
	),
};
const TRIGGER_LABELS = {
	label: __('Trigger Enabled', 'mailster'),
	help: __('Disable this trigger to skip this workflow is used.', 'mailster'),
};

export default function Disabler(props) {
	const { attributes, setAttributes, name } = props;
	const { disabled = false } = attributes;

	const label =
		name == 'mailster-workflow/trigger' ? TRIGGER_LABELS : STEP_LABELS;

	useUpdateEffect(() => {
		const msg = disabled
			? __('Step disabled', 'mailster')
			: __('Step enabled', 'mailster');
		dispatch('core/notices').createNotice('success', msg, {
			type: 'snackbar',
			isDismissible: true,
		});
	}, [disabled]);

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
								checked={!disabled}
								onChange={(val) =>
									setAttributes({ disabled: val ? undefined : true })
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</>
	);
}
