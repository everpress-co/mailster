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
	CardMedia,
	Card,
	CardHeader,
	CardBody,
	CardDivider,
	CardFooter,
	Button,
	Modal,
	Icon,
	RangeControl,
	FocalPointPicker,
	SelectControl,
	__experimentalBoxControl as BoxControl,
	__experimentalUnitControl as UnitControl,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';

import { settings } from '@wordpress/icons';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function PlacementOption(props) {
	const { attributes, setAttributes, title, image, isSelected } = props;
	const [isOpen, setOpen] = useState(false);
	const [isChecked, setChecked] = useState(false);

	const openModal = () => setOpen(true);
	const closeModal = () => setOpen(false);

	const MyIcon = () => <Icon icon={settings} />;

	return (
		<Card size="small" elevation={5}>
			<CardHeader>
				<Grid columns={2}>
					<Button
						variant="secondary"
						onClick={openModal}
						icon={MyIcon}
					/>
					<CheckboxControl
						checked={isChecked}
						onChange={setChecked}
					/>
				</Grid>
			</CardHeader>
			<CardMedia>
				<img src={image} alt="React Logo" />
			</CardMedia>
			<CardFooter>{title}</CardFooter>
			{isOpen && (
				<Modal title="This is my modal" onRequestClose={closeModal}>
					<Button variant="secondary" onClick={closeModal}>
						My custom close button
					</Button>
				</Modal>
			)}
		</Card>
	);
}
