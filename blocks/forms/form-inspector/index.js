/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	PluginDocumentSettingPanel,
	PluginPrePublishPanel,
	PluginPostPublishPanel,
	PluginPostStatusInfo,
} from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */

/**
 * Internal dependencies
 */
import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import {
	useSelect,
	select,
	useDispatch,
	dispatch,
	subscribe,
} from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerBlockVariation } from '@wordpress/blocks';

import InputFields from './InputFields';
import Styles from './Styles';
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

	const [attributes, setInitialAttributes] = useState(false);
	const [root, setRoot] = useState(false);

	useEffect(() => {
		const root = blocks.find((block) => {
			return block.name == 'mailster/form-wrapper';
		});

		if (root) {
			setRoot(root);
			setInitialAttributes(
				select('core/block-editor').getBlockAttributes(root.clientId)
			);
		}
	}, [blocks]);

	const setAttributes = (attributes = {}) => {
		dispatch('core/block-editor').updateBlockAttributes(root.clientId, {
			...select('core/block-editor').getBlockAttributes(root.clientId),
			...attributes,
		});
	};

	useEffect(() => {}, []);

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
			<Styles
				meta={meta}
				setMeta={setMeta}
				attributes={attributes}
				setAttributes={setAttributes}
			/>
			<InputFields
				meta={meta}
				setMeta={setMeta}
				attributes={attributes}
				setAttributes={setAttributes}
			/>
			<Css
				meta={meta}
				setMeta={setMeta}
				attributes={attributes}
				setAttributes={setAttributes}
			/>
			<Placement meta={meta} setMeta={setMeta} />
		</>
	);
}

registerPlugin('mailster-block-form-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});
