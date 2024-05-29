/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import {
	InspectorControls,
	RichTextToolbarButton,
	BlockControls,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect, useState, useCallback } from '@wordpress/element';
import { people } from '@wordpress/icons';
import {
	PanelBody,
	SelectControl,
	ToolbarGroup,
	ToolbarButton,
	ToggleControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

import { registerBlockExtension } from '@10up/block-components';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */

import './editor.scss';

const COREBLOCKS = ['paragraph', 'heading'];

const Attributes = {
	mailster: {
		type: 'object',
		default: {
			count: undefined,
			round: 100,
			formatted: true,
		},
	},
};

function fromHTML(html) {
	html = html.trim();
	if (!html) return null;

	const template = document.createElement('template');
	template.innerHTML = html;
	const result = template.content.children;

	if (result.length === 1) return result[0];
	return result;
}

// foreach block
COREBLOCKS.forEach((block) => {
	registerBlockExtension('core/' + block, {
		extensionName: 'mailster-subscriber-count',
		attributes: Attributes,
		classNameGenerator: () => null,
		inlineStyleGenerator: () => null,
		Edit: (props) => {
			const { attributes, setAttributes } = props;
			const { content, mailster } = attributes;

			const [data, setData] = useState(null);

			const setOption = (options) => {
				setAttributes({ mailster: { ...attributes.mailster, ...options } });
			};

			const getData = useCallback(async () => {
				const response = await apiFetch({
					path: addQueryArgs('/mailster/v1/subscriber_count', {
						mailster: mailster || {},
					}),
				});
				setData(response);
			}, [mailster]);

			useEffect(() => {
				/mailster-subscriber-count/.test(content) && getData();
			}, [mailster]);

			useEffect(() => {
				//check if the block contains the class
				if (data && /mailster-subscriber-count/.test(content)) {
					const c = fromHTML('<div>' + content + '</div>');
					c.querySelectorAll('.mailster-subscriber-count').forEach((el) => {
						el.textContent = data.output;
					});

					if (c.innerHTML !== content) {
						setAttributes({ content: c.innerHTML });
					}
					if (mailster.count !== data.count) {
						setOption({ count: data.count });
					}

					// restore the default value
				} else if (mailster && mailster.count !== undefined) {
					setAttributes({ mailster: Attributes.mailster.default });
				}
			}, [content, data]);

			// don't show if there is no count
			if (!mailster.count) {
				return null;
			}

			return (
				<InspectorControls>
					<PanelBody title="Subscriber Count">
						<p>
							{__(
								'This block contains the number of your subscribers. Adjust the settings here.',
								'mailster'
							)}
						</p>
						<p>
							{sprintf(__('Subscriber count: %s', 'mailster'), mailster.count)}
						</p>
						<ToggleControl
							label={__('Format Value', 'mailster')}
							help={__(
								'Format the number with thousand separators.',
								'mailster'
							)}
							checked={mailster.formatted}
							onChange={(val) => setOption({ formatted: val })}
						/>
						<SelectControl
							label="Round to"
							value={mailster.round}
							onChange={(val) => setOption({ round: val })}
							options={[
								{ label: __('Do not round', 'mailster'), value: 1 },
								{ label: '10', value: 10 },
								{ label: '100', value: 100 },
								{ label: '1.000', value: 1000 },
								{ label: '10.000', value: 10000 },
							]}
						/>
					</PanelBody>
				</InspectorControls>
			);
		},
	});

	registerFormatType('core/' + block, {
		title: __('Insert Newsletter Subscribers Count', 'mailster'),
		tagName: 'span',
		className: 'mailster-subscriber-count-' + block,
		edit: (props) => {
			const { isActive, onChange, onFocus, value } = props;

			const selectedBlock = useSelect((select) => {
				return select('core/block-editor').getSelectedBlock();
			}, []);

			if (selectedBlock && selectedBlock.name !== 'core/' + block) {
				return null;
			}

			const onClick = () => {
				onChange(
					toggleFormat(value, {
						type: 'core/' + block,
						attributes: {
							//overwrite this so we have a unique identifier
							class: 'mailster-subscriber-count',
						},
					})
				);
				onFocus();
			};

			return (
				<BlockControls>
					<ToolbarGroup>
						<ToolbarButton
							icon={people}
							title={__('Insert Newsletter Subscribers Count', 'mailster')}
							onClick={onClick}
							isActive={isActive}
						/>
					</ToolbarGroup>
				</BlockControls>
			);
		},
	});
});
