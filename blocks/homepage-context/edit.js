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
	InspectorControls,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import apiFetch from '@wordpress/api-fetch';
import { TabPanel, Tooltip } from '@wordpress/components';
import { useSelect, select, useDispatch, dispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { TABS } from '../homepage/constants';
import HomepageContextInspectorControls from './inspector';

const SUBSCRIBE_TEMPLATE = [
	['core/heading', { content: __('Thanks for your interest!', 'mailster') }],
	[
		'core/paragraph',
		{
			content: __(
				"Thank you for confirming your subscription to our newsletter. We're excited to have you on board!",
				'mailster'
			),
		},
	],
];

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, context } = props;

	const { type = 'submission' } = attributes;

	const contextAlign = context['mailster-homepage-context/align'];

	useEffect(() => {
		setAttributes({ align: contextAlign });
	}, [contextAlign]);

	useEffect(() => {
		if (!isSelected) return;
		location.hash = '#mailster-' + type;
	}, [isSelected]);

	const className = ['mailster-form-type'];

	className.push('mailster-form-type-' + type);

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	const currentTab = TABS.find((tab) => tab.id === type);

	const template =
		type != 'subscribe' ? [['mailster/form']] : SUBSCRIBE_TEMPLATE;

	return (
		<>
			<div {...blockProps}>
				{currentTab && (
					<Tooltip text={currentTab.label}>
						<span className="section-info">
							{sprintf(__('[Mailster]: %s', 'mailster'), currentTab.name)}
						</span>
					</Tooltip>
				)}
				<InnerBlocks templateLock={false} template={template} />
			</div>
			<HomepageContextInspectorControls {...props} />
		</>
	);
}
