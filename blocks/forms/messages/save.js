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
	const { attributes, setAttributes, isSelected } = props;
	const { successMessage, errorMessage, align } = attributes;
	const className = ['mailster-block-form-info'];

	if (align) className.push('has-text-align-' + align);

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

	const blockProps = useBlockProps.save({
		className: classnames({}, className),
	});

	return (
		<div {...blockProps}>
			<div
				className="mailster-block-form-info-success"
				style={styleSuccessMessage}
			>
				<RichText.Content tagName="div" value={successMessage} />
				<div className="mailster-block-form-info-extra"></div>
			</div>
			<div
				className="mailster-block-form-info-error"
				style={styleErrorMessage}
			>
				<RichText.Content tagName="div" value={errorMessage} />
				<div className="mailster-block-form-info-extra"></div>
			</div>
		</div>
	);
}
