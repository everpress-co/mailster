/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { BlockControls, BlockAlignmentToolbar } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

export default function InputBlockControls(props) {
	const { attributes, setAttributes } = props;
	const { align } = attributes;

	function updateAlignment(alignment) {
		setAttributes({ align: alignment });
	}

	return (
		<BlockControls>
			<BlockAlignmentToolbar value={align} onChange={updateAlignment} />
		</BlockControls>
	);
}
