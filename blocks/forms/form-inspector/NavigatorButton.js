/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

import { Button } from '@wordpress/components';

import { __experimentalUseNavigator as useNavigator } from '@wordpress/components';
/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function NavigatorButton({
	as: Tag = Button,
	path,
	isBack = false,
	disabled = false,
	...props
}) {
	const navigator = useNavigator();

	return (
		<Tag
			onClick={() => !disabled && navigator.push(path, { isBack })}
			{...props}
			disabled={disabled}
		/>
	);
}
