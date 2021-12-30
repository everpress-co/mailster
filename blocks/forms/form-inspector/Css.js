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
import { PanelColorSettings } from '@wordpress/block-editor';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { brush } from '@wordpress/icons';

import { CssContent } from '../shared/CssContent';

/**
 * Internal dependencies
 */

export default function Css(props) {
	const { attributes, setAttributes, meta, setMeta } = props;

	return (
		<PluginDocumentSettingPanel
			name="custom-css"
			title={__('Custom Css', 'mailster')}
			initialOpen={false}
			icon={brush}
		>
			{!attributes && <Spinner />}
			{attributes && (
				<CssContent
					attributes={attributes}
					setAttributes={setAttributes}
				/>
			)}
		</PluginDocumentSettingPanel>
	);
}
