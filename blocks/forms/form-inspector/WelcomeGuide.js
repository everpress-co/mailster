/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { Guide } from '@wordpress/components';

import { useSelect, useDispatch } from '@wordpress/data';
import { store as Store } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */

import FormModal from './FormModal';

const STORAGENAME = 'mailsterFormsWelcomeGuide';

export default function WelcomeGuide(props) {
	const { toggleFeature } = useDispatch(Store);

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
			className="mailster-block-forms-welcome"
			pages={[
				{
					image: (
						<img src="https://static.mailster.co/images/blockforms/welcome/185736691-1f415765-469e-4f45-b48f-d6e78a8f2c29.png" />
					),
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__('Welcome to the Mailster Block Form Editor', 'mailster')}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									'Block forms are the future of newsletter signup forms for Mailster. No matter if you like to display a signup form after each blog post, explicitly on a specific page or triggered as popup, we got you covered.',
									'mailster'
								)}
							</p>
							<p className="edit-post-welcome-guide__text">
								{__(
									'If you run a promotion only for a certain time you can schedule your form to get displayed in the future.',
									'mailster'
								)}
							</p>
						</>
					),
				},
				{
					image: (
						<img src="https://static.mailster.co/images/blockforms/welcome/185736663-efafe5a5-69d9-44a8-b965-f9af5e7f0434.png" />
					),
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__(
									'Start by choosing one of the provided template',
									'mailster'
								)}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									"Choose one of the predefined forms to get started. You can customize all aspects of the forms to match your current websites look and feel. Don't forget a name for your form to identify it later.",
									'mailster'
								)}
							</p>
						</>
					),
				},
				{
					image: (
						<img src="https://static.mailster.co/images/blockforms/welcome/185736767-744ba094-d1d4-4b65-858b-6f4740fc86d5.png" />
					),
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__('Add additional fields to your form.', 'mailster')}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									'All your custom fields can be added to your signup forms so you get the right information from your leads. You can make them required on a form-to-form basis.',
									'mailster'
								)}
							</p>
							<p className="edit-post-welcome-guide__text">
								{__(
									'You can also add most of the core blocks inside your form to get even more personal.',
									'mailster'
								)}
							</p>
						</>
					),
				},
				{
					image: (
						<img src="https://static.mailster.co/images/blockforms/welcome/185736854-037dcbda-56aa-4796-ab9e-7ad685158bcd.png" />
					),
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__('Customize the look and feel of your form.', 'mailster')}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									'Mailster Block forms provide a variation of settings to make the form custom to your site. Define colors and background images and apply custom CSS which only gets applied to the current form explicitly.',
									'mailster'
								)}
							</p>
							<p className="edit-post-welcome-guide__text">
								{__(
									'Make GDPR compliant forms and get a custom double-opt-in texts from each form. Decide if users can signup to certain lists and redirect them to a custom page after form submission.',
									'mailster'
								)}
							</p>
						</>
					),
				},
				{
					image: (
						<img src="https://static.mailster.co/images/blockforms/welcome/185736994-7cff4335-061a-4305-a762-938d8855ab25.png" />
					),
					content: (
						<>
							<h1 className="edit-post-welcome-guide__heading">
								{__(
									'Decide where and when Mailster should display your form',
									'mailster'
								)}
							</h1>
							<p className="edit-post-welcome-guide__text">
								{__(
									'Mailster can automatically place your form as popup or inside your content on defined pages. Define further conditions like time frames or trigger events to get the form in front of your visitors at the right time.',
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
