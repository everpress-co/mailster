/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { isRTL, __ } from '@wordpress/i18n';

import Select from 'react-select';

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

import { undo, chevronRight, chevronLeft } from '@wordpress/icons';
import apiFetch from '@wordpress/api-fetch';
import { useDebounce } from '@wordpress/compose';
import { useEntityProp } from '@wordpress/core-data';
import { select, dispatch, subscribe } from '@wordpress/data';
import {
	__experimentalNavigatorProvider as NavigatorProvider,
	__experimentalNavigatorScreen as NavigatorScreen,
	__experimentalUseNavigator as useNavigator,
} from '@wordpress/components';

import NavigatorButton from './NavigatorButton';
import PlacementSettingsContent from './PlacementSettingsContent';
import PlacementSettingsTriggers from './PlacementSettingsTriggers';

import {
	__experimentalItemGroup as ItemGroup,
	__experimentalItem as Item,
} from '@wordpress/components';
/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

const currentPostId = select('core/editor').getCurrentPostId();

export default function PlacementSettings(props) {
	const { meta, setMeta, type, image, title } = props;
	const { placements } = meta;

	const options = meta['placement_' + type] || {};

	function setOptions(options) {
		var newOptions = { ...meta['placement_' + type] };
		newOptions = { ...newOptions, ...options };
		setMeta({ ['placement_' + type]: newOptions });
	}

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

	const triggers = options.triggers || [];

	function setTriggers(trigger, add) {
		var newTriggers = [...triggers];
		if (add) {
			newTriggers.push(trigger);
		} else {
			newTriggers = newTriggers.filter((el) => {
				return el != trigger;
			});
		}
		setOptions({ triggers: newTriggers });
	}

	const closeMethods = options.close || [];

	function setCloseMethods(method, add) {
		var newMethods = [...closeMethods];
		if (add) {
			newMethods.push(method);
		} else {
			newMethods = newMethods.filter((el) => {
				return el != method;
			});
		}
		setOptions({ close: newMethods });
	}

	const closeModal = () => {};

	return (
		<>
			{'other' == type ? (
				<ItemGroup isBordered={false} isSeparated size="small">
					<Item>PHP</Item>
					<Item>
						<code id={'form-php-' + currentPostId}>
							{'<?php echo mailster_form( ' +
								currentPostId +
								'); ?>'}
						</code>
					</Item>
					<Item>
						<code id="form-php-2">
							{'echo mailster_form( ' + currentPostId + ');'}
						</code>
					</Item>
					<Item>
						<code id="form-php-3">
							{'<?php $form_html = mailster_form( ' +
								currentPostId +
								'); ?>'}
						</code>
					</Item>
				</ItemGroup>
			) : (
				<>
					<ItemGroup isBordered={false} isSeparated size="small">
						<Item>
							<CheckboxControl
								label={__('Enabled', 'mailster')}
								value={type}
								checked={placements.includes(type)}
								onChange={(val) => {
									setPlacements(type, val);
								}}
							/>
						</Item>
					</ItemGroup>

					{placements.includes(type) && (
						<>
							<ItemGroup
								isBordered={false}
								isSeparated
								size="small"
							>
								<Item>
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
								</Item>
							</ItemGroup>
							{!options.all && (
								<ItemGroup
									isBordered={false}
									isSeparated
									size="small"
								>
									<PostTokenField
										{...props}
										entity="posts"
										options={options}
										setOptions={setOptions}
										title={__('Posts/Pages', 'mailster')}
										help={__(
											'Display on following posts/pages.',
											'mailster'
										)}
									/>
									<PostTokenField
										{...props}
										entity="category"
										options={options}
										setOptions={setOptions}
										title={__('Categories', 'mailster')}
										help={__(
											'Display on posts/pages with these categories.',
											'mailster'
										)}
									/>
									<PostTokenField
										{...props}
										entity="post_tag"
										options={options}
										setOptions={setOptions}
										title={__('Tags', 'mailster')}
										help={__(
											'Display on posts/pages with these tags.',
											'mailster'
										)}
									/>
								</ItemGroup>
							)}
							{'content' == type && (
								<PlacementSettingsContent
									{...props}
									setOptions={setOptions}
									options={options}
									setTriggers={setTriggers}
									triggers={triggers}
								/>
							)}
							{'content' != type && (
								<>
									<PlacementSettingsTriggers
										{...props}
										setOptions={setOptions}
										options={options}
										setTriggers={setTriggers}
										triggers={triggers}
									/>
									<ItemGroup isBordered={false} size="small">
										<SelectControl
											label={__('Animation', 'mailster')}
											value={options.animation}
											onChange={(val) => {
												setOptions({ animation: val });
											}}
										>
											<option value="fadein">
												{__('FadeIn', 'mailster')}
											</option>
											<option value="shake">
												{__('Shake', 'mailster')}
											</option>
											<option value="swing">
												{__('Swing', 'mailster')}
											</option>
											<option value="heartbeat">
												{__('Heart Beat', 'mailster')}
											</option>
											<option value="tada">
												{__('Tada', 'mailster')}
											</option>
											<option value="wobble">
												{__('Wobble', 'mailster')}
											</option>
										</SelectControl>
									</ItemGroup>
								</>
							)}
							<PanelRow>
								<RangeControl
									className="widefat"
									label={__('Width', 'mailster')}
									help={__(
										'Set the with of your form in %',
										'mailster'
									)}
									value={options.width}
									allowReset={true}
									onChange={(val) =>
										setOptions({
											width: val,
										})
									}
									min={10}
									max={100}
								/>
							</PanelRow>
						</>
					)}
				</>
			)}
		</>
	);
}

const PostTokenField = (props) => {
	const { entity, meta, setMeta, type, title, help, options, setOptions } =
		props;

	const [selectedTokens, setSelectedTokens] = useState([]);
	const [suggestions, setSuggestions] = useState([]);
	const [loading, setLoading] = useState(false);

	const [posts, setTokensState] = useState(options[entity]);

	useEffect(() => {
		posts &&
			posts.length &&
			apiFetch({
				path:
					getEndPointByEntity(entity) + '?include=' + posts.join(','),
			}).then(
				(result) => {
					setLoading(false);
					const r = mapResult(result);
					setSelectedTokens(r);
				},
				(error) => {}
			) &&
			setLoading(true);
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
					return { value: s.id, label: s.name };
				});
			case 'posts':
				return result.map((s, i) => {
					return { value: s.id, label: s.title.rendered };
				});
		}
	}

	function searchTokens(token) {
		token &&
			apiFetch({
				path: getEndPointByEntity(entity) + '?search=' + token,
			}).then(
				(result) => {
					setLoading(false);
					setSuggestions(mapResult(result));
				},
				(error) => {}
			) &&
			setLoading(true);
	}

	function validateInput(token) {
		return sug.includes(token);
	}

	const searchTokensDebounce = useDebounce(searchTokens, 500);

	function setTokens(tokens) {
		var newTokens = tokens.map((post) => {
			return parseInt(post.value, 10);
		});
		var newPlacement = { ...options };

		newPlacement[entity] = newTokens;
		if (newTokens.length) {
			newPlacement['all'] = false;
		}
		setSelectedTokens(tokens);
		setOptions(newPlacement);
	}

	return (
		<Item>
			<BaseControl id={'form-token-field-' + entity} label={help}>
				<Select
					options={suggestions}
					value={selectedTokens}
					placeholder={__('Select ' + title + '…', 'mailster')}
					onInputChange={(tokens) => searchTokensDebounce(tokens)}
					onChange={(tokens) => setTokens(tokens)}
					isMulti
					isLoading={loading}
				/>
			</BaseControl>
		</Item>
	);
};
