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
	__experimentalNumberControl as NumberControl,
	BaseControl,
	DateTimePicker,
	SelectControl,
	Popover,
	TimePicker,
	FlexBlock,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

import { dateI18n, gmdateI18n, humanTimeDiff } from '@wordpress/date';

/**
 * Internal dependencies
 */
import {
	DELAY_OPTIONS,
	IS_12_HOUR,
	TIME_FORMAT,
	DATE_FORMAT,
} from './constants';
import { set } from 'lodash';
import { Flex } from '@wordpress/components';
import { FlexItem } from '@wordpress/components';

export default function Selector(props) {
	const { attributes, setAttributes, isAnniversary = false } = props;
	const { date, field, offset = 0 } = attributes;

	const site = useSelect((select) => select('core').getSite());
	const [popover, setPopover] = useState(false);
	const [relative, setRelative] = useState('');
	const [amount, setAmount] = useState(0);
	const [unit, setUnit] = useState(0);

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

	useEffect(() => {
		if (!offset) return;
		if (offset % 604800 == 0) {
			setUnit(604800);
			setAmount(offset / 604800);
		} else if (offset % 86400 == 0) {
			setUnit(86400);
			setAmount(offset / 86400);
		} else if (offset % 3600 == 0) {
			setUnit(3600);
			setAmount(offset / 3600);
		} else {
			setUnit(60);
			setAmount(Math.floor(offset / 60));
		}
	}, [offset]);
	useEffect(() => {
		if (field) return;

		setUnit(0);
		setAmount(0);
	}, [field]);

	useEffect(() => {
		const newOffset = amount * unit;
		setAttributes({ offset: newOffset || undefined });
	}, [unit, amount]);

	const isInPast = +new Date() - +new Date(date) - offset * 1000 > 0;

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
			{field && (
				<PanelRow>
					<BaseControl
						label={__('Offset', 'mailster')}
						help={__(
							'Offset the time to trigger either before ar after the defined date.',
							'mailster'
						)}
					>
						<Flex expanded={false}>
							<FlexBlock>
								<NumberControl
									step={1}
									shiftStep={10}
									value={amount}
									onChange={(val) => {
										setAmount(val);
									}}
								/>
							</FlexBlock>
							<FlexBlock>
								<SelectControl
									value={unit}
									onChange={(val) => {
										setUnit(val);
									}}
									options={[
										{ value: 1, label: __('Seconds', 'mailster') },
										{ value: 60, label: __('Minutes', 'mailster') },
										{ value: 3600, label: __('Hours', 'mailster') },
										{ value: 86400, label: __('Days', 'mailster') },
										{ value: 604800, label: __('Weeks', 'mailster') },
									]}
								/>
							</FlexBlock>
							<FlexBlock>
								<Button
									variant="link"
									size={'compact'}
									isDestructive
									onClick={() => setAmount(0)}
								>
									{__('Reset', 'mailster')}
								</Button>
							</FlexBlock>
						</Flex>
					</BaseControl>
				</PanelRow>
			)}
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
