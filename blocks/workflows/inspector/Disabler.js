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

export default function Disabler(props) {
	const { attributes, setAttributes } = props;
	const { disabled = false } = attributes;

	return (
		<>
			<BlockControls>
				<ToolbarButton
					icon={disabled ? 'hidden' : 'visibility'}
					isPressed={disabled}
					title={
						disabled
							? __('Enable step', 'mailster')
							: __('Disable step', 'mailster')
					}
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
								label={__('Disable step', 'mailster')}
								help={__(
									'Disables this step. It will be skipped when the workflow is executed.',
									'mailster'
								)}
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
