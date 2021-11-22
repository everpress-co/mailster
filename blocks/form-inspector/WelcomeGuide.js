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
	Guide,
	MenuItem,
	createSlotFill,
	MenuGroup,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect, select, useDispatch, dispatch } from '@wordpress/data';
import { store as myStore } from '@wordpress/edit-post';

import { more } from '@wordpress/icons';

import FormModal from './FormModal';

const STORAGENAME = 'mailsterFormsWelcomeGuide';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function WelcomeGuide(props) {
	const [isOpen, setOpen] = useState(false);

	const { toggleFeature } = useDispatch(myStore);

	const { isActive } = useSelect((select) => {
		return {
			isActive: select(myStore).isFeatureActive(STORAGENAME),
		};
	}, []);

	if (isActive) {
		return <FormModal />;
	}

	return (
		<Guide
			onFinish={() => toggleFeature(STORAGENAME)}
			pages={[
				{
					image: <img src="https://dummy.mailster.co/500x200.jpg" />,
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__('Welcome to the block editor', 'mailster')}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									'In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.',
									'mailster'
								)}
							</p>
						</>
					),
				},
				{
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__('Welcome to the block editor', 'mailster')}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									'In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.',
									'mailster'
								)}
							</p>
						</>
					),
				},
				{
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__('Welcome to the block editor', 'mailster')}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									'In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.',
									'mailster'
								)}
							</p>
						</>
					),
				},
			]}
			finishButtonText={__('Get started', 'mailster')}
			contentLabel={__('Mailster Forms Welcome Guide', 'mailster')}
		/>
	);
}
