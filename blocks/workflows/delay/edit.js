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
import { useSelect } from '@wordpress/data';
import * as Icons from '@wordpress/icons';
import { Card, CardBody, CardHeader, Icon } from '@wordpress/components';

/**
 * Internal dependencies
 */
import DelayInspectorControls from './inspector.js';
import QueueBadge from '../inspector/QueueBadge.js';
import Comment from '../inspector/Comment.js';
import StepId from '../inspector/StepId.js';

import { getInfo, getLabel, isRelative } from './functions.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id, amount, unit, date, month, timezone, weekdays } = attributes;
	const className = [];

	useEffect(() => {
		!amount && setAttributes({ amount: 1 });
		!unit && setAttributes({ unit: 'hours' });
		!month && setAttributes({ month: 1 });
		!date && setAttributes({ date: new Date() });
	});

	id && className.push('mailster-step-' + id);

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	const info = getInfo(attributes);
	const label = getLabel(attributes);

	return (
		<>
			<DelayInspectorControls {...props} />
			<div {...blockProps}>
				<Card className="mailster-step" title={info}>
					<QueueBadge {...props} />
					<StepId {...props} />
					<Comment {...props} />
					<CardBody size="small">
						<div className="mailster-step-label">
							<Icon icon={Icons.backup} />
							{label}
						</div>
						{info && <div className="mailster-step-info">{info}</div>}
						{!isRelative() && timezone && (
							<div className="mailster-step-info">
								<br />
								{__('Respect users timezone', 'mailster')}
							</div>
						)}
					</CardBody>
				</Card>
				<div className="end-stop canvas-handle"></div>
			</div>
		</>
	);
}
