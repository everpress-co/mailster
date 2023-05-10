/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { sprintf, __, _n } from '@wordpress/i18n';

import { TextControl } from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { dispatch, useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */

export default function WorkflowName(props) {
	const { meta, setMeta } = props;

	const [title, setTitle] = useEntityProp(
		'postType',
		'mailster-workflow',
		'title'
	);

	return (
		<PluginDocumentSettingPanel name="workflowname" initialOpen={true}>
			<TextControl
				label={__('Workflow Name', 'mailster')}
				value={title}
				onChange={(value) => setTitle(value)}
				help={__('Define a name for your workflow.', 'mailster')}
				placeholder={__('Add Workflow name', 'mailster')}
			/>
		</PluginDocumentSettingPanel>
	);
}
