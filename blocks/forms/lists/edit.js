/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
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

import InputFieldInspectorControls from './inspector.js';

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
