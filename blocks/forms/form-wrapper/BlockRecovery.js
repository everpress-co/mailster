/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { useSelect, dispatch } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */

export default function BlockRecovery(props) {
	const [brokenBlocks, setBrokenBlocks] = useState(0);

	const getAllBlocks = (blocks) => {
		let allBlocks = [];

		blocks.forEach((block) => {
			allBlocks.push(block);
			if (block.innerBlocks.length > 0) {
				allBlocks = allBlocks.concat(getAllBlocks(block.innerBlocks));
			}
		});

		return allBlocks;
	};

	const allBlocks = useSelect((select) =>
		getAllBlocks(select('core/block-editor').getBlocks())
	);

	const getBrokenBlocks = () => {
		const broken = allBlocks.filter((block) => {
			return block.isValid === false;
		});
		return broken;
	};

	const recoverAllBlocks = () => {
		const broken = getBrokenBlocks();

		broken.map((block) => {
			const b = createBlock(block.name, block.attributes, block.innerBlocks);
			dispatch('core/block-editor').replaceBlock(block.clientId, b);
		});
		setBrokenBlocks(0);
	};

	useEffect(() => {
		brokenBlocks &&
			recoverAllBlocks() &&
			dispatch('core/notices').createNotice(
				'success',
				__('Automatically fixed broken Blocks.', 'mailster'),
				{
					type: 'snackbar',
					isDismissible: true,
				}
			);
		const broken = getBrokenBlocks();
		setBrokenBlocks(broken.length);
	}, [brokenBlocks]);

	return null;
}
