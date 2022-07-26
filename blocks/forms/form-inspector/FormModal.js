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

/**
 * Internal dependencies
 */

const editor = select('core/editor');

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

const ModalContent = ({ setOpen, patterns }) => {
	if (!patterns) {
		return <></>;
	}
	if (!patterns || !patterns.length) {
		return <h4>{__('No Pattern found.', 'mailster')}</h4>;
	}

	const setPattern = (pattern, block) => {
		if (!title) {
			setTitle(pattern.title);
		}
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
				className="form-title"
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
							key={i}
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
			//insert block
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

	const patterns = useSelect((select) => {
		return select('core')
			.getBlockPatterns()
			.filter((pattern) =>
				pattern.categories?.includes('mailster-forms')
			);
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
					<ModalContent setOpen={setOpen} patterns={patterns} />
				</Modal>
			)}
		</>
	);
}
