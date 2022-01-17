/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	PanelBody,
	PanelRow,
	Button,
	Dropdown,
	BaseControl,
	Notice,
	DateTimePicker,
	__experimentalItemGroup as ItemGroup,
	__experimentalItem as Item,
} from '@wordpress/components';

import { useState, useEffect } from '@wordpress/element';
import { format, __experimentalGetSettings } from '@wordpress/date';

/**
 * Internal dependencies
 */
import DisplayOptionsContent from './DisplayOptionsContent';
import PostTypeFields from './PostTypeFields';

function DatePicker(props) {
	const { date, setDate } = props;

	return (
		<DateTimePicker
			currentDate={date}
			onChange={(newDate) => setDate(newDate)}
			//is12Hour={true}
		/>
	);
}

function ScheduleEntry(props) {
	const { index, setDate, schedule } = props;

	const [isValid, setIsValid] = useState(false);

	function setStartDate(date) {
		setDate(index, 'start', date);
	}
	function setEndDate(date) {
		setDate(index, 'end', date);
	}

	function formatDate(date, fallback) {
		const settings = __experimentalGetSettings();
		if (!date) return fallback;
		return format(
			`${settings.formats.date} ${settings.formats.time}`,
			date
		);
	}

	useEffect(() => {
		const isValid =
			+new Date(schedule.end) - +new Date(schedule.start) > 0 ||
			(!schedule.start && !schedule.end);
		setIsValid(isValid);
	}, [schedule]);
	console.warn(new Date(schedule.end));
	console.warn(new Date(schedule.start));

	return (
		<ItemGroup className="widefat" isBordered size="medium">
			<Item>
				<BaseControl label={__('Start', 'mailster')}>
					<Dropdown
						position="bottom left"
						renderToggle={({ onToggle, isOpen }) => (
							<Button
								onClick={onToggle}
								aria-expanded={isOpen}
								variant="tertiary"
							>
								{formatDate(
									schedule.start,
									__('immediately', 'mailster')
								)}
							</Button>
						)}
						renderContent={() => (
							<DatePicker
								date={schedule.start}
								setDate={setStartDate}
							/>
						)}
					/>
				</BaseControl>
				<BaseControl label={__('End', 'mailster')}>
					<Dropdown
						position="bottom left"
						renderToggle={({ onToggle, isOpen }) => (
							<Button
								onClick={onToggle}
								aria-expanded={isOpen}
								variant="tertiary"
							>
								{formatDate(
									schedule.end,
									__('never', 'mailster')
								)}
							</Button>
						)}
						renderContent={() => (
							<DatePicker
								date={schedule.end}
								setDate={setEndDate}
							/>
						)}
					/>
				</BaseControl>
				{!isValid && (
					<Notice status="warning" isDismissible={false}>
						{__(
							'The start time is after the end time. Please fix schedule settings to function properly.',
							'mailster'
						)}
					</Notice>
				)}
			</Item>
		</ItemGroup>
	);
}

const EMPTY_SCHEDULE = {
	start: null,
	end: null,
};

export default function Schedule(props) {
	const { options, setOptions, placement } = props;
	const { type } = placement;
	const { schedule = [] } = options;

	function addSchedule() {
		const newSchedule = [...schedule];
		newSchedule.push(EMPTY_SCHEDULE);
		setOptions({ schedule: newSchedule });
	}

	function setDate(i, prop, value) {
		const newSchedule = [...schedule];
		newSchedule[i][prop] = value;
		setOptions({ schedule: newSchedule });
	}

	const isOpen = false;

	function onToggle() {
		console.warn('onToggle');
	}

	function Title() {
		return (
			<>
				{__('Schedule', 'mailster')}
				{schedule.length > 0 && (
					<span className="component-count-indicator">
						{schedule.length}
					</span>
				)}
			</>
		);
	}

	return (
		<PanelBody
			title={__('Schedule', 'mailster')}
			title={<Title />}
			initialOpen={false}
		>
			<PanelRow>
				<p>
					{__(
						'Show the form if at least one schedule applies.',
						'mailster'
					)}
				</p>
			</PanelRow>
			<PanelRow>
				{schedule.map((s, i) => {
					return (
						<ScheduleEntry
							index={i}
							key={i}
							setDate={setDate}
							schedule={s}
						/>
					);
				})}
			</PanelRow>
			<PanelRow>
				<Button variant="secondary" onClick={addSchedule}>
					{__('Add Schedule', 'mailster')}
				</Button>
			</PanelRow>
		</PanelBody>
	);
}
