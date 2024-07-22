/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	Panel,
	PanelBody,
	Placeholder,
	Spinner,
	Flex,
	ToolbarGroup,
	ToolbarButton,
	email,
	Button,
	SelectControl,
} from '@wordpress/components';
import { useSelect, dispatch } from '@wordpress/data';

import { useState, useEffect, useRef } from '@wordpress/element';
import { edit, external, home, plus, update } from '@wordpress/icons';

/**
 * Internal dependencies
 */

import './editor.scss';
import HomepageInspectorControls from '../homepage/inspector';
import { searchBlock } from '../util';
import { TABS } from '../homepage/constants';
import InlineStyles from '../util/InlineStyles';
import FormSelector from './FormSelector';

export default function Edit(props) {
	const { attributes, isSelected, setAttributes, context, clientId } = props;
	const { id = false, align } = attributes;

	const [contextType, setContextType] = useState(
		context['mailster-homepage-context/type']
	);
	const homepage = searchBlock('mailster/homepage');

	const contextAlign = context['mailster-homepage-context/align'] || align;
	const contextId = context['mailster-homepage-context/' + contextType];
	const formId = contextId || id;

	const [displayForm, setDisplayForm] = useState(false);
	const [displayHeight, setDisplayHeight] = useState(undefined);

	const postId = useSelect((select) => {
		return select('core/editor').getCurrentPostId();
	});

	const blockRef = useRef();

	useEffect(() => {
		setAttributes({ align: contextAlign });
	}, [contextAlign]);

	useEffect(() => {
		if (!isSelected || !contextType) return;
		location.hash = '#mailster-' + contextType;
	}, [isSelected]);

	const selectForm = (id) => {
		// if we are in context of the homepage block
		if (contextType && homepage) {
			dispatch('core/block-editor').updateBlockAttributes(homepage.clientId, {
				[contextType]: id,
			});
		} else {
			setAttributes({ id: id });
		}
	};

	//set height of the block
	useEffect(() => {
		if (!blockRef.current) return;
		const observer = new MutationObserver((entries) => {
			entries.forEach((entry) => {
				const node = entry.addedNodes.length > 0 ? entry.addedNodes[0] : null;
				if (
					node &&
					displayHeight != node.offsetHeight &&
					node.classList.contains('mailster-block-form-editor-wrap-inner')
				) {
					node.offsetHeight && setDisplayHeight(node.offsetHeight);
				}
			});
		});
		observer.observe(blockRef.current, {
			childList: true,
		});

		return () => observer.disconnect();
	}, [blockRef.current]);

	useEffect(() => {
		setDisplayForm(!!id);
	}, [id]);

	const EmptyPlaceholder = ({ children }) => (
		<div
			style={{
				minHeight: displayHeight ? displayHeight + 'px' : undefined,
				zIndex: 10,
			}}
		>
			{(!children || !displayForm) && (
				<Flex justify="center">
					<Spinner />
				</Flex>
			)}
			{displayForm && children}
		</div>
	);
	const reloadForm = () => {
		setDisplayForm(false);
		dispatch('core').receiveEntityRecords(
			'postType',
			'mailster-form',
			[],
			{},
			true
		);
		setTimeout(() => {
			setDisplayForm(true);
		}, 1);
	};

	const onSelect = (type, index) => {
		if (!homepage) return;
		location.hash = '#mailster-' + type;
		setContextType(type);

		//select current block
		//const formBlocks = searchBlocks('mailster/form');
		//select the active block
		//dispatch('core/block-editor').selectBlock(formBlocks[index].clientId);
	};

	const editForm = () => {
		window.open(
			'post.php?post=' + formId + '&action=edit',
			'edit_form_' + formId
		);
	};

	const editHomepage = () => {
		window.open(
			'post.php?post=' + postId + '&action=edit#mailster-' + contextType,
			'mailster-newsletter-homepage'
		);
	};

	const currentTab = TABS.find((tab) => tab.id === contextType);

	const getPlaceholderLabel = () => {
		if (contextType) {
			return sprintf(
				__('Newsletter Homepage: %s', 'mailster'),
				currentTab.name
			);
		}

		return __('Mailster Subscription Form', 'mailster');
	};
	const getPlaceholderInstructions = () => {
		if (contextType) {
			return currentTab.help;
		}

		return __('Select a form you like to display on this page.', 'mailster');
	};

	const blockProps = useBlockProps({
		style: { minHeight: displayHeight ? displayHeight + 'px' : undefined },
	});

	const ServerSideRenderAttributes = {
		...attributes,
		...{
			type: contextType,
			id: formId,
		},
	};

	return (
		<>
			<div {...blockProps}>
				{formId ? (
					<div className="mailster-block-form-editor-wrap" ref={blockRef}>
						<Flex className="update-form-button" justify="center">
							{homepage ? (
								<>
									<Button
										variant="primary"
										icon={edit}
										onClick={editForm}
										text={__('Edit form', 'mailster')}
									/>
									<Button
										variant="secondary"
										icon={update}
										onClick={reloadForm}
										text={__('Reload Form', 'mailster')}
									/>
								</>
							) : (
								<Button
									variant="primary"
									icon={external}
									onClick={editHomepage}
									text={__(
										'Update Forms on the Newsletter Homepage',
										'mailster'
									)}
								/>
							)}
						</Flex>
						<ServerSideRender
							className="mailster-block-form-editor-wrap-inner"
							block="mailster/form"
							attributes={ServerSideRenderAttributes}
							EmptyResponsePlaceholder={EmptyPlaceholder}
							XLoadingResponsePlaceholder={EmptyPlaceholder}
						/>
					</div>
				) : (
					<Placeholder
						icon={email}
						label={getPlaceholderLabel()}
						instructions={getPlaceholderInstructions()}
					>
						<FormSelector {...props} selectForm={selectForm} formId={formId} />
					</Placeholder>
				)}
			</div>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						label={__('Edit Form', 'mailster')}
						icon={edit}
						disabled={!formId}
						onClick={editForm}
					/>
					<ToolbarButton
						label={__('Reload Form', 'mailster')}
						icon={update}
						onClick={reloadForm}
					/>
				</ToolbarGroup>
			</BlockControls>
			{contextType && (
				<HomepageInspectorControls current={contextType} onSelect={onSelect} />
			)}
			{homepage && contextType !== 'subscribe' && (
				<InspectorControls>
					<Panel>
						<PanelBody
							title={__('Form Selector', 'mailster')}
							initialOpen={true}
						>
							<FormSelector
								{...props}
								selectForm={selectForm}
								formId={formId}
							/>
						</PanelBody>
					</Panel>
				</InspectorControls>
			)}
			<InlineStyles />
		</>
	);
}
