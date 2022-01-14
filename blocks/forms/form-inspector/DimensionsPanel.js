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
import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';

import {
	GapEdit,
	hasGapSupport,
	hasGapValue,
	resetGap,
	useIsGapDisabled,
} from '@wordpress/block-editor/gap';
import {
	MarginEdit,
	hasMarginSupport,
	hasMarginValue,
	resetMargin,
	useIsMarginDisabled,
} from '@wordpress/block-editor/margin';
import {
	PaddingEdit,
	hasPaddingSupport,
	hasPaddingValue,
	resetPadding,
	useIsPaddingDisabled,
} from '@wordpress/block-editor/padding';

/**
 * Internal dependencies
 */

export const DimensionsPanel = (props) => {
	const { attributes, setAttributes } = props;

	function hasPaddingValue() {}
	function resetPadding() {}
	function resetAll() {}

	return (
		<ToolsPanel label={__('Dimensions')} resetAll={resetAll}>
			<p>
				Select dimensions or spacing related settings from the menu for
				additional controls.
			</p>

			<ToolsPanelItem
				hasValue={() => hasPaddingValue(props)}
				label={__('Padding')}
				onDeselect={() => resetPadding(props)}
				//resetAllFilter={createResetAllFilter('padding')}
				//isShownByDefault={defaultSpacingControls?.padding}
				panelId={props.clientId}
			>
				<PaddingEdit {...props} />
			</ToolsPanelItem>
		</ToolsPanel>
	);
};
