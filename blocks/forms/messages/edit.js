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

import InputFieldInspectorControls from './inspector.js';
import InputBlockControls from './InputBlockControls';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { successMessage, errorMessage, align } = attributes;
	const className = ['mailster-block-form-info'];

	if (align) className.push('has-text-align-' + align);

	const [meta, setMeta] = useEntityProp(
		'postType',
		'newsletter_form',
		'meta'
	);

	const styleSuccessMessage = {
		width: attributes.width + '%',
		color: attributes.success,
		background: attributes.successBackground,
	};
	const styleErrorMessage = {
		width: attributes.width + '%',
		color: attributes.error,
		background: attributes.errorBackground,
	};

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	console.warn(blockProps);

	return (
		<>
			<div {...blockProps}>
				<div
					className="mailster-block-form-info-success"
					style={styleSuccessMessage}
				>
					<RichText
						tagName="div"
						value={successMessage}
						onChange={(val) =>
							setAttributes({ successMessage: val })
						}
						placeholder={__('Enter Success Message', 'mailster')}
					/>
					<div className="mailster-block-form-info-extra"></div>
				</div>
				<div
					className="mailster-block-form-info-error"
					style={styleErrorMessage}
				>
					<RichText
						tagName="div"
						value={errorMessage}
						onChange={(val) => setAttributes({ errorMessage: val })}
						placeholder={__('Enter Error Message', 'mailster')}
					/>
					<div className="mailster-block-form-info-extra"></div>
				</div>

				<InputBlockControls {...props} />
				<InputFieldInspectorControls
					meta={meta}
					setMeta={setMeta}
					attributes={attributes}
					setAttributes={setAttributes}
				/>
			</div>
		</>
	);
}
