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
	const { label, type, inline, requried } = attributes;

	return (
		<div {...useBlockProps.save()}>
			{!inline && <label for={clientId}>{label || '&nbsp;'}</label>}
			<input
				name="asdads"
				type={type}
				value=""
				id={clientId}
				placeholder={inline && label}
				className="input mailster-email mailster-required"
				ariaRequired={requried}
				ariaLabel={label}
				spellcheck="false"
			/>
		</div>
	);
}
