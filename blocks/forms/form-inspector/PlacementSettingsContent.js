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
	const { meta, setMeta, type, image, title } = props;
	const { placements } = meta;

	const options = meta['placement_' + type];

	function setOptions(options) {
		var newOptions = { ...meta['placement_' + type] };
		newOptions = { ...newOptions, ...options };
		setMeta({ ['placement_' + type]: newOptions });
	}

	return (
		<ItemGroup isBordered={false} isSeparated size="small">
			<Item>
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
					onChange={(val) => setOptions({ pos: val })}
				/>
			</Item>
			<Item>
				{__('Display form after:', 'mailster')}
				<Flex>
					<FlexBlock>
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
					</FlexBlock>
					<FlexItem>
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
					</FlexItem>
				</Flex>
			</Item>
		</ItemGroup>
	);
}
