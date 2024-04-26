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
import Comment from '../inspector/Comment';
import StepId from '../inspector/StepId';
import { useDocument } from '../../util';

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
	} = props;
	const { id } = attributes;

	const doc = useDocument();

	useEffect(() => {
		if (!doc || !hasStepId) return;
		if (!id || doc.querySelectorAll('.mailster-step-' + id).length > 1)
			setAttributes({ id: clientId.substring(30) });
	}, [doc]);

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
	isIncomplete && className.push('mailster-step-incomplete');

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
					<QueueBadge {...props} />
					<Comment {...props} />
					<StepId {...props} />
					{children}
				</Card>
				{after}
				<div className="end-stop canvas-handle"></div>
			</div>
		</>
	);
}
