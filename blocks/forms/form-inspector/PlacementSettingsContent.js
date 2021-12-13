/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { isRTL, __ } from '@wordpress/i18n';

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
	__experimentalNavigatorProvider as NavigatorProvider,
	__experimentalNavigatorScreen as NavigatorScreen,
	__experimentalUseNavigator as useNavigator,
} from '@wordpress/components';

import NavigatorButton from './NavigatorButton';
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

export default function PlacementSettingsContent(props) {
	const { options, setOptions, triggers, setTriggers } = props;

	return (
		<ItemGroup isBordered={false} isSeparated size="small">
			<Item>
				<RadioControl
					selected={options.display}
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
					<Flex align="flexStart">
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
								value={options.pos}
								labelPosition="edge"
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
										label: 'Heading 3',
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
			<BlockAlignmentToolbar
				value={options.align}
				onChange={(val) => setOptions({ align: val })}
			/>
		</ItemGroup>
	);
}
