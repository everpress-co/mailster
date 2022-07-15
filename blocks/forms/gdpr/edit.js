/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
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

/**
 * Internal dependencies
 */

import InputBlockControls from './InputBlockControls';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { content, align } = attributes;
	const className = ['mailster-wrapper'];

	if (align) className.push('mailster-wrapper-label-align-' + align);

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

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	return (
		<>
			<div {...blockProps}>
				{meta.gdpr ? (
					<label className="mailster-label">
						<input type="checkbox" name="_gdpr" value="1" />
						<RichText
							tagName="span"
							value={content}
							onChange={(val) => setAttributes({ content: val })}
							placeholder={__('Enter Label', 'mailster')}
						/>
					</label>
				) : (
					<></>
				)}
			</div>
			<InputBlockControls {...props} />
		</>
	);
}