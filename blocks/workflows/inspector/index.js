/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { sprintf, __, _n } from '@wordpress/i18n';

import {
	PluginPrePublishPanel,
	PluginPostPublishPanel,
	PluginPostStatusInfo,
	PluginSidebarMoreMenuItem,
} from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { useState, useEffect, createRoot } from '@wordpress/element';
import { createBlock } from '@wordpress/blocks';
import { useSelect, select, useDispatch, dispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { BaseControl, Flex, FlexItem, PanelRow } from '@wordpress/components';

/**
 * Internal dependencies
 */

import '../store';
import Triggers from './Triggers';
import Options from './Options';
import PatternModal from './PatternModal';
import PublishInfo from './PublishInfo';
import CanvasToolbar from './CanvasToolbar';
import {
	whenEditorIsReady,
	searchBlocks,
	searchBlock,
	useLocalStorage,
} from '../../util';
import { ButtonGroup } from '@wordpress/components';
import { Button } from '@wordpress/components';

whenEditorIsReady().then(() => {
	window.addEventListener('popstate', (event) => {
		selectBlockFromHash();
	});
	selectBlockFromHash();
});

whenEditorIsReady().then(() => {
	const editorToolbar = document.querySelector('.edit-post-header__toolbar');

	// If toolbar doesn't exist, we can't continue
	if (!editorToolbar) {
		return;
	}

	const wrap = document.createElement('div');
	wrap.id = 'canvas-toolbar';
	wrap.style.cssText = 'display:flex;';

	editorToolbar.appendChild(wrap);

	const root = createRoot(wrap);
	root.render(<CanvasToolbar />);
});

const selectBlockFromHash = () => {
	const step = location.hash.match(/#step-([a-z0-9]+)/);
	if (step) {
		const el = document.querySelector('.mailster-step-' + step[1]);
		el && dispatch('core/block-editor').flashBlock(el.dataset.block);
		el && dispatch('core/block-editor').selectBlock(el.dataset.block);
	}
};

function SettingsPanelPlugin() {
	const [meta, setMeta] = useEntityProp(
		'postType',
		'mailster-workflow',
		'meta'
	);

	const {
		selectBlock,
		toggleBlockHighlight,
		flashBlock,
		moveBlockToPosition,
		updateBlockAttributes,
		removeBlock,
	} = useDispatch('core/block-editor');
	const { getBlockRootClientId, getBlockIndex, getBlocks } =
		useSelect('core/block-editor');
	const { isAutosavingPost, isSavingPost, isCleanNewPost } =
		useSelect('core/editor');

	const blocks = getBlocks();
	const [invalidBlocks, setinvalidBlocks] = useState([]);

	const invalidSteps = invalidBlocks.length;

	const allNumbers = useSelect((select) =>
		select('mailster/automation').getNumbers()
	);

	const finished = allNumbers ? allNumbers['finished'] : 0;
	const active = allNumbers ? allNumbers['active'] : 0;
	const total = allNumbers ? allNumbers['total'] : 0;

	// TODO Make this better
	useEffect(() => {
		//console.log('BLOCK ADDED');
		return;
		const count = [
			...document.querySelectorAll('.mailster-step-incomplete'),
		].map((el) => el.dataset.block);
		setinvalidBlocks(count);

		if (count.length) {
			//	dispatch('core/editor').lockPostSaving('invalidBlocks');
		} else {
			//	dispatch('core/editor').unlockPostSaving('invalidBlocks');
		}
	}, [blocks]);

	// TODO Make this better
	useEffect(() => {}, []);

	useEffect(() => {
		if (isCleanNewPost()) return;
		const triggerBlocks = searchBlocks('mailster-workflow/triggers');
		if (!triggerBlocks.length) {
			const block = createBlock('mailster-workflow/triggers');
			dispatch('core/block-editor').insertBlock(block, 0);
		} else if (triggerBlocks.length > 1) {
			triggerBlocks.slice(1).map((block) => {
				dispatch('core/block-editor').updateBlockAttributes(block.clientId, {
					lock: false,
				});
				dispatch('core/block-editor').removeBlock(block.clientId);
			});
		}
	}, [blocks]);

	const iframed = document.querySelector('iframe[name="editor-canvas"]');
	const [conditionsDepth, setConditionsDepth] = useState();

	useEffect(() => {
		if (!iframed) return;

		const findMaxDepth = (root, selector, depth = 0) =>
			[...root.querySelectorAll(selector)].reduce(
				(maxDepth, children) =>
					Math.max(maxDepth, findMaxDepth(children, selector, depth + 1)),
				depth
			);

		const check = () => {
			console.warn('CHECK');

			if (iframed.contentWindow.document.readyState == 'complete') {
				if (searchBlock('mailster-workflow/conditions')) {
					const root =
						iframed.contentWindow.document.querySelector('.is-root-container');
					if (root) {
						setConditionsDepth(findMaxDepth(root, '.mailster-step-conditions'));
					}
				} else {
					setConditionsDepth(0);
				}
				const wrap = iframed.contentWindow.document.querySelector(
					'.editor-styles-wrapper'
				);
				if (wrap) wrap.style.minWidth = conditionsDepth * 800 + 'px';
			}
		};

		iframed && iframed.addEventListener('load', check);

		check();

		console.warn('Asdasd', iframed.contentWindow.document.readyState);
	}, [conditionsDepth, iframed, blocks]);

	return (
		<>
			<PatternModal />
			<PluginPrePublishPanel
				className="my-plugin-pre-publish-panel"
				initialOpen={true}
			>
				<PanelRow>
					<BaseControl>
						{!!invalidSteps && (
							<div>
								<span className="number">{invalidSteps}</span>
								<span className="label">{__('invalidSteps', 'mailster')}</span>
							</div>
						)}
					</BaseControl>
				</PanelRow>
			</PluginPrePublishPanel>
			<PluginPostPublishPanel
				className="my-plugin-publish-panel"
				title="Panel title"
				initialOpen={true}
			>
				PluginPostPublishPanel
			</PluginPostPublishPanel>
			<PluginSidebarMoreMenuItem target="sidebar-name">
				PluginSidebarMoreMenuItem
			</PluginSidebarMoreMenuItem>
			<PublishInfo meta={meta} setMeta={setMeta} />
			<PluginPostStatusInfo className="status-numbers">
				<Flex align="center" justify="space-between">
					<FlexItem>
						<span className="label">{__('active', 'mailster')}</span>
						<span className="number">{active}</span>
					</FlexItem>

					<FlexItem>
						<span className="label">{__('finished', 'mailster')}</span>
						<span className="number">{finished}</span>
					</FlexItem>

					<FlexItem>
						<span className="label">{__('total', 'mailster')}</span>
						<span className="number">{total}</span>
					</FlexItem>
				</Flex>
			</PluginPostStatusInfo>

			<Options meta={meta} setMeta={setMeta} />

			{false && <Triggers meta={meta} setMeta={setMeta} />}
		</>
	);
}

registerPlugin('mailster-automation-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});
