/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

import { useBlockProps, RichText } from '@wordpress/block-editor';

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
	const { attributes, setAttributes, isSelected } = props;
	const { content } = attributes;
	const className = ['mailster-wrapper'];

	//if (required) className.push('mailster-wrapper-required');
	//if (inline) className.push('mailster-wrapper-inline');

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
		>
			<label>
				<input type="checkbox" name="_gdpr" value="1" />
				<RichText.Content tagName="span" value={content} />
			</label>
		</div>
	);
}
