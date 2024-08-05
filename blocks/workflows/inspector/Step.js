/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { useBlockProps } from '@wordpress/block-editor';
import { Card } from '@wordpress/components';

import { useEffect, useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import QueueBadge from '../inspector/QueueBadge';
import Disabler from '../inspector/Disabler';
import Comment from '../inspector/Comment';
import StepId from '../inspector/StepId';
import { useDocument } from '../../util';
import StepAppender from './StepAppender';

export default function Step(props) {
	const {
		attributes,
		setAttributes,
		children,
		className = [],
		hasStepId = true,
		before,
		after,
		inspectorControls,
		isSelected,
		isIncomplete,
		clientId,
		blockAttributes = {},
		isSelectionEnabled,
	} = props;
	const { id, disabled = false } = attributes;

	useDocument((doc) => {
		if (!doc || !hasStepId) return;
		if (!id || doc.querySelectorAll('.mailster-step-' + id).length > 1)
			setAttributes({ id: clientId.substring(30) });
	});

	useEffect(() => {
		if (!isSelected || !id) return;
		history.replaceState(undefined, undefined, '#step-' + id);

		return () => {
			history.pushState(
				'',
				document.title,
				location.pathname + location.search
			);
		};
	}, [isSelected]);

	const ref = useRef();

	id && className.push('mailster-step-' + id);
	isIncomplete &&
		isSelectionEnabled &&
		className.push('mailster-step-incomplete');
	disabled && className.push('mailster-step-disabled');

	const blockProps = useBlockProps({
		className: classnames({}, className),
		...blockAttributes,
	});

	return (
		<>
			{inspectorControls}
			<div {...blockProps}>
				{before}
				<Card className="mailster-step" ref={ref}>
					<Comment {...props} />
					<Disabler {...props} />
					<QueueBadge {...props} />
					<StepId {...props} />
					<div className="mailster-step-inner">{children}</div>
				</Card>
				{after}
				<div className="end-stop canvas-handle"></div>
			</div>
			<StepAppender {...props} />
		</>
	);
}
