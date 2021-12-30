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
	Button,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
	BaseControl,
	RadioControl,
	Flex,
	FlexItem,
	FlexBlock,
	Spinner,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { more } from '@wordpress/icons';
import apiFetch from '@wordpress/api-fetch';
import { Icon, arrowUp, arrowDown, trash } from '@wordpress/icons';
import {
	useSelect,
	select,
	dispatch,
	subscribe,
	useDispatch,
} from '@wordpress/data';

import ListsPanel from './ListsPanel';

/**
 * Internal dependencies
 */

export default function Lists(props) {
	return (
		<PluginDocumentSettingPanel
			name="userschoice"
			title={__('Lists', 'mailster')}
		>
			<ListsPanel {...props} />
		</PluginDocumentSettingPanel>
	);
}
