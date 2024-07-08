/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */

import { sprintf, __, _n } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';

import {
	PluginPrePublishPanel,
	PluginPostPublishPanel,
	PluginPostStatusInfo,
} from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { useState, useEffect, createRoot } from '@wordpress/element';
import { createBlock } from '@wordpress/blocks';
import { useSelect, select, dispatch } from '@wordpress/data';
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
import ActiveStatus from './ActiveStatus';
import {
	whenEditorIsReady,
	searchBlocks,
	searchBlock,
	useBlockChange,
	useInterval,
	clearData,
	useFocus,
	useWindow,
} from '../../util';

whenEditorIsReady().then((w) => {
	w.addEventListener('popstate', selectBlockFromHash);
	selectBlockFromHash();
});

const selectBlockFromHash = () => {
	whenEditorIsReady().then((w) => {
		const step = location.hash.match(/#step-([a-z0-9]+)/);
		if (step) {
			const el = w.document.querySelector('.mailster-step-' + step[1]);
			el && dispatch('core/block-editor').flashBlock(el.dataset.block);
			el && dispatch('core/block-editor').selectBlock(el.dataset.block);
			el && el.scrollIntoView();
		}
	});
};

function SettingsPanelPlugin() {
	// TODO check if cblock count change on removing blocks
	const blocks = select('core/block-editor').getBlocks();
	const [invalidBlocks, setinvalidBlocks] = useState([]);

	const invalidSteps = invalidBlocks.length;

	const allNumbers = useSelect((select) =>
		select('mailster/automation').getNumbers()
	);

	const finished = allNumbers ? allNumbers['finished'] : 0;
	const active = allNumbers ? allNumbers['active'] : 0;
	const total = allNumbers ? allNumbers['total'] : 0;

	const [isDynmanic, setDynamic] = useState(true);

	const [ref, isFocused] = useFocus(window);

	const queueInterval = isFocused ? 2000 : 20000;

	isDynmanic &&
		useInterval(
			() => {
				clearData('getQueue', 'mailster/automation');
			},
			queueInterval,
			isFocused
		);
	const numbersInterval = isFocused ? 15000 : 30000;

	isDynmanic &&
		useInterval(
			() => {
				clearData('getCampaignStats', 'mailster/automation');
				clearData('getNumbers', 'mailster/automation');
			},
			numbersInterval,
			isFocused
		);

	// TODO Make this better
	// Toolbar
	useWindow((w) => {
		const editorToolbar = document.querySelector(
			'.editor-header__settings, .edit-post-header__settings'
		);
		const wrapper = document.querySelector('.edit-post-visual-editor');

		// If toolbar doesn't exist, we can't continue
		if (!editorToolbar) {
			return;
		}

		const canvasToolbar = document.createElement('div');
		canvasToolbar.className = 'interface-interface-canvas-toolbar';

		const toolbarWrap = document.createElement('div');
		toolbarWrap.className = 'edit-post-header-toolbar-extra';
		//canvasToolbar.style.cssText = 'display:flex;';
		//editorToolbar.appendChild(canvasToolbar);

		editorToolbar.prepend(toolbarWrap);
		wrapper.prepend(canvasToolbar);

		createRoot(canvasToolbar).render(<CanvasToolbar />);
		createRoot(toolbarWrap).render(<ActiveStatus />);
	}, []);

	useBlockChange(() => {
		//if (isCleanNewPost()) return;
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
	});

	const [conditionsDepth, setConditionsDepth] = useState();

	useBlockChange(() => {
		whenEditorIsReady().then((w) => {
			const findMaxDepth = (root, selector, depth = 0) =>
				[...root.querySelectorAll(selector)].reduce(
					(maxDepth, children) =>
						Math.max(maxDepth, findMaxDepth(children, selector, depth + 1)),
					depth
				);

			const check = () => {
				if (searchBlock('mailster-workflow/conditions')) {
					setConditionsDepth(
						findMaxDepth(
							w.document.querySelector('.is-root-container'),
							'.mailster-step-conditions'
						)
					);
				} else {
					setConditionsDepth(0);
				}
			};

			check();
		});
	});

	useEffect(() => {
		whenEditorIsReady().then((w) => {
			const wrap = w.document.querySelector('.editor-styles-wrapper');
			if (wrap) wrap.style.minWidth = conditionsDepth * 800 + 'px';
		});
	}, [conditionsDepth]);

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
								<span className="label">{__('Invalid Steps', 'mailster')}</span>
							</div>
						)}
					</BaseControl>
				</PanelRow>
			</PluginPrePublishPanel>

			<PublishInfo />

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

			<Options />
		</>
	);
}

registerPlugin('mailster-automation-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});

// only allow blocks inside the form wrapper
function convertP(settings, name) {
	if ('core/paragraph' !== name) {
		return settings;
	}
	settings.supports.inserter = false;
	//settings.supports.multiple = false;
	//settings.attributes.placeholder.default = 'aaaaa';

	return settings;
}

addFilter('blocks.registerBlockType', 'mailster/workflows/convert-p', convertP);
