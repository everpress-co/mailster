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
import { StylesContent, colorSettings } from '../shared/StylesContent';

export default function InputFields(props) {
	const { attributes, setAttributes, meta, setMeta } = props;

	return (
		<PluginDocumentSettingPanel
			className="with-panel"
			name="input-fields"
			title={__('Input Fields', 'mailster')}
			initialOpen={true}
			opened={true}
		>
			{attributes && (
				<StylesContent
					attributes={attributes}
					setAttributes={setAttributes}
				/>
			)}
		</PluginDocumentSettingPanel>
	);
}
