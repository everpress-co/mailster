/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { Button } from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

import TriggerSelector from '../trigger/TriggerSelector';

export default function Triggers(props) {
	const { meta, setMeta } = props;

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
