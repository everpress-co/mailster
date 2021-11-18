/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	useBlockProps,
	InspectorControls,
	RichText,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
	ColorPaletteControl,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	Card,
	CardBody,
	CardMedia,
	Button,
	RangeControl,
	FocalPointPicker,
	SelectControl,
	BaseControl,
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { more } from '@wordpress/icons';

import PlacementOption from './PlacementOption';
import PlacementIcons from './PlacementIcons';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Placement(props) {
	const { meta, setMeta } = props;

	return (
		<PluginDocumentSettingPanel name="placement" title="Placement">
			<PanelRow>
				<BaseControl
					id={'extra-options-'}
					label={__(
						'Define where you like to display thus form',
						'mailster'
					)}
				></BaseControl>
			</PanelRow>
			<Grid columns={2}>
				<PlacementOption
					{...props}
					title="In Content"
					type="content"
					image={PlacementIcons.content}
				/>
				<PlacementOption
					{...props}
					title="Fixed bar"
					type="bar"
					image={PlacementIcons.bar}
				/>
				<PlacementOption
					{...props}
					title="Popup"
					type="popup"
					image={PlacementIcons.popup}
				/>
				<PlacementOption
					{...props}
					title="Slide-in"
					type="side"
					image={PlacementIcons.other}
				/>
				<PlacementOption
					{...props}
					title="Other"
					type="other"
					image={PlacementIcons.other}
				/>
			</Grid>
		</PluginDocumentSettingPanel>
	);
}
