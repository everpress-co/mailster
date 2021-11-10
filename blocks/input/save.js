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
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { label, type, inline, required, style } = attributes;
	const className = ['mailster-wrapper'];

	if (required) className.push('mailster-wrapper-required');
	if (inline) className.push('mailster-wrapper-inline');
	const styleSheets = {
		width: style.width + '%',
	};

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
			data-label={label}
			style={styleSheets}
		>
			<label for={clientId}>{label || '&nbsp;'}</label>
			<input
				name="input_name"
				type={type}
				value=""
				id={clientId}
				className="input mailster-email mailster-required"
				ariaRequired={required}
				ariaLabel={label}
				spellcheck="false"
			/>
		</div>
	);
}
