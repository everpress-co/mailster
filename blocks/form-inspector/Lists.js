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
import { useSelect } from '@wordpress/data';

import ListsPanel from './ListsPanel';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Lists(props) {
	return (
		<PluginDocumentSettingPanel name="userschoice" title="Lists">
			<CheckboxControl
				label="Users Choice"
				checked={!!props.meta.userschoice}
				onChange={() =>
					props.setMeta({ userschoice: !props.meta.userschoice })
				}
				help="Users decide which list they subscribe to"
			/>
			<ListsPanel {...props} />
		</PluginDocumentSettingPanel>
	);
}
