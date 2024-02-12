/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { PanelRow } from '@wordpress/components';

import { PluginPostStatusInfo } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */

export default function PublishInfo(props) {
	return (
		<PluginPostStatusInfo>
			<PanelRow></PanelRow>
		</PluginPostStatusInfo>
	);
}
