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
	RadioControl,
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
	FlexItem,
	FlexBlock,
	BaseControl,
	SelectControl,
	useCopyToClipboard,
	__experimentalNumberControl as NumberControl,
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
	const { entity, meta, setMeta, type, title, options, setOptions } = props;

	const [selectedTokens, setSelectedTokens] = useState([]);
	const [suggestions, setSuggestions] = useState([]);

	const sug = mapResult(suggestions);

	const [posts, setTokensState] = useState(options[entity]);

	useEffect(() => {
		posts &&
			posts.length &&
			apiFetch({
				path:
					getEndPointByEntity(entity) + '?include=' + posts.join(','),
			}).then(
				(result) => {
					setSelectedTokens(mapResult(result));
				},
				(error) => {}
			);
	}, []);

	function getEndPointByEntity(entity) {
		switch (entity) {
			case 'category':
				entity = 'categories';
				break;
			case 'post_tag':
				entity = 'tags';
				break;
		}
		return 'wp/v2/' + entity;
	}

	function mapResult(result) {
		switch (entity) {
			case 'category':
			case 'post_tag':
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
			path: getEndPointByEntity(entity) + '?search=' + token,
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
		var newPlacement = { ...options };

		newPlacement[entity] = newTokens;
		if (newTokens.length) {
			newPlacement['all'] = false;
		}
		setOptions(newPlacement);
	}

	return (
		<BaseControl id={'form-token-field-' + entity} label={title}>
			<FormTokenField
				id={'form-token-field-' + entity}
				value={selectedTokens}
				saveTransform={(token) => {
					return token;
				}}
				suggestions={sug}
				onInputChange={(tokens) => searchTokensDebounce(tokens)}
				onChange={(tokens) => setTokens(tokens)}
				__experimentalValidateInput={(tokens) => validateInput(tokens)}
			/>
		</BaseControl>
	);
};

export default function PlacementOption(props) {
	const { meta, setMeta, type, image, title } = props;
	const { placements } = meta;

	const [isOpen, setOpen] = useState(false);
	const [isEnabled, setEnabled] = useState(false);

	const options = meta['placement_' + type];

	function setOptions(options) {
		var newOptions = { ...meta['placement_' + type] };
		newOptions = { ...newOptions, ...options };
		setMeta({ ['placement_' + type]: newOptions });
	}

	const openModal = () => setOpen(true);
	const closeModal = () => setOpen(false);

	const className = ['placement-option'];

	placements.includes(type) && className.push('enabled');

	function setPlacements(placement, add) {
		var newPlacements = [...placements];
		if (add) {
			newPlacements.push(placement);
		} else {
			newPlacements = newPlacements.filter((el) => {
				return el != placement;
			});
		}

		setMeta({ placements: newPlacements });
	}

	const currentPostId = select('core/editor').getCurrentPostId();

	return (
		<>
			<Card size="small" className={className.join(' ')}>
				<CardHeader>
					<Flex align="center">
						{'other' != type && (
							<CheckboxControl
								value={type}
								checked={placements.includes(type)}
								onChange={(val) => {
									setPlacements(type, val);
								}}
							/>
						)}
						<Button
							variant="link"
							onClick={openModal}
							icon={<Icon icon={settings} />}
							isSmall={true}
						/>
					</Flex>
				</CardHeader>
				<CardMedia onClick={openModal}>{image}</CardMedia>
				<CardFooter>{title}</CardFooter>
			</Card>
			{isOpen && (
				<Modal
					title={'Display this form on your pages ' + type}
					onRequestClose={closeModal}
				>
					{'other' == type ? (
						<div>
							<h4>PHP</h4>
							<p>
								<code id={'form-php-' + currentPostId}>
									{'<?php echo mailster_form( ' +
										currentPostId +
										'); ?>'}
								</code>
							</p>
							<p>
								<code id="form-php-2">
									{'echo mailster_form( ' +
										currentPostId +
										');'}
								</code>
							</p>
							<p>
								<code id="form-php-3">
									{'<?php $form_html = mailster_form( ' +
										currentPostId +
										'); ?>'}
								</code>
							</p>
						</div>
					) : (
						<>
							{!placements.includes(type) && (
								<>
									<p>
										{__(
											'Please enable this option to define further settings.',
											'mailster'
										)}
									</p>
									<CheckboxControl
										label={__('Enabled', 'mailster')}
										value={type}
										checked={placements.includes(type)}
										onChange={(val) => {
											setPlacements(type, val);
										}}
									/>
								</>
							)}
							{placements.includes(type) && (
								<>
									<CheckboxControl
										label={__(
											'Display on all pages',
											'mailster'
										)}
										checked={options.all}
										onChange={(val) => {
											setOptions({ all: val });
										}}
									/>
									{!options.all && (
										<>
											<PostTokenField
												{...props}
												entity="posts"
												options={options}
												setOptions={setOptions}
												title={__(
													'Display on following posts/pages.',
													'mailster'
												)}
											/>
											<PostTokenField
												{...props}
												entity="category"
												options={options}
												setOptions={setOptions}
												title={__(
													'Display on posts/pages with these categories.',
													'mailster'
												)}
											/>
											<PostTokenField
												{...props}
												entity="post_tag"
												options={options}
												setOptions={setOptions}
												title={__(
													'Display on posts/pages with these tags.',
													'mailster'
												)}
											/>
										</>
									)}
									{'content' == type && (
										<BaseControl
											id={'extra-options-' + type}
											label={__(
												'Display options',
												'mailster'
											)}
										>
											<div id={'extra-options-' + type}>
												<RadioControl
													selected={options.pos}
													options={[
														{
															label: 'Start of content',
															value: '0',
														},
														{
															label: 'End of content',
															value: '-1',
														},
													]}
													onChange={(val) =>
														setOptions({ pos: val })
													}
												/>

												<Flex align="start">
													<FlexItem>
														{__(
															'Display form after:',
															'mailster'
														)}
													</FlexItem>
													<FlexItem>
														<NumberControl
															onChange={(val) =>
																setOptions({
																	pos: val,
																})
															}
															isDragEnabled
															isShiftStepEnabled
															shiftStep={10}
															step={1}
															value={options.pos}
														/>
													</FlexItem>
													<FlexBlock>
														<SelectControl
															value={options.tag}
															onChange={(val) =>
																setOptions({
																	tag: val,
																})
															}
															options={[
																{
																	value: 'p',
																	label: 'Paragraph',
																},
																{
																	value: 'h2',
																	label: 'Heading 2',
																},
																{
																	value: 'h3',
																	label: 'Heading 3',
																},
															]}
														/>
													</FlexBlock>
												</Flex>
											</div>
										</BaseControl>
									)}
								</>
							)}
						</>
					)}
					<Flex style={{ marginTop: '2em' }}>
						<Button variant="secondary" onClick={closeModal}>
							Close
						</Button>
					</Flex>
				</Modal>
			)}
		</>
	);
}
