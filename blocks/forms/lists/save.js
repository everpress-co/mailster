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
	const { labels } = attributes;
	const className = ['mailster-wrapper mailster-wrapper-_lists'];

	//if (required) className.push('mailster-wrapper-required');
	//if (inline) className.push('mailster-wrapper-inline');
	// const allLists = useSelect(
	// 	(select) => select('mailster/form').getLists(),
	// 	[]
	// );
	//

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
		>
			{labels.map((label, i) => (
				<div key={i} className="mailster-group mailster-group-checkbox">
					<label>
						<input type="checkbox" />
						<RichText.Content
							key={i}
							tagName="span"
							listid={label.id}
							value={label.name}
							className="mailster-label"
						/>
					</label>
				</div>
			))}
		</div>
	);

	console.warn(x);

	return x;
	return null;
	const allLists = select('mailster/form').getLists();
	const meta = select('core/editor').getEditedPostAttribute('meta');

	if (!allLists || !meta) return null;

	return (
		<>
			<div
				{...useBlockProps.save({
					className: className.join(' '),
				})}
			>
				{allLists &&
					meta.lists.map((list_id, i) => {
						const list = getList(list_id);
						if (!list) return;
						return (
							<div
								key={i}
								className="mailster-group mailster-group-checkbox"
							>
								<label>
									<input type="checkbox" />
									<span className="mailster-label">
										{list.name}
									</span>
								</label>
							</div>
						);
					})}
			</div>
		</>
	);
}
