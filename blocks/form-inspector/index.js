/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
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
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

import Doubleoptin from './Doubleoptin';
import Lists from './Lists';
import Styling from './Styling';

function PluginDocumentSettingPanelDemo() {
	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	useEffect(() => {
		console.warn('ONCE');
	}, []);

	console.warn('META', meta);

	return (
		<Fragment>
			<PluginDocumentSettingPanel
				name="doubleoptin"
				title={meta.doubleoptin ? 'Double Opt In ' : 'Single Opt In'}
			>
				<Doubleoptin {...meta} setMeta={setMeta} />
			</PluginDocumentSettingPanel>
			<PluginDocumentSettingPanel name="lists" title="Styling">
				<Styling {...meta} setMeta={setMeta} />
			</PluginDocumentSettingPanel>
			<PluginDocumentSettingPanel name="lists" title="Lists Options">
				<Lists {...meta} setMeta={setMeta} />
			</PluginDocumentSettingPanel>
		</Fragment>
	);
}
/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

registerPlugin('plugin-document-setting-panel-demo', {
	render: PluginDocumentSettingPanelDemo,
	icon: false,
});
