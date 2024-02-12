/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { Card, CardBody, Spinner } from '@wordpress/components';

/**
 * Internal dependencies
 */
import ConditionInspectorControls from './inspector.js';
import QueueBadge from '../inspector/QueueBadge';
import Comment from '../inspector/Comment';
import StepId from '../inspector/StepId';
import { useSelect } from '@wordpress/data';

const BLOCK_TEMPLATE = [
	['mailster-workflow/condition-yes'],
	['mailster-workflow/condition-no'],
];

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id, comment, conditions, weight } = attributes;
	const className = ['mailster-step-conditions', 'canvas-handle'];

	id && className.push('mailster-step-' + id);
	!conditions && className.push('mailster-step-incomplete');

	const allEmails = useSelect((select) =>
		select('mailster/automation').getEmails()
	);

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

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

	const label = conditions ? __('Check if', 'mailster') : null;

	return (
		<>
			<ConditionInspectorControls {...props} />
			<div {...blockProps}>
				<Card className="mailster-step">
					<QueueBadge {...props} />
					<Comment {...props} />
					<CardBody>
						{label && <div className="mailster-step-label">{label}</div>}
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
				</Card>
				<div {...innerBlocksProps} />
				<div className="end-stop"></div>
			</div>
			<StepId {...props} />
		</>
	);
}
