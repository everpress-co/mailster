/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { createInterpolateElement } from '@wordpress/element';

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
import TriggerStepSlotFill from './TriggerStepSlotFill';

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
	return t1.length ? t1[0] : false;
}

export function getInfo(props) {
	const { attributes } = props;
	const { lists, forms, tags, pages, links, hook, field, date, offset } =
		attributes;
	const { trigger, conditions, repeat } = attributes;

	if (!trigger) {
		return <>{__('Set up a trigger', 'mailster')}</>;
	}

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
				hook && <strong className="mailster-step-badge code">{hook}</strong>
			);
		case 'date':
			if (field) {
				return (
					<>
						{formatField(
							field,
							dateI18n(TIME_FORMAT, date),
							__('On subscribers %s field at %s', 'mailster')
						)}
						{formatOffset(offset)}
					</>
				);
			}
			return createInterpolateElement(
				sprintf(__('On %s at %s', 'mailster'), '<date />', '<time />'),
				{
					date: (
						<strong className="mailster-step-badge">
							{dateI18n(DATE_FORMAT, date)}
						</strong>
					),
					time: (
						<strong className="mailster-step-badge">
							{dateI18n(TIME_FORMAT, date)}
						</strong>
					),
				}
			);
		case 'anniversary':
			if (field) {
				return (
					<>
						{formatField(
							field,
							dateI18n(TIME_FORMAT, date),
							__('Yearly based on the subscribers %s field at %s', 'mailster')
						)}
						{formatOffset(offset)}
					</>
				);
			}
			return createInterpolateElement(
				sprintf(__('On %s at %s', 'mailster'), '<date />', '<time />'),
				{
					date: (
						<strong className="mailster-step-badge">
							{dateI18n('F j', date)}
						</strong>
					),
					time: (
						<strong className="mailster-step-badge">
							{dateI18n(TIME_FORMAT, date)}
						</strong>
					),
				}
			);

		default:
			return <TriggerStepSlotFill {...props} />;
	}
}
