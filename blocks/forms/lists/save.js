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
	const { lists, dropdown } = attributes;
	const className = ['mailster-wrapper mailster-wrapper-_lists'];

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
		>
			{dropdown ? (
				<select name="_lists[]" className="input">
					{lists.map((list, i) => {
						return (
							<option key={i} value={list.ID}>
								{list.name}
							</option>
						);
					})}
				</select>
			) : (
				<>
					{lists.map((list, i) => {
						return (
							<div
								key={i}
								className="mailster-group mailster-group-checkbox"
							>
								<label>
									<input
										type="checkbox"
										name="_lists[]"
										value={list.id}
										checked={list.checked}
										aria-label={list.name}
									/>
									<RichText.Content
										tagName="span"
										value={list.name}
										className="mailster-label"
									/>
								</label>
							</div>
						);
					})}
				</>
			)}
		</div>
	);
}
