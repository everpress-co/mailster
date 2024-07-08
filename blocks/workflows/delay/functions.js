/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { sprintf, __, _n } from '@wordpress/i18n';
import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import {
	DELAY_OPTIONS,
	WEEK_OPTIONS,
	MONTH_OPTIONS,
	DATE_FORMAT,
	TIME_FORMAT,
	DATE_TIME_FORMAT,
} from './constants.js';

const WAIT_FOR = __('Wait for %s', 'mailster');
const WAIT_UNTIL = __('Wait until %s', 'mailster');

export function getInfo(attributes) {
	const { unit, amount, weekdays, date = new Date(), month = 1 } = attributes;
	if (isRelative(unit)) {
		return '';
	}

	const currDate = new Date(date);

	switch (unit) {
		case 'day':
			return '';

		case 'week':
			if (!weekdays || weekdays.length == 7) {
				return __('on ever day in the week.', 'mailster');
			}

			const names = WEEK_OPTIONS.filter((key, index) => {
				return weekdays.includes(index);
			})
				.join(', ')
				.replace(/,([^,]*)$/, ' ' + __('or', 'mailster') + '$1');

			return sprintf(__('on a %s.', 'mailster'), names);

		case 'month':
			return sprintf(__('on the %s.', 'mailster'), MONTH_OPTIONS[month]);

		case 'year':
			return sprintf(__('on the %s.', 'mailster'), dateI18n(DATE_FORMAT, date));
	}

	return new Date(date).toString();
}

export function getLabel(attributes) {
	const { unit, amount, weekdays, date = new Date(), month = 1 } = attributes;

	const value = getDate(attributes);

	if (isRelative(unit)) {
		return sprintf(WAIT_FOR, value);
	}
	return sprintf(WAIT_UNTIL, value);
}

function getDate(attributes) {
	const { unit, amount, weekdays, date = new Date(), month = 1 } = attributes;
	if (isRelative(unit)) {
		const element = DELAY_OPTIONS.find((item) => item.value === unit);

		return sprintf(
			'%d %s',
			amount,
			(amount > 1 && element.plural) || element.single
		);
	}

	const currDate = new Date(date);

	switch (unit) {
		case 'day':
		case 'week':
		case 'month':
		case 'year':
			return dateI18n(TIME_FORMAT, currDate);
	}

	return dateI18n(TIME_FORMAT, currDate);
}

export function isRelative(unit) {
	return ['minutes', 'hours', 'days', 'weeks', 'months'].includes(unit);
}
