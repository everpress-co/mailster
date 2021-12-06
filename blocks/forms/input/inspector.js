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
	PanelColorSettings,
	RichText,
	PlainText,
} from '@wordpress/block-editor';
import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	RadioControl,
	SelectControl,
	RangeControl,
	ColorPalette,
	MenuGroup,
	MenuItem,
	Draggable,
	IconButton,
	Flex,
	FlexItem,
	FlexBlock,
	Button,
	BaseControl,
} from '@wordpress/components';

import { Fragment, Component, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

import { Icon, chevronUp, chevronDown, trash } from '@wordpress/icons';

import Styles from './Styles';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function InputFieldInspectorControls(props) {
	const { attributes, setAttributes, isSelected } = props;
	const {
		label,
		inline,
		required,
		native,
		name,
		type,
		selected,
		style,
		values,
		hasLabel,
	} = attributes;

	const [width, setWidth] = useState(100);
	const hasValues = ['radio', 'dropdown'].includes(type);

	function setStyle(prop, data) {
		var newStyle = { ...style };
		newStyle[prop] = data;
		setAttributes({ style: newStyle });
	}

	function updateValue(i, val) {
		var newvalues = [...values];
		newvalues[i] = val;
		setAttributes({ values: newvalues });
	}
	function moveValue(i, delta) {
		var newvalues = [...values];
		var element = newvalues[i];
		newvalues.splice(i, 1);
		newvalues.splice(i + delta, 0, element);
		setAttributes({ values: newvalues });
	}
	function removeValue(i) {
		var newvalues = [...values];
		newvalues.splice(i, 1);
		setAttributes({ values: newvalues });
	}
	function addValue() {
		var newvalues = [...values];
		newvalues.push(__('Value', 'mailster'));
		setAttributes({ values: newvalues });
	}

	return (
		<InspectorControls>
			<Styles {...props} />
			<Panel>
				<PanelBody
					title={__('Field Settings', 'mailster')}
					initialOpen={true}
				>
					<PanelRow>
						<TextControl
							label={__('Label', 'mailster')}
							help={__(
								'Define a label for your field',
								'mailster'
							)}
							value={label}
							onChange={(val) => setAttributes({ label: val })}
						/>
					</PanelRow>
					{typeof type !== 'undefined' && hasLabel && (
						<PanelRow>
							<CheckboxControl
								label={__('Inline Labels', 'mailster')}
								checked={inline}
								onChange={() =>
									setAttributes({ inline: !inline })
								}
							/>
						</PanelRow>
					)}
					{typeof required !== 'undefined' && (
						<PanelRow>
							<CheckboxControl
								label={__('Required Field', 'mailster')}
								checked={required || name == 'email'}
								disabled={name == 'email'}
								onChange={() =>
									setAttributes({ required: !required })
								}
							/>
						</PanelRow>
					)}
					{(type == 'email' || type == 'date') && (
						<PanelRow>
							<CheckboxControl
								label={__(
									'Use native form element',
									'mailster'
								)}
								help="Native form elements provide a better user experience but often miss some styling."
								checked={native}
								onChange={() =>
									setAttributes({ native: !native })
								}
							/>
						</PanelRow>
					)}
					<PanelRow>
						<RangeControl
							className="widefat"
							label="Width"
							value={style.width}
							allowReset={true}
							initialPosition={100}
							onChange={(value) => setStyle('width', value)}
							min={10}
							max={100}
						/>
					</PanelRow>
					{hasValues && (
						<>
							<PanelRow>
								<BaseControl
									id="mailster-values"
									label={__('Values', 'mailster')}
									help={__(
										'Define options for this input field',
										'mailster'
									)}
								>
									<Flex
										className="mailster-value-options"
										justify="flex-end"
										id="mailster-values"
										style={{ flexWrap: 'wrap' }}
									>
										{values.map((value, i) => {
											return (
												<Flex
													key={i}
													style={{ flexShrink: 0 }}
												>
													<FlexItem>
														<RadioControl
															selected={selected}
															options={[
																{
																	value: value,
																},
															]}
															onChange={() => {
																setAttributes({
																	selected:
																		value,
																});
															}}
														/>
													</FlexItem>
													<FlexBlock>
														<TextControl
															autoFocus
															value={value}
															onChange={(val) => {
																updateValue(
																	i,
																	val
																);
															}}
														/>
													</FlexBlock>
													<FlexItem>
														<Button
															disabled={!i}
															icon={chevronUp}
															isSmall={true}
															label={__(
																'move up',
																'mailster'
															)}
															onClick={(val) => {
																moveValue(
																	i,
																	-1
																);
															}}
														/>
														<Button
															disabled={
																i + 1 ==
																values.length
															}
															icon={chevronDown}
															isSmall={true}
															label={__(
																'move down',
																'mailster'
															)}
															onClick={(val) => {
																moveValue(i, 1);
															}}
														/>
														<Button
															icon={trash}
															isSmall={true}
															label={__(
																'Trash',
																'mailster'
															)}
															onClick={(val) => {
																removeValue(i);
															}}
														/>
													</FlexItem>
												</Flex>
											);
										})}
									</Flex>
									<Button variant="link" onClick={addValue}>
										{__('Add new Value', 'mailster')}
									</Button>
								</BaseControl>
							</PanelRow>
						</>
					)}
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
