/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * WordPress dependencies
 */

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */

import { InputStylesPanel, colorSettings } from './InputStylesPanel';

export default function InputStyles(props) {
	return (
		<PluginDocumentSettingPanel
			name="input-styles"
			title={__('Input Styles', 'mailster')}
		>
			<InputStylesPanel {...props} />
		</PluginDocumentSettingPanel>
	);
}
