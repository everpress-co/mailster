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
import { BlockPreview } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */

import { HelpBeacon } from '../../util';
import { ExternalLink } from '@wordpress/components';

export default function PatternModal(props) {
	const { attributes, setAttributes, isSelected, clientId } = props;

	const { resetBlocks } = useDispatch('core/block-editor');
	const { __experimentalGetAllowedPatterns } = useSelect('core/block-editor');

	const { isCleanNewPost } = useSelect('core/editor');

	const [title, setTitle] = useEntityProp(
		'postType',
		'mailster-workflow',
		'title'
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
			title={__('Choose a Workflow', 'mailster')}
			onRequestClose={onModalClose}
			isFullScreen
			isDismissible={true}
			shouldCloseOnClickOutside={false}
			shouldCloseOnEsc={false}
			className="mailster-patterns-modal"
		>
			<HelpBeacon id="6460f6909a2fac195e609002" align="right" />
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

	const { title, categories = [], blocks, article } = pattern;

	const ref = useRef(null);

	const modifiedBlocks = blocks
		//remove comment block
		.filter((block) => {
			return block.name != 'mailster-workflow/comment';
		})
		//add example attribute
		.map((block) => {
			block.attributes.isExample = true;
			return block;
		});

	const Categories = () => {
		return (
			<>
				{categories.map((category, i) => {
					return (
						<span key={i} className="mailster-workflow-badge">
							{category.replace('mailster', '')}
						</span>
					);
				})}
			</>
		);
	};

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
							blocks={modifiedBlocks}
							viewportWidth={viewportWidth}
							minHeight={344}
							additionalStyles={[{ css: 'body { padding: 16px }' }]}
						/>
					)}
				</FlexItem>
				<FlexItem className={`${baseClassName}-actions`}>
					<h5 className={`${baseClassName}-category`}></h5>
					<h3 className={`${baseClassName}-title`}>{title}</h3>

					<p>{pattern.description}</p>

					<Button variant="secondary" onClick={insertPattern}>
						{__('Start with this Workflow', 'mailster')}
					</Button>

					{article && (
						<p>
							<ExternalLink href={article} target="_blank">
								{__('Learn how to use this Workflow', 'mailster')}
							</ExternalLink>
						</p>
					)}
				</FlexItem>
			</Flex>
		</Card>
	);
}
