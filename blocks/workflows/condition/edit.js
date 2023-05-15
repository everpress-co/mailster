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

/**
 * Internal dependencies
 */

import { searchBlock, searchBlocks } from '../../util';
import ConditionInspectorControls from './inspector.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId, context } = props;
	const { fulfilled } = attributes;
	const className = ['mailster-step-condition', 'canvas-handle'];

	const hasStop = (clientId) => {
		return searchBlock('mailster-workflow/stop', clientId, false);
	};

	hasStop(clientId) && className.push('mailster-has-stop');

	className.push(
		fulfilled ? 'mailster-step-condition-yes' : 'mailster-step-condition-no'
	);

	const title = fulfilled
		? __('Condition is fullfilled', 'mailster')
		: __('Condition is not fullfilled', 'mailster');

	//const weight = context['mailster-workflow/weight'] * 100;
	//const w = fulfilled ? 100 - weight : weight;

	const blockProps = useBlockProps({
		//style: { width: w + '%' },
		className: classnames({}, className),
	});
	const ALLOWED_BLOCKS = ['mailster-workflow/stop'];

	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'canvas-handle',
		},
		{
			allowedBlocks: ALLOWED_BLOCKS,
			templateLock: false,
		}
	);

	return (
		<>
			<div {...blockProps}>
				<div className="line line-in canvas-handle">
					<span className="condition-label" title={title}></span>
				</div>

				<div {...innerBlocksProps} />
				<div className="line line-out canvas-handle"></div>
			</div>
			<ConditionInspectorControls {...props} />
		</>
	);
}
