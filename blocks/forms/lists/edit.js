/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	RichText,
	PlainText,
} from '@wordpress/block-editor';
import {
	Button,
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';
import { Fragment, useState, useEffect } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect, select } from '@wordpress/data';

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
	const { lists, dropdown } = attributes;
	const className = ['mailster-wrapper mailster-wrapper-_lists'];

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	const allLists = useSelect(
		(select) => select('mailster/form').getLists(),
		[]
	);
	useEffect(() => {
		return () => {
			setMeta({ userschoice: false });
		};
	}, []);

	useEffect(() => {
		if (!meta.lists || !allLists) return;

		var newLists = meta.lists.map((list_id) => {
			return {
				id: list_id.toString(),
				name: getFromListId(list_id).name,
				checked: !!getFromListId(list_id).checked,
			};
		});

		setAttributes({ lists: newLists });
	}, [meta.lists, allLists]);

	const getFromListId = (list_id) => {
		const labelList = lists.find((list) => {
			return list.id == list_id;
		});
		if (labelList) {
			return labelList;
		}
		const list = allLists.find((list) => {
			return list.ID == list_id;
		});

		return list;
	};

	const setLabel = (label, i) => {
		var newLists = [...lists];
		newLists[i].name = label;
		setAttributes({ lists: newLists });
	};

	const setChecked = (label, i) => {
		var newLists = [...lists];
		newLists[i].checked = label;
		setAttributes({ lists: newLists });
	};

	return (
		<div
			{...useBlockProps({
				className: className.join(' '),
			})}
		>
			{dropdown ? (
				<select className="input">
					{lists.map((list, i) => {
						return (
							<option key={i} value={list.ID}>
								{list.name}
							</option>
						);
					})}
				</select>
			) : (
				<>
					{lists.map((list, i) => {
						return (
							<div
								key={i}
								className="mailster-group mailster-group-checkbox"
							>
								<label>
									<input
										type="checkbox"
										value={list.id}
										checked={list.checked || false}
										aria-label={list.name}
										onChange={() =>
											setChecked(!list.checked, i)
										}
									/>
									<RichText
										tagName="span"
										value={list.name}
										onChange={(val) => setLabel(val, i)}
										allowedFormats={[]}
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
				</>
			)}

			<InputFieldInspectorControls
				meta={meta}
				setMeta={setMeta}
				attributes={attributes}
				setAttributes={setAttributes}
			/>
		</div>
	);
}
