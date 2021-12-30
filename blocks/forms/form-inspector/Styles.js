/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

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
	TextareaControl,
	Spinner,
} from '@wordpress/components';
import { PanelColorSettings } from '@wordpress/block-editor';
import { useSelect, select, useDispatch, dispatch } from '@wordpress/data';
import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { brush } from '@wordpress/icons';

import { BackgroundContent } from '../shared/BackgroundContent';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Styles(props) {
	const { attributes, setAttributes, meta, setMeta } = props;

	const blocks = useSelect(
		(select) =>
			select('core/edit-post').isEditorPanelOpened(
				'plugin-document-setting-panel-demo/styling'
			),
		[]
	);

	return (
		<PluginDocumentSettingPanel
			name="styling"
			title={__('Form Styles', 'mailster')}
			initialOpen={false}
			icon={brush}
		>
			{!attributes && <Spinner />}
			{attributes && (
				<BackgroundContent
					attributes={attributes}
					setAttributes={setAttributes}
				/>
			)}
		</PluginDocumentSettingPanel>
	);
}
