/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { useSelect, select, dispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

import { __experimentalDimensionControl as DimensionControl } from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function Dimensions(props) {
	const { attributes, setAttributes } = props;

	console.warn('attributes', attributes);

	const isInputPanelOpened = useSelect((select) => {
		return select('core/edit-post').isEditorPanelOpened(
			'mailster-block-form-settings-panel/dimensions'
		);
	});

	useEffect(() => {
		!isInputPanelOpened &&
			dispatch('core/edit-post').toggleEditorPanelOpened(
				'mailster-block-form-settings-panel/dimensions'
			);
	}, [isInputPanelOpened]);

	const updateSpacing = (dimension, size, device = '') => {
		setAttributes({
			[`${dimension}${device}`]: size,
		});
	};

	return (
		<PluginDocumentSettingPanel className="with-panel" name="dimensions">
			{attributes && (
				<DimensionControl
					label={__('Padding')}
					icon={'desktop'}
					onChange={() => {}}
					value={attributes.paddingSize}
				/>
			)}
		</PluginDocumentSettingPanel>
	);
}
