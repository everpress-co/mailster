/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { ButtonBlockAppender } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */

import { useSelect } from '@wordpress/data';
import { Tooltip } from '@wordpress/components';

export default function StepAppender(props) {
	const { attributes, clientId, name, isSelectionEnabled } = props;
	const { isExample } = attributes;

	const lastBlock = useSelect((select) => {
		const blocks = select('core/block-editor').getBlocks();
		if (!blocks.length) return null;
		return blocks.at(-1).clientId;
	});

	if (lastBlock !== clientId) return null;

	const isTrigger = name === 'mailster-workflow/triggers';

	const label = isTrigger
		? __('Click + to add a step', 'mailster')
		: __('Workflow Finished', 'mailster');

	return (
		<>
			<Tooltip
				text={__(
					'Once the user reaches this point the workflow will be finished.',
					'mailster'
				)}
			>
				<span
					className={
						'workflow-finished ' +
						name.replace('mailster-workflow/', 'workflow-finished-')
					}
				>
					{!isExample && label}
				</span>
			</Tooltip>
			{isSelectionEnabled && <ButtonBlockAppender />}
		</>
	);
}
