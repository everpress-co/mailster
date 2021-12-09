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

import { more } from '@wordpress/icons';
import { useEntityProp } from '@wordpress/core-data';

import PlacementSettings from './PlacementSettings';
import PlacementSettingsContent from './PlacementSettingsContent';
import PlacementSettingsTriggers from './PlacementSettingsTriggers';

const ModalContent = (props) => {
	const { setOpen, placements, meta } = props;

	const [section, setSection] = useState('content');
	const [url, setUrl] = useState('');
	const [urlLoggedIn, setUrlLoggedIn] = useState(false);

	const [currentPage, setCurrentPage] = useState(false);

	const placement_options = section ? meta['placement_' + section] || {} : [];

	const categories = placement_options.category || undefined;
	const tags = placement_options.tags || undefined;
	const all = placement_options.all;

	const [siteUrl] = useEntityProp('root', 'site', 'url');

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

	// Set up the isLoading.
	const isLoading = useSelect((select) => {
		return select('core/data').isResolving('core', 'getEntityRecords', [
			'postType',
			'post',
			postQuery,
		]);
	});

	// useEffect(() => {
	// 	console.warn('new cats', categories);
	// 	invalidateResolver();
	// }, [categories]);

	const { invalidateResolution } = useDispatch('core/data');

	// Create a custom function for the button so we can trigger this on click.
	const invalidateResolver = () => {
		invalidateResolution('core', 'getEntityRecords', postQuery);
	};

	useEffect(() => {
		if (!posts) {
			return;
		}
		if (!posts.length) {
			return;
		}
		if (!section) {
			return;
		}
		if (!siteUrl) {
			return;
		}

		const post = posts[0];

		const id = placement_options.posts?.length
			? placement_options.posts[0]
			: post.id;

		//const tempId = getPostIdFromOptions(placement_options);

		setUrl(mapUrl(siteUrl, id));
	}, [posts, placement_options, urlLoggedIn]);

	function mapUrl(url, postId) {
		const myurl = new URL(url);
		//remove them from the options to prevent reloads
		const { post_types, posts, category, post_tag, ...options } =
			placement_options;

		const obj = {
			section: section,
			user: urlLoggedIn,
			options: options,
			form_id: formId,
		};

		postId && myurl.searchParams.set('p', postId);
		myurl.searchParams.set('mailster-block-preview', JSON.stringify(obj));

		return myurl.toString();
	}

	function displayIframe(event) {
		event.target.contentWindow.document
			.querySelector('.wp-block-mailster-form-outside-wrapper-147')
			.scrollIntoView({
				//behavior: 'smooth',
				block: 'center',
				inline: 'nearest',
			});
	}

	function getPostIdFromOptions(options, fallback) {
		if (options.posts?.length) {
			return options.posts[0];
		}
		if (options.category?.length) {
			//setCategories(options.category);
		}
		if (options.tags?.length) {
			//setTags(options.tags);
		}
		return fallback;
	}

	console.warn('Xxx', meta);

	return (
		<>
			<Grid columns={2}>
				<div className="preview-pane">
					{!section && (
						<Flex className="preview-pane-info" justify="center">
							<FlexItem>
								<h3>
									{__(
										'Please choose a Placement option on the right',
										'mailster'
									)}
								</h3>
							</FlexItem>
						</Flex>
					)}
					{section && isLoading && (
						<Flex className="preview-pane-info" justify="center">
							<FlexItem>
								<h3>
									{__(
										'Please wait while the preview is loading.',
										'mailster'
									)}
								</h3>
								<Spinner />
							</FlexItem>
						</Flex>
					)}
					{section && !isLoading && (
						<iframe
							src={url}
							style={{
								width: '100%',
								height: '100%',
							}}
							onLoad={displayIframe}
							id="preview-pane-iframe"
							sandbox="allow-scripts allow-same-origin"
							onFocus={() => console.log('iframe is focused')}
						/>
					)}
				</div>
				<div className="preview-sidebar">
					{placements.map((placement) => {
						return (
							<PanelBody
								key={placement.type}
								name={'placement-' + placement.type}
								title={placement.title}
								opened={section == placement.type}
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
								label="User logged in"
								checked={urlLoggedIn}
								onChange={() => setUrlLoggedIn(!urlLoggedIn)}
								help="Users decide which list they subscribe to"
							/>
						</PanelRow>
					</BaseControl>
				</div>
			</Grid>
		</>
	);
};

export default function PreviewModal(props) {
	const { meta, setMeta, setOpen, isOpen } = props;

	const modalStyle = {
		width: '96vw',
		height: '96vh',
		maxHeight: '96vh',
	};

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
