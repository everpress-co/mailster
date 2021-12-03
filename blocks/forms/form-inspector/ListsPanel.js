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
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	Button,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
	BaseControl,
	RadioControl,
	Flex,
	FlexItem,
	FlexBlock,
	Spinner,
	Tip,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';

import { more } from '@wordpress/icons';
import apiFetch from '@wordpress/api-fetch';
import { Icon, arrowUp, arrowDown, trash } from '@wordpress/icons';
import { useSelect } from '@wordpress/data';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function ListsPanel(props) {
	const { meta, setMeta } = props;
	const { userschoice, lists } = meta;

	const allLists = useSelect(
		(select) => select('mailster/form').getLists(),
		[]
	);

	function setList(id, add) {
		var newLists = [...lists];
		if (add) {
			newLists.push(id);
		} else {
			newLists = newLists.filter((el) => {
				return el != id;
			});
		}

		setMeta({ lists: newLists });
	}

	function moveValue(i, delta) {
		var newLists = [...lists];
		var element = newLists[i];
		newLists.splice(i, 1);
		newLists.splice(i + delta, 0, element);

		console.warn(i, delta);

		setMeta({ lists: newLists });
	}
	const getList = (id) => {
		const list = allLists.filter((list) => {
			return list.ID == id;
		});
		return list.length ? list[0] : null;
	};

	const avLists = allLists
		? allLists.filter((list) => {
				return !lists.includes(list.ID);
		  })
		: [];

	return (
		<>
			<PanelRow>
				<CheckboxControl
					label="Users Choice"
					checked={!!meta.userschoice}
					onChange={() => setMeta({ userschoice: !meta.userschoice })}
					help="Users decide which list they subscribe to"
				/>
			</PanelRow>
			{!allLists && <Spinner />}
			{allLists && lists.length > 0 && (
				<PanelRow>
					<BaseControl
						id="mailster-values"
						className="widefat"
						label={
							userschoice
								? __('Users can subscribe to', 'mailster')
								: __('Subscribe new users to', 'mailster')
						}
					>
						<Flex
							className="mailster-value-options"
							justify="flex-end"
							id="mailster-values"
							style={{ flexWrap: 'wrap' }}
						>
							{lists.map((list_id, i) => {
								const list = getList(list_id);
								if (!list) return;
								return (
									<Flex key={i} style={{ flexShrink: 0 }}>
										<FlexItem>
											<CheckboxControl
												checked={true}
												value={list.ID}
												onChange={(checked) => {
													setList(list.ID, checked);
												}}
												label={list.name}
											/>
										</FlexItem>
										{lists.length > 1 && (
											<FlexItem>
												<Button
													disabled={!i}
													icon={arrowUp}
													isSmall={true}
													label={__(
														'move up',
														'mailster'
													)}
													onClick={(val) => {
														moveValue(i, -1);
													}}
												/>
												<Button
													disabled={
														i + 1 == lists.length
													}
													icon={arrowDown}
													isSmall={true}
													label={__(
														'move down',
														'mailster'
													)}
													onClick={(val) => {
														moveValue(i, 1);
													}}
												/>
											</FlexItem>
										)}
									</Flex>
								);
							})}
						</Flex>
					</BaseControl>
				</PanelRow>
			)}
			{avLists.length > 0 && (
				<PanelRow>
					<BaseControl
						id="mailster-values"
						className="widefat"
						label={__('Available Lists', 'mailster')}
					>
						<Flex
							className="mailster-value-options"
							justify="flex-end"
							id="mailster-values"
							style={{ flexWrap: 'wrap' }}
						>
							{avLists.map((list, i) => {
								return (
									<Flex key={i} style={{ flexShrink: 0 }}>
										<FlexItem>
											<CheckboxControl
												checked={lists.includes(
													list.ID
												)}
												value={list.ID}
												onChange={(checked) => {
													setList(list.ID, checked);
												}}
												label={list.name}
											/>
										</FlexItem>
									</Flex>
								);
							})}
						</Flex>
					</BaseControl>
				</PanelRow>
			)}
			{meta.userschoice && lists.length > 0 && (
				<PanelRow>
					<Tip>
						{__(
							'You can update the list names and the precheck status in the editor.',
							'mailster'
						)}
					</Tip>
				</PanelRow>
			)}
		</>
	);
}
