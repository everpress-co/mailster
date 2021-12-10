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
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	RadioControl,
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
	FormTokenField,
	Flex,
	FlexItem,
	FlexBlock,
	BaseControl,
	SelectControl,
	useCopyToClipboard,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';

import {
	__experimentalItemGroup as ItemGroup,
	__experimentalItem as Item,
} from '@wordpress/components';
/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

export default function PlacementSettingsTriggers(props) {
	const { meta, setMeta, type, image, title } = props;
	const { placements } = meta;

	const options = meta['placement_' + type] || {};
	const triggers = options.triggers || [];

	function setOptions(options) {
		var newOptions = { ...meta['placement_' + type] };
		newOptions = { ...newOptions, ...options };
		setMeta({ ['placement_' + type]: newOptions });
	}

	function setTriggers(trigger, add) {
		var newTriggers = [...triggers];
		if (add) {
			newTriggers.push(trigger);
		} else {
			newTriggers = newTriggers.filter((el) => {
				return el != trigger;
			});
		}
		setOptions({ triggers: newTriggers });
	}

	return (
		<ItemGroup isBordered={false} isSeparated size="small">
			<Item>
				<CheckboxControl
					label={__('Trigger after delay', 'mailster')}
					checked={triggers.includes('delay')}
					onChange={(val) => {
						setTriggers('delay', val);
					}}
					help={__(
						'Mailster will show this popup after a give time. The preview will always trigger after 2 seconds.',
						'mailster'
					)}
				/>
			</Item>
			{triggers.includes('delay') && (
				<Item>
					<NumberControl
						className="small-text"
						onChange={(val) => {
							setOptions({
								trigger_delay: val,
							});
						}}
						isDragEnabled
						isShiftStepEnabled
						step={1}
						min={1}
						value={options.trigger_delay}
						label={__('Delay in Seconds', 'mailster')}
						labelPosition="edge"
					/>
				</Item>
			)}
			<Item>
				<CheckboxControl
					label={__('Trigger after inactive', 'mailster')}
					checked={triggers.includes('inactive')}
					onChange={(val) => {
						setTriggers('inactive', val);
					}}
					help={__(
						"Mailster will show this popup when the user doesn't do any interaction with the website. The preview will always trigger after 4 seconds.",
						'mailster'
					)}
				/>
			</Item>
			{triggers.includes('inactive') && (
				<Item>
					<NumberControl
						className="small-text"
						onChange={(val) =>
							setOptions({
								trigger_inactive: val,
							})
						}
						isDragEnabled
						isShiftStepEnabled
						step={1}
						min={1}
						value={options.trigger_inactive}
						label={__('Inactive for x Seconds', 'mailster')}
						labelPosition="edge"
					/>
				</Item>
			)}
			<Item>
				<CheckboxControl
					label={__('Trigger after scroll', 'mailster')}
					checked={triggers.includes('scroll')}
					onChange={(val) => {
						setTriggers('scroll', val);
					}}
					help={__(
						'Mailster will show this popup once the user scrolls to a certain position on your website.',
						'mailster'
					)}
				/>
			</Item>
			{triggers.includes('scroll') && (
				<Item>
					<NumberControl
						className="small-text"
						onChange={(val) =>
							setOptions({
								trigger_scroll: val,
							})
						}
						isDragEnabled
						isShiftStepEnabled
						step={1}
						min={1}
						min={100}
						value={options.trigger_scroll}
						label={__('Scroll Position in %', 'mailster')}
						labelPosition="edge"
					/>
				</Item>
			)}
			<Item>
				<CheckboxControl
					label={__('Trigger after click', 'mailster')}
					checked={triggers.includes('click')}
					onChange={(val) => {
						setTriggers('click', val);
					}}
					help={__(
						'Show the form once the user clicks on specific element on the website.',
						'mailster'
					)}
				/>
			</Item>
			{triggers.includes('click') && (
				<Item>
					<TextControl
						className="small-text"
						onChange={(val) =>
							setOptions({
								trigger_click: val,
							})
						}
						value={options.trigger_click}
						label={__('Selector', 'mailster')}
					/>
				</Item>
			)}
			<Item>
				<CheckboxControl
					label={__('Trigger after exit intent', 'mailster')}
					checked={triggers.includes('exit')}
					onChange={(val) => {
						setTriggers('exit', val);
					}}
					help={__(
						"Mailster will show this popup once the user tries to move away from the site. This doens't work on mobile.",
						'mailster'
					)}
				/>
			</Item>
		</ItemGroup>
	);
}
