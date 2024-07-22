/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	Panel,
	PanelBody,
	PanelRow,
	BaseControl,
	MenuGroup,
	MenuItem,
	Tip,
	ExternalLink,
	ToggleControl,
} from '@wordpress/components';

import { useSelect, dispatch } from '@wordpress/data';

import { external } from '@wordpress/icons';

const PanelDescription = styled.div`
	grid-column: span 2;
`;

const SingleColumnItem = styled.div`
	grid-column: span 1;
`;
/**
 * Internal dependencies
 */
import { TABS } from './constants';
import { HelpBeacon, searchBlock } from '../util';

const EditorInspector = () => {
	const postID = useSelect((select) => {
		return select('core/editor').getCurrentPostId();
	});
	return (
		<InspectorControls>
			<Panel>
				<PanelBody initialOpen={true}>
					<PanelRow>
						<Tip>
							{__(
								'You can edit the forms on the Newsletter Homepage.',
								'mailster'
							)}
						</Tip>
					</PanelRow>
					<PanelRow>
						<Button
							variant="primary"
							icon={external}
							target="mailster-newsletter-homepage"
							href={
								'post.php?post=' + postID + '&action=edit#mailster-submission'
							}
						>
							{__('Newsletter Homepage', 'mailster')}
						</Button>
					</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
};

export default function HomepageInspectorControls(props) {
	const { current, onSelect } = props;

	const homepage = searchBlock('mailster/homepage');

	if (!homepage) return <EditorInspector />;

	const permalink = useSelect((select) => {
		return select('core/editor').getPermalink();
	});
	const attributes = useSelect((select) => {
		return select('core/block-editor').getBlockAttributes(homepage.clientId);
	});

	const setAttributes = (object) => {
		const merged = { ...attributes, ...object };
		const homepage = searchBlock('mailster/homepage');

		return dispatch('core/block-editor').updateBlockAttributes(
			homepage.clientId,
			merged
		);
	};

	const { showAll } = attributes;

	// get current status
	const status = useSelect((select) => {
		return select('core/editor').getEditedPostAttribute('status');
	}, []);

	const disabled = status == 'auto-draft';

	const currentTab = TABS.find((tab) => tab.id === current);

	const ContextButtons = ({ onClose = () => {} }) => {
		return TABS.map((a, i) => {
			const link = getPermalink(a.id);
			const defined = attributes[a.id] || a.id == 'subscribe';
			return (
				<MenuGroup key={i} isSelected={a.id === current}>
					<MenuItem
						info={defined ? link : __('Not defined yet!', 'mailster')}
						isDestructive={!defined}
						isPressed={a.id === current}
						onClick={() => {
							onSelect(a.id, i);
							onClose();
						}}
					>
						{a.name}
						{!disabled && <ExternalLink href={link} />}
					</MenuItem>
				</MenuGroup>
			);
		});
	};

	const getPermalink = (id) => {
		return id == 'submission'
			? permalink
			: sprintf('%s%s', permalink, mailster_homepage_slugs[id] || id);
	};

	const getHelp = () => {
		if (!currentTab) return <></>;

		return <p className="section-info">{currentTab.help}</p>;
	};
	const getTitle = () => {
		if (!currentTab) return <></>;

		return <h3>{sprintf(__('[Section] %s', 'mailster'), currentTab.name)}</h3>;
	};
	const getLink = () => {
		if (!currentTab) return <></>;

		const link = getPermalink(currentTab.id);

		return (
			<Button
				href={link}
				icon={external}
				disabled={disabled}
				variant="secondary"
				target="mailster-newsletter-homepage-preview"
			>
				{sprintf(__('Preview %s', 'mailster'), currentTab.name)}
			</Button>
		);
	};

	return (
		<InspectorControls>
			<Panel>
				<PanelBody initialOpen={true}>
					<HelpBeacon id="6453abdab9f4b70821b98a1b" align="right" />
					<PanelDescription>
						{getTitle()}
						{getHelp()}
						{getLink()}
					</PanelDescription>
				</PanelBody>
			</Panel>
			<Panel>
				<PanelBody initialOpen={true}>
					<PanelRow>
						<BaseControl label={__('Newsletter Homepage Sections', 'mailster')}>
							<div className="components-dropdown-menu__menu context-buttons">
								<ContextButtons />
							</div>
						</BaseControl>
					</PanelRow>
				</PanelBody>
				<PanelBody initialOpen={true}>
					<PanelRow>
						<ToggleControl
							label={__('Show all sections', 'mailster')}
							help={__(
								'Show all sections at once. This is only for preview purposes.',
								'mailster'
							)}
							checked={showAll}
							onChange={() => {
								setAttributes({ showAll: !showAll });
							}}
						/>
					</PanelRow>
					<PanelRow>
						<Tip>
							{__(
								'You have to define a form for each section. You can use the same form as well.',
								'mailster'
							)}
						</Tip>
					</PanelRow>
				</PanelBody>
				{(current == 'profile' || current == 'unsubscribe') && (
					<PanelBody initialOpen={true}>
						<PanelRow>
							<ExternalLink href="edit.php?post_type=newsletter&page=mailster_settings#texts">
								{__(
									'Change the text of the button on the Texts tab in the settings.',
									'mailster'
								)}
							</ExternalLink>
						</PanelRow>
					</PanelBody>
				)}
			</Panel>
		</InspectorControls>
	);
}
