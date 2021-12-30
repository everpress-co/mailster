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
 * Internal dependencies
 */

export default function Doubleoptin(props) {
	const { meta, setMeta } = props;
	const { doubleoptin, subject, headline, link, content, confirmredirect } =
		meta;

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
				label={__('Enable double opt in', 'mailster')}
				checked={!!doubleoptin}
				onChange={() => setMeta({ doubleoptin: !doubleoptin })}
			/>
			{doubleoptin && (
				<>
					<TextControl
						label={__('Subject', 'mailster')}
						value={subject}
						help="Helptext"
						onChange={(value) => setMeta({ subject: value })}
					/>
					<TextControl
						label={__('Headline', 'mailster')}
						value={headline}
						help="Helptext"
						onChange={(value) => setMeta({ headline: value })}
					/>
					<TextareaControl
						label={__('Content', 'mailster')}
						className={!isValidContent && 'error-message'}
						value={content}
						help={
							!isValidContent &&
							'Make sure this field contain a {link} tag.'
						}
						onChange={(value) => setMeta({ content: value })}
					/>
					<TextControl
						label={__('Linktext', 'mailster')}
						value={link}
						help="Helptext"
						onChange={(value) => setMeta({ link: value })}
					/>
					<TextControl
						label={__('Redirect after confirm', 'mailster')}
						help={__(
							'Redirect subscribers after they have confirmed their subscription',
							'mailster'
						)}
						value={confirmredirect}
						onChange={(value) =>
							setMeta({ confirmredirect: value })
						}
						type="url"
					/>
				</>
			)}
		</PluginDocumentSettingPanel>
	);
}
