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

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

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
