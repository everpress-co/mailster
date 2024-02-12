/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n, sprintf } from '@wordpress/i18n';

import { PanelRow, PanelBody, Panel } from '@wordpress/components';

import { useSelect } from '@wordpress/data';

import { InspectorControls } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

export default function QueueBadge(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id } = attributes;

	const allNumbers = useSelect((select) => {
		return select('mailster/automation').getNumbers();
	}, []);

	const queued = allNumbers ? allNumbers['steps'][id]?.count : null;

	const title =
		queued &&
		sprintf(
			_n('%d subscriber queued', '%d subscribers queued', queued, 'mailster'),
			queued
		);

	return (
		<>
			{queued && (
				<span className="mailster-step-queued" title={title}>
					{queued}
				</span>
			)}
			<InspectorControls>
				{queued && (
					<Panel>
						<PanelBody title={__('Queue', 'mailster')} initialOpen={false}>
							<PanelRow>
								{sprintf(__('%d items in the queue', 'mailster'), queued)}
							</PanelRow>
						</PanelBody>
					</Panel>
				)}
			</InspectorControls>
		</>
	);
}
