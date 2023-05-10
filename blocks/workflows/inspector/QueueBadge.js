/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n, sprintf } from '@wordpress/i18n';

import {
	Button,
	PanelRow,
	DropdownMenu,
	TextControl,
	MenuGroup,
	MenuItem,
	Spinner,
	PanelBody,
	__experimentalItemGroup as ItemGroup,
	__experimentalTreeGrid as TreeGrid,
	__experimentalTreeGridRow as TreeGridRow,
	__experimentalTreeGridCell as TreeGridCell,
	Panel,
	Animate,
} from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import { useEffect, useMemo } from '@wordpress/element';

import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';
import { AsyncModeProvider } from '@wordpress/data';
import {
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

export default function QueueBadge(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;
	const { id } = attributes;

	const allNumbers = useSelect((select) => {
		return select('mailster/automation').getNumbers();
	}, []);

	const queued = allNumbers ? allNumbers['steps'][id]?.count : null;

	const title =
		queued &&
		sprintf(
			_n('%d subscriber queued', '%d subscribers queued', queued, 'mailster'),
			queued
		);

	return (
		<>
			{queued && (
				<span className="mailster-step-queued" title={title}>
					{queued}
				</span>
			)}
			<InspectorControls>
				{queued && (
					<Panel>
						<PanelBody title={__('Queue', 'mailster')} initialOpen={false}>
							<PanelRow>
								{sprintf(__('%d items in the queue', 'mailster'), queued)}
							</PanelRow>
						</PanelBody>
					</Panel>
				)}
			</InspectorControls>
		</>
	);
}
