/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	PluginPrePublishPanel,
	PluginPostPublishPanel,
	PluginPostStatusInfo,
} from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import {
	useSelect,
	select,
	useDispatch,
	dispatch,
	subscribe,
} from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import {
	registerBlockVariation,
	getBlockType,
	createBlock,
	rawHandler,
} from '@wordpress/blocks';

/**
 * Internal dependencies
 */

import InputStyles from './InputStyles';
import Dimensions from './Dimensions';
import FormStyles from './FormStyles';
import Css from './Css';
import Options from './Options';
import Doubleoptin from './Doubleoptin';
import Lists from './Lists';
import WelcomeGuide from './WelcomeGuide';
import Placement from './Placement';
import PublishChecks from './PublishChecks';

import InlineStyles from './InlineStyles';

import '../store';

function SettingsPanelPlugin() {
	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	const blocks = useSelect(
		(select) => select('core/block-editor').getBlocks(),
		[]
	);

	const [blockProps, setBlockProps] = useState(false);

	useEffect(() => {
		const root = blocks.find((block) => {
			return block.name == 'mailster/form-wrapper';
		});

		if (root) {
			const blockProps =
				select('core/block-editor').getBlock(root.clientId) || {};

			blockProps.setAttributes = (attributes = {}) => {
				dispatch('core/block-editor').updateBlockAttributes(
					root.clientId,
					{
						...select('core/block-editor').getBlockAttributes(
							root.clientId
						),
						...attributes,
					}
				);
			};

			setBlockProps(blockProps);
		}
	}, [blocks]);

	return (
		<>
			<PluginPrePublishPanel
				className="my-plugin-pre-publish-panel"
				title="Panel title"
				initialOpen={true}
			>
				<PublishChecks meta={meta} setMeta={setMeta} />
			</PluginPrePublishPanel>
			<PluginPostPublishPanel
				className="my-plugin-publish-panel"
				title="Panel title"
				initialOpen={true}
			>
				<PublishChecks meta={meta} setMeta={setMeta} />
			</PluginPostPublishPanel>
			<PluginPostStatusInfo className="my-plugin-post-status-info">
				<PublishChecks meta={meta} setMeta={setMeta} />
			</PluginPostStatusInfo>
			<InlineStyles />
			<WelcomeGuide meta={meta} setMeta={setMeta} />
			<Options meta={meta} setMeta={setMeta} />
			<Doubleoptin meta={meta} setMeta={setMeta} />
			<Lists meta={meta} setMeta={setMeta} />
			<FormStyles {...blockProps} />
			<InputStyles {...blockProps} />
			<Dimensions {...blockProps} />
			<Css {...blockProps} />
			<Placement meta={meta} setMeta={setMeta} />
		</>
	);
}

registerPlugin('mailster-block-form-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});
