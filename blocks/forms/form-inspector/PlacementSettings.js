/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __, sprintf } from '@wordpress/i18n';

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
	Spinner,
	Notice,
	useCopyToClipboard,
	__experimentalNumberControl as NumberControl,
	__experimentalBoxControl as BoxControl,
	__experimentalFormGroup as FormGroup,
} from '@wordpress/components';
import { Fragment, Component, useState, useEffect } from '@wordpress/element';

import { undo, chevronRight, chevronLeft, helpFilled } from '@wordpress/icons';
import apiFetch from '@wordpress/api-fetch';
import { useDebounce } from '@wordpress/compose';
import { useEntityProp } from '@wordpress/core-data';
import { select, useSelect, dispatch, subscribe } from '@wordpress/data';
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

export default function PlacementSettings(props) {
	const {
		meta,
		setMeta,
		placement,
		setPlacements,
		useThemeStyle,
		setUseThemeStyle,
	} = props;
	const { type, title } = placement;

	const options = meta['placement_' + type] || {};

	function setOptions(options) {
		var newOptions = { ...meta['placement_' + type] };
		newOptions = { ...newOptions, ...options };
		setMeta({ ['placement_' + type]: newOptions });
	}

	const currentPostId = useSelect(
		(select) => select('core/editor').getCurrentPostId(),
		[]
	);

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

	const [isEnabled, setIsEnabled] = useState(meta.placements.includes(type));
	useEffect(() => {
		meta.placements && setIsEnabled(meta.placements.includes(type));
	}, [meta.placements]);

	const [isDisplayed, setIsDisplayed] = useState(false);
	useEffect(() => {
		setIsDisplayed(
			isEnabled &&
				(options.all.length ||
					options.posts.length ||
					options.taxonomies.length)
		);
	}, [isEnabled, options.all, options.posts, options.taxonomies]);

	return (
		<Panel>
			{'other' == type ? (
				<PanelRow>
					<ItemGroup
						className="widefat"
						isBordered={false}
						size="medium"
					>
						<Item>PHP</Item>
						<Item>
							<code id={'form-php-' + currentPostId}>
								{'<?php echo mailster_form( ' +
									currentPostId +
									' ); ?>'}
							</code>
						</Item>
						<Item>
							<code id="form-php-2">
								{'echo mailster_form( ' + currentPostId + ' );'}
							</code>
						</Item>
						<Item>
							<code id="form-php-3">
								{'<?php $form_html = mailster_form( ' +
									currentPostId +
									' ); ?>'}
							</code>
						</Item>
						<Item>
							<CheckboxControl
								label={__('useThemeStyle', 'mailster')}
								checked={useThemeStyle}
								onChange={(val) => {
									setUseThemeStyle(!useThemeStyle);
								}}
							/>
						</Item>
					</ItemGroup>
				</PanelRow>
			) : (
				<>
					<PanelBody opened={true}>
						<PanelRow>
							<CheckboxControl
								label={sprintf(
									__('Enabled this form for %s.', 'mailster'),
									title
								)}
								value={type}
								checked={isEnabled}
								onChange={(val) => {
									setPlacements(type, val);
								}}
							/>
						</PanelRow>
					</PanelBody>

					{isEnabled && (
						<>
							<PanelBody
								title="Display Options"
								initialOpen={false}
							>
								<PostTypeFields
									options={options}
									setOptions={setOptions}
								/>
								{'content' == type && (
									<PlacementSettingsContent
										{...props}
										setOptions={setOptions}
										options={options}
										setTriggers={setTriggers}
										triggers={triggers}
									/>
								)}
							</PanelBody>
							{'content' != type && (
								<>
									<PlacementSettingsTriggers
										{...props}
										setOptions={setOptions}
										options={options}
										setTriggers={setTriggers}
										triggers={triggers}
									/>
									<PanelBody
										title="Extra"
										initialOpen={false}
									>
										<PanelRow>
											<ItemGroup
												isBordered={false}
												size="small"
											>
												<SelectControl
													label={__(
														'Animation',
														'mailster'
													)}
													value={options.animation}
													onChange={(val) => {
														setOptions({
															animation: val,
														});
													}}
												>
													<option value="">
														{__('None', 'mailster')}
													</option>
													<option value="fadein">
														{__(
															'FadeIn',
															'mailster'
														)}
													</option>
													<option value="shake">
														{__(
															'Shake',
															'mailster'
														)}
													</option>
													<option value="swing">
														{__(
															'Swing',
															'mailster'
														)}
													</option>
													<option value="heartbeat">
														{__(
															'Heart Beat',
															'mailster'
														)}
													</option>
													<option value="tada">
														{__('Tada', 'mailster')}
													</option>
													<option value="wobble">
														{__(
															'Wobble',
															'mailster'
														)}
													</option>
												</SelectControl>
											</ItemGroup>
										</PanelRow>
									</PanelBody>
								</>
							)}
							<PanelBody title="Style" initialOpen={false}>
								<PanelRow>
									<RangeControl
										className="widefat"
										label={__('Form Width', 'mailster')}
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
										initialPosition={100}
									/>
								</PanelRow>
								<PanelRow>
									<BoxControl
										label={__('Form Padding', 'mailster')}
										values={options.padding}
										help={__(
											'Set the padding of your form in %',
											'mailster'
										)}
										resetValues={{
											top: undefined,
											left: undefined,
											right: undefined,
											bottom: undefined,
										}}
										onChange={(val) =>
											setOptions({
												padding: val,
											})
										}
									/>
								</PanelRow>
							</PanelBody>
						</>
					)}
				</>
			)}
			{!isDisplayed && (
				<Notice status="warning" isDismissible={false}>
					This form is currently not displayed anywhere.
				</Notice>
			)}{' '}
		</Panel>
	);
}

const PostTypeFields = (props) => {
	const { options, setOptions } = props;

	const postTypes = useSelect((select) => {
		const result = select('core').getEntityRecords('root', 'postType');
		return !result
			? []
			: result.filter((type) => {
					return (
						type.viewable &&
						!['attachment', 'custom-post-type_', 'post_'].includes(
							type.slug
						)
					);
			  });
	});

	const alls = options.all || [];

	function setAll(all, add) {
		var newAlls = [...alls];
		if (add) {
			newAlls.push(all);
		} else {
			newAlls = newAlls.filter((el) => {
				return el != all;
			});
		}
		setOptions({ all: newAlls });
	}

	return (
		<>
			{postTypes.map((postType) => {
				return (
					<PanelRow>
						<ItemGroup
							key={postType.slug}
							isBordered={true}
							className="widefat"
							size="medium"
						>
							<Item>
								<CheckboxControl
									label={__(
										'Display on all ' + postType.name,
										'mailster'
									)}
									checked={alls.includes(postType.slug)}
									onChange={(val) => {
										setAll(postType.slug, val);
									}}
								/>
							</Item>

							{!alls.includes(postType.slug) && (
								<PostTokenFields
									options={options}
									setOptions={setOptions}
									postType={postType}
								/>
							)}
						</ItemGroup>
					</PanelRow>
				);
			})}
		</>
	);
};

const PostTokenFields = (props) => {
	const { postType, taxonomy, options, setOptions } = props;

	const taxonomies = useSelect((select) => {
		return select('core').getEntityRecords('root', 'taxonomy');
	});

	// const specifcTax = useSelect((select) => {
	// 	return select('core').getEntityRecords('taxonomy', taxonomy);
	// });

	// const tax = useSelect((select) => {
	// 	return select('core').getEntityRecords('root', 'taxonomy');
	// });
	return (
		<>
			<PostTokenField
				postType={postType}
				options={options}
				setOptions={setOptions}
			/>
			{false &&
				taxonomies &&
				taxonomies
					.filter((taxonomy) => {
						return postType.taxonomies.includes(taxonomy.slug);
					})
					.map((taxonomy) => {
						return (
							<PostTokenField
								key={taxonomy.slug}
								postType={postType}
								taxonomy={taxonomy}
								options={options}
								setOptions={setOptions}
							/>
						);
					})}
		</>
	);
};

const PostTokenField = (props) => {
	const { postType, taxonomy = false, options, setOptions } = props;

	const [selectedTokens, setSelectedTokens] = useState([]);
	const [loading, setLoading] = useState(false);

	const storeKey = taxonomy ? 'taxonomies' : 'posts';
	const ids = options[storeKey] || [];

	const [currentPosts, setCurrentPosts] = useState([]);
	const [suggestions, setSuggestions] = useState([]);

	const title = !taxonomy
		? sprintf(__('Select %s…', 'mailster'), postType.name)
		: sprintf(__('Select %s…', 'mailster'), taxonomy.name);

	const help = !taxonomy
		? sprintf(__('Display on these %s', 'mailster'), postType.name)
		: sprintf(
				__('Display on these %s with these %s', 'mailster'),
				postType.name,
				taxonomy.name
		  );

	const entries =
		ids &&
		useSelect((select) => {
			return select('core').getEntityRecords(
				taxonomy ? 'taxonomy' : 'postType',
				taxonomy ? taxonomy.slug : postType.slug,
				{ include: ids }
			);
		});

	//return only valid tokens
	function getTokensFromIds(ids) {
		console.warn('idsToTokens', ids, mapResult(entries));
	}

	const isLoading = useSelect((select) => {
		return select('core/data').isResolving('core', 'getEntityRecords', [
			taxonomy ? 'taxonomy' : 'postType',
			taxonomy ? taxonomy.slug : postType.slug,
			{ include: ids },
		]);
	});

	useEffect(() => {
		entries && setSelectedTokens(mapResult(entries));
	}, [entries]);

	if (taxonomy) {
		//console.warn('entries', entries);
	}

	getTokensFromIds(ids);

	// useEffect(() => {
	// 	ids &&
	// 		ids.length &&
	// 		apiFetch({
	// 			path:
	// 				getEndPointByEntity(entity) +
	// 				'?include=' +
	// 				ids.join(','),
	// 		}).then(
	// 			(result) => {
	// 				setLoading(false);
	// 				const r = mapResult(result);
	// 				setSelectedTokens(r);
	// 				setCurrentPosts(r);
	// 			},
	// 			(error) => {}
	// 		) &&
	// 		setLoading(true);
	// }, []);

	// useEffect(() => {
	// 	specifcTax && setSuggestions(mapResult(specifcTax));
	// }, [specifcTax]);

	// function getEndPointByEntity(entity) {
	// 	if (taxonomy) {
	// 		entity = 'taxonomies';
	// 	}
	// 	return 'wp/v2/' + entity;
	// }

	function mapResult(result) {
		if (!result) {
			return [];
		}

		return result
			.map((s, i) => {
				return {
					value: s.id,
					label: s.id + ' ' + (s.name || s.title.rendered),
				};
			})
			.sort((a, b) => {
				return b.id - a.id;
			});
	}

	function searchTokens(token) {
		const endpoint =
			'wp/v2/' + (taxonomy ? taxonomy.name : postType.rest_base);
		token &&
			apiFetch({
				path:
					endpoint +
					'?search=' +
					token +
					'&type=' +
					postType.rest_base,
			}).then(
				(result) => {
					setLoading(false);
					setSuggestions(mapResult(result));
				},
				(error) => {}
			) &&
			setLoading(true);
	}
	const searchTokensDebounce = useDebounce(searchTokens, 500);

	function setTokens(tokens) {
		var newTokens = tokens.map((token) => {
			return parseInt(token.value, 10);
		});
		var newPlacement = { ...options };

		newPlacement[storeKey] = newTokens;
		//newPlacement[storeKey] = [224, 2, 3, 1];

		if (newTokens.length) {
			newPlacement['all'] = [];
		}
		setSelectedTokens(tokens);
		setOptions(newPlacement);
	}

	return (
		<Item>
			<BaseControl label={help}>
				<Select
					options={suggestions}
					value={selectedTokens}
					placeholder={isLoading ? 'Loading' : title}
					onInputChange={(tokens) => searchTokensDebounce(tokens)}
					onChange={(tokens) => setTokens(tokens)}
					isMulti
					isLoading={loading}
				/>
			</BaseControl>
		</Item>
	);
};
