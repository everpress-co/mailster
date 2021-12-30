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

import { __experimentalItem as Item } from '@wordpress/components';

import PostTokenField from './PostTokenField';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function PostTokenFields(props) {
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
			{taxonomies &&
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
}
