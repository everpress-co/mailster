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
import { useSelect, select } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

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
	const { lists } = attributes;
	const className = ['mailster-wrapper mailster-wrapper-_lists'];

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
		>
			{lists.length > 0 &&
				lists.map((list, i) => (
					<div
						key={i}
						className="mailster-group mailster-group-checkbox"
					>
						<label>
							<input
								type="checkbox"
								value={list.id}
								aria-label={list.label}
							/>
							<RichText.Content
								tagName="span"
								value={list.label}
								className="mailster-label"
							/>
						</label>
					</div>
				))}
		</div>
	);
}
