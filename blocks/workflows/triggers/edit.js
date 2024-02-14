/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

import { useEffect, useState } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect, useDispatch, dispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */

import TriggersInspectorControls from './inspector';
import { searchBlock, searchBlocks } from '../../util';
import { ButtonGroup } from '@wordpress/components';
import { Button } from '@wordpress/components';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const className = ['mailster-step-triggers', 'canvas-handle'];

	const [triggerBlocks, setTriggerBlocks] = useState(0);

	// const { trigger } = meta;

	const { selectBlock, toggleBlockHighlight, flashBlock, moveBlockToPosition } =
		useDispatch('core/block-editor');
	const { getBlockRootClientId, getBlockIndex, getBlocks } =
		useSelect('core/block-editor');

	const blocks = getBlocks();

	triggerBlocks && className.push('mailster-step-count-' + triggerBlocks);

	// TODO Make this better
	// scroll triggers in the view on load
	useEffect(() => {
		const triggers = document.querySelector(
			'.wp-block-mailster-workflow-triggers'
		);
		triggers &&
			triggers.scrollIntoView({
				inline: 'center',
			});
	}, []);

	// make sure the first block is our trigger block
	useEffect(() => {
		const firstBlock = getBlocks()[0];
		if (firstBlock && firstBlock.name !== 'mailster-workflow/triggers') {
			const triggerBlock = searchBlock('mailster-workflow/triggers');
			const root = getBlockRootClientId(firstBlock.clientId);
			dispatch('core/block-editor').updateBlockAttributes(
				triggerBlock.clientId,
				{
					lock: false,
				}
			);
			// not working
			moveBlockToPosition(triggerBlock.clientId, root, root, 0);

			// TODO not working lock it again
			// dispatch('core/block-editor').updateBlockAttributes(
			// 	triggerBlock.clientId,
			// 	{
			// 		lock: true,
			// 	}
			// );
		}

		setTriggerBlocks(searchBlocks('mailster-workflow/trigger').length);
	}, [blocks]);

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	// const BLOCK_TEMPLATE_OLD = trigger.length
	// 	? trigger.map((t, i) => ['mailster-workflow/trigger', { trigger: t }])
	// 	: [['mailster-workflow/trigger', {}]];

	const BLOCK_TEMPLATE = [['mailster-workflow/trigger', {}]];

	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'canvas-handle',
		},
		{
			template: BLOCK_TEMPLATE,
			orientation: 'horizontal',
		}
	);

	return (
		<>
			<div {...blockProps}>
				<div {...innerBlocksProps} />
				<div className="wrap-line canvas-handle"></div>
			</div>
			<TriggersInspectorControls {...props} />
		</>
	);
}
