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
	BlockControls,
	BlockAlignmentToolbar,
	RichText,
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	RadioControl,
	SelectControl,
	RangeControl,
	ColorPalette,
	MenuGroup,
	MenuItem,
	Draggable,
	IconButton,
	Flex,
	FlexItem,
	FlexBlock,
	Button,
	BaseControl,
} from '@wordpress/components';

import { Fragment, Component, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

import { Icon, arrowUp, arrowDown, trash } from '@wordpress/icons';

export default function InputBlockControls(props) {
	const { attributes, setAttributes, isSelected } = props;
	const {
		label,
		inline,
		required,
		native,
		name,
		type,
		selected,
		style,
		values,
		hasLabel,
		align,
	} = attributes;

	function updateAlignment(alignment) {
		setAttributes({ align: alignment });
	}

	return (
		<BlockControls>
			<BlockAlignmentToolbar value={align} onChange={updateAlignment} />
		</BlockControls>
	);
}
