/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	RichText,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
	ColorPaletteControl,
	PanelColorSettings,
	Warning,
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
	Modal,
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { select, dispatch, subscribe } from '@wordpress/data';

import { more } from '@wordpress/icons';
import { getBlockType, createBlock, rawHandler } from '@wordpress/blocks';

/**
 * Internal dependencies
 */

import { useUpdateEffect } from '../../util';

export default function BlockRecovery(props) {
	const { attributes, setAttributes, clientId } = props;

	const [hasBrokenBlocks, setHasBrokenBlocks] = useState(0);

	const getBrokenBlocks = () => {
		const all = select('core/block-editor').getBlocks(clientId);
		const broken = all.filter((block) => {
			return block.isValid === false;
		});
		return broken;
	};
	const recoverAllBlocks = () => {
		const broken = getBrokenBlocks();

		broken.map((block) => {
			const b = createBlock(
				block.name,
				block.attributes,
				block.innerBlocks
			);
			dispatch('core/block-editor').replaceBlock(block.clientId, b);
		});
		setHasBrokenBlocks(0);

		return true;
	};

	useEffect(() => {
		const broken = getBrokenBlocks();
		setHasBrokenBlocks(broken.length);
	}, [hasBrokenBlocks]);

	useUpdateEffect(() => {
		hasBrokenBlocks &&
			recoverAllBlocks() &&
			dispatch('core/notices').createNotice(
				'success',
				__('Automatically fixed broken Blocks.', 'mailster'),
				{
					type: 'snackbar',
					isDismissible: true,
				}
			);
	}, [hasBrokenBlocks]);

	return null;
}
