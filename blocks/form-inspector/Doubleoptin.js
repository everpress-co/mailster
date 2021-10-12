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
	TextareaControl,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';

import { more } from '@wordpress/icons';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Doubleoptin({
	doubleoptin,
	subject,
	headline,
	content,
	setMeta,
}) {
	return (
		<Fragment>
			<CheckboxControl
				label="Enable Double Opt in for this form"
				help="Is the user a author or not?"
				checked={!!doubleoptin}
				onChange={() => setMeta({ doubleoptin: !doubleoptin })}
			/>
			{doubleoptin && (
				<>
					<TextControl
						label="Subject"
						value={subject}
						help="Helptext"
						onChange={(value) => setMeta({ subject: value })}
					/>
					<TextControl
						label="Headline"
						value={headline}
						help="Helptext"
						onChange={(value) => setMeta({ headline: value })}
					/>
					<TextareaControl
						label="Content"
						value={content}
						help="Helptext"
						onChange={(value) => setMeta({ content: value })}
					/>
				</>
			)}
		</Fragment>
	);
}
