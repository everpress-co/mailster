/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { sprintf, __, _n } from '@wordpress/i18n';

import {
	Button,
	PanelRow,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { dispatch, useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */

export default function Options(props) {
	const { meta, setMeta } = props;

	return (
		<PluginDocumentSettingPanel
			name="options"
			title={__('Options', 'mailster')}
		>
			<PanelRow></PanelRow>
		</PluginDocumentSettingPanel>
	);
}
