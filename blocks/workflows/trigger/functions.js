/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import {
	formatLists,
	formatForms,
	formatTags,
	formatPages,
	formatField,
	formatOffset,
	formatLinks,
} from '../../util';

import { TIME_FORMAT, DATE_FORMAT } from './constants';

export function getTrigger(id) {
	const allTriggers = useSelect((select) =>
		select('mailster/automation').getTriggers()
	);

	if (!allTriggers) {
		return null;
	}
	const t1 = allTriggers.filter((t) => {
		return t.id == id;
	});
	return t1.length ? t1[0] : null;
}

export function getInfo(attributes) {
	const { lists, forms, tags, pages, links, hook, field, date, offset } =
		attributes;
	const { trigger, conditions, repeat } = attributes;

	switch (trigger) {
		case 'list_add':
		case 'list_removed':
			return formatLists(lists);
		case 'form_conversion':
			return formatForms(forms);
		case 'tag_added':
		case 'tag_removed':
			return formatTags(tags);
		case 'updated_field':
			return formatField(field, false);
		case 'page_visit':
			return formatPages(pages);
		case 'link_click':
			return formatLinks(links);
		case 'hook':
			return (
				hook && '<strong class="mailster-step-badge code">' + hook + '</strong>'
			);
		case 'date':
			if (field) {
				return (
					formatField(
						field,
						dateI18n(TIME_FORMAT, date),
						__('On subscribers %s field at %s', 'mailster')
					) + formatOffset(offset)
				);
			}
			return sprintf(
				__('On %s at %s', 'mailster'),
				'<strong class="mailster-step-badge">' +
					dateI18n(DATE_FORMAT, date) +
					'</strong>',
				'<strong class="mailster-step-badge">' +
					dateI18n(TIME_FORMAT, date) +
					'</strong>'
			);
		case 'anniversary':
			if (field) {
				return (
					formatField(
						field,
						dateI18n(TIME_FORMAT, date),
						__('Yearly based on the subscribers %s field at %s', 'mailster')
					) + formatOffset(offset)
				);
			}
			return sprintf(
				__('Yearly on the %s at %s', 'mailster'),
				'<strong class="mailster-step-badge">' +
					dateI18n('F j', date) +
					'</strong>',
				'<strong class="mailster-step-badge">' +
					dateI18n(TIME_FORMAT, date) +
					'</strong>'
			);

		default:
			return __('Set up a trigger', 'mailster');
	}
}
