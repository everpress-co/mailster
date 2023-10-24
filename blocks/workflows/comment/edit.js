/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { RichText, useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

import InspectorControls from './inspector.js';

export default function Edit(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { comment = '' } = attributes;
	const className = [];

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	return (
		<>
			<div {...blockProps}>
				<div className="mailster-comment">
					<RichText
						tagName="blockquote"
						placeholder={__('Enter Comment', 'mailster')}
						allowedFormats={[]}
						value={comment.replace(/(\r\n|\r|\n)/g, '<br>')}
						onChange={(val) =>
							setAttributes({ comment: val ? val : undefined })
						}
					/>
				</div>
				<div className="end-stop canvas-handle"></div>
			</div>
			<InspectorControls {...props} />
		</>
	);
}
