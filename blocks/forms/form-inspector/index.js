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
import { Button } from '@wordpress/components';

import { registerPlugin } from '@wordpress/plugins';
import { useState, useEffect } from '@wordpress/element';
import { useSelect, select, dispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */

import InputStyles from './InputStyles';
import FormStyles from './FormStyles';
import Css from './Css';
import Events from './Events';
import Options from './Options';
import Doubleoptin from './Doubleoptin';
import Lists from './Lists';
import WelcomeGuide from './WelcomeGuide';
import Placement from './Placement';
import PublishChecks from './PublishChecks';
import '../store';
import { searchBlock, whenEditorIsReady } from '../../util';

function SettingsPanelPlugin() {
	const [meta, setMeta] = useEntityProp('postType', 'mailster-form', 'meta');

	const [blockProps, setBlockProps] = useState(false);

	//get root client if of the form wrapper
	const root = useSelect((select) =>
		select('core/block-editor')
			.getBlocks()
			.map((block) => block.clientId)
			.pop()
	);

	// define articifcially the setAttribute and attribute properties
	useEffect(() => {
		if (!root) return;

		if (!blockProps) {
			const tempBlockProps = select('core/block-editor').getBlock(root) || {};

			tempBlockProps.setAttributes = (attributes = {}) => {
				const newBlockProps = { ...tempBlockProps };
				const current = select('core/block-editor').getBlockAttributes(root);
				const merged = { ...current, ...attributes };

				newBlockProps.attributes = merged;
				setBlockProps(newBlockProps);

				dispatch('core/block-editor').updateBlockAttributes(root, merged);
			};

			setBlockProps(tempBlockProps);
		}
	}, [root]);

	// enter the root wrapper or replace it with a new one
	useEffect(() => {
		const all = select('core/block-editor').getBlocks(),
			count = all.length;

		if (count > 1) {
			console.warn('enter the root wrapper or replace it with a new one');
			const inserted = select('core/block-editor').getBlock(clientId);
			const current = all.find(
				(block) =>
					block.name == 'mailster/form-wrapper' && block.clientId != clientId
			);
			if (
				confirm(
					'This will replace your current form with the selected one. Continue?'
				)
			) {
				dispatch('core/block-editor').removeBlock(current.clientId);
			} else {
				dispatch('core/block-editor').removeBlock(inserted.clientId);
			}
		}
	}, []);

	// add or remove Lists checkbox depending on the meta
	useEffect(() => {
		if (!root) return;

		setTimeout(() => {
			const block = searchBlock('mailster/lists');

			if (block && !meta.userschoice) {
				//remove lock
				block.attributes.lock.remove = false;
				dispatch('core/block-editor').removeBlock(block.clientId);
				dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
			} else if (!block && meta.userschoice) {
				console.warn('Add lists block');
				const block = wp.blocks.createBlock('mailster/lists');
				const referenceBlock = searchBlock('mailster/field-submit');
				const pos = referenceBlock
					? select('core/block-editor').getBlockIndex(
							referenceBlock.clientId,
							referenceBlock.rootClientId
					  )
					: 0;

				dispatch('core/block-editor').insertBlock(
					block,
					pos,
					referenceBlock.rootClientId
				);
			}
		}, 1);
	}, [root, meta.userschoice]);

	// add or remove GDPR checkbox depending on the meta
	useEffect(() => {
		if (!root) return;

		setTimeout(() => {
			const block = searchBlock('mailster/gdpr');

			if (block && !meta.gdpr) {
				console.warn('Remove gdpr block');
				//remove lock
				block.attributes.lock.remove = false;

				dispatch('core/block-editor').removeBlock(block.clientId);
				dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
			} else if (!block && meta.gdpr) {
				console.warn('Add gdpr block');
				const block = wp.blocks.createBlock('mailster/gdpr');
				const referenceBlock = searchBlock('mailster/field-submit');

				const pos = referenceBlock
					? select('core/block-editor').getBlockIndex(
							referenceBlock.clientId,
							referenceBlock.rootClientId
					  )
					: 0;

				const x = dispatch('core/block-editor').insertBlock(
					block,
					pos,
					referenceBlock.rootClientId
				);
			}
		}, 1);
	}, [root, meta.gdpr]);

	// add message block if it's missing
	useEffect(() => {
		if (!root) return;

		setTimeout(() => {
			const messagesBlock = searchBlock('mailster/messages');

			if (!messagesBlock) {
				console.warn('Add message block');
				const block = wp.blocks.createBlock('mailster/messages');
				const referenceBlock = searchBlock(/^mailster\/field-/);
				const pos = referenceBlock
					? select('core/block-editor').getBlockIndex(
							referenceBlock.clientId,
							referenceBlock.rootClientId
					  )
					: 0;

				dispatch('core/block-editor').insertBlock(
					block,
					pos,
					referenceBlock.rootClientId,
					false
				);

				// clear any selected block
				dispatch('core/block-editor').clearSelectedBlock();
				// select "Form" in side panel
				dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
			}
		}, 1);
	}, [root]);

	return (
		<>
			<PluginPrePublishPanel
				className="mailster-block-forms-pre-publish-panel"
				initialOpen={true}
			>
				<PublishChecks meta={meta} setMeta={setMeta} />
			</PluginPrePublishPanel>
			<PluginPostStatusInfo className="mailster-block-forms-post-status-info">
				<PublishChecks meta={meta} setMeta={setMeta} />
			</PluginPostStatusInfo>
			<WelcomeGuide meta={meta} setMeta={setMeta} />
			<Options meta={meta} setMeta={setMeta} />
			<Doubleoptin meta={meta} setMeta={setMeta} />
			<Lists meta={meta} setMeta={setMeta} />
			{blockProps && (
				<>
					<FormStyles {...blockProps} />
					<InputStyles {...blockProps} />
					<Css {...blockProps} />
					<Events {...blockProps} />
					<Placement meta={meta} setMeta={setMeta} {...blockProps} />
				</>
			)}
		</>
	);
}

registerPlugin('mailster-block-form-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});
