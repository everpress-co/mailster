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
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
	ColorPaletteControl,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	CardMedia,
	Card,
	CardHeader,
	CardBody,
	CardDivider,
	CardFooter,
	Button,
	Modal,
	Icon,
	RangeControl,
	FormTokenField,
	Flex,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';

import { settings } from '@wordpress/icons';
import apiFetch from '@wordpress/api-fetch';
import { useDebounce } from '@wordpress/compose';
import { useEntityProp } from '@wordpress/core-data';
import { select, dispatch, subscribe } from '@wordpress/data';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

const PostTokenField = (props) => {
	const { type } = props;

	const [selectedTokens, setSelectedTokens] = useState([]);
	const [suggestions, setSuggestions] = useState([]);

	const sug = mapResult(suggestions);
	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	const [posts, setTokensState] = useState(meta[type]);

	useEffect(() => {
		posts.length &&
			apiFetch({
				path: 'wp/v2/' + type + '?include=' + posts.join(','),
			}).then(
				(result) => {
					setSelectedTokens(mapResult(result));
				},
				(error) => {}
			);
	}, []);

	function mapResult(result) {
		switch (type) {
			case 'categories':
			case 'tags':
				return result.map((s, i) => {
					return '(#' + s.id + ') ' + s.name;
				});
			case 'posts':
				return result.map((s, i) => {
					return '(#' + s.id + ') ' + s.title.rendered;
				});
		}
	}

	function searchTokens(token) {
		apiFetch({
			path: 'wp/v2/' + type + '?search=' + token,
		}).then(
			(result) => {
				setSuggestions(result);
			},
			(error) => {}
		);
	}

	function validateInput(token) {
		return sug.includes(token);
	}

	const searchTokensDebounce = useDebounce(searchTokens, 500);

	function setTokens(tokens) {
		var newTokens = tokens.map((post) => {
			return parseInt(post.match(/^\(#([0-9]+)\)/)[1], 10);
		});
		setSelectedTokens(tokens);

		switch (type) {
			case 'tags':
				setMeta({ tags: newTokens });
				break;
			case 'categories':
				setMeta({ categories: newTokens });
				break;
			case 'posts':
				setMeta({ posts: newTokens });
				break;
		}
	}

	return (
		<FormTokenField
			value={selectedTokens}
			saveTransform={(token) => {
				return token;
			}}
			suggestions={sug}
			onInputChange={(tokens) => searchTokensDebounce(tokens)}
			onChange={(tokens) => setTokens(tokens)}
			__experimentalValidateInput={(tokens) => validateInput(tokens)}
		/>
	);
};

export default function PlacementOption(props) {
	const { attributes, setAttributes, title, type, image, isSelected } = props;
	const [isOpen, setOpen] = useState(false);
	const [isChecked, setChecked] = useState(false);

	const openModal = () => setOpen(true);
	const closeModal = () => setOpen(false);

	const MyIcon = () => <Icon icon={settings} />;
	const post_types = select('core').getPostTypes() || [];

	return (
		<Card size="small" elevation={5}>
			<CardHeader>
				<Flex>
					<CheckboxControl
						label="Enabled"
						checked={isChecked}
						onChange={setChecked}
					/>
					<Button
						variant="secondary"
						onClick={openModal}
						icon={MyIcon}
					/>
				</Flex>
			</CardHeader>
			<CardMedia>
				<img src={image} alt="React Logo" />
			</CardMedia>
			<CardFooter>{title}</CardFooter>
			{isOpen && (
				<Modal
					title="Display this form on your pages"
					onRequestClose={closeModal}
				>
					<p>
						This form placement allows you to add this form at the
						end of all the pages or posts, below the content.
					</p>
					<CheckboxControl
						label="Display on all pages"
						checked={isChecked}
						onChange={setChecked}
					/>
					<h3>Post types</h3>
					<Flex justify="flex-start">
						{post_types.map((post_type) => {
							return (
								post_type.viewable &&
								post_type.slug != 'attachment' && (
									<CheckboxControl
										key={post_type.slug}
										label={post_type.name}
										value={post_type.slug}
										checked={isChecked}
										onChange={setChecked}
									/>
								)
							);
						})}
					</Flex>
					<PostTokenField {...props} type="posts" />
					<PostTokenField {...props} type="categories" />
					<PostTokenField {...props} type="tags" />
					<p>
						lorem Lorem ipsum dolor sit amet, consectetur adipiscing
						elit. Praesent fringilla mollis tortor a scelerisque.
					</p>
					<Button variant="secondary" onClick={closeModal}>
						Close
					</Button>
				</Modal>
			)}
		</Card>
	);
}
