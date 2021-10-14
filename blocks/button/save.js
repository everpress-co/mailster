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
import { RichText, useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save({ attributes, className }) {
	const { fontSize, style, text, title, width } = attributes;

	if (!text) {
		return null;
	}

	const borderProps = getBorderClassesAndStyles(attributes);
	const colorProps = getColorClassesAndStyles(attributes);
	const spacingProps = getSpacingClassesAndStyles(attributes);
	const buttonClasses = classnames(
		'wp-block-button__link',
		colorProps.className,
		borderProps.className,
		{
			// For backwards compatibility add style that isn't provided via
			// block support.
			'no-border-radius': style?.border?.radius === 0,
		}
	);
	const buttonStyle = {
		...borderProps.style,
		...colorProps.style,
		...spacingProps.style,
	};

	// The use of a `title` attribute here is soft-deprecated, but still applied
	// if it had already been assigned, for the sake of backward-compatibility.
	// A title will no longer be assigned for new or updated button block links.

	const wrapperClasses = classnames(className, {
		[`has-custom-width wp-block-button__width-${width}`]: width,
		[`has-custom-font-size`]: fontSize || style?.typography?.fontSize,
	});

	return (
		<div {...useBlockProps.save({ className: wrapperClasses })}>
			<RichText.Content
				tagName="a"
				className={buttonClasses}
				title={title}
				style={buttonStyle}
				value={text}
			/>
		</div>
	);
}
