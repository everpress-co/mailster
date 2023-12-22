/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelRow, PanelBody, Button } from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import * as Icons from '@wordpress/icons';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */

import { searchBlocks } from '../../util';

export default function TriggersInspectorControls(props) {
	// const [meta, setMeta] = useEntityProp(
	// 	'postType',
	// 	'mailster-workflow',
	// 	'meta'
	// );

	const { selectBlock } = useDispatch('core/block-editor');

	const allTriggers = useSelect((select) =>
		select('mailster/automation').getTriggers()
	);

	const triggers = searchBlocks('mailster-workflow/trigger');

	const selectTrigger = (clientId) => {
		selectBlock(clientId);
	};

	return (
		<InspectorControls>
			{allTriggers && (
				<Panel>
					<PanelBody>
						<PanelRow>
							{sprintf(
								__('There are %d triggers in this workflow.', 'mailster'),
								triggers.length || 0
							)}
						</PanelRow>
						<PanelRow>{__('Click to edit the trigger.', 'mailster')}</PanelRow>
					</PanelBody>

					{triggers &&
						triggers.map((trigger, index) => {
							const element = allTriggers.find(
								(item) => item.id === trigger.attributes.trigger
							);

							return (
								<PanelBody key={index}>
									{element && (
										<PanelRow>
											<Button
												variant="secondary"
												onClick={(e) => selectTrigger(trigger.clientId)}
												info={element.info}
												icon={Icons[element.icon]}
											>
												{element.label}
											</Button>
										</PanelRow>
									)}
								</PanelBody>
							);
						})}
				</Panel>
			)}
		</InspectorControls>
	);
}
