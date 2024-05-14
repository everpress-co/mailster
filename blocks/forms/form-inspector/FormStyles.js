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

import { FormStylesPanel } from './FormStylesPanel';

export default function Styles(props) {
	return (
		<PluginDocumentSettingPanel
			name="form-styles"
			title={__('Form Styles', 'mailster')}
		>
			<FormStylesPanel {...props} />
		</PluginDocumentSettingPanel>
	);
}
