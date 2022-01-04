/**
 * External dependencies
 */

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
	const { successMessage, errorMessage } = attributes;
	const className = ['mailster-block-form-info'];

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

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
		>
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
