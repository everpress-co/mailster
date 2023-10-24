/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */

import TriggerSelector from '../trigger/TriggerSelector';

export default function Triggers(props) {
	const { meta } = props;

	return (
		<PluginDocumentSettingPanel
			name="trigger"
			title={__('Trigger', 'mailster')}
		>
			{meta.trigger.map((trigger, index) => (
				<TriggerSelector
					key={index}
					trigger={trigger}
					index={index}
					{...props}
				/>
			))}
		</PluginDocumentSettingPanel>
	);
}
