/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { sprintf, __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelRow,
	PanelBody,
	Button,
	Tip,
	ToggleControl,
	__experimentalNumberControl as NumberControl,
	BaseControl,
} from '@wordpress/components';

import { useSelect, dispatch, select } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */

import TriggerSelector from './TriggerSelector';
import Conditions from '../inspector/Conditions.js';

import { searchBlock, searchBlocks } from '../../util';

const MIN_TRIGGER_COUNT = 1;
const MAX_TRIGGER_COUNT = 5;

export default function TriggerInspectorControls(props) {
	const { attributes, setAttributes, clientId } = props;
	const { trigger, repeat, pending } = attributes;

	const rootClientId = useSelect((select) =>
		select('core/block-editor').getBlockRootClientId(clientId)
	);

	const count = searchBlocks('mailster-workflow/trigger', rootClientId).length;

	const removeTrigger = (trigger) => {
		dispatch('core/block-editor').updateBlockAttributes(clientId, {
			lock: false,
		});
		dispatch('core/block-editor').removeBlocks(clientId);

		// const index = meta.trigger.indexOf(trigger);
		// const newTrigger = [...meta.trigger];
		// if (index > -1) {
		// 	newTrigger.splice(index, 1);
		// }
		// setMeta({ trigger: newTrigger });
	};

	const addTrigger = () => {
		const block = wp.blocks.createBlock('mailster-workflow/trigger');
		const r = searchBlock('mailster-workflow/triggers');
		const pos = r
			? select('core/block-editor').getBlockIndex(clientId, r.clientId) + 1
			: 0;

		dispatch('core/block-editor').insertBlock(block, pos, r.clientId, false);
		dispatch('core/block-editor').selectBlock(block.clientId);
	};

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<PanelRow>
						<Button
							variant="secondary"
							isDestructive
							onClick={() => removeTrigger(trigger)}
							disabled={count <= MIN_TRIGGER_COUNT}
						>
							{__('Remove this Trigger', 'mailster')}
						</Button>
						<Button
							variant="secondary"
							onClick={() => addTrigger()}
							disabled={count >= MAX_TRIGGER_COUNT}
						>
							{__('Add Trigger', 'mailster')}
						</Button>
					</PanelRow>
					{count >= MAX_TRIGGER_COUNT && (
						<PanelRow>
							<Tip>
								{sprintf(
									__('You can add max %d triggers!', 'mailster'),
									MAX_TRIGGER_COUNT
								)}
							</Tip>
						</PanelRow>
					)}
				</PanelBody>
				<PanelBody>
					<TriggerSelector {...props} />
				</PanelBody>
				{trigger && (
					<>
						<PanelBody>
							<Conditions
								{...props}
								help={__(
									'Define conditions which must be fullfilled for the subscriber.',
									'mailster'
								)}
							/>
						</PanelBody>
						<PanelBody>
							<BaseControl
								help={__(
									'Define how often the workflow with this trigger can get triggered for each subscriber.',
									'mailster'
								)}
							>
								{repeat != 0 && (
									<ToggleControl
										onChange={(val) =>
											setAttributes({
												repeat: val ? -1 : 1,
											})
										}
										checked={repeat && repeat == -1}
										label={__('Run unlimited times', 'mailster')}
									/>
								)}
								{repeat > 0 && (
									<NumberControl
										onChange={(val) =>
											setAttributes({ repeat: val ? parseInt(val, 10) : 0 })
										}
										value={repeat}
										labelPosition="edge"
										label={__('Number of runs', 'mailster')}
										min={1}
									/>
								)}
							</BaseControl>
						</PanelBody>
						<PanelBody>
							<BaseControl
								help={__(
									'Allow unconfirmed subscribers to enter the workflow. This is useful if they need to confirm their subscription first.',
									'mailster'
								)}
							>
								<ToggleControl
									onChange={(val) => setAttributes({ pending: val })}
									checked={pending}
									label={__('Enable for pending Subscribers', 'mailster')}
								/>
							</BaseControl>
						</PanelBody>
					</>
				)}
			</Panel>
		</InspectorControls>
	);
}
