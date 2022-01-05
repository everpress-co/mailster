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

	return (
		<PluginDocumentSettingPanel
			className="with-panel"
			name="form-fields"
			title={__('Form Style', 'mailster')}
			initialOpen={true}
			opened={true}
		>
			<BackgroundContent
				attributes={attributes}
				setAttributes={setAttributes}
			/>
		</PluginDocumentSettingPanel>
	);
}
