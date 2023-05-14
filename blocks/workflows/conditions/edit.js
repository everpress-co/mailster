/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { useEffect, useRef, useState } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';
import { Card, CardBody, CardHeader, Spinner } from '@wordpress/components';

/**
 * Internal dependencies
 */
import ConditionInspectorControls from './inspector.js';
import QueueBadge from '../inspector/QueueBadge';
import Comment from '../inspector/Comment';
import StepId from '../inspector/StepId';
import { searchBlocks, useUpdateEffect } from '../../util';
import { useSelect, select } from '@wordpress/data';

const BLOCK_TEMPLATE = [
	['mailster-workflow/condition', { fulfilled: true }],
	['mailster-workflow/condition', { fulfilled: false }],
];

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id, comment, conditions, weight } = attributes;
	const className = ['mailster-step-conditions', 'canvas-handle'];

	className.push('mailster-step-' + id);
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
