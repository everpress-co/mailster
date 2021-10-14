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
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';
import { Fragment, useState } from '@wordpress/element';

import { more } from '@wordpress/icons';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import InputFieldInspectorControls from '../input/inspector.js';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { blockId } = attributes;
	let placeholder = attributes.label || __('Enter Label', 'mailster');

	return (
		<Fragment>
			<div {...useBlockProps({ className: 'mailster-wrapper' })}>
				{!attributes.inline && placeholder && (
					<RichText
						tagName="label"
						value={!attributes.inline && attributes.label}
						onChange={(val) => setAttributes({ label: val })}
						allowedFormats={[]}
						placeholder={placeholder}
					/>
				)}
				<input
					style={{
						color: attributes.color,
						borderColor: attributes.borderColor,
						backgroundColor: attributes.backgroundColor,
					}}
					onChange={() => {}}
					value={(attributes.inline && placeholder) || ''}
				/>
			</div>
			<InputFieldInspectorControls {...props} />
		</Fragment>
	);
}
