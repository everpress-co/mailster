/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n, sprintf } from '@wordpress/i18n';
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
	1: sprintf(__('%s day of the month', 'mailster'), '1st'),
	2: sprintf(__('%s day of the month', 'mailster'), '2nd'),
	3: sprintf(__('%s day of the month', 'mailster'), '3rd'),
	4: sprintf(__('%s day of the month', 'mailster'), '4th'),
	5: sprintf(__('%s day of the month', 'mailster'), '5th'),
	6: sprintf(__('%s day of the month', 'mailster'), '6th'),
	7: sprintf(__('%s day of the month', 'mailster'), '7th'),
	8: sprintf(__('%s day of the month', 'mailster'), '8th'),
	9: sprintf(__('%s day of the month', 'mailster'), '9th'),
	10: sprintf(__('%s day of the month', 'mailster'), '10th'),
	11: sprintf(__('%s day of the month', 'mailster'), '11st'),
	12: sprintf(__('%s day of the month', 'mailster'), '12nd'),
	13: sprintf(__('%s day of the month', 'mailster'), '13rd'),
	14: sprintf(__('%s day of the month', 'mailster'), '14th'),
	15: sprintf(__('%s day of the month', 'mailster'), '15th'),
	16: sprintf(__('%s day of the month', 'mailster'), '16th'),
	17: sprintf(__('%s day of the month', 'mailster'), '17th'),
	18: sprintf(__('%s day of the month', 'mailster'), '18th'),
	19: sprintf(__('%s day of the month', 'mailster'), '19th'),
	20: sprintf(__('%s day of the month', 'mailster'), '20th'),
	21: sprintf(__('%s day of the month', 'mailster'), '21st'),
	22: sprintf(__('%s day of the month', 'mailster'), '22nd'),
	23: sprintf(__('%s day of the month', 'mailster'), '23rd'),
	24: sprintf(__('%s day of the month', 'mailster'), '24th'),
	25: sprintf(__('%s day of the month', 'mailster'), '25th'),
	26: sprintf(__('%s day of the month', 'mailster'), '26th'),
	27: sprintf(__('%s day of the month', 'mailster'), '27th'),
	28: sprintf(__('%s day of the month', 'mailster'), '28th'),
	29: sprintf(__('%s day of the month', 'mailster'), '29th'),
	30: sprintf(__('%s day of the month', 'mailster'), '30th'),
	31: sprintf(__('%s day of the month', 'mailster'), '31st'),
	'-1': sprintf(__('%s day of the month', 'mailster'), 'Last'),
};

export const WEEK_OPTIONS = settings.l10n.weekdays;

export const START_OF_WEEK = settings.l10n.startOfWeek;

export const DATE_FORMAT = settings.formats.date;

export const TIME_FORMAT = settings.formats.time;

export const DATE_TIME_FORMAT = settings.formats.datetime;

export const IS_12_HOUR = !!settings.formats.time.match(/[a|A]/);
