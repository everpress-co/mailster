/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	useBlockProps,
	InspectorControls,
	RichText,
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';

import { Fragment, Component, useState } from '@wordpress/element';

import { more } from '@wordpress/icons';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Lists(props) {
	const { meta, setMeta } = props;
	return (
		<PluginDocumentSettingPanel name="lists" title="Lists Options">
			<PanelRow>Lists</PanelRow>
			<PanelRow>ooo</PanelRow>
			<PanelRow>uuu</PanelRow>
		</PluginDocumentSettingPanel>
	);
}
