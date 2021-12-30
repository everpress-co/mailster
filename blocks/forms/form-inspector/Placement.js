/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

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
import { select, dispatch, subscribe } from '@wordpress/data';

/**
 * Internal dependencies
 */

import PlacementOption from './placement-option';
import PlacementIcons from './placement-icons';
import PreviewModal from './preview-modal';

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
		image: PlacementIcons.side,
	},
	{
		title: 'Other',
		type: 'other',
		image: PlacementIcons.other,
	},
];

export default function Placement(props) {
	const { meta, setMeta } = props;

	const [isOpen, setOpen] = useState(false);

	function setPlacements(placement, add) {
		var newPlacements = [...meta.placements];
		if (add) {
			newPlacements.push(placement);
		} else {
			newPlacements = newPlacements.filter((el) => {
				return el != placement;
			});
		}

		setMeta({ placements: newPlacements });
	}

	const closeModal = () => {
		setOpen(false);
	};

	return (
		<PluginDocumentSettingPanel name="placement" title="Placement">
			<PreviewModal
				{...props}
				isOpen={isOpen}
				setOpen={setOpen}
				placements={placements}
				setPlacements={setPlacements}
				initialType={isOpen}
			/>
			<PanelRow>
				<BaseControl className="widefat">
					<Grid columns={2}>
						{placements.map((placement) => {
							return (
								<PlacementOption
									{...props}
									key={placement.type}
									placement={placement}
									setPlacements={setPlacements}
									setOpen={setOpen}
								/>
							);
						})}
					</Grid>
				</BaseControl>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}
