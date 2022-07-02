/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

export default function save(props) {
	const { attributes, setAttributes } = props;
	const { content, align } = attributes;
	const className = ['mailster-wrapper'];

	if (align) className.push('mailster-wrapper-label-align-' + align);

	const blockProps = useBlockProps.save({
		className: classnames({}, className),
	});

	return (
		<div {...blockProps}>
			<label>
				<input type="checkbox" name="_gdpr" value="1" />
				<RichText.Content tagName="span" value={content} />
			</label>
		</div>
	);
}
