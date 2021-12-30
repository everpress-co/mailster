/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	PanelColorSettings,
	RichText,
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	RangeControl,
	ColorPalette,
} from '@wordpress/components';

import { Fragment, Component, useState } from '@wordpress/element';

import { more } from '@wordpress/icons';

import ListsPanel from '../form-inspector/ListsPanel';

export default function InputFieldInspectorControls(props) {
	return (
		<InspectorControls>
			<Panel>
				<PanelBody title={__('List Settings', 'mailster')}>
					<ListsPanel {...props} />
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
