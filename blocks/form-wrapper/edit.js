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
import { Button } from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

const ALLOWED_BLOCKS = ['mailster/*'];

const getBlockList = () => wp.data.select('core/block-editor').getBlocks();
let blockList = getBlockList();
wp.data.subscribe(() => {
	const newBlockList = getBlockList();
	if (
		newBlockList.length < blockList.length &&
		blockList.some((block) => block.name === 'mailster/form-wrapper') &&
		newBlockList.every((block) => block.name !== 'mailster/form-wrapper')
	) {
		alert('SORRY');
		wp.data.dispatch('core/block-editor').resetBlocks(blockList);
	}
	blockList = newBlockList;
});

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit() {
	return (
		<div {...useBlockProps()}>
			<InnerBlocks />
			<Button variant="secondary">Click me!</Button>
		</div>
	);
}
