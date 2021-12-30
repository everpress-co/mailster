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
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';
import { Fragment, useState, useEffect } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';

import { more } from '@wordpress/icons';

//import InputFieldInspectorControls from '../input/inspector.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { content } = attributes;
	const className = ['mailster-wrapper'];

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	useEffect(() => {
		setMeta({ gdpr: true });
		return () => {
			//need to check if in the main editor
			setMeta({ gdpr: false });
		};
	}, []);

	return (
		<Fragment>
			<div
				{...useBlockProps({
					className: className.join(' '),
				})}
			>
				<label className="mailster-label">
					<input type="checkbox" name="_gdpr" value="1" />
					<RichText
						tagName="span"
						value={content}
						onChange={(val) => setAttributes({ content: val })}
						placeholder={__('Enter Label', 'mailster')}
					/>
				</label>
			</div>
		</Fragment>
	);
}
