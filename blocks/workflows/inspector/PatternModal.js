/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';

import {
	Button,
	Spinner,
	Modal,
	Flex,
	FlexItem,
	Card,
	BaseControl,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useRef, useState } from '@wordpress/element';

import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';
import { BlockPreview } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

import { searchBlock } from '../../util';

export default function PatternModal(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;

	const {
		selectBlock,
		toggleBlockHighlight,
		flashBlock,
		moveBlockToPosition,
		removeBlocks,
		resetBlocks,
	} = useDispatch('core/block-editor');
	const {
		getBlockRootClientId,
		getBlockIndex,
		getBlocks,
		__experimentalGetAllowedPatterns,
	} = useSelect('core/block-editor');

	const { isCleanNewPost, undoManager, __unstableIsEditorReady } =
		useSelect('core/editor');

	const [title, setTitle] = useEntityProp(
		'postType',
		'mailster-workflow',
		'title'
	);
	const [meta, setMeta] = useEntityProp(
		'postType',
		'mailster-workflow',
		'meta'
	);

	const [patterns, setPatterns] = useState([]);

	const [isPatternModal, showPatternModal] = useState(false);

	useEffect(() => {
		if (isCleanNewPost()) {
			setPatterns(__experimentalGetAllowedPatterns());
			showPatternModal(true);
		}
	}, []);

	const onModalClose = () => {
		window.location = window.location.href.replace('post-new.php', 'edit.php');
	};
	const onInsertPattern = (pattern) => {
		const block = wp.blocks.parse(pattern.content);
		const isScratchPattern = pattern.name == 'mailster-workflow/scratch';
		resetBlocks(block);
		!title && !isScratchPattern && setTitle(pattern.title || '');

		//reset saved meta triggers
		setMeta({ trigger: [] });

		showPatternModal(false);
	};

	useEffect(() => {
		if (!isPatternModal) return;
		const timeout = setTimeout(() => {
			if (patterns.length == 0) {
				reload();
			}
		}, 2000);

		return () => clearTimeout(timeout);
	}, [isPatternModal]);

	const reload = () => {
		setTimeout(() => {
			setPatterns(__experimentalGetAllowedPatterns());
		}, 1);
		setPatterns([]);
	};

	if (!isPatternModal) return <></>;

	return (
		<Modal
			title={__('Choose a Workflows', 'mailster')}
			onRequestClose={onModalClose}
			isFullScreen
			isDismissible={true}
			shouldCloseOnClickOutside={false}
			shouldCloseOnEsc={false}
			className="mailster-patterns-modal"
		>
			<div className="mailster-patterns-explorer">
				{patterns.length == 0 && (
					<Flex className="mailster-patterns-loader" justify="center">
						<FlexItem>
							<BaseControl>
								<Spinner />
								<Button onClick={reload} variant="link">
									{__('Loading Workflows', 'mailster')}
								</Button>
							</BaseControl>
						</FlexItem>
					</Flex>
				)}
				{patterns &&
					patterns.map((pattern, i) => {
						return (
							<WorkflowPattern
								key={i}
								pattern={pattern}
								onInsertPattern={onInsertPattern}
							/>
						);
					})}
			</div>
		</Modal>
	);
}

function WorkflowPattern({ pattern, onInsertPattern }) {
	const baseClassName = 'mailster-pattern';

	const { title, categories = [], blocks } = pattern;

	const ref = useRef(null);

	const blockWithExamples = blocks.map((block) => {
		block.attributes.isExample = true;
		return block;
	});

	const insertPattern = () => {
		onInsertPattern(pattern);
	};

	const [visible, setVisible] = useState(false);

	useEffect(() => {
		if (!ref.current) return;
		const observer = new IntersectionObserver(
			(entries) => {
				if (entries[0].isIntersecting) {
					setVisible(true);
					observer.unobserve(ref.current);
				}
			},
			{
				threshold: [0],
			}
		);
		observer.observe(ref.current);
	}, [ref.current]);

	const viewportWidth = pattern.viewportWidth;

	return (
		<Card className={baseClassName} aria-label={pattern.title} ref={ref}>
			<Flex
				align="center"
				justify="space-between"
				className={`${baseClassName}-inner`}
			>
				<FlexItem
					className={`${baseClassName}-preview`}
					onClick={insertPattern}
				>
					{visible && (
						<BlockPreview
							blocks={blockWithExamples}
							viewportWidth={viewportWidth}
						/>
					)}
				</FlexItem>
				<FlexItem className={`${baseClassName}-actions`}>
					<h6 className={`${baseClassName}-category`}>{categories}</h6>
					<h3 className={`${baseClassName}-title`}>{title}</h3>

					<p>{pattern.description}</p>

					<Button variant="secondary" onClick={insertPattern}>
						{__('Start with this Workflow', 'mailster')}
					</Button>
				</FlexItem>
			</Flex>
		</Card>
	);
}
