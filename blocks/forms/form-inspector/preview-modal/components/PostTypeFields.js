/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __, sprintf } from '@wordpress/i18n';

import Select from 'react-select';

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

import PostTokenFields from './PostTokenFields';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function PostTypeFields(props) {
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
					<PanelRow key={postType.slug}>
						<ItemGroup
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
}
