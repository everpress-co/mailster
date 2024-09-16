/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n, _x, sprintf } from '@wordpress/i18n';
import { getSettings } from '@wordpress/date';

/**
 * Internal dependencies
 */

const settings = getSettings();

export const DELAY_OPTIONS = [
	{
		single: __('Minute', 'mailster'),
		plural: __('Minutes', 'mailster'),
		value: 'minutes',
	},
	{
		single: __('Hour', 'mailster'),
		plural: __('Hours', 'mailster'),
		value: 'hours',
	},
	{
		single: __('Day', 'mailster'),
		plural: __('Days', 'mailster'),
		value: 'days',
	},
	{
		single: __('Week', 'mailster'),
		plural: __('Weeks', 'mailster'),
		value: 'weeks',
	},
	{
		single: __('Month', 'mailster'),
		plural: __('Months', 'mailster'),
		value: 'months',
	},
	{ single: __('Specific Time of the day', 'mailster'), value: 'day' },
	{ single: __('Specific day of the week', 'mailster'), value: 'week' },
	{ single: __('Specific day of the month', 'mailster'), value: 'month' },
	{ single: __('Specific date', 'mailster'), value: 'year' },
];

export const MONTH_OPTIONS = {
	1: sprintf(
		__('%s day of the month', 'mailster'),
		_x('1st', '[x] day of the month', 'mailster')
	),
	2: sprintf(
		__('%s day of the month', 'mailster'),
		_x('2nd', '[x] day of the month', 'mailster')
	),
	3: sprintf(
		__('%s day of the month', 'mailster'),
		_x('3rd', '[x] day of the month', 'mailster')
	),
	4: sprintf(
		__('%s day of the month', 'mailster'),
		_x('4th', '[x] day of the month', 'mailster')
	),
	5: sprintf(
		__('%s day of the month', 'mailster'),
		_x('5th', '[x] day of the month', 'mailster')
	),
	6: sprintf(
		__('%s day of the month', 'mailster'),
		_x('6th', '[x] day of the month', 'mailster')
	),
	7: sprintf(
		__('%s day of the month', 'mailster'),
		_x('7th', '[x] day of the month', 'mailster')
	),
	8: sprintf(
		__('%s day of the month', 'mailster'),
		_x('8th', '[x] day of the month', 'mailster')
	),
	9: sprintf(
		__('%s day of the month', 'mailster'),
		_x('9th', '[x] day of the month', 'mailster')
	),
	10: sprintf(
		__('%s day of the month', 'mailster'),
		_x('10th', '[x] day of the month', 'mailster')
	),
	11: sprintf(
		__('%s day of the month', 'mailster'),
		_x('11st', '[x] day of the month', 'mailster')
	),
	12: sprintf(
		__('%s day of the month', 'mailster'),
		_x('12nd', '[x] day of the month', 'mailster')
	),
	13: sprintf(
		__('%s day of the month', 'mailster'),
		_x('13rd', '[x] day of the month', 'mailster')
	),
	14: sprintf(
		__('%s day of the month', 'mailster'),
		_x('14th', '[x] day of the month', 'mailster')
	),
	15: sprintf(
		__('%s day of the month', 'mailster'),
		_x('15th', '[x] day of the month', 'mailster')
	),
	16: sprintf(
		__('%s day of the month', 'mailster'),
		_x('16th', '[x] day of the month', 'mailster')
	),
	17: sprintf(
		__('%s day of the month', 'mailster'),
		_x('17th', '[x] day of the month', 'mailster')
	),
	18: sprintf(
		__('%s day of the month', 'mailster'),
		_x('18th', '[x] day of the month', 'mailster')
	),
	19: sprintf(
		__('%s day of the month', 'mailster'),
		_x('19th', '[x] day of the month', 'mailster')
	),
	20: sprintf(
		__('%s day of the month', 'mailster'),
		_x('20th', '[x] day of the month', 'mailster')
	),
	21: sprintf(
		__('%s day of the month', 'mailster'),
		_x('21st', '[x] day of the month', 'mailster')
	),
	22: sprintf(
		__('%s day of the month', 'mailster'),
		_x('22nd', '[x] day of the month', 'mailster')
	),
	23: sprintf(
		__('%s day of the month', 'mailster'),
		_x('23rd', '[x] day of the month', 'mailster')
	),
	24: sprintf(
		__('%s day of the month', 'mailster'),
		_x('24th', '[x] day of the month', 'mailster')
	),
	25: sprintf(
		__('%s day of the month', 'mailster'),
		_x('25th', '[x] day of the month', 'mailster')
	),
	26: sprintf(
		__('%s day of the month', 'mailster'),
		_x('26th', '[x] day of the month', 'mailster')
	),
	27: sprintf(
		__('%s day of the month', 'mailster'),
		_x('27th', '[x] day of the month', 'mailster')
	),
	28: sprintf(
		__('%s day of the month', 'mailster'),
		_x('28th', '[x] day of the month', 'mailster')
	),
	29: sprintf(
		__('%s day of the month', 'mailster'),
		_x('29th', '[x] day of the month', 'mailster')
	),
	30: sprintf(
		__('%s day of the month', 'mailster'),
		_x('30th', '[x] day of the month', 'mailster')
	),
	31: sprintf(
		__('%s day of the month', 'mailster'),
		_x('31st', '[x] day of the month', 'mailster')
	),
	'-1': __('Last day of the month', 'mailster'),
};

export const WEEK_OPTIONS = settings.l10n.weekdays;

export const START_OF_WEEK = settings.l10n.startOfWeek;

export const DATE_FORMAT = settings.formats.date;

export const TIME_FORMAT = settings.formats.time;

export const DATE_TIME_FORMAT = settings.formats.datetime;

export const IS_12_HOUR = !!settings.formats.time.match(/[a|A]/);
