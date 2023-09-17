/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelRow,
	PanelBody,
	CheckboxControl,
	SelectControl,
	FlexItem,
	Flex,
	BaseControl,
	TimePicker,
	DateTimePicker,
	Button,
	Popover,
	ToggleControl,
	__experimentalNumberControl as NumberControl,
	Tip,
} from '@wordpress/components';
import { dateI18n, gmdateI18n } from '@wordpress/date';

import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */

import {
	DELAY_OPTIONS,
	MONTH_OPTIONS,
	WEEK_OPTIONS,
	START_OF_WEEK,
	IS_12_HOUR,
} from './constants';

import { HelpBeacon } from '../../util';

const ORDERERD_WEEK_OPTIONS = [
	...WEEK_OPTIONS.slice(START_OF_WEEK),
	...WEEK_OPTIONS.slice(0, START_OF_WEEK),
];

export default function DelayInspectorControls({
	attributes,
	setAttributes,
	getLabel,
}) {
	const {
		amount,
		date,
		unit,
		timezone,
		month,
		weekdays = [0, 1, 2, 3, 4, 5, 6],
	} = attributes;

	const [popover, setPopover] = useState(false);

	const isInPast = +new Date() - +new Date(date) > 0;

	const setDate = (newDate) => {
		// store in UTC
		setAttributes({ date: new Date(newDate).toISOString() });
	};

	function setWeek(index, add = true) {
		var newWeek = [...weekdays];
		if (add) {
			newWeek.push(index);
		} else {
			newWeek = newWeek.filter((el) => {
				return el != index;
			});
		}
		setAttributes({ weekdays: newWeek.length ? newWeek.sort() : undefined });
	}

	function setUnit(val) {
		switch (val) {
			case 'month':
				if (!month) setAttributes({ month: 1 });
				break;
			case 'year':
				// set date in the future (+ 1 day)
				if (isInPast) setDate(+new Date() + 60000 * 60 * 24);
				break;
		}
		setAttributes({ unit: val });
	}

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<HelpBeacon id="64623a1035c39a6db5f441e4" align="right" />
					<PanelRow>
						<BaseControl label="Delay Workflow">
							<Flex gap={4} align="center" justify="space-between">
								{['minutes', 'hours', 'days', 'weeks', 'months'].includes(
									unit
								) && (
									<>
										{__('for', 'mailster')}
										<FlexItem>
											<NumberControl
												onChange={(val) =>
													setAttributes({ amount: parseInt(val, 10) })
												}
												value={amount}
												min="1"
											/>
										</FlexItem>
									</>
								)}
								<FlexItem>
									<SelectControl
										value={unit}
										options={Object.keys(DELAY_OPTIONS).map((key, index) => {
											return {
												label:
													(amount > 1 && DELAY_OPTIONS[key].plural) ||
													DELAY_OPTIONS[key].single,
												value: DELAY_OPTIONS[key].value,
											};
										})}
										onChange={(val) => setUnit(val)}
									/>
								</FlexItem>
							</Flex>
						</BaseControl>
					</PanelRow>
					{unit === 'day' && (
						<PanelRow>
							<Flex gap={4} align="center" justify="space-between">
								<BaseControl
									label={__(
										'Wait until current time of the day is',
										'mailster'
									)}
									help={__(
										'If the current time is later the workflow will be delayed by one day.',
										'mailster'
									)}
								>
									<FlexItem>
										<Button
											variant="secondary"
											onClick={(e) => setPopover(true)}
										>
											{getLabel()}
											{popover && (
												<Popover
													onClose={(e) => setPopover(false)}
													className="delay-popover"
												>
													<TimePicker
														currentTime={date}
														onChange={(val) => setDate(val)}
														is12Hour={IS_12_HOUR}
													/>
												</Popover>
											)}
										</Button>
									</FlexItem>
								</BaseControl>
							</Flex>
						</PanelRow>
					)}
					{unit === 'week' && (
						<>
							<PanelRow>
								<BaseControl
									label={__(
										'Wait until current time of the day is',
										'mailster'
									)}
								>
									<Button variant="secondary" onClick={(e) => setPopover(true)}>
										{getLabel()}
										{popover && (
											<Popover
												onClose={(e) => setPopover(false)}
												className="delay-popover"
											>
												<TimePicker
													currentTime={date}
													onChange={(val) => setDate(val)}
													is12Hour={IS_12_HOUR}
												/>
											</Popover>
										)}
									</Button>
								</BaseControl>
							</PanelRow>
							<PanelRow>
								<BaseControl
									label={__('only on', 'mailster')}
									help={__(
										'If the current time is later the workflow will be delayed by next selected weekday.',
										'mailster'
									)}
								>
									{ORDERERD_WEEK_OPTIONS.map((key, index) => {
										const i = WEEK_OPTIONS.indexOf(
											ORDERERD_WEEK_OPTIONS[index]
										);
										return (
											<CheckboxControl
												className="inspector-checkbox"
												key={i}
												label={ORDERERD_WEEK_OPTIONS[index]}
												checked={!weekdays || weekdays.includes(i)}
												onChange={(val) => {
													setWeek(i, val);
												}}
												__nextHasNoMarginBottom
											/>
										);
									})}
								</BaseControl>
							</PanelRow>
						</>
					)}
					{unit === 'month' && (
						<>
							<PanelRow>
								<BaseControl
									label={__(
										'Wait until current time of the day is',
										'mailster'
									)}
								>
									<Button variant="secondary" onClick={(e) => setPopover(true)}>
										{getLabel()}
										{popover && (
											<Popover
												onClose={(e) => setPopover(false)}
												className="delay-popover"
											>
												<TimePicker
													currentTime={date}
													onChange={(val) => setDate(val)}
													is12Hour={IS_12_HOUR}
												/>
											</Popover>
										)}
									</Button>
								</BaseControl>
							</PanelRow>
							<PanelRow>
								<BaseControl
									label={__('and it is the', 'mailster')}
									help={__(
										'If the current time is later the workflow will be delayed by next selected day in the next month.',
										'mailster'
									)}
								>
									<SelectControl
										value={month}
										options={Object.keys(MONTH_OPTIONS).map((key, index) => {
											return { label: MONTH_OPTIONS[key], value: key };
										})}
										onChange={(val) =>
											setAttributes({ month: parseInt(val, 10) })
										}
									/>
								</BaseControl>
							</PanelRow>
							{month >= 29 && (
								<PanelRow>
									<Tip>
										{sprintf(
											__(
												"The month will be skipped if it doesn't have %d days.",
												'mailster'
											),
											month
										)}
									</Tip>
								</PanelRow>
							)}
						</>
					)}
					{unit === 'year' && (
						<>
							<PanelRow>
								<BaseControl label={__('Continue Workflow on the', 'mailster')}>
									<Button
										variant="secondary"
										onClick={(e) => setPopover(true)}
										isDestructive={isInPast}
									>
										{dateI18n('Y-m-d', date) + ' @ ' + dateI18n('H:i', date)}
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
								</BaseControl>
							</PanelRow>
							{isInPast && (
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
					)}
				</PanelBody>
				{['day', 'week', 'month', 'year'].includes(unit) && (
					<PanelBody>
						<PanelRow>
							<FlexItem>
								<HelpBeacon id="63fb5f4e0b394c459d8a5c1e" align="right" />
								<ToggleControl
									onChange={(val) => setAttributes({ timezone: !timezone })}
									checked={timezone}
									label={__('Timezone based sending', 'mailster')}
									help={__(
										'Delay based on the subscribers timezone if known. This is usefull if you have global subscribers and like to get the email in their in box at the defined time.',
										'mailster'
									)}
								/>
							</FlexItem>
						</PanelRow>
					</PanelBody>
				)}
			</Panel>
		</InspectorControls>
	);
}
