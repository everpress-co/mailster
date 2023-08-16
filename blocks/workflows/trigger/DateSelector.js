/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import {
	Button,
	PanelRow,
	DropdownMenu,
	TextControl,
	MenuGroup,
	MenuItem,
	Spinner,
	PanelBody,
	__experimentalItemGroup as ItemGroup,
	BaseControl,
	Card,
	Flex,
	FlexBlock,
	FlexItem,
	Tip,
	DateTimePicker,
	SelectControl,
	Popover,
	DatePicker,
	TimePicker,
} from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { useEffect, useState } from '@wordpress/element';

import * as Icons from '@wordpress/icons';
import {
	useBlockProps,
	__experimentalLinkControl as LinkControl,
} from '@wordpress/block-editor';
import { dateI18n, gmdateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import {
	DELAY_OPTIONS,
	IS_12_HOUR,
	TIME_FORMAT,
	DATE_FORMAT,
} from './constants';

export default function Selector(props) {
	const { attributes, setAttributes, isAnniversary = false } = props;
	const { date, field } = attributes;

	const site = useSelect((select) => select('core').getSite());
	const [popover, setPopover] = useState(false);

	const setDate = (newDate) => {
		// store in UTC
		setAttributes({ date: new Date(newDate).toISOString() });
	};

	const allFields = useSelect((select) =>
		select('mailster/automation').getFields()
	);

	const dateFields = allFields.filter((field) => {
		return field.type == 'date';
	});

	const isInPast = +new Date() - +new Date(date) > 0;

	return (
		<>
			<PanelRow>
				<BaseControl className="widefat">
					<SelectControl
						label={
							field
								? __('Trigger based on the subscribers', 'mailster')
								: __('Select Custom Field', 'mailster')
						}
						value={field}
						onChange={(val) => {
							setAttributes({ field: val ? val : undefined });
						}}
					>
						<option value="">{__('Use a specific date', 'mailster')}</option>
						<optgroup label={__('Use a custom field', 'mailster')}>
							{dateFields.map((field, i) => {
								return (
									<option key={i} value={field.id}>
										{field.name}
									</option>
								);
							})}
						</optgroup>
					</SelectControl>
				</BaseControl>
			</PanelRow>
			<PanelRow>
				{isAnniversary && (
					<BaseControl
						label={
							field ? __('Yearly', 'mailster') : __('Yearly on the', 'mailster')
						}
						help={
							field
								? __(
										'Use this trigger to send emails based on user values. This is usefull for anniversaries like birthdays. To trigger this campaign every year you have to define repeats below.',
										'mailster'
								  )
								: __(
										'This workfow gets triggered for all subscribers on the defined date. Narrow down your selection with conditions below. If you only send a single email in this workflow consider using a regular campaign instead.',
										'mailster'
								  )
						}
					>
						{!field && (
							<>
								{'  '}
								<Button
									variant="secondary"
									onClick={(e) => setPopover(true)}
									isDestructive={!isAnniversary && isInPast}
								>
									{dateI18n('F j', date) + ' @ ' + dateI18n(TIME_FORMAT, date)}
									{popover && (
										<Popover
											onClose={(e) => setPopover(false)}
											className="delay-popover"
										>
											<DateTimePicker
												currentDate={date}
												onChange={(val) => setDate(val)}
												is12Hour={IS_12_HOUR}
												__nextRemoveHelpButton
												__nextRemoveResetButton
											/>
										</Popover>
									)}
								</Button>
							</>
						)}
						{field && (
							<>
								{' @ '}
								<Button variant="secondary" onClick={(e) => setPopover(true)}>
									{dateI18n(TIME_FORMAT, date)}
									{popover && (
										<Popover
											onClose={(e) => setPopover(false)}
											className="delay-popover"
										>
											<TimePicker
												currentDate={date}
												onChange={(val) => setDate(val)}
												is12Hour={IS_12_HOUR}
												__nextRemoveHelpButton
												__nextRemoveResetButton
											/>
										</Popover>
									)}
								</Button>
							</>
						)}
					</BaseControl>
				)}
				{!isAnniversary && (
					<BaseControl
						label={
							field
								? sprintf(__('Once on %s', 'mailster'), field)
								: __('Once on', 'mailster')
						}
						help={
							field
								? __(
										'Use this trigger to send emails based on user values.',
										'mailster'
								  )
								: __(
										'This workfow gets triggered for all subscribers on the defined date. Narrow down your selection with conditions below. If you only send a single email in this workflow consider using a regular campaign instead.',
										'mailster'
								  )
						}
					>
						{!field && (
							<>
								{'  '}
								<Button
									variant="secondary"
									onClick={(e) => setPopover(true)}
									isDestructive={!isAnniversary && isInPast}
								>
									{dateI18n(DATE_FORMAT, date) +
										' @ ' +
										dateI18n(TIME_FORMAT, date)}
									{popover && (
										<Popover
											onClose={(e) => setPopover(false)}
											className="delay-popover"
										>
											<DateTimePicker
												currentDate={date}
												onChange={(val) => setDate(val)}
												is12Hour={IS_12_HOUR}
												__nextRemoveHelpButton
												__nextRemoveResetButton
											/>
										</Popover>
									)}
								</Button>
							</>
						)}
						{field && (
							<>
								{' @ '}
								<Button variant="secondary" onClick={(e) => setPopover(true)}>
									{dateI18n(TIME_FORMAT, date)}
									{popover && (
										<Popover
											onClose={(e) => setPopover(false)}
											className="delay-popover"
										>
											<TimePicker
												currentDate={date}
												onChange={(val) => setDate(val)}
												is12Hour={IS_12_HOUR}
												__nextRemoveHelpButton
												__nextRemoveResetButton
											/>
										</Popover>
									)}
								</Button>
							</>
						)}
					</BaseControl>
				)}
			</PanelRow>
			{!isAnniversary && !field && isInPast && (
				<PanelRow>
					<Button
						variant="link"
						onClick={(e) => setPopover(true)}
						isDestructive
					>
						{__('Date is in the past!', 'mailster')}
					</Button>
				</PanelRow>
			)}
		</>
	);
}
