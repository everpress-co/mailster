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

import { StylesContent, colorSettings } from '../shared/StylesContent';

/**
 * Internal dependencies
 */

export default function InputFields(props) {
	const { attributes, setAttributes, meta, setMeta } = props;

	return (
		<PluginDocumentSettingPanel
			name="input-fields"
			title={__('Input Fields', 'mailster')}
			initialOpen={false}
			icon={brush}
		>
			{!attributes && <Spinner />}
			{attributes && (
				<StylesContent
					attributes={attributes}
					setAttributes={setAttributes}
				/>
			)}
		</PluginDocumentSettingPanel>
	);
}
