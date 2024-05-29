/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import { CardBody, Icon, Spinner } from '@wordpress/components';

/**
 * Internal dependencies
 */
import InspectorControls from './inspector.js';
import StepIcon from './Icon.js';
import Step from '../inspector/Step.js';

export default function Edit(props) {
	const { attributes } = props;
	const { conditions, step } = attributes;

	const label = sprintf(__('Jump to #%s', 'mailster'), step || '…');

	return (
		<Step
			{...props}
			isIncomplete={!step}
			inspectorControls={<InspectorControls {...props} />}
		>
			<CardBody size="small">
				<div className="mailster-step-label">
					<Icon icon={StepIcon} />
					{label}
				</div>
				{conditions && (
					<div className="mailster-step-info">
						{__('only if', 'mailster')}
						<ServerSideRender
							block="mailster-workflow/conditions"
							attributes={{
								...attributes,
								...{ render: true, plain: true },
							}}
							EmptyResponsePlaceholder={() => <Spinner />}
						/>
						{__('otherwise continue', 'mailster')}
					</div>
				)}
			</CardBody>
		</Step>
	);
}
