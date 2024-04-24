/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { sprintf, __, _n } from '@wordpress/i18n';

import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { useSelect, select } from '@wordpress/data';
import * as Icons from '@wordpress/icons';
import ServerSideRender from '@wordpress/server-side-render';

import {
	Card,
	CardBody,
	CardHeader,
	Icon,
	Spinner,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import QueueBadge from '../inspector/QueueBadge';
import JumperInspectorControls from './inspector.js';
import Comment from '../inspector/Comment.js';
import StepId from '../inspector/StepId.js';
import StepIcon from './Icon.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { id, conditions, step } = attributes;
	const className = [];

	const { getBlockIndex, getBlock, getBlocks, getBlockAttributes } =
		select('core/block-editor');

	id && className.push('mailster-step-' + id);

	!step && className.push('mailster-step-incomplete');

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	const info = __('If', 'mailster');
	const label = __('Jump to', 'mailster');

	return (
		<>
			<JumperInspectorControls {...props} />
			<div {...blockProps}>
				<Card className="mailster-step" title={info}>
					<QueueBadge {...props} />
					<StepId {...props} />
					<Comment {...props} />
					<CardBody size="small">
						<div className="mailster-step-label">
							<Icon icon={StepIcon} />
							{label}
						</div>
						<div className="mailster-step-info">
							{conditions && (
								<>
									{__('If', 'mailster')}
									<ServerSideRender
										block="mailster-workflow/conditions"
										attributes={{
											...attributes,
											...{ render: true, plain: true },
										}}
										EmptyResponsePlaceholder={() => <Spinner />}
									/>
									{__('jump to', 'mailster')}
								</>
							)}
							{!conditions && __('Define a condition', 'mailster')}
						</div>
					</CardBody>
				</Card>
				<div className="end-stop canvas-handle"></div>
			</div>
		</>
	);
}
