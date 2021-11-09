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
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { more } from '@wordpress/icons';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Doubleoptin(props) {
	const { doubleoptin, subject, headline, content, setMeta, isSelected } =
		props;

	const [isValidContent, setValidContent] = useState(false);

	useEffect(() => {
		setValidContent(/{link}/.test(content));
	}, [content]);

	return (
		<PluginDocumentSettingPanel
			name="doubleoptin"
			title={doubleoptin ? 'Double Opt In ' : 'Single Opt In'}
		>
			<CheckboxControl
				label="Send a confirmation message"
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
						className={isValidContent ? '' : 'error-message'}
						value={content}
						help={
							isValidContent
								? ''
								: 'Make sure this field contain a {link} tag.'
						}
						onChange={(value) => setMeta({ content: value })}
					/>
				</>
			)}
		</PluginDocumentSettingPanel>
	);
}
