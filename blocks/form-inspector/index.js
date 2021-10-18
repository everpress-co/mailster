/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import {
	PluginDocumentSettingPanel,
	PluginPrePublishPanel,
} from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
//import './style.scss';

/**
 * Internal dependencies
 */
import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { useSelect, select, dispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerBlockVariation } from '@wordpress/blocks';

import FormModal from './FormModal';
import Doubleoptin from './Doubleoptin';
import Lists from './Lists';
import Placement from './Placement';

console.warn('xxx', window.xxxx);

registerBlockVariation('mailster/input', {
	name: 'date2',
	title: 'Date Field',
	description: 'Code is poetry!',
	scope: ['inserter', 'block', 'transform'],
	icon: {
		background: '#f00',
		src: 'button',
	},
	attributes: {
		type: 'text',
		label: 'Date',
		requried: false,
		forceRequired: false,
	},
});

function SettingsPanelPlugin() {
	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	useEffect(() => {
		console.warn('ONCE');
	}, []);

	// wp.data.dispatch('core/editor').lockPostSaving();
	// //do stuff
	// wp.data.dispatch('core/editor').unlockPostSaving();
	//
	return (
		<>
			<PluginPrePublishPanel
				className="my-plugin-publish-panel"
				title="Panel title"
				initialOpen={true}
			>
				PluginPrePublishPanel
			</PluginPrePublishPanel>
			<FormModal />
			<Doubleoptin {...meta} setMeta={setMeta} />
			<Lists {...meta} setMeta={setMeta} />
			<Placement {...meta} setMeta={setMeta} />
		</>
	);
}

registerPlugin('mailster-form-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});
