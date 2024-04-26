/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';

import { useBlockProps } from '@wordpress/block-editor';

import { useSelect } from '@wordpress/data';
import { Card, CardBody, Icon } from '@wordpress/components';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

import ActionInspectorControls from './inspector.js';
import QueueBadge from '../inspector/QueueBadge.js';
import Comment from '../inspector/Comment.js';
import StepId from '../inspector/StepId.js';
import { getAction, getInfo } from './functions.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id, action, comment } = attributes;
	const className = [];

	id && className.push('mailster-step-' + id);
	!action && className.push('mailster-step-incomplete');

	const allActions = useSelect((select) =>
		select('mailster/automation').getActions()
	);

	const actionObj = getAction(action);

	const label = actionObj?.label || <></>;
	const info = getInfo(attributes);
	const icon = actionObj?.icon;

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});
	return (
		<>
			<ActionInspectorControls {...props} />
			<div {...blockProps}>
				<Card className="mailster-step" title={info}>
					<QueueBadge {...props} />
					<StepId {...props} />
					<Comment {...props} />
					<CardBody size="small">
						<div className="mailster-step-label">
							<Icon icon={Icons[icon]} />
							{label}
						</div>
						{info && (
							<div
								className="mailster-step-info"
								dangerouslySetInnerHTML={{ __html: info }}
							/>
						)}
					</CardBody>
				</Card>
				<div className="end-stop canvas-handle"></div>
			</div>
		</>
	);
}
