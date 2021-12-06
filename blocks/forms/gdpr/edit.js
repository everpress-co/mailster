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
import { Fragment, useState, useEffect } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';

import { more } from '@wordpress/icons';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */

//import InputFieldInspectorControls from '../input/inspector.js';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { content } = attributes;
	const className = ['mailster-wrapper'];

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	useEffect(() => {
		console.warn(meta.gdpr);
		setMeta({ gdpr: true });
		return () => {
			//need to check if in the main editor
			//setMeta({ gdpr: false });
		};
	}, []);

	return (
		<Fragment>
			<div
				{...useBlockProps({
					className: className.join(' '),
				})}
			>
				<label className="mailster-label">
					<input type="checkbox" name="_gdpr" value="1" />
					<RichText
						tagName="span"
						value={content}
						onChange={(val) => setAttributes({ content: val })}
						placeholder={__('Enter Label', 'mailster')}
					/>
				</label>
			</div>
		</Fragment>
	);
}
