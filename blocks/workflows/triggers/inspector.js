/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n, sprintf } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelRow, PanelBody } from '@wordpress/components';

import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */

import { searchBlocks } from '../../util';

export default function TriggersInspectorControls(props) {
	const allTriggers = useSelect((select) =>
		select('mailster/automation').getTriggers()
	);

	const triggers = searchBlocks('mailster-workflow/trigger');

	const count = triggers.length - 1 || 0;

	const label = _n(
		'There is %d trigger in this workflow.',
		'There are %d triggers in this workflow.',
		count,
		'mailster'
	);

	return (
		<InspectorControls>
			{allTriggers && (
				<Panel>
					<PanelBody>
						<PanelRow>{sprintf(label, count)}</PanelRow>
					</PanelBody>
				</Panel>
			)}
		</InspectorControls>
	);
}
