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
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { more } from '@wordpress/icons';
import apiFetch from '@wordpress/api-fetch';
import { Icon, arrowUp, arrowDown, trash } from '@wordpress/icons';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function Lists(props) {
	const { meta, setMeta } = props;
	const { userschoice } = meta;

	const [allLists, setAllLists] = useState([]);
	const [lists, setLists] = useState(meta.lists);

	useEffect(() => {
		apiFetch({
			path: '/mailster/v1/lists',
		}).then(
			(result) => {
				setAllLists(result);
			},
			(error) => {}
		);
	}, []);

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
		setLists(newLists);
	}

	function moveValue(i, delta) {
		var newLists = [...lists];
		var element = newLists[i];
		newLists.splice(i, 1);
		newLists.splice(i + delta, 0, element);
		setMeta({ lists: newLists });
		setLists(newLists);
	}
	const avLists = allLists.filter((list) => {
		return !lists.includes(list.ID);
	});

	return (
		<PluginDocumentSettingPanel name="userschoice" title="Lists">
			<CheckboxControl
				label="Users decide which list they subscribe to"
				checked={!!userschoice}
				onChange={() => setMeta({ userschoice: !userschoice })}
			/>
			{lists.length > 0 && allLists.length > 0 && (
				<PanelRow>
					<BaseControl
						id="mailster-values"
						label={__('Selected Lists', 'mailster')}
					>
						<Flex
							className="mailster-value-options"
							justify="flex-end"
							id="mailster-values"
							style={{ flexWrap: 'wrap' }}
						>
							{lists.map((list_id, i) => {
								const list = allLists.filter((l) => {
									return l.ID == list_id;
								})[0];
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
		</PluginDocumentSettingPanel>
	);
}
