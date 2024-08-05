/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	TextControl,
	TextareaControl,
	Tip,
} from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */

import { HelpBeacon } from '../../util';

export default function JumperInspectorControls(props) {
	const { attributes, setAttributes } = props;
	const { id, email, subject, message } = attributes;

	const currentUser = useSelect((select) => {
		return select('core').getCurrentUser();
	}, []);

	const user_email = useSelect(
		(select) => {
			if (!currentUser) return;
			return select('core').getUser(currentUser.id);
		},
		[currentUser]
	);

	useEffect(() => {
		if (!email && user_email) {
			setAttributes({ email: user_email.email });
		}
	}, [user_email]);

	useEffect(() => {
		if (!id) return;
		!subject &&
			setAttributes({
				subject: sprintf(__('Step #%s reached!', 'mailster'), id),
			});
		!message &&
			setAttributes({
				message: sprintf(__('Step #%s has been reached!', 'mailster'), id),
			});
	}, [id, subject, message]);

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<HelpBeacon id="66336f5b0cfcb4508af6ae55" align="right" />
					<TextControl
						label={__('Email', 'mailster')}
						type="email"
						value={email}
						onChange={(value) => {
							setAttributes({ email: value });
						}}
						help={__(
							'Define an email address for the notification.',
							'mailster'
						)}
						placeholder={__('Add email', 'mailster')}
					/>
					<TextControl
						label={__('Subject', 'mailster')}
						type="subject"
						value={subject}
						onChange={(value) => {
							setAttributes({ subject: value });
						}}
						help={__('Set a subject.', 'mailster')}
						placeholder={__('Add subject', 'mailster')}
					/>
					<TextareaControl
						label={__('Message', 'mailster')}
						type="message"
						rows={10}
						value={message}
						onChange={(value) => {
							setAttributes({ message: value });
						}}
						help={__('Set a message.', 'mailster')}
						placeholder={__('Add message', 'mailster')}
					/>
					<Tip>
						{__(
							'You can use placeholders like {firstname} or {lastname} to add subscriber data to your message.',
							'mailster'
						)}
					</Tip>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
