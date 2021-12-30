/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	RichText,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
	ColorPaletteControl,
	BlockAlignmentToolbar,
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
	__experimentalItemGroup as ItemGroup,
	__experimentalItem as Item,
} from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function PlacementSettingsContent(props) {
	const { options, setOptions, triggers, setTriggers } = props;

	return (
		<>
			<PanelRow>
				<ItemGroup className="widefat" isBordered={true} size="medium">
					<Item>
						<RadioControl
							selected={options.display || 'after'}
							options={[
								{
									label: 'Start of content',
									value: 'start',
								},
								{
									label: 'End of content',
									value: 'end',
								},
								{
									label: 'After',
									value: 'after',
								},
							]}
							onChange={(val) => setOptions({ display: val })}
						/>
					</Item>
					{options.display == 'after' && (
						<Item>
							{__('Display form after:', 'mailster')}
							<Flex align="flex-start">
								<FlexItem>
									<NumberControl
										onChange={(val) =>
											setOptions({
												pos: val,
											})
										}
										min={0}
										step={1}
										disabled={options.tag == 'more'}
										value={options.pos || 0}
										labelPosition="edge"
									/>
								</FlexItem>
								<FlexBlock>
									<SelectControl
										value={options.tag || 'p'}
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
												value: 'more',
												label: 'More Tag',
											},
											{
												value: 'h2',
												label: 'Heading 2',
											},
											{
												value: 'h3',
												label: 'Heading 3',
											},
											{
												value: 'h4',
												label: 'Heading 4',
											},
										]}
									/>
								</FlexBlock>
							</Flex>
							<div>
								{__(
									'Form will be displayed at the very bottom if no matching elements were found.',
									'mailster'
								)}
							</div>
						</Item>
					)}
				</ItemGroup>
			</PanelRow>
			<PanelRow>
				<ItemGroup className="widefat" isBordered={true} size="medium">
					<Item>
						<BlockAlignmentToolbar
							value={options.align}
							onChange={(val) => setOptions({ align: val })}
						/>
					</Item>
				</ItemGroup>
			</PanelRow>
		</>
	);
}
