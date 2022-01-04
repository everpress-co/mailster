/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

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

import {
	__experimentalItemGroup as ItemGroup,
	__experimentalItem as Item,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import PlacementSettingsContent from './PlacementSettingsContent';
import PlacementSettingsTriggers from './PlacementSettingsTriggers';
import PostTypeFields from './PostTypeFields';

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

	return (
		<Panel>
			{'other' == type ? (
				<PanelRow>
					<ItemGroup
						className="widefat"
						isBordered={false}
						size="medium"
					>
						<Item>
							<h3>PHP</h3>
						</Item>
						<Item>
							<pre>
								<code id={'form-php-' + currentPostId}>
									{'<?php echo mailster_form( ' +
										currentPostId +
										' ); ?>'}
								</code>
							</pre>
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
								initialOpen={true}
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
								<PlacementSettingsTriggers
									{...props}
									setOptions={setOptions}
									options={options}
									setTriggers={setTriggers}
									triggers={triggers}
								/>
							)}
							<PanelBody title="Appearance" initialOpen={false}>
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
								{'content' != type && (
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
													{__('FadeIn', 'mailster')}
												</option>
												<option value="shake">
													{__('Shake', 'mailster')}
												</option>
												<option value="swing">
													{__('Swing', 'mailster')}
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
													{__('Wobble', 'mailster')}
												</option>
											</SelectControl>
										</ItemGroup>
									</PanelRow>
								)}
							</PanelBody>
						</>
					)}
				</>
			)}
		</Panel>
	);
}
