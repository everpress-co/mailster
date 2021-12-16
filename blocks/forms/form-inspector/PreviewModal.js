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

import {
	Fragment,
	Component,
	useState,
	useEffect,
	useRef,
} from '@wordpress/element';
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

	const [type, setType] = useState(initialType);
	const [url, setUrl] = useState('');
	const [displayUrl, setDisplayUrl] = useState('');
	const [urlLoggedIn, setUrlLoggedIn] = useState(false);
	const [device, setDevice] = useState('desktop');
	const [siteUrl] = useEntityProp('root', 'site', 'url');

	const [currentPage, setCurrentPage] = useState(false);

	const options = meta['placement_' + type] || {};
	const isOther = type == 'other';

	const myIframe = useRef(null);

	const typeActive = meta.placements.includes(type) || isOther;

	const categories = options.category || undefined;
	const tags = options.tags || undefined;
	const all = options.all;

	const postQuery = {
		per_page: 1,
		categories: !all ? categories : undefined,
		tags: !all ? tags : undefined,
	};

	const formId = useSelect(
		(select) => select('core/editor').getCurrentPostId(),
		[]
	);

	const postContent = useSelect((select) => {
		return select('core/editor').getEditedPostContent();
	});

	const posts = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'post', postQuery);
	});

	const [isLoading, setIsLoading] = useState(
		useSelect((select) => {
			return select('core/data').isResolving('core', 'getEntityRecords', [
				'postType',
				'post',
				postQuery,
			]);
		})
	);

	const setUrlDebounce = useDebounce(setUrl, 1000);
	const setPreviewOptionsDebounce = useDebounce(setPreviewOptions, 1000);

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

		const id = options.posts?.length ? options.posts[0] : post.id;

		const newUrl = isOther
			? siteUrl + '/wp-content/plugins/mailster/form.php?id=' + formId
			: post.link;

		setDisplayUrl(newUrl);

		setUrlDebounce(mapUrl(newUrl, id));
	}, [posts, options, urlLoggedIn]);

	useEffect(() => {
		if (!options || Object.keys(options).length === 0) {
			return;
		}

		setIsLoading(true);
		setPreviewOptions();

		console.warn('options ', options);
	}, [options]);

	useEffect(() => {
		window.addEventListener('message', function (event) {
			var data = JSON.parse(event.data);
			console.warn(data);
			setIsLoading(false);
			const form = myIframe.current.contentWindow.document.querySelector(
				'.wp-block-mailster-form-outside-wrapper-' + formId
			);

			if (form && 'content' == type) {
				form.scrollIntoView({
					behavior: 'smooth',
					block: 'center',
					inline: 'nearest',
				});
			}
		});
	}, []);

	function setOptions(options) {
		var newOptions = { ...meta['placement_' + type] };
		newOptions = { ...newOptions, ...options };
		setMeta({ ['placement_' + type]: newOptions });
	}

	console.warn('isLoading', isLoading);

	function mapUrl(url, postId) {
		const newUrl = new URL(url);

		const { display, pos, tag } = meta['placement_' + type] || {};

		const obj = {
			type: type,
			user: urlLoggedIn,
			options: {
				all: true, //all => display always as its a preview
				display: display,
				pos: pos,
				tag: tag,
			},
			form_id: formId,
		};

		postId && newUrl.searchParams.set('p', postId);
		newUrl.searchParams.set('mailster-block-preview', JSON.stringify(obj));

		return newUrl.toString();
	}

	function setPreviewOptions() {
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
			post_content: postContent,
		};
		if (myIframe.current) {
			setIsLoading(true);
			myIframe.current.contentWindow.postMessage(
				JSON.stringify(obj),
				siteUrl
			);
		}
	}

	function reload() {
		const currentUrl = url;
		setUrl('');
		setUrlDebounce(currentUrl);
	}

	return (
		<div className="preview-pane-grid-wrap">
			<Flex columns={2}>
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
							<ToolbarButton
								icon={desktop}
								label="Edit"
								isDisabled={device == 'desktop' || !url}
								isPressed={device == 'desktop'}
								onClick={() => setDevice('desktop')}
							/>
							<ToolbarButton
								icon={tablet}
								label="Edit"
								isDisabled={device == 'tablet' || !url}
								isPressed={device == 'tablet'}
								onClick={() => setDevice('tablet')}
							/>
							<ToolbarButton
								icon={mobile}
								label="More"
								isDisabled={device == 'mobile' || !url}
								isPressed={device == 'mobile'}
								onClick={() => setDevice('mobile')}
							/>

							<div className="preview-pane-url">
								<TextControl
									className="widefat"
									value={displayUrl}
									readOnly
								/>
							</div>
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
								ref={myIframe}
								src={url}
								onLoad={setPreviewOptionsDebounce}
								_sandbox="allow-scripts allow-same-origin"
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
										setType(placement.type);
									} else {
										setType(false);
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
			</Flex>
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
