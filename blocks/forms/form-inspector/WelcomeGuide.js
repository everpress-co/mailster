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
	Guide,
	MenuItem,
	createSlotFill,
	MenuGroup,
	Button,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect, select, useDispatch, dispatch } from '@wordpress/data';
import { store as Store } from '@wordpress/edit-post';

import { more } from '@wordpress/icons';

/**
 * Internal dependencies
 */

import FormModal from './FormModal';

const STORAGENAME = 'mailsterFormsWelcomeGuide';

export default function WelcomeGuide(props) {
	const { meta, setMeta } = props;
	const [isOpen, setOpen] = useState(false);

	const { toggleFeature, togglePublishSidebar } = useDispatch(Store);

	const { showGeneralBlockWelcomeGuide } = useSelect((select) => {
		return {
			showGeneralBlockWelcomeGuide:
				select(Store).isFeatureActive('welcomeGuide'),
		};
	}, []);

	const { isActive } = useSelect((select) => {
		return {
			isActive: select(Store).isFeatureActive(STORAGENAME),
		};
	}, []);

	// show native block editor welcome screen first
	if (showGeneralBlockWelcomeGuide) {
		toggleFeature('welcomeGuide');
		if (isActive) {
			toggleFeature(STORAGENAME);
		}
		return;
	}

	if (isActive) {
		return <FormModal {...props} />;
	}

	return (
		<Guide
			onFinish={() => toggleFeature(STORAGENAME)}
			pages={[
				{
					image: (
						<video
							width="680"
							loop
							muted
							preload="auto"
							autoPlay
							src="https://mailster.github.io/videos/mailster_editor.mp4"
							poster="https://mailster.github.io/videos/mailster_editor.png"
						>
							<source
								src="https://mailster.github.io/videos/mailster_editor.mp4"
								type="video/mp4"
							/>
						</video>
					),
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__(
									'Welcome to the Mailster Form Builder',
									'mailster'
								)}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									'In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.',
									'mailster'
								)}
							</p>
							<Button
								onClick={() =>
									togglePublishSidebar(STORAGENAME)
								}
							>
								Go
							</Button>
						</>
					),
				},
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
					image: (
						<video
							width="680"
							loop
							muted
							preload="auto"
							autoPlay
							src="https://mailster.github.io/videos/mailster_editor.mp4"
							poster="https://mailster.github.io/videos/mailster_editor.png"
						>
							<source
								src="https://mailster.github.io/videos/mailster_editor.mp4"
								type="video/mp4"
							/>
						</video>
					),
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
