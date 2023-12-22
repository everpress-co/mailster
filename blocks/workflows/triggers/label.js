/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { searchBlocks } from '../../util';

export default function Label(attributes, { context }) {
	const { content } = attributes;

	const [triggerBlocks, setTriggerBlocks] = useState(0);

	const { getBlocks } = useSelect('core/block-editor');

	const blocks = getBlocks();
	useEffect(() => {
		setTriggerBlocks(searchBlocks('mailster-workflow/trigger').length - 1);
	}, [blocks]);

	if (!triggerBlocks) {
		return content;
	}

	if (triggerBlocks == 1) {
		return __('One Trigger', 'mailster');
	}

	return sprintf(__('%d Triggers', 'mailster'), triggerBlocks);
}
