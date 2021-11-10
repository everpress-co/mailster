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
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

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
	const { attributes } = props;
	const { messages } = attributes;

	const styleSuccessMessage = {
		color: messages.success,
		backgroundColor: messages.successBackground,
	};
	const styleErrorMessage = {
		color: messages.error,
		backgroundColor: messages.errorBackground,
	};

	return (
		<form
			method="post"
			novalidate
			action="/mailster/subscribe"
			{...useBlockProps.save({
				className: 'mailster-form',
			})}
		>
			<div className="mailster-form-info">
				<div
					className="mailster-form-info-success"
					style={styleSuccessMessage}
				>
					This is a success message
				</div>
				<div
					className="mailster-form-info-error"
					style={styleErrorMessage}
				>
					Following fields are missing or incorrect. This is an error
					message
				</div>
			</div>
			<InnerBlocks.Content />
			<input type="submit" style={{ display: 'none !important' }} />
		</form>
	);
}
