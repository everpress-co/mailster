/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import {
	Button,
	TextControl,
	BaseControl,
	ClipboardButton,
	Card,
	CardBody,
	CardHeader,
	CardFooter,
	__experimentalNumberControl as NumberControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	SelectControl,
	__experimentalConfirmDialog as ConfirmDialog,
	Flex,
	PanelBody,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';
import { useState, useEffect } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { useDebounce } from '@wordpress/compose';

/**
 * Internal dependencies
 */

import { usePostTypes } from '../../util';

import { TaxonomyControls } from './taxonomy-controls';
import AuthorControl from './author-controls';
import { FlexItem } from '@wordpress/components';
import { FlexBlock } from '@wordpress/components';

export default function Selector(props) {
	const { attributes, setAttributes } = props;
	const { query, postCount = 1 } = attributes;

	const { author: authorIds, postType, taxQuery } = query;

	const { postTypesTaxonomiesMap, postTypesSelectOptions } = usePostTypes();

	const onPostTypeChange = (postType) => {
		setQuery({ postType });
	};

	const setQuery = (newQuery) => {
		setAttributes({ query: { ...query, ...newQuery } });
	};

	const postTypeObj = postTypesSelectOptions.find((o) => o.value == postType);

	const showPostTypeControl = true;
	const showTaxControl = true;
	const showAuthorControl = true;

	return (
		<>
			{showPostTypeControl && (
				<SelectControl
					__nextHasNoMarginBottom
					options={postTypesSelectOptions}
					value={postType}
					label={__('Whenever a new', 'mailster')}
					onChange={onPostTypeChange}
					help={__('has been published', 'mailster')}
				/>
			)}
			<ToolsPanel
				className="published_post-toolspanel__filters"
				label={__('Filter selection', 'mailster')}
				resetAll={() => {
					setQuery({
						author: '',
						//parents: [],
						//search: '',
						taxQuery: null,
					});
				}}
			>
				{showTaxControl && (
					<ToolsPanelItem
						label={__('Taxonomies', 'mailster')}
						hasValue={() =>
							Object.values(taxQuery || {}).some((terms) => !!terms.length)
						}
						onDeselect={() => setQuery({ taxQuery: null })}
					>
						<TaxonomyControls onChange={setQuery} query={query} />
					</ToolsPanelItem>
				)}
				{showAuthorControl && (
					<ToolsPanelItem
						hasValue={() => !!authorIds}
						label={__('Authors', 'mailster')}
						onDeselect={() => setQuery({ author: '' })}
					>
						<AuthorControl value={authorIds} onChange={setQuery} />
					</ToolsPanelItem>
				)}
			</ToolsPanel>
			<BaseControl label={__('Skip Release', 'mailster')}>
				{postTypeObj && (
					<Flex>
						<FlexItem>
							<NumberControl
								help={sprintf(
									__(
										'Define how many %s must have been published to trigger this workflow.',
										'mailster'
									),
									postTypeObj.plural
								)}
								onChange={(val) =>
									setAttributes({ postCount: parseInt(val, 10) })
								}
								value={postCount}
								min={0}
							/>
						</FlexItem>
					</Flex>
				)}
			</BaseControl>
		</>
	);
}
