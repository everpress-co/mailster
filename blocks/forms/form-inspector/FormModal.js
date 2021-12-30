/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	RichText,
	PlainText,
	BlockPatternList,
	BlockPreview,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
	CardMedia,
	Card,
	CardHeader,
	CardBody,
	CardDivider,
	CardFooter,
	__experimentalGrid as Grid,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { useSelect, select, dispatch, subscribe } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

import { Modal, Button, Tooltip } from '@wordpress/components';

import { more } from '@wordpress/icons';

const editor = select('core/editor');
const blockEditor = select('core/block-editor');

const EmptyEditor = () => {
	if (editor.isEditedPostEmpty()) {
		return true;
	}

	if (
		editor
			.getEditedPostContent()
			.indexOf('<!-- /wp:mailster/form-wrapper -->') !== 0
	) {
		return false;
	}

	return true;
};

const ModalContent = ({ setOpen }) => {
	const { __experimentalBlockPatterns } = blockEditor.getSettings();

	const patterns = __experimentalBlockPatterns.filter((pattern) =>
		pattern.categories.includes('mailster-forms')
	);

	if (!patterns.length) {
		return <></>;
	}

	const setPattern = (pattern, block) => {
		dispatch('core/block-editor').resetBlocks(block);
		setOpen(false);
	};

	const [title, setTitle] = useEntityProp(
		'postType',
		'newsletter_form',
		'title'
	);

	return (
		<>
			<TextControl
				label={__('Form Name', 'mailster')}
				value={title}
				onChange={(value) => setTitle(value)}
				help={__('Define a name for your form.', 'mailster')}
				placeholder={__('Add title', 'mailster')}
			/>
			<Grid columns={3}>
				{patterns.map((pattern, i) => {
					const block = wp.blocks.parse(pattern.content);
					return (
						<Card
							key={'form_pattern_' + i}
							onClick={() => setPattern(pattern, block)}
						>
							<Tooltip
								text={pattern.description}
								position="center"
							>
								<CardBody size="small">
									<BlockPreview
										blocks={block}
										viewportWidth={pattern.viewportWidth}
									/>
								</CardBody>
								<CardFooter>{pattern.title}</CardFooter>
							</Tooltip>
						</Card>
					);
				})}
			</Grid>
		</>
	);
};

export default function FormModal(props) {
	const { meta, setMeta } = props;

	const [isOpen, setOpen] = useState(false);
	const [isEmpty, setEmpty] = useState(EmptyEditor());

	const openModal = () => setOpen(true);

	const closeModal = () => {
		if (isEmpty) {
			const insertedBlock = wp.blocks.createBlock(
				'mailster/form-wrapper',
				{},
				['field-email', 'gdpr', 'field-submit'].flatMap((field) => {
					if (false) {
						return [];
					}
					return wp.blocks.createBlock('mailster/' + field);
				})
			);
			dispatch('core/block-editor').resetBlocks([insertedBlock]);
		}
		setOpen(false);
	};

	subscribe(() => {
		const newRequireModal = EmptyEditor();
		if (newRequireModal !== isEmpty) {
			setEmpty(newRequireModal);
		}
	});

	useEffect(() => {
		setOpen(isEmpty);
	}, [isEmpty]);

	return (
		<>
			{isOpen && (
				<Modal
					title="Please select a form to start with"
					className="form-select-modal"
					isFullScreen
					onRequestClose={closeModal}
				>
					<ModalContent setOpen={setOpen} />
				</Modal>
			)}
		</>
	);
}
