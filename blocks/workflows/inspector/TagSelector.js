/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { Spinner, FormTokenField } from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

export default function TagSelector(props) {
	const {
		attributes,
		setAttributes,
		help,
		label = __('Tags', 'mailster'),
	} = props;
	const { tags = [] } = attributes;

	const allTags = useSelect((select) =>
		select('mailster/automation').getTags()
	);

	function setTag(tokens) {
		setAttributes({ tags: tokens.length ? tokens : undefined });
	}

	const getSuggestions = () => {
		return allTags.map((t) => t.name);
	};

	return (
		<>
			{!allTags && <Spinner />}
			<FormTokenField
				label={label}
				help={help}
				value={tags}
				suggestions={getSuggestions()}
				onChange={(tokens) => setTag(tokens)}
			/>
		</>
	);
}
