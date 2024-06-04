/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { useInnerBlocksProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { CardBody, Icon, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import InspectorControls from './inspector.js';
import StepIcon from './Icon.js';
import Step from '../inspector/Step';

const BLOCK_TEMPLATE = [
	['mailster-workflow/condition-yes'],
	['mailster-workflow/condition-no'],
];

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id, comment, conditions, weight } = attributes;
	const className = ['mailster-step-conditions', 'canvas-handle'];

	const allEmails = useSelect((select) =>
		select('mailster/automation').getEmails()
	);

	const transform = 'translateX(' + (0.5 - weight) * -100 + '%)';

	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'canvas-handle',
			style: { transform: transform },
		},
		{
			template: BLOCK_TEMPLATE,
			templateLock: 'all',
		}
	);

	const label = __('Check if', 'mailster');
	const info = '';

	return (
		<Step
			{...props}
			className={className}
			after={<div {...innerBlocksProps} />}
			isIncomplete={!conditions}
			inspectorControls={<InspectorControls {...props} />}
		>
			<CardBody>
				<div className="mailster-step-label">
					<Icon icon={StepIcon} />
					{label}
				</div>
				<div className="mailster-step-info">
					{conditions && (
						<ServerSideRender
							block="mailster-workflow/conditions"
							attributes={{
								...attributes,
								...{ render: true, plain: true, emails: allEmails },
							}}
							EmptyResponsePlaceholder={() => <Spinner />}
						/>
					)}
					{!conditions && __('Define a condition', 'mailster')}
				</div>
			</CardBody>
		</Step>
	);
}
