/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

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
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';
import { Fragment, useState, useEffect } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';

import { more } from '@wordpress/icons';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */

import InputFieldInspectorControls from './inspector.js';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { labels = [] } = attributes;
	const className = ['mailster-wrapper mailster-wrapper-_lists'];

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	useEffect(() => {
		if (!meta.lists) return;
		console.warn('EFFECT', meta.lists);
		var newLabels = labels.sort(function (a, b) {
			return (
				meta.lists.indexOf(parseInt(a.id, 10)) -
				meta.lists.indexOf(parseInt(b.id, 10))
			);
		});

		console.warn(newLabels, labels);
		setAttributes({ labels: newLabels });
	}, [meta.lists]);

	const allLists = useSelect(
		(select) => select('mailster/form').getLists(),
		[]
	);

	const getList = (list_id, i) => {
		const list2 = labels.filter((list) => {
			return list.id == list_id;
		});
		if (list2.length) {
			return {
				id: list_id,
				name: list2[0].name,
			};
		}
		const list = allLists.filter((list) => {
			return list.ID == list_id;
		});

		if (list.length) {
			return {
				id: list_id,
				name: list[0].name,
			};
		}

		return null;
	};

	const setLabel = (label, list_id) => {
		const i = labels.findIndex((list) => {
			return list.id == list_id;
		});
		var newLabels = [...labels];
		if (!newLabels[i]) {
			newLabels[i] = {
				id: list_id,
				name: label,
			};
		} else {
			newLabels[i].name = label;
		}
		setAttributes({ labels: newLabels });
	};

	return (
		<div
			{...useBlockProps({
				className: className.join(' '),
			})}
		>
			{allLists &&
				meta.lists.map((list_id, i) => {
					const label = getList(list_id, i);
					return (
						<div
							key={i}
							className="mailster-group mailster-group-checkbox"
						>
							<label>
								<input type="checkbox" />
								{label.id}
								<RichText
									tagName="span"
									value={label.name}
									onChange={(val) => setLabel(val, label.id)}
									//allowedFormats={[]}
									className="mailster-label"
									placeholder={__('Enter Label', 'mailster')}
								/>
							</label>
						</div>
					);
				})}
			<InputFieldInspectorControls meta={meta} setMeta={setMeta} />
		</div>
	);

	//if (required) className.push('mailster-wrapper-required');
	//if (inline) className.push('mailster-wrapper-inline');

	return (
		<Fragment>
			<div
				{...useBlockProps({
					className: className.join(' '),
				})}
			>
				{allLists &&
					meta.lists.map((list_id, i) => {
						const list = getList(list_id, i);
						console.warn(list);
						if (!list) return;
						return (
							<div
								key={i}
								className="mailster-group mailster-group-checkbox"
							>
								<label>
									<input type="checkbox" />
									<RichText
										tagName="span"
										value={list.label}
										onChange={(val) => setLabel(val, i)}
										//allowedFormats={[]}
										className="mailster-label"
										placeholder={__(
											'Enter Label',
											'mailster'
										)}
									/>
								</label>
							</div>
						);
					})}
				<InputFieldInspectorControls meta={meta} setMeta={setMeta} />
			</div>
		</Fragment>
	);
}
