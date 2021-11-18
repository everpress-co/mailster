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
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
	ColorPaletteControl,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextareaControl,
	Card,
	CardBody,
	CardMedia,
	Button,
	RangeControl,
	FocalPointPicker,
	SelectControl,
	ComboboxControl,
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { useDebounce } from '@wordpress/compose';

import { more } from '@wordpress/icons';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Css(props) {
	const { attributes, setAttributes, isSelected, setCss, setShowClasses } =
		props;

	const { css } = attributes;

	let codeEditor;

	const setCssDebounce = useDebounce(setCss, 500);

	const placeholder =
		'.mailster-form{\n}\n.mailster-form .mailster-wrapper{\n}';

	const initCodeMirror = (isOpened) => {
		setShowClasses(isOpened);
		if (!isOpened || !wp.CodeMirror) return;
		setTimeout(() => {
			codeEditor = wp.CodeMirror.fromTextArea(
				document.getElementById('custom-css-textarea'),
				{
					tabMode: 'indent',
					lineNumbers: true,
					autofocus: true,
					type: 'text/css',
					lineWrapping: true,
					placeholder: placeholder,
				}
			).on('change', function (editor) {
				setCssDebounce(editor.getValue());
			});
		}, 0);

		return;
	};

	return (
		<PanelBody
			name="css"
			title="Custom CSS"
			initialOpen={false}
			onToggle={initCodeMirror}
		>
			<PanelRow>
				<TextareaControl
					id="custom-css-textarea"
					help="Enter your custom CSS here. Every declaration will get prefixed to work only for this specific form."
					value={css}
					onChange={(value) => wp.CodeMirror && setCssDebounce(value)}
				/>
			</PanelRow>
		</PanelBody>
	);
}
