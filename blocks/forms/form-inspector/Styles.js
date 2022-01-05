/**
 * External dependencies
 */

/**
 * WordPress dependencies
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
import {
	PanelColorSettings,
	__experimentalPanelColorGradientSettings as PanelColorGradientSettings,
} from '@wordpress/block-editor';
import { useSelect, select, useDispatch, dispatch } from '@wordpress/data';
import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { brush } from '@wordpress/icons';

/**
 * Internal dependencies
 */

import { BackgroundContent } from '../shared/BackgroundContent';

export default function Styles(props) {
	const { attributes, setAttributes, meta, setMeta } = props;

	const blocks = useSelect(
		(select) =>
			select('core/edit-post').isEditorPanelOpened(
				'plugin-document-setting-panel-demo/styling'
			),
		[]
	);
	const colorValue1 = '#ff0000';
	const gradientValue1 = undefined;
	return (
		<PluginDocumentSettingPanel
			className="with-panel"
			name="styling"
			initialOpen={true}
			open={true}
		>
			<BackgroundContent
				attributes={attributes}
				setAttributes={setAttributes}
			/>
		</PluginDocumentSettingPanel>
	);
}
