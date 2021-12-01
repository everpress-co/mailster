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
import { Fragment, useState } from '@wordpress/element';
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
	const { content } = attributes;
	const className = ['mailster-wrapper mailster-wrapper-_gdpr'];

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	const allLists = useSelect(
		(select) => select('mailster/form').getLists(),
		[]
	);

	const getList = (id) => {
		const list = allLists.filter((list) => {
			return list.ID == id;
		});
		return list.length ? list[0] : null;
	};

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
						const list = getList(list_id);
						if (!list) return;
						return (
							<div
								key={i}
								className="mailster-group mailster-group-checkbox"
							>
								<label>
									<input type="checkbox" />
									<span className="mailster-label">
										{list.name}
									</span>
								</label>
							</div>
						);
					})}
				<InputFieldInspectorControls meta={meta} setMeta={setMeta} />
			</div>
		</Fragment>
	);
}
