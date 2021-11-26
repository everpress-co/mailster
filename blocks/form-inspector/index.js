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

/**
 * Internal dependencies
 */
import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { useSelect, select, useDispatch, dispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerBlockVariation } from '@wordpress/blocks';

import Doubleoptin from './Doubleoptin';
import Gdpr from './Gdpr';
import Lists from './Lists';
import WelcomeGuide from './WelcomeGuide';
import Placement from './Placement';

import InlineStyles from './InlineStyles';

function SettingsPanelPlugin() {
	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	useEffect(() => {
		//console.warn('ONCE');
	}, []);

	return (
		<>
			<PluginPrePublishPanel
				className="my-plugin-publish-panel"
				title="Panel title"
				initialOpen={true}
			>
				PluginPrePublishPanel
			</PluginPrePublishPanel>
			<InlineStyles />
			<WelcomeGuide meta={meta} setMeta={setMeta} />
			<Doubleoptin meta={meta} setMeta={setMeta} />
			<Gdpr meta={meta} setMeta={setMeta} />
			<Lists meta={meta} setMeta={setMeta} />
			<Placement meta={meta} setMeta={setMeta} />
		</>
	);
}

registerPlugin('mailster-form-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});
