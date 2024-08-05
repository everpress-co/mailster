/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n, sprintf } from '@wordpress/i18n';
import { CardBody, Icon, Spinner } from '@wordpress/components';

/**
 * Internal dependencies
 */
import InspectorControls from './inspector.js';
import StepIcon from './Icon.js';
import Step from '../inspector/Step.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { email } = attributes;

	const label = __('Send notification', 'mailster');

	return (
		<Step
			{...props}
			isIncomplete={!email}
			inspectorControls={<InspectorControls {...props} />}
		>
			<CardBody size="small">
				<div className="mailster-step-label">
					<Icon icon={StepIcon} />
					{label}
				</div>
				{email && (
					<div className="mailster-step-info">
						{sprintf(__('to %s', 'mailster'), email)}
					</div>
				)}
			</CardBody>
		</Step>
	);
}
