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
import {
	__experimentalNavigatorProvider as NavigatorProvider,
	__experimentalNavigatorScreen as NavigatorScreen,
	__experimentalUseNavigator as useNavigator,
} from '@wordpress/components';

import PlacementOption from './PlacementOption';
import PlacementSettings from './PlacementSettings';
import PlacementIcons from './PlacementIcons';
import NavigatorButton from './NavigatorButton';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

const placements = [
	{
		title: 'In Content',
		type: 'content',
		image: PlacementIcons.content,
	},
	{
		title: 'Fixed bar',
		type: 'bar',
		image: PlacementIcons.bar,
	},
	{
		title: 'Popup',
		type: 'popup',
		image: PlacementIcons.popup,
	},
	{
		title: 'Slide-in',
		type: 'side',
		image: PlacementIcons.other,
	},
	{
		title: 'Other',
		type: 'other',
		image: PlacementIcons.other,
	},
];

export default function Placement(props) {
	const { meta, setMeta } = props;
	return (
		<PluginDocumentSettingPanel name="placement" title="Placement">
			<PanelRow>
				<NavigatorProvider initialPath="/">
					<BaseControl id={'extra-options-'} label="">
						<Grid columns={2}>
							{placements.map((placement) => {
								return (
									<PlacementOption
										{...props}
										key={placement.type}
										title={placement.title}
										type={placement.type}
										image={placement.image}
									/>
								);
							})}
						</Grid>
					</BaseControl>
					{placements.map((placement) => {
						return (
							<PlacementSettings
								{...props}
								key={placement.type}
								title={placement.title}
								type={placement.type}
								image={placement.image}
							/>
						);
					})}
				</NavigatorProvider>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}
