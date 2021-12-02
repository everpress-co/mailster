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
import { useBlockProps, RichText } from '@wordpress/block-editor';
import FormElement from './FormElement';

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
	const { label, name, type, inline, required, style, hasLabel } = attributes;
	const className = ['mailster-wrapper', 'mailster-wrapper-type-' + type];

	if (required) className.push('mailster-wrapper-required');
	if (inline) className.push('mailster-wrapper-inline');
	if ('submit' == type) className.push('wp-block-button');

	const styleSheets = {
		width: style.width ? style.width + '%' : undefined,
	};

	const labelElement = (
		<RichText.Content
			tagName="label"
			//htmlFor={type != 'radio' ? id : null}
			style={{ color: style.labelColor }}
			className="mailster-label"
			value={label}
		/>
	);

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
			//data-label={label}
			style={styleSheets}
		>
			{hasLabel && label && !inline && labelElement}
			<FormElement {...props} />
			{hasLabel && label && inline && labelElement}
		</div>
	);
}
