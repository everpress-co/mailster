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
	BlockPatternList,
	BlockPreview,
} from '@wordpress/block-editor';
import {
	BaseControl,
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
	CardMedia,
	Card,
	CardHeader,
	CardBody,
	CardDivider,
	CardFooter,
	Spinner,
	Flex,
	FlexItem,
	Toolbar,
	ToolbarGroup,
	ToolbarItem,
	ToolbarButton,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import {
	useSelect,
	select,
	useDispatch,
	dispatch,
	subscribe,
} from '@wordpress/data';

import { useFocusableIframe } from '@wordpress/compose';

import { Modal, Button, Tooltip } from '@wordpress/components';
import { useDebounce } from '@wordpress/compose';

import { check, desktop, tablet, mobile, update } from '@wordpress/icons';
import { useEntityProp } from '@wordpress/core-data';

import PlacementSettings from './PlacementSettings';
import PlacementSettingsContent from './PlacementSettingsContent';
import PlacementSettingsTriggers from './PlacementSettingsTriggers';

const ModalContent = (props) => {
	const { setOpen, placements, meta, initialType } = props;

	const [type, setSection] = useState(initialType);
	const [url, setUrl] = useState('');
	const [displayUrl, setDisplayUrl] = useState('');
	const [urlLoggedIn, setUrlLoggedIn] = useState(false);
	const [device, setDevice] = useState('desktop');
	const [siteUrl] = useEntityProp('root', 'site', 'url');

	const [currentPage, setCurrentPage] = useState(false);

	const placement_options = type ? meta['placement_' + type] || {} : [];

	const typeActive = meta.placements.includes(type);

	const categories = placement_options.category || undefined;
	const tags = placement_options.tags || undefined;
	const all = placement_options.all;

	const formId = useSelect(
		(select) => select('core/editor').getCurrentPostId(),
		[]
	);

	const postQuery = {
		per_page: 1,
		categories: !all ? categories : undefined,
		tags: !all ? tags : undefined,
	};

	const posts = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'post', postQuery);
	});

	const isLoading = useSelect((select) => {
		return select('core/data').isResolving('core', 'getEntityRecords', [
			'postType',
			'post',
			postQuery,
		]);
	});
	const setUrlDebounce = useDebounce(setUrl, 1000);

	useEffect(() => {
		if (!posts) {
			return;
		}
		if (!posts.length) {
			return;
		}
		if (!type) {
			return;
		}
		if (!siteUrl) {
			return;
		}

		const post = posts[0];

		const id = placement_options.posts?.length
			? placement_options.posts[0]
			: post.id;

		setDisplayUrl(post.link);

		setUrlDebounce(mapUrl(siteUrl, id));
	}, [posts, placement_options, urlLoggedIn]);

	function mapUrl(url, postId) {
		const myurl = new URL(url);
		//remove them from the options to prevent reloads
		const { post_types, posts, category, post_tag, ...options } =
			placement_options;

		const obj = {
			type: type,
			user: urlLoggedIn,
			options: {
				...options,
				all: true,
				trigger_delay: 2,
				trigger_inactive: 4,
			}, //all => display always as its a preview
			form_id: formId,
			post_content: select('core/editor').getEditedPostContent(),
		};

		postId && myurl.searchParams.set('p', postId);
		myurl.searchParams.set('mailster-block-preview', JSON.stringify(obj));

		return myurl.toString();
	}

	function displayIframe(event) {
		const form = event.target.contentWindow.document.querySelector(
			'.wp-block-mailster-form-outside-wrapper-' + formId
		);

		if (form && 'content' == type) {
			form.scrollIntoView({
				//behavior: 'smooth',
				block: 'center',
				inline: 'nearest',
			});
		}
	}

	function reload() {
		const currentUrl = url;
		setUrl('');
		setTimeout(() => {
			setUrl(currentUrl);
		}, 1);
	}

	return (
		<div className="preview-pane-grid-wrap">
			<Grid columns={2}>
				<div className="preview-pane">
					<div
						className="interface-interface-skeleton__header"
						role="region"
						tabIndex="-1"
					>
						<Toolbar
							label="Options"
							className="preview-pane-toolbar widefat"
						>
							<ToolbarGroup>
								<ToolbarButton
									icon={desktop}
									label="Edit"
									isDisabled={device == 'desktop' || !url}
									onClick={() => setDevice('desktop')}
								/>
								<ToolbarButton
									icon={tablet}
									label="Edit"
									isDisabled={device == 'tablet' || !url}
									onClick={() => setDevice('tablet')}
								/>
								<ToolbarButton
									icon={mobile}
									label="More"
									isDisabled={device == 'mobile' || !url}
									onClick={() => setDevice('mobile')}
								/>
							</ToolbarGroup>
							<ToolbarGroup className="preview-pane-url">
								<TextControl
									className="widefat"
									value={displayUrl}
									readOnly
								/>
							</ToolbarGroup>
							<ToolbarButton
								icon={update}
								label="reload"
								isDisabled={!url}
								onClick={reload}
								isBusy={isLoading}
							/>
						</Toolbar>
					</div>
					{typeActive ? (
						<div
							className={
								'preview-pane-iframe preview-pane-iframe-' +
								device
							}
						>
							<iframe
								src={url}
								onLoad={displayIframe}
								id="preview-pane-iframe"
								sandbox="allow-scripts allow-same-origin"
								hidden={!url}
							/>
						</div>
					) : (
						<Flex className="preview-pane-info" justify="center">
							<FlexItem>
								<h3>
									{__(
										'Please enable a Placement option on the right',
										'mailster'
									)}
								</h3>
							</FlexItem>
						</Flex>
					)}
				</div>
				<div className="preview-sidebar">
					{placements.map((placement) => {
						return (
							<PanelBody
								key={placement.type}
								name={'placement-' + placement.type}
								title={placement.title}
								icon={
									meta.placements.includes(placement.type) &&
									check
								}
								opened={type == placement.type}
								onToggle={(v) => {
									if (v) {
										setSection(placement.type);
									} else {
										setSection(false);
									}
								}}
							>
								<PlacementSettings
									{...props}
									title={placement.title}
									type={placement.type}
									image={placement.image}
								/>
							</PanelBody>
						);
					})}
					<BaseControl className="widefat">
						<PanelRow>
							<CheckboxControl
								label={__('Stay logged in', 'mailster')}
								checked={urlLoggedIn}
								onChange={() => setUrlLoggedIn(!urlLoggedIn)}
								help={__(
									'Show the preview as currently logged in user.',
									'mailster'
								)}
							/>
						</PanelRow>
					</BaseControl>
				</div>
			</Grid>
		</div>
	);
};

export default function PreviewModal(props) {
	const { meta, setMeta, setOpen, isOpen } = props;

	const modalStyle = {};

	return (
		<>
			{isOpen && (
				<Modal
					title={__('Define your placement options', 'mailster')}
					className="preview-modal"
					onRequestClose={() => setOpen(false)}
					style={modalStyle}
					shouldCloseOnClickOutside={false}
					isFullScreen={true}
				>
					<ModalContent {...props} />
				</Modal>
			)}
		</>
	);
}
